# Panduan Deployment Development — XAMPP

Panduan untuk menjalankan sistem di **localhost** menggunakan XAMPP.

---

## 1. Prasyarat

| Aplikasi | Versi | Keterangan |
|:---------|:-----:|:-----------|
| XAMPP | 8.2+ | PHP + MySQL + Apache |
| Composer | 2.x | Dependency PHP |
| Python | 3.10+ | Untuk AI Service |
| Node.js | 18+ | Untuk Vite/assets |
| Git | - | Version control |

---

## 2. Setting XAMPP

### 2.1 Aktifkan Module
Buka **XAMPP Control Panel**, start:
- **Apache** (Port 80)
- **MySQL** (Port 3306)

### 2.2 PHP Extensions
Pastikan di `php.ini` extension berikut aktif:
```ini
extension=fileinfo
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=zip
```

---

## 3. Database MySQL

### 3.1 Buat Database via phpMyAdmin
1. Buka `http://localhost/phpmyadmin`
2. Klik **New**
3. Database name: `medical_ai`
4. Collation: `utf8mb4_unicode_ci`
5. Klik **Create**

Atau via terminal:
```bash
mysql -u root -p
CREATE DATABASE medical_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

## 4. Backend Laravel

```bash
# Pindah ke folder project
cd medical-ai-system/backend-laravel

# Install dependencies PHP
composer install

# Copy .env dan setting database
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env — sesuaikan database
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=medical_ai
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migration & seeder
php artisan migrate --seed

# Buat storage link
php artisan storage:link

# Install & build assets (Vite)
npm install
npm run build

# Jalankan development server
php artisan serve
```

Laravel akan berjalan di: **http://127.0.0.1:8000**

---

## 5. AI Service (FastAPI)

```bash
# Pindah ke folder AI Service
cd medical-ai-system/ai-service

# Install dependencies Python
pip install -r requirements.txt

# Jalankan server
python main.py
```

FastAPI akan berjalan di: **http://127.0.0.1:8000**

---

## 6. Verifikasi

| Halaman | URL |
|:--------|:----|
| Beranda | http://127.0.0.1:8000 |
| Form Pemeriksaan | http://127.0.0.1:8000/pemeriksaan |
| Login Admin | http://127.0.0.1:8000/login |
| Dashboard Admin | http://127.0.0.1:8000/dashboard |
| FastAPI Health | http://127.0.0.1:8000/health |

**Akun Admin Default:**
- Email: `admin@medical-ai.com`
- Password: `password`

---

## 7. Troubleshooting

### 7.1 `composer install` error
```bash
composer clear-cache
composer install --no-scripts
```

### 7.2 Storage link error
```bash
php artisan storage:link
# Jika folder public/storage sudah ada, hapus dulu:
rm public/storage
php artisan storage:link
```

### 7.3 Permission denied storage/
```bash
# Windows: right-click folder storage/ → Properties → Security → Full Control
# Atau via terminal (run as admin):
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

### 7.4 FastAPI module not found
```bash
pip install --upgrade -r requirements.txt
```

### 7.5 Port 8000 sudah dipakai
```bash
# Laravel: ganti port
php artisan serve --port=8080

# FastAPI: ganti port
python main.py --port 8001

# Update .env AI_SERVICE_URL sesuai port baru
```
