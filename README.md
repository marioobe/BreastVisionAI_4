# BreastVisionAI 4

**Sistem Klasifikasi Citra USG Payudara Berbasis Artificial Intelligence**

BreastVisionAI 4 adalah sistem microservice untuk klasifikasi citra Ultrasound (USG) payudara menggunakan **MobileNetV2 + Transfer Learning + Dense Layer + Softmax**. Sistem mampu mengklasifikasikan hasil USG ke dalam 3 kategori: **Benign**, **Malignant**, dan **Normal**.

Proyek ini dibuat sebagai tugas UAS / Skripsi pada mata kuliah Kecerdasan Buatan.

---

## Daftar Isi

- [Fitur](#fitur)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Tech Stack](#tech-stack)
- [Struktur Folder](#struktur-folder)
- [Database](#database)
- [Model Deep Learning](#model-deep-learning)
- [Cara Install dan Menjalankan](#cara-install-dan-menjalankan)
- [Testing](#testing)
- [API Endpoints](#api-endpoints)
- [Dataset](#dataset)
- [Deployment](#deployment)
- [Dokumentasi](#dokumentasi)

---

## Fitur

### Fitur User (Pasien)

| Fitur | Keterangan |
|-------|------------|
| Landing Page | Halaman beranda informasi sistem, cara kerja, kategori hasil |
| Form Biodata | Input NIK, nama lengkap, jenis kelamin, tanggal lahir, alamat, nomor HP, email |
| Upload Gambar USG | Format JPG/JPEG/PNG, maksimal 10 MB, dilengkapi preview gambar |
| Disclaimer Wajib | Checkbox persetujuan AI (tombol submit disabled sampai dicentang) |
| Analisis Otomatis | Prediksi dilakukan oleh AI secara real-time |
| Halaman Hasil | Menampilkan kelas prediksi, confidence score, probabilitas semua kelas, nama model, analysis_time, informasi pasien |
| Export PDF | Cetak hasil prediksi via browser print (berisi biodata, gambar, hasil, disclaimer) |
| Layout Responsif | Tampilan mobile dan desktop |

### Fitur Admin

| Fitur | Keterangan |
|-------|------------|
| Login Session-based | Autentikasi admin via session (bukan token) |
| Dashboard | Statistik total pasien, total prediksi, total model, model aktif, total penggunaan model, distribusi kelas (chart), 10 prediksi terbaru |
| CRUD Model AI | Upload model `.keras`, edit nama/versi/akurasi/loss, hapus, aktivasi (auto-deactivate model lama) |
| Monitoring Model | Lihat nama, versi, akurasi, loss, jumlah penggunaan, tanggal upload |
| Manajemen Prediksi | List pagination + search NIK/nama + filter kelas/tanggal, detail (gambar + probabilitas bar chart + data pasien), hapus |

---

## Arsitektur Sistem

```
                          ┌───────────────────────────────────┐
                          │      Browser (Blade Frontend)      │
                          │  HTML5 + CSS3 + JavaScript         │
                          └──────────────┬────────────────────┘
                                         │
                          ┌──────────────▼───────────────────┐
                          │       Laravel 12 (Backend)        │
                          │   PHP 8.2.12 + Composer 2.9.2    │
                          │  ┌─────────────────────────────┐ │
                          │  │  Web Routes (Session Auth)  │ │
                          │  │  API Routes (Sanctum Auth)  │ │
                          │  │  PredictionService           │ │
                          │  │  AiService (Guzzle HTTP)     │ │
                          │  │  Blade Views (Blade Engine)  │ │
                          │  └──────────┬──────────────────┘ │
                          └─────────────┼─────────────────────┘
                                        │ HTTP POST /predict
                          ┌─────────────▼─────────────────────┐
                          │   FastAPI AI Service (Python)      │
                          │   Python 3.12 + Uvicorn 0.30.0    │
                          │  ┌─────────────────────────────┐  │
                          │  │  POST /predict               │  │
                          │  │  GET /health                 │  │
                          │  │  Preprocessing 224x224       │  │
                          │  │  MobileNetV2 Model (Keras)   │  │
                          │  │  Softmax Classification      │  │
                          │  │  CORS Middleware             │  │
                          │  └─────────────────────────────┘  │
                          └─────────────┬─────────────────────┘
                                        │
                          ┌─────────────▼─────────────────────┐
                          │         MySQL Database             │
                          │   7 tabel + port 3306             │
                          │  database: medical_ai             │
                          └───────────────────────────────────┘
```

### Alur Sistem

1. Pasien membuka halaman pemeriksaan
2. Mengisi biodata (NIK, nama, JK, tgl lahir, alamat, HP, email)
3. Upload gambar USG (max 10 MB, format JPG/JPEG/PNG)
4. Mencentang checkbox disclaimer
5. Klik "Mulai Analisis"
6. Laravel mengirim gambar ke FastAPI (port 8001)
7. FastAPI melakukan preprocessing (224x224, MobileNetV2 preprocess_input)
8. Model MobileNetV2 melakukan prediksi
9. Hasil (prediction, confidence, probabilities) dikembalikan ke Laravel
10. Laravel menyimpan hasil ke database MySQL
11. Hasil ditampilkan ke pasien
12. Pasien dapat mencetak PDF

---

## Tech Stack

### Frontend

| Teknologi | Keterangan |
|-----------|------------|
| HTML5 | Struktur halaman |
| CSS3 | Styling kustom (dark theme, teal accent) |
| JavaScript | Interaktivitas frontend |
| Blade (Laravel) | Template engine |
| Google Fonts | Fraunces (heading), Inter (body), JetBrains Mono (kode) |

### Backend

| Teknologi | Versi |
|-----------|-------|
| Laravel | 12.x |
| PHP | 8.2.12 |
| Composer | 2.9.2 |
| Node.js | 24.11.1 |
| npm | 11.6.2 |
| Vite | 6.x |
| Laravel Sanctum | 4.0 (API auth) |
| Laravel Http (Guzzle) | HTTP Client ke FastAPI |

### AI Service

| Teknologi | Versi |
|-----------|-------|
| Python | 3.12.x |
| FastAPI | 0.115.0 |
| Uvicorn | 0.30.0 |
| TensorFlow | 2.21.0 |
| Keras | 3.14.1 |
| Pillow | 10.4.x (image processing) |
| NumPy | - |
| python-multipart | 0.0.9 (file upload) |
| Pytest | 8.x |
| httpx | 0.27.x (test client) |

### Machine Learning Model

| Komponen | Detail |
|----------|--------|
| Arsitektur Dasar | MobileNetV2 (ImageNet weights) |
| Input Layer | 224x224x3 |
| Feature Extractor | MobileNetV2 + GlobalAveragePooling2D |
| Dense Layer 1 | Dense(256, ReLU) + Dropout(0.5) |
| Dense Layer 2 | Dense(128, ReLU) + Dropout(0.3) |
| Output Layer | Dense(3, Softmax) |
| Optimizer | Adam (learning rate 0.0001 untuk fine tuning) |
| Loss Function | Categorical Crossentropy |
| Callbacks | EarlyStopping, ReduceLROnPlateau, ModelCheckpoint, TensorBoard |
| Transfer Learning | Feature extraction + Fine Tuning (unfreeze from layer 120) |
| Output File | `model.keras` |

### Database

| Komponen | Detail |
|----------|--------|
| DBMS | MySQL |
| Port | 3306 |
| Database | `medical_ai` |
| Tabel | 7 tabel |

### Tools Pendukung

| Tools | Kegunaan |
|-------|----------|
| XAMPP | Development environment (Apache + MySQL + PHP) |
| Git | Version control |
| GitHub | Repository remote |
| Docker | Containerization |
| Nginx | Web server production |
| Supervisor | Process manager production |

---

## Struktur Folder

```
UAS_KecerdasanBuatan_Kel4/
├── README.md                         # Dokumentasi proyek ini
├── Prompt.md                         # Spesifikasi awal proyek
├── Rangkuman.md                      # Ringkasan proyek
├── TODO.md                           # Tracking progress
├── archive.zip                       # Arsip (tidak di-commit)
├── medical-ai-system/
│   ├── backend-laravel/              # Laravel 12
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   │   ├── Admin/
│   │   │   │   │   │   ├── AuthController.php         # Login/logout admin
│   │   │   │   │   │   ├── DashboardController.php     # Dashboard admin
│   │   │   │   │   │   ├── AiModelController.php       # CRUD model web
│   │   │   │   │   │   └── PredictionController.php    # Manajemen prediksi web
│   │   │   │   │   ├── Api/
│   │   │   │   │   │   ├── AuthController.php          # API login
│   │   │   │   │   │   ├── DashboardController.php     # API dashboard
│   │   │   │   │   │   ├── AiModelController.php       # API CRUD model
│   │   │   │   │   │   └── PredictionController.php    # API prediksi + results
│   │   │   │   │   └── Controller.php
│   │   │   │   └── Requests/
│   │   │   │       ├── LoginRequest.php
│   │   │   │       ├── PredictRequest.php
│   │   │   │       ├── StoreAiModelRequest.php
│   │   │   │       └── UpdateAiModelRequest.php
│   │   │   ├── Models/
│   │   │   │   ├── Admin.php
│   │   │   │   ├── Patient.php
│   │   │   │   ├── AiModel.php
│   │   │   │   ├── Prediction.php
│   │   │   │   ├── PredictionProbability.php
│   │   │   │   ├── ActivityLog.php
│   │   │   │   └── User.php
│   │   │   └── Services/
│   │   │       ├── AiService.php                       # HTTP Client ke FastAPI
│   │   │       └── PredictionService.php                # Logika bisnis prediksi
│   │   ├── config/
│   │   │   ├── app.php
│   │   │   ├── auth.php
│   │   │   ├── database.php
│   │   │   ├── sanctum.php
│   │   │   └── session.php
│   │   ├── database/
│   │   │   ├── migrations/                             # 9 file migration
│   │   │   ├── factories/                              # Model factories
│   │   │   └── seeders/                                # Database seeders
│   │   ├── resources/views/
│   │   │   ├── layouts/
│   │   │   │   └── app.blade.php                       # Layout utama
│   │   │   ├── landing.blade.php                       # Halaman beranda
│   │   │   ├── pasien/
│   │   │   │   ├── form.blade.php                      # Form pemeriksaan
│   │   │   │   └── hasil.blade.php                     # Hasil prediksi
│   │   │   ├── dashboard/
│   │   │   │   └── index.blade.php                     # Dashboard admin
│   │   │   ├── admin/
│   │   │   │   ├── login.blade.php                     # Login admin
│   │   │   │   ├── models/
│   │   │   │   │   ├── index.blade.php                 # Manajemen model
│   │   │   │   │   └── edit.blade.php                  # Edit model
│   │   │   │   └── predictions/
│   │   │   │       ├── index.blade.php                 # List prediksi
│   │   │   │       └── show.blade.php                  # Detail prediksi
│   │   │   └── welcome.blade.php
│   │   ├── routes/
│   │   │   ├── web.php                                 # Web routes
│   │   │   └── api.php                                 # API routes
│   │   └── tests/
│   │       ├── Feature/
│   │       │   ├── AuthTest.php
│   │       │   ├── AiModelTest.php
│   │       │   ├── PredictionTest.php
│   │       │   └── ExampleTest.php
│   │       └── Unit/
│   │           ├── PredictionServiceTest.php
│   │           └── ExampleTest.php
│   ├── ai-service/                     # FastAPI Python
│   │   ├── main.py                    # Server FastAPI (port 8001)
│   │   ├── requirements.txt           # Dependensi Python
│   │   ├── test_main.py               # Pytest
│   │   └── test_tf.py                 # Test TensorFlow
│   ├── models/                        # Deep Learning
│   │   ├── model.keras                # Model terlatih (25.7 MB)
│   │   ├── class_names.json           # ["benign","malignant","normal"]
│   │   ├── class_indices.json         # {"benign":0, "malignant":1, "normal":2}
│   │   ├── history.json               # Riwayat training
│   │   ├── confusion_matrix.png       # Matriks konfusi
│   │   ├── training_history.png       # Grafik training
│   │   ├── MobileNetV2_TransferLearning.ipynb  # Notebook training
│   │   └── MobileNetV2_TransferLearning (1).ipynb
│   ├── storage/                       # Upload & logs
│   ├── database/                      # ERD & scripts
│   ├── docs/                          # Dokumentasi
│   │   ├── api-documentation.md       # REST API docs
│   │   ├── postman-collection.json    # Postman collection
│   │   ├── flowcharts.md              # 4 diagram flowchart
│   │   ├── deployment-xampp.md        # Panduan dev (XAMPP)
│   │   └── deployment-vps.md          # Panduan prod (VPS)
│   └── deployment/                    # Docker & Nginx
│       ├── docker-compose.yml
│       ├── laravel/Dockerfile
│       ├── fastapi/Dockerfile
│       └── nginx/nginx.conf
```

---

## Database

### Konfigurasi

| Parameter | Nilai |
|-----------|-------|
| DBMS | MySQL |
| Host | 127.0.0.1 |
| Port | 3306 |
| Database | `medical_ai` |
| Username | `root` |
| Password | (disesuaikan di .env) |

### Tabel (7 tabel)

| Tabel | Deskripsi | Kolom Utama |
|-------|-----------|-------------|
| `admins` | Admin pengelola sistem | id, name, email, password |
| `patients` | Data pasien | id, nik, nama, jenis_kelamin, tanggal_lahir, alamat, no_hp, email |
| `ai_models` | Model AI terdaftar | id, name, version, file_path, metrics (JSON), is_active, usage_count |
| `predictions` | Hasil prediksi | id, patient_id, ai_model_id, image_path, predicted_class, confidence, consent_approved, analysis_time |
| `prediction_probabilities` | Probabilitas per kelas | id, prediction_id, class_name, probability |
| `activity_logs` | Log aktivitas admin | id, description, type, causer, metadata, ip_address, user_agent |
| `personal_access_tokens` | Token Sanctum | id, tokenable_type, tokenable_id, name, token, abilities |

---

## Model Deep Learning

### Arsitektur

```
Input Layer (224x224x3)
        │
        ▼
MobileNetV2 (ImageNet Weights)
        │
        ▼
GlobalAveragePooling2D
        │
        ▼
Dense(256, ReLU)
        │
        ▼
Dropout(0.5)
        │
        ▼
Dense(128, ReLU)
        │
        ▼
Dropout(0.3)
        │
        ▼
Dense(3, Softmax)
        │
        ▼
Output: [Benign, Malignant, Normal]
```

### Detail Training

| Parameter | Nilai |
|-----------|-------|
| Framework | TensorFlow 2.21.0 + Keras 3.14.1 |
| Dataset | BUSI (Breast Ultrasound Images Dataset) |
| Kelas | 3 (Benign, Malignant, Normal) |
| Input Size | 224x224 piksel |
| Batch Size | 32 |
| Phase 1 (Feature Extraction) | MobileNetV2 frozen, train Dense layers |
| Phase 2 (Fine Tuning) | Unfreeze from layer 120, learning rate 0.0001 |
| Optimizer | Adam |
| Loss Function | Categorical Crossentropy |
| Callbacks | EarlyStopping (patience=5), ReduceLROnPlateau (patience=3), ModelCheckpoint, TensorBoard |
| Data Augmentation | Rotation, width/height shift, zoom, horizontal flip |
| Output | `model.keras` |

### File Model

| File | Ukuran | Deskripsi |
|------|--------|-----------|
| `model.keras` | ~25.7 MB | Model terlatih MobileNetV2 |
| `class_names.json` | ~50 B | Label kelas |
| `class_indices.json` | ~80 B | Mapping kelas ke index |
| `history.json` | ~10 KB | Riwayat akurasi/loss training |
| `confusion_matrix.png` | - | Matriks konfusi hasil evaluasi |
| `training_history.png` | - | Grafik akurasi & loss |

---

## Cara Install dan Menjalankan

### Prasyarat

- PHP 8.2+
- Composer 2.x
- Python 3.10+
- MySQL 8.0+
- Node.js 18+ & npm
- Git

### 1. Clone Repository

```bash
git clone https://github.com/marioobe/BreastVisionAI_4.git
cd BreastVisionAI_4/medical-ai-system
```

### 2. Setup Backend Laravel

```bash
cd backend-laravel
cp .env.example .env
composer install
php artisan key:generate
```

Sesuaikan file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_ai
DB_USERNAME=root
DB_PASSWORD=

AI_SERVICE_URL=http://127.0.0.1:8001
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
php artisan storage:link
```

Install frontend assets:

```bash
npm install
npm run build
```

Jalankan Laravel development server:

```bash
php artisan serve --port=8080
```

> Laravel berjalan di **http://127.0.0.1:8080**

### 3. Setup AI Service (FastAPI)

```bash
cd ai-service
pip install -r requirements.txt
python main.py
```

> FastAPI berjalan di **http://127.0.0.1:8001**

### 4. Setup Database MySQL

Buat database di MySQL:

```sql
CREATE DATABASE medical_ai;
```

Atau jalankan via migration Laravel (sudah otomatis di langkah 2).

### 5. Akses Aplikasi

| Halaman | URL |
|---------|-----|
| Halaman Beranda | http://127.0.0.1:8080 |
| Form Pemeriksaan | http://127.0.0.1:8080/pemeriksaan |
| Login Admin | http://127.0.0.1:8080/login |
| Dashboard Admin | http://127.0.0.1:8080/dashboard |

### 6. Login Admin Default

| Field | Value |
|-------|-------|
| Email | `admin@medical-ai.com` |
| Password | `password` |

---

## Testing

### Laravel (PHPUnit)

```bash
cd medical-ai-system/backend-laravel
php artisan test
```

**Hasil: 20 tests, 45 assertions**

| Test Class | File | Tests |
|------------|------|-------|
| AuthTest | `tests/Feature/AuthTest.php` | Login, logout, validasi |
| AiModelTest | `tests/Feature/AiModelTest.php` | CRUD, aktivasi model |
| PredictionTest | `tests/Feature/PredictionTest.php` | Prediksi, validasi upload |
| PredictionServiceTest | `tests/Unit/PredictionServiceTest.php` | Service logic |

### FastAPI (Pytest)

```bash
cd medical-ai-system/ai-service
python -m pytest test_main.py -v
```

**Cakupan test:**
- `GET /health` — health check endpoint
- `POST /predict` — prediksi dengan gambar valid
- Error handling — file invalid, request tanpa file
- CORS middleware
- Preprocessing image

---

## API Endpoints

### Web Routes (Session-based)

| Method | URI | Route Name | Auth | Keterangan |
|--------|-----|------------|:----:|------------|
| GET | `/` | `landing` | ❌ | Halaman beranda |
| GET | `/pemeriksaan` | `pasien.form` | ❌ | Form upload & biodata |
| POST | `/pemeriksaan` | `pasien.predict` | ❌ | Proses prediksi |
| GET | `/hasil/{prediction}` | `pasien.hasil` | ❌ | Halaman hasil prediksi |
| GET | `/login` | `login` | ❌ | Form login admin |
| POST | `/login` | `login.authenticate` | ❌ | Proses login admin |
| POST | `/logout` | `logout` | ✅ | Logout admin |
| GET | `/dashboard` | `dashboard` | ✅ | Dashboard admin |
| GET | `/admin/models` | `admin.models` | ✅ | List model AI |
| POST | `/admin/models` | `admin.models.store` | ✅ | Upload model baru |
| GET | `/admin/models/{id}/edit` | `admin.models.edit` | ✅ | Form edit model |
| PUT | `/admin/models/{id}` | `admin.models.update` | ✅ | Update model |
| DELETE | `/admin/models/{id}` | `admin.models.destroy` | ✅ | Hapus model |
| POST | `/admin/models/{id}/activate` | `admin.models.activate` | ✅ | Aktifkan model |
| GET | `/admin/predictions` | `admin.predictions` | ✅ | List prediksi (pagination + search + filter) |
| GET | `/admin/predictions/{id}` | `admin.predictions.show` | ✅ | Detail prediksi |
| DELETE | `/admin/predictions/{id}` | `admin.predictions.destroy` | ✅ | Hapus prediksi |

### API Routes (JSON, Sanctum Auth)

| Method | Endpoint | Auth | Deskripsi | Request | Response |
|:------:|:---------|:----:|-----------|---------|----------|
| POST | `/api/login` | ❌ | Login admin | `{email, password}` | `{token, admin}` |
| POST | `/api/predict` | ✅ | Prediksi gambar | `multipart: image, patient_id` | `{prediction, confidence, probabilities}` |
| GET | `/api/models` | ✅ | Daftar semua model | - | Array of models |
| POST | `/api/models` | ✅ | Upload model baru | `multipart: file, name, version` | `{model}` |
| PUT | `/api/models/{id}` | ✅ | Update model | `{name, version, metrics}` | `{model}` |
| DELETE | `/api/models/{id}` | ✅ | Hapus model | - | `{message}` |
| POST | `/api/models/{id}/activate` | ✅ | Aktifkan model (auto-deactivate) | - | `{model}` |
| GET | `/api/predictions` | ✅ | Daftar prediksi | `?search=&class=&date=` | Array of predictions |
| GET | `/api/predictions/{id}` | ✅ | Detail prediksi | - | `{prediction, probabilities, patient}` |
| DELETE | `/api/predictions/{id}` | ✅ | Hapus prediksi | - | `{message}` |
| GET | `/api/dashboard` | ✅ | Statistik dashboard | - | `{total_patients, total_predictions, ...}` |

Dokumentasi API lengkap: [docs/api-documentation.md](medical-ai-system/docs/api-documentation.md)

---

## Dataset

### Breast Ultrasound Images Dataset (BUSI)

Dataset berasal dari Kaggle: [Breast Ultrasound Images Dataset](https://www.kaggle.com/datasets/aryashah2k/breast-ultrasound-images-dataset)

**Detail Dataset:**

| Kategori | Jumlah Gambar |
|-----------|---------------|
| Benign | 437 |
| Malignant | 210 |
| Normal | 133 |
| **Total** | **780** |

Dataset dikumpulkan dari rumah sakit dan pusat medis, terdiri dari gambar USG payudara dalam format PNG dengan resolusi 500x500 piksel. Setiap gambar telah melalui proses anotasi oleh tenaga medis profesional.

---

## Deployment

### Development (XAMPP)

Panduan lengkap: [docs/deployment-xampp.md](medical-ai-system/docs/deployment-xampp.md)

- Gunakan XAMPP untuk Apache + MySQL + PHP
- Jalankan Laravel via `php artisan serve --port=8080`
- Jalankan FastAPI via `python main.py` (port 8001)

### Production (VPS Ubuntu + Nginx)

Panduan lengkap: [docs/deployment-vps.md](medical-ai-system/docs/deployment-vps.md)

- Nginx sebagai reverse proxy
- Supervisor untuk process management
- MySQL production database
- SSL/HTTPS via Let's Encrypt

### Docker

```bash
cd medical-ai-system/deployment
docker-compose up -d
```

File konfigurasi:
- `deployment/docker-compose.yml` — Orkestrasi container
- `deployment/laravel/Dockerfile` — Container Laravel (PHP 8.2 FPM)
- `deployment/fastapi/Dockerfile` — Container FastAPI (Python 3.10)
- `deployment/nginx/nginx.conf` — Konfigurasi Nginx

---

## Flowchart

Empat diagram flowchart tersedia di [docs/flowcharts.md](medical-ai-system/docs/flowcharts.md):

1. **Flowchart Sistem** — Arsitektur microservice
2. **Flowchart User** — Alur pasien dari awal sampai cetak PDF
3. **Flowchart Admin** — Alur admin dari login sampai manajemen
4. **Flowchart Prediksi AI** — Alur teknis dari request sampai response

---

## Dokumentasi

| File | Deskripsi |
|------|-----------|
| [Prompt.md](Prompt.md) | Spesifikasi awal proyek (referensi dosen) |
| [TODO.md](TODO.md) | Tracking progress pengerjaan |
| [Rangkuman.md](Rangkuman.md) | Ringkasan proyek |
| [docs/api-documentation.md](medical-ai-system/docs/api-documentation.md) | Dokumentasi REST API lengkap |
| [docs/postman-collection.json](medical-ai-system/docs/postman-collection.json) | Collection Postman untuk testing API |
| [docs/flowcharts.md](medical-ai-system/docs/flowcharts.md) | 4 diagram flowchart (Mermaid) |
| [docs/deployment-xampp.md](medical-ai-system/docs/deployment-xampp.md) | Panduan development dengan XAMPP |
| [docs/deployment-vps.md](medical-ai-system/docs/deployment-vps.md) | Panduan production di VPS Ubuntu |

---

## Lisensi

Proyek ini dibuat untuk tujuan akademik sebagai tugas UAS Kecerdasan Buatan / Skripsi.

---

## Author

**Mario** — [@marioobe](https://github.com/marioobe)

---

*BreastVisionAI 4 — Klasifikasi Citra USG Payudara dengan MobileNetV2 + Transfer Learning*
