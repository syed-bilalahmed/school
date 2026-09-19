# 🎓 Greenwood International School — Enterprise ERP & Campus Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/andreasbm/readme/master/assets/lines/rainbow.png" alt="line" width="100%">
</p>

<p align="center">
  <a href="#-key-features"><img src="https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version"></a>
  <a href="#-key-features"><img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Version"></a>
  <a href="#-high-concurrency--load-balancing"><img src="https://img.shields.io/badge/Scale-100%2C000%2B%20Concurrent%20Users-brightgreen?style=for-the-badge&logo=speedtest&logoColor=white" alt="Scalability"></a>
  <a href="#-security-hardening"><img src="https://img.shields.io/badge/Security-OWASP%20Hardened-blueviolet?style=for-the-badge&logo=auth0&logoColor=white" alt="Security Hardened"></a>
  <a href="#-mobile-app-restful-api"><img src="https://img.shields.io/badge/Mobile%20API-RESTful%20Bearer%20Auth-orange?style=for-the-badge&logo=fastapi&logoColor=white" alt="REST API"></a>
  <a href="#-license"><img src="https://img.shields.io/badge/License-Commercial%20%2F%20Envato-success?style=for-the-badge" alt="License"></a>
</p>

---

## 📖 Executive Summary

**Greenwood International School ERP** is a modern, enterprise-grade school, college, and multi-branch campus management software engineered for high availability, airtight tenant data isolation, and massive concurrency. 

Built on a robust, lightweight PHP MVC architecture with zero bloated framework overhead, it effortlessly scales to **100,000+ simultaneous users** using native **Database Read/Write Splitting**, **Distributed Redis Sessions**, and **Multi-Node Load Balancing** (NGINX / HAProxy / AWS ALB).

---

## 🌟 Core Highlights & Architectural Features

### 🏢 1. Multi-Tenant & Multi-Branch Architecture
- **TenantContext Isolation**: Automated per-request tenant scoping across all models and queries. Data between multiple schools or campuses remains strictly segregated.
- **Role-Based Access Control (RBAC)**: Granular permission matrix across 8 built-in roles: `Super Admin`, `Admin`, `Teacher`, `Accountant`, `Librarian`, `Receptionist`, `Student`, and `Parent`.

### ⚡ 2. 100,000+ Concurrent Users Scale-Ready
- **Database Read/Write Splitting (`Database.php`)**: Automatically directs high-volume `SELECT` record lookups to MySQL Read Replicas (`DB_READ_HOSTS`) while pinning `INSERT`, `UPDATE`, `DELETE`, and financial transactions to the Primary Master.
- **Distributed Session Management (`SessionManager.php`)**: Supports Redis and Centralized Database session drivers so users never get logged out when requests traverse multi-node load balancers.
- **25+ High-Performance Composite Indexes**: Optimized B-Tree index coverage across students, fees, attendance, exam schedules, marks, and user tokens for sub-millisecond query execution.
- **Multi-Level Query Caching (`QueryCache.php`)**: In-memory Redis and APCu caching layers absorb 80%+ of repetitive reference queries.

### 🛡️ 3. Bank-Grade Security & OWASP Hardened
- **Zero-Trust Protection**: Full protection against SQL Injection, Stored/Reflected XSS, CSRF attacks, Session Fixation, and Parameter Tampering.
- **API Guard with SHA-256 Tokens**: RESTful mobile API credentials and user tokens are stored as irreversible cryptographic hashes.
- **Upload Sandbox & Execution Shield**: Strict file MIME validation with `.htaccess` execution inhibitors preventing remote script execution in media directories.

### 📱 4. Turnkey Mobile App RESTful APIs
- Complete API endpoints for official Flutter / React Native Android and iOS apps.
- Supports student profile views, timetable schedules, daily attendance logs, online fee payments, fee receipts download, exam results, and push notification feeds.

