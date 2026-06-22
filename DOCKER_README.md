# 🏥 InfyHMS v14.0 — Docker Deployment Guide

> **InfyHMS** — Laravel 10 + PHP 8.1 + MySQL 8 + Redis 7

---

## 📋 Prerequisites

| Tool | Version |
|------|---------|
| Docker Desktop | 24.x+ |
| Docker Compose | v2.x+ |
| RAM | 4GB+ recommended |

---

## 🚀 Quick Start (5 minutes)

### Step 1 — Environment Setup
```powershell
# hms folder mein jao
cd "d:\Mobile Devices\InfyHMS v14.0\InfyHMS v14.0\dist\hms"

# Docker env copy karo
copy .docker.env .env
```

### Step 2 — Build & Start
```powershell
docker compose up -d --build
```

### Step 3 — Wait & Open Browser
- ⏳ **1-3 minutes** wait karo (first time slow hoga)
- 🌐 App: **http://localhost:8080**
- 🗄️ phpMyAdmin: **http://localhost:8081**

---

## 🔧 Configuration

`.env` file mein ye settings change karo:

```env
# App Settings
APP_URL=http://localhost:8080
APP_DEBUG=false

# Database (IMPORTANT: Change passwords!)
DB_PASSWORD=your_strong_password_here
DB_ROOT_PASSWORD=your_root_password_here

# Mail Settings
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=noreply@yourhospital.com
```

---

## 🐳 Docker Services

| Service | Port | Purpose |
|---------|------|---------|
| **app** | 8080 | Laravel Application (PHP 8.1 + Apache) |
| **mysql** | 3306 | MySQL 8.0 Database |
| **redis** | 6379 | Cache + Sessions + Queue |
| **phpmyadmin** | 8081 | Database Management GUI |
| **queue** | — | Laravel Queue Worker |

---

## 📟 Common Commands

### Start / Stop
```powershell
# Start karo
docker compose up -d

# Stop karo
docker compose down

# Restart karo
docker compose restart
```

### Logs Dekhna
```powershell
# Sab logs
docker compose logs -f

# Sirf app
docker compose logs -f app

# Sirf MySQL
docker compose logs -f mysql
```

### Shell Access
```powershell
# App container mein enter karo
docker compose exec app bash

# MySQL mein enter karo
docker compose exec mysql mysql -u hms_user -phms_secret hms
```

### Laravel Commands
```powershell
# Migrations run karo
docker compose exec app php artisan migrate

# Cache clear karo
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear

# Storage link banao
docker compose exec app php artisan storage:link

# Koi bhi artisan command
docker compose exec app php artisan <command>
```

---

## 💾 Database Backup & Restore

### Backup Lena
```powershell
# Backup folder banao
mkdir backups

# Backup karo
docker compose exec mysql mysqldump -u root -proot_secret_change_me_123 hms > backups\hms_backup.sql
```

### Restore Karna
```powershell
docker compose exec -T mysql mysql -u root -proot_secret_change_me_123 hms < backups\hms_backup.sql
```

---

## 🔐 Production Deployment Checklist

- [ ] `.env` mein strong passwords set karo
- [ ] `APP_DEBUG=false` karo
- [ ] `APP_ENV=production` karo
- [ ] Proper `APP_URL` set karo
- [ ] SSL/HTTPS setup karo (Nginx reverse proxy)
- [ ] Mail settings configure karo
- [ ] Database backup cron set karo
- [ ] `APP_KEY` change karo: `docker compose exec app php artisan key:generate`

---

## 🌐 Production (VPS/Server) Deployment

### Nginx Reverse Proxy (HTTPS ke liye)
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

---

## ❌ Common Issues & Fix

### Issue: "Port 8080 already in use"
```powershell
# .env mein port change karo
APP_PORT=9090
PMA_PORT=9091
docker compose up -d
```

### Issue: "MySQL connection refused"
```powershell
# MySQL status check karo
docker compose ps
docker compose logs mysql
# MySQL ready hone tak wait karo
```

### Issue: "Permission denied on storage"
```powershell
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Issue: "Page not found / 404"
```powershell
# Storage link recreate karo
docker compose exec app php artisan storage:link --force
# Cache clear karo
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
```

---

## 📁 Docker Files Structure

```
hms/
├── Dockerfile                    ← Main PHP+Apache image
├── docker-compose.yml            ← All services config
├── .docker.env                   ← Docker environment template
├── .dockerignore                 ← Build context exclusions
├── Makefile                      ← Easy commands
└── docker/
    ├── apache/
    │   └── 000-default.conf      ← Apache VirtualHost
    ├── mysql/
    │   ├── my.cnf                ← MySQL performance config
    │   └── init.sql              ← DB initialization
    ├── supervisor/
    │   └── supervisord.conf      ← Queue worker config
    ├── cron/
    │   └── laravel-cron          ← Laravel Scheduler cron
    └── scripts/
        └── entrypoint.sh         ← Container startup script
```

---

## 🆘 Support

Problems? These logs dekho:
```powershell
docker compose logs app --tail=100
docker compose logs mysql --tail=50
```
