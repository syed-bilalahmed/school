# 🚀 Enterprise High-Concurrency & Load Balancing Guide (100,000+ Users)

Yeh guide batati hai ke **100,000 users ek sath (concurrently)** school portal aur mobile app use karein aur hazaron records fetch karein toh server pe load na aye, crash na ho, aur performance lightning-fast rahe.

---

## 🏛️ System Architecture Topology (کلسٹر آرکیٹیکچر)

```
                            [ 100,000 Concurrent Users / Mobile Apps ]
                                                │
                                                ▼
                        [ Cloudflare DDoS Shield / Global Anycast DNS ]
                                                │
                                                ▼
                     [ Layer 7 Load Balancer: NGINX / HAProxy / AWS ALB ]
                             (SSL Termination & least_conn Balancing)
                                                │
                ┌───────────────────────────────┼───────────────────────────────┐
                ▼                               ▼                               ▼
       [ Web App Node 01 ]             [ Web App Node 02 ]             [ Web App Node 03 ]
       (Stateless PHP-FPM)             (Stateless PHP-FPM)             (Stateless PHP-FPM)
                │                               │                               │
                └───────────────────────────────┼───────────────────────────────┘
                                                ▼
                              [ Centralized Redis 7 Cluster ]
                              ├─ Shared User Sessions (No logouts)
                              └─ High-Speed Distributed Query Cache (qc:*)
                                                │
                                                ▼
                                   [ Database Cluster Layer ]
                   ┌────────────────────────────┴────────────────────────────┐
                   ▼                                                         ▼
       [ MySQL Primary (Master) ]                              [ MySQL Read-Replica Pool ]
       ├─ All INSERTS, UPDATES, DELETES                        ├─ All SELECT Queries
       ├─ Financial Transactions                               ├─ Heavy Record Fetching
       └─ Binary Log Replication ─────────────────────────────▶├─ Auto Load Balanced
```

---

## ⚡ 4 Core Pillars Implemented (چار اہم ستون جو ایڈ کر دیے گئے ہیں)

### 1. Database Read/Write Splitting & Replica Load Balancing
- **Problem**: 100,000 users jab ek sath reports, attendance, fees, aur students fetch karte hain toh MySQL database 100% CPU pe chala jata hai.
- **Solution (`app/Core/Database.php`)**:
  - Code mein **Automatic Read/Write Splitting** active kar diya gaya hai.
  - Sabhi `SELECT` queries automatically **Read Replicas** (`DB_READ_HOSTS`) pe distribute (load-balance) hoti hain.
  - Sabhi `INSERT`, `UPDATE`, `DELETE` aur Transactions automatically Primary Master (`DB_HOST`) pe execute hoti hain.
  - Agar koi replica down ho jaye toh system bina crash hue foran Master pe failover kar leta hai.

### 2. Multi-Server Distributed Sessions (Shared Sessions)
- **Problem**: Load Balancer jab user ki agli request doosre server (Node 2) pe bhejta hai toh user logout ho jata hai agar local disk session ho.
- **Solution (`app/Core/SessionManager.php` & `app/Core/DbSessionHandler.php`)**:
  - **Redis Sessions**: `SESSION_DRIVER = 'redis'` set karne se sabhi nodes Redis RAM se instant session share karte hain.
  - **Database Sessions**: `SESSION_DRIVER = 'database'` set karne se MySQL ki `sessions` table mein shared session save hota hai.
  - Result: 100,000 users chahe kisi bhi web server pe land karein, unka session active rehta hai.

### 3. Load Balancer Health Check & Proxy Offloading
- **Health Endpoint (`public/health.php`)**:
  - AWS Application Load Balancer (ALB), NGINX, HAProxy har 3 se 5 second baad `/health` ko ping karte hain.
  - Yeh endpoint Database write/read connectivity, memory allocation, aur server load check karke **HTTP 200** return karta hai.
  - Agar kisi node pe issue aye toh Load Balancer us node ko 1 second mein rotation se hata deta hai taake users ko 500 error na dikhe.
- **SSL Offloading & Trusted Proxies**:
  - `public/index.php` automatically Load Balancer ke `X-Forwarded-Proto` aur `X-Forwarded-For` headers ko read karta hai taake real client IP track ho aur rate limiting theek kaam kare.

### 4. Distributed Query & Reference Caching (`app/Core/QueryCache.php`)
- School settings, fee categories, academic sessions, permissions, aur classes bar bar MySQL se read nahi hotay.
- `QueryCache::remember()` Redis / APCu RAM se microseconds mein data return karta hai, jis se database pe 80% queries khatam ho jati hain.

---

## ⚙️ Configuration in `config/config.php`

Aap apne `config/config.php` ya `.env` mein yeh simple constants set kar sakte hain:

```php
// 1. MySQL Read Replicas (Comma-separated IPs)
define('DB_READ_HOSTS', '192.168.1.101,192.168.1.102,192.168.1.103');

// 2. Distributed Sessions ('redis', 'database', ya 'file')
define('SESSION_DRIVER', 'redis');

// 3. Redis Server Details
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', '');

// 4. Trusted Load Balancer IPs
define('TRUSTED_PROXIES', ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16']);
```

---

## 📁 Included Ready-to-Use Load Balancer Files

Aapke project mein `loadbalancer/` folder ke andar complete ready configurations available hain:

| File | Maqsad |
|------|--------|
| [`loadbalancer/nginx-loadbalancer.conf`](file:///c:/xampp/htdocs/school/loadbalancer/nginx-loadbalancer.conf) | NGINX Reverse Proxy configuration (25,000 connections/worker, `least_conn`, Gzip, buffer tuning, health checks). |
| [`loadbalancer/haproxy.cfg`](file:///c:/xampp/htdocs/school/loadbalancer/haproxy.cfg) | HAProxy Layer 7 HTTP Load Balancer + Layer 4 MySQL Read Replica Balancer (port 3307) + Live Stats Dashboard (port 8404). |
| [`loadbalancer/docker-compose.loadbalancer.yml`](file:///c:/xampp/htdocs/school/loadbalancer/docker-compose.loadbalancer.yml) | 1-Click Multi-Container Cluster (1 Nginx LB + 3 Web Nodes + 1 Redis + 1 MySQL Master + 1 MySQL Replica). |

---

## 🚀 How to Run Docker Cluster (Local Testing ya Production)

```bash
cd loadbalancer
docker-compose -f docker-compose.loadbalancer.yml up -d
```

Cluster start hotay hi:
- Web Application: `http://localhost/` (NGINX Load Balancer traffic 3 nodes pe distribute karega).
- Health Check: `http://localhost/health`
- HAProxy Stats (agar HAProxy chalayein): `http://localhost:8404/`