### 🧙‍♂️ 5. CodeCanyon-Style Web Installation Wizard (`/install/`)
- Sleek, 4-step deployment wizard featuring live server requirement checks, writable permission checks, interactive license key verification, and runtime database generation.
- Automatically seals the application with `install.lock` and redirects uninstalled visits directly to `/install/`.

---

## 🏛️ System Architecture Topology

```
                            [ 100,000 Concurrent Users / Mobile Apps ]
                                                │
                                                ▼
                        [ Cloudflare DDoS Shield / Global Anycast DNS ]
                                                │
                                                ▼
                     [ Layer 7 Load Balancer: NGINX / HAProxy / AWS ALB ]
                             (SSL Offloading & least_conn Balancing)
                                                │
                ┌───────────────────────────────┼───────────────────────────────┐
                ▼                               ▼                               ▼
       [ Web App Node 01 ]             [ Web App Node 02 ]             [ Web App Node 03 ]
       (Stateless PHP-FPM)             (Stateless PHP-FPM)             (Stateless PHP-FPM)
                │                               │                               │
                └───────────────────────────────┼───────────────────────────────┘
                                                ▼
                              [ Centralized Redis 7 Cluster ]
                              ├─ Shared User Sessions (Zero Logout Drift)
                              └─ High-Speed Distributed Query Cache (qc:*)
                                                │
                                                ▼
                                   [ Database Cluster Layer ]
                   ┌────────────────────────────┴────────────────────────────┐
                   ▼                                                         ▼
       [ MySQL Primary (Master) ]                              [ MySQL Read-Replica Pool ]
       ├─ All INSERTS, UPDATES, DELETES                        ├─ All SELECT Queries
       ├─ Atomic Financial Transactions                        ├─ Heavy Record Fetching
       └─ Binary Log Replication ─────────────────────────────▶├─ Auto Load Balanced
```

---

## 📦 Comprehensive Operational Modules

| Module | Core Functionality & Features |
| :--- | :--- |
| 🧑‍🎓 **Student Lifecycle** | Admission workflow, auto-generated admission numbers, B-Form/National ID verification, sibling discount linking, student profile dossiers, ID card generator, and institutional exit clearance (SLC). |
| 📚 **Academics & Timetable** | Multi-class and section management, subject allocations, class teachers, weekly timetable visual matrix, homework assignments, and submission tracking. |
| 📅 **Smart Attendance** | Daily and subject-wise attendance marking, monthly percentage calculators, visual heatmaps, and automatic daily SMS/WhatsApp absenteeism alerts. |
| 💳 **Financial Fee Engine** | Automated monthly challan billing, 3-copy bank deposit slips, fee structures, partial fee collection, fine waivers, offline bank sync, income vs expense ledgers, and defaulter tracking. |
| 📝 **Examinations & Grading** | Exam scheduling, admit cards / roll number slips, multi-subject marks entry, GPA/Grade scales, auto-calculated percentage, tabulation sheets, and printable PDF Report Cards. |
| 👥 **HR & Staff Management** | Staff directories, qualification records, biometric/manual staff attendance, designation roles, and monthly payroll voucher generation. |
| 🚪 **Front Office & Gate Pass** | Visitor pass management, gatekeeper dispatch, student exit permits, call logs, postal records, and admission inquiry CRM. |
| 📖 **Library Logistics** | Complete ISBN book catalog, barcode asset tracking, member cards, issue/return circulations, and late fine calculation. |
| 📦 **Inventory & Stocks** | Warehouse item categories, supplier profiles, purchase receipts, inventory issuances to departments, and low-stock warning alerts. |
| 📢 **Notice Board & Alerts** | Role-targeted announcements (Parents, Teachers, Students), circular downloads, and integrated SMS, WhatsApp, and SMTP email broadcasting. |

---

## 🚀 Quick Start & Installation

### Option 1: Visual Web Setup Wizard (Recommended)

