# MySQL Performance & High-Concurrency Guide (3000+ Users)

This document provides database tuning parameters, recommended composite indexes, and load-balancer configurations to keep the School Management ERP fast, stable, and responsive under peak traffic (e.g. results day, fee due dates, morning attendance rush).

---

## 1. `my.ini` Tuning (XAMPP / MySQL / MariaDB)

Open `C:\xampp\mysql\bin\my.ini` (or your production `/etc/mysql/my.cnf`) and apply under the `[mysqld]` section:

```ini
[mysqld]
# ----------------------------------------------------------------------
# 1. Connection Limits (Default 151 is too small for 3000 users)
# ----------------------------------------------------------------------
max_connections         = 500
max_user_connections    = 450
thread_cache_size       = 64
wait_timeout            = 300
interactive_timeout     = 300

# ----------------------------------------------------------------------
# 2. InnoDB Engine (Crucial — School ERP tables use InnoDB)
# ----------------------------------------------------------------------
# Allocate 50-70% of available server RAM to InnoDB buffer pool:
# If server has 8GB RAM:  set to 4G or 5G
# If server has 16GB RAM: set to 10G or 12G
# For local dev / small VPS (4GB RAM):
innodb_buffer_pool_size         = 1G
innodb_buffer_pool_instances     = 4
innodb_log_file_size            = 256M
innodb_log_buffer_size          = 16M
innodb_flush_log_at_trx_commit  = 2     # Drastically speeds up fee token batch generation
innodb_file_per_table           = 1
innodb_read_io_threads          = 4
innodb_write_io_threads         = 4

# ----------------------------------------------------------------------
# 3. Query & Sort Buffers
# ----------------------------------------------------------------------
table_open_cache        = 2000
table_definition_cache  = 1400
sort_buffer_size        = 2M
read_buffer_size        = 1M
read_rnd_buffer_size    = 2M
join_buffer_size        = 2M
tmp_table_size          = 64M
max_heap_table_size     = 64M

# ----------------------------------------------------------------------
# 4. Slow Query Logging (Helps spot bottlenecks before they crash the server)
# ----------------------------------------------------------------------
slow_query_log          = 1
slow_query_log_file     = "mysql-slow.log"
long_query_time         = 1.5   # Log any query taking > 1.5 seconds
```

After modifying `my.ini`, restart MySQL from the XAMPP Control Panel.

---

## 2. High-Performance Database Indexes

Run the following SQL queries in phpMyAdmin (`http://localhost/phpmyadmin`) or MySQL CLI to index the tables most heavily queried when 3000 students and parents are active:

```sql
-- 1. Students table: Fast lookup by school, status, and class/section
ALTER TABLE students 
    ADD INDEX idx_school_status_class (school_id, status, class_id),
    ADD INDEX idx_school_admission_no (school_id, admission_no),
    ADD INDEX idx_school_parent_user (school_id, parent_user_id);

-- 2. Student Fees table: High-speed fee collection and challan checks
ALTER TABLE student_fees 
    ADD INDEX idx_school_student_month (school_id, student_id, billing_month),
    ADD INDEX idx_school_challan (school_id, challan_no),
    ADD INDEX idx_school_status_due (school_id, status, due_date);

-- 3. Fee Payments table: Quick joins during receipt generation
ALTER TABLE fee_payments 
    ADD INDEX idx_student_fee_id (student_fee_id),
    ADD INDEX idx_payment_date (payment_date);

-- 4. Student Attendance table: Fast morning roll-call and monthly reports
ALTER TABLE student_attendance 
    ADD INDEX idx_school_class_date (school_id, class_id, section_id, attendance_date),
    ADD INDEX idx_school_student_date (school_id, student_id, attendance_date);

-- 5. Users table: Instant authentication
ALTER TABLE users 
    ADD INDEX idx_email_school (email, school_id),
    ADD INDEX idx_role (role);

-- 6. Incomes table: Fast finance ledger & cash book daily closing
ALTER TABLE incomes 
    ADD INDEX idx_school_date (school_id, date);
```

---

## 3. Market-Level Load Balancing Architecture

For enterprise scale (5,000 to 20,000 active students and parents across multiple campuses):

```
                        [ Internet / Cloudflare CDN ]
                         (Caches static CSS/JS/Images)
                                      │
                                      ▼
                        [ Nginx Load Balancer / Reverse Proxy ]
                         (SSL Termination + Round-Robin / Least Conn)
                                      │
                 ┌────────────────────┴────────────────────┐
                 ▼                                         ▼
       [ App Node 1: Apache/PHP ]               [ App Node 2: Apache/PHP ]
        Health: /school/health                   Health: /school/health
                 └────────────────────┬────────────────────┘
                                      │
                         [ MySQL Master + Replica ]
                           (InnoDB Persistent Pool)
```

### Nginx Upstream Configuration Example:
```nginx
upstream school_backend {
    least_conn;
    server 192.168.1.10:80 max_fails=3 fail_timeout=10s;
    server 192.168.1.11:80 max_fails=3 fail_timeout=10s;
    keepalive 32;
}

server {
    listen 80;
    server_name portal.school.edu.pk;

    location / {
        proxy_pass http://school_backend;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```
