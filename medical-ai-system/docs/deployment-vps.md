# Panduan Deployment Production — VPS Ubuntu

Panduan untuk mendeploy sistem ke **VPS Ubuntu 22.04 LTS**.

---

## 1. Spesifikasi Server Minimum

| Komponen | Minimal | Rekomendasi |
|:---------|:-------:|:-----------:|
| CPU | 2 core | 4 core |
| RAM | 4 GB | 8 GB |
| Storage | 20 GB | 40 GB SSD |
| OS | Ubuntu 22.04 | Ubuntu 22.04 LTS |
| Domain | - | example.com |

---

## 2. Setup Awal Server

```bash
# SSH ke server
ssh user@ip-server

# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-gd php8.2-zip php8.2-curl composer \
    python3 python3-pip python3-venv \
    git unzip curl supervisor certbot python3-certbot-nginx
```

---

## 3. Database MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login ke MySQL
sudo mysql -u root

# Buat database & user
CREATE DATABASE medical_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'medical_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON medical_ai.* TO 'medical_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4. Clone Project

```bash
cd /var/www
sudo git clone https://github.com/username/medical-ai-system.git
sudo chown -R $USER:$USER medical-ai-system
```

---

## 5. Backend Laravel

```bash
cd /var/www/medical-ai-system/backend-laravel

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy .env
cp .env.example .env
nano .env
```

Edit `.env` untuk production:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_ai
DB_USERNAME=medical_user
DB_PASSWORD=strong_password_here

AI_SERVICE_URL=http://127.0.0.1:8001
```

Lanjutkan setup:
```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## 6. Nginx

Buat file konfigurasi:
```bash
sudo nano /etc/nginx/sites-available/medical-ai
```

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name example.com www.example.com;
    root /var/www/medical-ai-system/backend-laravel/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2?|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Aktifkan site:
```bash
sudo ln -s /etc/nginx/sites-available/medical-ai /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate (Let's Encrypt)
```bash
sudo certbot --nginx -d example.com -d www.example.com
```

---

## 7. AI Service (FastAPI)

### 7.1 Setup Python Virtual Environment
```bash
cd /var/www/medical-ai-system
python3 -m venv venv
source venv/bin/activate
pip install -r ai-service/requirements.txt
```

### 7.2 Supervisor Configuration
Jalankan FastAPI sebagai service dengan Supervisor:

```bash
sudo nano /etc/supervisor/conf.d/fastapi.conf
```

```ini
[program:fastapi]
command=/var/www/medical-ai-system/venv/bin/uvicorn main:app --host 127.0.0.1 --port 8001
directory=/var/www/medical-ai-system/ai-service
user=www-data
autostart=true
autorestart=true
stderr_logfile=/var/log/fastapi.err.log
stdout_logfile=/var/log/fastapi.out.log
environment=PATH="/var/www/medical-ai-system/venv/bin"
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start fastapi
```

---

## 8. Queue Worker (Jika diperlukan)

```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/medical-ai-system/backend-laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/medical-ai-system/backend-laravel
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
```

---

## 9. Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

---

## 10. Verifikasi

```bash
# Cek status service
sudo supervisorctl status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm

# Cek log
tail -f /var/log/fastapi.out.log
tail -f /var/log/nginx/medical-ai-error.log
```

---

## 11. Monitoring & Maintenance

### Backup Database
```bash
# Cron job — backup setiap hari jam 2 pagi
0 2 * * * /usr/bin/mysqldump -u medical_user -p'password' medical_ai > /backup/medical_ai_$(date +\%Y\%m\%d).sql
```

### Update Aplikasi
```bash
cd /var/www/medical-ai-system
git pull origin main
cd backend-laravel
composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart all
```

### Logs
```bash
# Laravel
tail -f /var/www/medical-ai-system/backend-laravel/storage/logs/laravel.log

# FastAPI
tail -f /var/log/fastapi.err.log

# Nginx
tail -f /var/log/nginx/medical-ai-error.log
```

---

## 12. Docker Deployment (Alternatif)

Jika lebih suka Docker, gunakan `docker-compose.yml` yang sudah disediakan:

```bash
cd /var/www/medical-ai-system/deployment
docker-compose up -d --build
```

---

© 2026 — OsteoScan AI — Panduan Deployment VPS Ubuntu