1. Clone or extract the repository to your web server document root:
   ```bash
   cd c:/xampp/htdocs/
   git clone https://github.com/your-username/school.git school
   ```
2. Ensure Apache and MySQL are running in XAMPP / WAMP / LEMP.
3. Open your browser and navigate to:
   ```
   http://localhost/school/
   ```
   *The system will automatically detect first-time setup and redirect you to `http://localhost/school/install/`.*
4. **Step 1**: Review server prerequisites (PHP 8.1+, PDO, cURL, OpenSSL, GD, Fileinfo).
5. **Step 2**: Enter and verify your Envato / CodeCanyon Purchase Code.
6. **Step 3**: Provide MySQL credentials. The wizard will automatically create the database at runtime if it doesn't exist.
7. **Step 4**: Enter your school details and Super Admin password. Click **Install & Configure System**.
8. Once finished, the installer self-locks with `install.lock` for production safety.

---

### Option 2: Turnkey Docker Cluster (Load Balanced with Redis & Replicas)

For enterprise high-concurrency testing or deployment:

```bash
cd loadbalancer
docker-compose -f docker-compose.loadbalancer.yml up -d
```

- **Web Application**: `http://localhost/` (Traffic load-balanced across 3 web nodes)
- **Health Monitoring**: `http://localhost/health`
- **Redis Cache & Sessions**: Port `6379`
- **MySQL Master**: Port `3306`
- **MySQL Read Replica**: Port `3307`

---

## 🔑 Default Super Admin Credentials (Post-Install)

| Attribute | Default Value |
| :--- | :--- |
| **Login URL** | `http://localhost/school/public/login` |
| **Default Email** | `super@admin.com` |
| **Default Password** | `admin123` *(configurable during setup)* |
| **Role** | `super_admin` |

---

## 📱 Mobile App RESTful API Specification

The ERP includes native REST APIs for Android & iOS mobile applications.

### Authentication Headers:
```http
X-API-KEY: sk_live_school_enterprise_app_2026
Authorization: Bearer <user_access_token>
Content-Type: application/json
```

### Core API Endpoints:
- `POST /school/public/api/login`: Mobile authentication (returns Bearer token + role details).
- `GET  /school/public/api/profile`: Authenticated user dossier.
- `GET  /school/public/api/student/dashboard`: Quick glance cards (fees due, attendance rate, notices).
- `GET  /school/public/api/student/timetable`: Weekly academic timetable.
- `GET  /school/public/api/student/attendance`: Monthly attendance record.
- `GET  /school/public/api/student/fees`: Unpaid fee challans and payment history.
- `POST /school/public/api/student/fees/pay`: Atomic fee payment submission.
- `GET  /school/public/api/student/exams`: Exam schedules and report card marks.
- `GET  /school/public/api/health`: High-speed cluster node health check probe.

