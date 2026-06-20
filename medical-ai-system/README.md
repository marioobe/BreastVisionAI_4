# 🏥 OsteoScan AI — Sistem Klasifikasi Citra Medis

Sistem klasifikasi citra medis berbasis **MobileNetV2 + Transfer Learning** untuk deteksi dini kepadatan tulang (Normal/Osteopenia/Osteoporosis) menggunakan arsitektur **Microservice**: Laravel + FastAPI + MySQL.

## 📋 Fitur

### 👤 Pengguna
- 📄 Landing page informasi sistem
- 📝 Form biodata pasien (NIK, nama, gender, tanggal lahir)
- 🖼️ Upload gambar rontgen (JPG/JPEG/PNG, max 10MB)
- ✅ Checkbox disclaimer (tombol submit disabled sampai diceklis)
- 🤖 Analisis AI otomatis
- 📊 Tampilan hasil: prediksi, confidence, probabilitas semua kelas
- 🖨️ Export PDF hasil analisis
- 📱 Layout responsive mobile & desktop

### 🔐 Admin
- 📊 Dashboard statistik (total pasien, prediksi, model)
- 🤖 CRUD Model AI (upload .keras, edit, hapus, aktivasi)
- 📈 Monitoring model (nama, versi, metrik)
- 📋 Manajemen hasil prediksi (lihat, filter, hapus)

## 🏗️ Arsitektur

```
             ┌─────────────────────────────────┐
             │     Browser (Blade Frontend)     │
             └──────────────┬──────────────────┘
                            │
              ┌─────────────▼────────────────┐
              │     Laravel 12 (Backend)      │
              │  ┌─────────────────────────┐  │
              │  │  Web Routes (session)   │  │
              │  │  API Routes (Sanctum)   │  │
              │  │  PredictionService      │  │
              │  │  AiService (Guzzle)     │  │
              │  └──────────┬──────────────┘  │
              └─────────────┼──────────────────┘
                            │ HTTP POST /predict
              ┌─────────────▼──────────────────┐
              │   FastAPI AI Service (Python)   │
              │  ┌──────────────────────────┐  │
              │  │  Preprocessing 224×224   │  │
              │  │  MobileNetV2 Model       │  │
              │  │  Softmax Classification  │  │
              │  └──────────────────────────┘  │
              └─────────────────────────────────┘
                            │
              ┌─────────────▼──────────────────┐
              │         MySQL Database          │
              │  patients, predictions,         │
              │  prediction_probabilities,      │
              │  ai_models, admins,              │
              │  activity_logs                   │
              └─────────────────────────────────┘
```

## 🗂️ Struktur Folder

```
medical-ai-system/
├── backend-laravel/    # Laravel 12 (API + Web)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/       # Web auth & dashboard
│   │   │   │   └── Api/         # REST API controllers
│   │   │   └── Requests/        # Form Request validation
│   │   ├── Models/              # Eloquent models
│   │   └── Services/            # Business logic (AiService, PredictionService)
│   ├── database/
│   │   ├── factories/           # Model factories
│   │   ├── migrations/          # DB migrations
│   │   └── seeders/             # Database seeders
│   ├── resources/views/         # Blade templates
│   │   ├── layouts/
│   │   ├── pasien/
│   │   ├── dashboard/
│   │   └── admin/
│   ├── routes/
│   │   ├── api.php              # API routes
│   │   └── web.php              # Web routes
│   └── tests/                   # PHPUnit tests
├── ai-service/         # FastAPI Python service
│   ├── main.py                 # API server
│   ├── test_main.py            # Pytest tests
│   └── requirements.txt        # Python dependencies
├── models/             # Deep learning model
│   ├── model.keras             # Trained model
│   ├── class_names.json        # Class labels
│   ├── class_indices.json      # Class indices
│   └── history.json            # Training history
├── storage/            # Upload & logs
├── database/           # ERD & DB scripts
├── docs/               # Documentation
│   ├── api-documentation.md
│   ├── postman-collection.json
│   └── flowcharts.md
└── deployment/         # Docker & nginx configs
```

## 🧠 Model Deep Learning

- **Arsitektur:** MobileNetV2 (ImageNet) + GlobalAveragePooling2D → Dense(256) → Dropout(0.5) → Dense(128) → Dropout(0.3) → Dense(3, Softmax)
- **Dataset:** Breast Ultrasound Images (BUSI) — 3 kelas: Benign, Malignant, Normal
- **Transfer Learning:** Feature extraction + Fine Tuning (unfreeze from layer 120)
- **Callbacks:** EarlyStopping, ReduceLROnPlateau, ModelCheckpoint, TensorBoard
- **Output:** `model.keras`

## 🚀 Cara Menjalankan

### Prasyarat
- PHP 8.2+
- Composer
- Python 3.10+
- MySQL
- Node.js & npm (untuk Vite)

### 1. Backend Laravel
```bash
cd backend-laravel
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

### 2. AI Service (FastAPI)
```bash
cd ai-service
pip install -r requirements.txt
python main.py
# Running on http://0.0.0.0:8000
```

### 3. Akses Aplikasi
- **Website:** http://127.0.0.1:8000
- **Login Admin:** http://127.0.0.1:8000/login
- **Admin Default:** `admin@medical-ai.com` / `password`

## 🧪 Testing

### Laravel
```bash
cd backend-laravel
php artisan test
# 20 tests, 45 assertions
```

### FastAPI
```bash
cd ai-service
python -m pytest test_main.py -v
```

## 📡 API Endpoints

| Method | Endpoint | Auth | Deskripsi |
|:------:|:---------|:----:|:----------|
| POST | `/api/login` | ❌ | Login admin |
| POST | `/api/predict` | ✅ | Prediksi gambar |
| GET | `/api/models` | ✅ | Daftar model |
| POST | `/api/models` | ✅ | Upload model |
| PUT | `/api/models/{id}` | ✅ | Edit model |
| DELETE | `/api/models/{id}` | ✅ | Hapus model |
| POST | `/api/models/{id}/activate` | ✅ | Aktifkan model |
| GET | `/api/predictions` | ✅ | Daftar prediksi |
| GET | `/api/predictions/{id}` | ✅ | Detail prediksi |
| DELETE | `/api/predictions/{id}` | ✅ | Hapus prediksi |
| GET | `/api/dashboard` | ✅ | Dashboard statistik |

Dokumentasi lengkap: [docs/api-documentation.md](docs/api-documentation.md)

## 🛠️ Teknologi

| Layer | Teknologi |
|:------|:----------|
| Frontend | Blade, CSS3 (custom), JavaScript |
| Backend | Laravel 12, PHP 8.2 |
| AI Service | FastAPI, Python 3.10 |
| Database | MySQL |
| Machine Learning | TensorFlow, Keras, MobileNetV2 |
| HTTP Client | Guzzle (Laravel Http) |
| Auth | Laravel Sanctum (API) + Session (Web) |
| Testing | PHPUnit, Pytest |

## 📊 Flowchart

Lihat [docs/flowcharts.md](docs/flowcharts.md) untuk diagram:
1. Flowchart Sistem (arsitektur)
2. Flowchart User (alur pasien)
3. Flowchart Admin (alur admin)
4. Flowchart Prediksi AI (alur teknis prediksi)

---

📌 **Proyek Akademik** — UAS Kecerdasan Buatan / Skripsi