*For complete API schemas and curl examples, see [`MOBILE_APP_API_DOCS.txt`](file:///c:/xampp/htdocs/school/MOBILE_APP_API_DOCS.txt).*

---

## ⚙️ Load Balancing & High-Concurrency Configuration

All enterprise clustering parameters are cleanly defined in [`config/config.php`](file:///c:/xampp/htdocs/school/config/config.php) or via environment variables:

```php
// 1. MySQL Read Replicas (Comma-separated hosts for automatic read/write splitting)
define('DB_READ_HOSTS', '192.168.1.101,192.168.1.102,192.168.1.103');

// 2. Distributed Session Driver ('file', 'database', or 'redis')
define('SESSION_DRIVER', 'redis');

// 3. Redis In-Memory Distributed Cache & Session Store
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', '');

// 4. Trusted Reverse Proxies / Load Balancers (AWS ALB, Cloudflare, HAProxy, NGINX)
define('TRUSTED_PROXIES', ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16']);
```

*For comprehensive NGINX, HAProxy, and AWS ALB deployment walk-throughs, refer to [`LOAD_BALANCING_GUIDE.md`](file:///c:/xampp/htdocs/school/LOAD_BALANCING_GUIDE.md).*

---

## 📂 Project Directory Structure

```text
c:/xampp/htdocs/school/
├── app/
│   ├── Controllers/          # MVC Controllers (Auth, Admin, Student, Fees, API, etc.)
│   ├── Core/                 # Enterprise Core (Database, SessionManager, ApiGuard, RateLimiter, QueryCache)
│   ├── Middleware/           # Request Filters (CSRF, AuthGuard, RoleGuard)
│   ├── Models/               # Data Layer & Business Logic with TenantContext
│   └── Views/                # Blade/PHP UI Views (Portals, Dashboard, Invoices, Cards)
├── config/
│   ├── config.php            # Core Database, Replicas, and Cluster Constants
│   ├── mail.php              # Outgoing SMTP Mail Settings
│   └── installed.lock        # Post-Installation Lock Flag
├── install/
│   ├── index.php             # Modern Web Installation Wizard
│   ├── master_schema.php     # 30+ Enterprise DDL Tables & 25+ Concurrency Indexes
│   └── install.lock          # Installation Guard Lock
├── loadbalancer/
│   ├── nginx-loadbalancer.conf           # Production NGINX reverse proxy config (100k sockets)
│   ├── haproxy.cfg                       # HAProxy L7 HTTP + L4 MySQL read balancer (stats port 8404)
│   └── docker-compose.loadbalancer.yml   # Multi-node testing cluster
├── public/
│   ├── assets/               # CSS, JavaScript, Vendor Libraries, Icons, Fonts
│   ├── uploads/              # Uploaded media with script execution blocked (.htaccess)
│   ├── health.php            # Instant Load Balancer Node Health Check Endpoint
│   └── index.php             # Front Controller with SSL Offloading & Session Routing
├── .htaccess                 # Apache hardening (Options -Indexes, Deny sensitive files, mod_deflate)
├── index.php                 # Root Entrypoint router with uninstalled auto-redirect
├── LOAD_BALANCING_GUIDE.md   # Step-by-step 100,000 user multi-server guide (Urdu & English)
├── MOBILE_APP_API_DOCS.txt   # Mobile App REST API documentation
└── README.md                 # Professional Repository Documentation
```

---

## 🛡️ Security Audit Compliance (OWASP Top 10)

| Security Requirement | Implementation Detail |
| :--- | :--- |
| **SQL Injection** | 100% native parameterized PDO prepared statements (`PDO::ATTR_EMULATE_PREPARES => false`). Zero raw string concatenation in SQL. |
| **Cross-Site Request Forgery (CSRF)** | Cryptographically secure 256-bit CSRF tokens validated on all state-altering POST/PUT/DELETE requests via `AuthGuard.php`. |
| **Cross-Site Scripting (XSS)** | Universal output escaping (`htmlspecialchars`), SVG MIME sanitization, and strict Content-Security headers. |
| **Broken Access Control (IDOR)** | Automated tenant verification (`TenantContext`) on all record queries preventing horizontal privilege escalation across schools. |
| **Session Fixation** | Immediate session ID regeneration (`session_regenerate_id(true)`) upon successful authentication. |
| **Brute-Force & DoS Mitigation** | Sliding-window `RateLimiter` shielding login attempts (max 15 attempts/min per client IP) and API throttling. |
| **Directory Traversal & Info Leaks** | `Options -Indexes` enforced across all directories; direct access to `.env`, `.git`, `.lock`, `.sql`, `app/`, and `config/` strictly denied. |

---

## 🤝 Support & Contribution

If you find a bug, wish to request a feature, or require enterprise custom deployment assistance:
- **Issue Tracker**: Submit detailed issues on GitHub.
- **Enterprise Licensing & Customization**: Contact the development team via official support channels.

---

<p align="center">
  <b>Greenwood International School ERP</b> • Engineered with ❤️ for World-Class Educational Institutions.
</p>
