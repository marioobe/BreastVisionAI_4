# Rangkuman Proyek — BreastVisionAI 4

## 1. Identitas Proyek
- **Nama:** BreastVisionAI 4
- **Tema:** Klasifikasi Citra USG Payudara (Kanker Payudara)
- **Dataset:** Breast Ultrasound Images Dataset (BUSI)
- **Kelas:** Benign, Malignant, Normal
- **Tujuan UAS/Skripsi:** Sistem klasifikasi citra medis berbasis AI

## 2. Arsitektur Sistem
- **Frontend:** Laravel Blade (HTML5, CSS3, JavaScript)
- **Backend:** Laravel 12
- **AI Service:** FastAPI (Python)
- **Database:** MySQL (`medical_ai`)
- **Auth:** Sanctum (API) + Session (Web Admin)

## 3. Model AI
- **Arsitektur:** MobileNetV2 + Transfer Learning
- **Output:** Dense(256) → Dropout(0.5) → Dense(128) → Dropout(0.3) → Dense(3, Softmax)
- **Framework:** TensorFlow + Keras
- **File:** `model.keras`, `class_names.json`, `history.json`

## 4. Fitur Sistem
### User (Pasien):
- Landing page informasi sistem
- Form biodata (NIK, nama, JK, tgl lahir, alamat, HP, email)
- Upload gambar USG (JPG/PNG, max 10MB)
- Preview gambar
- Checkbox disclaimer wajib
- Hasil prediksi: kelas, confidence, probabilitas semua kelas
- Export PDF (print)
- Waktu analisis tersimpan

### Admin:
- Login session-based
- Dashboard: total pasien/prediksi/model, penggunaan model, distribusi kelas, 10 prediksi terbaru
- CRUD Model AI: upload .keras, edit, hapus, aktivasi (auto-deactivate)
- Monitoring model: nama, versi, akurasi, loss, jumlah penggunaan
- Manajemen prediksi: list pagination + search/filter, detail + gambar + probabilitas, hapus

## 5. Database (7 Tabel)
- `admins` — admin login
- `patients` — data pasien
- `ai_models` — model AI (name, version, path, metrics JSON, is_active)
- `predictions` — hasil prediksi (image_path, prediction, confidence, consent_approved, analysis_time)
- `prediction_probabilities` — probabilitas per kelas
- `activity_logs` — log aktivitas
- `personal_access_tokens` — token Sanctum

## 6. API Endpoints
### Web:
| Method | URI | Keterangan |
|--------|-----|------------|
| GET | `/` | Beranda |
| GET | `/pemeriksaan` | Form upload |
| POST | `/pemeriksaan` | Proses prediksi |
| GET | `/hasil/{id}` | Hasil prediksi |
| GET | `/login` | Form login admin |
| POST | `/login` | Login admin |
| POST | `/logout` | Logout admin |
| GET | `/dashboard` | Dashboard admin |
| GET/POST | `/admin/models` | Kelola model |
| GET/PUT/DELETE | `/admin/models/{id}` | Edit/hapus model |
| GET | `/admin/predictions` | List prediksi |
| GET | `/admin/predictions/{id}` | Detail prediksi |
| DELETE | `/admin/predictions/{id}` | Hapus prediksi |

### API (JSON, Sanctum):
| Method | URI | Keterangan |
|--------|-----|------------|
| POST | `/api/predict` | Prediksi via API |
| GET | `/api/models` | List model |
| POST | `/api/models` | Upload model |
| PUT | `/api/models/{id}` | Update model |
| DELETE | `/api/models/{id}` | Hapus model |
| POST | `/api/models/{id}/activate` | Aktifkan model |
| GET | `/api/predictions` | List prediksi |
| GET | `/api/predictions/{id}` | Detail prediksi |
| DELETE | `/api/predictions/{id}` | Hapus prediksi |

## 7. Deployment
- **Development:** XAMPP (Apache + MySQL + PHP 8.2)
- **AI Service:** Python FastAPI (port 8001)
- **Production:** VPS Ubuntu + Nginx + Supervisor (panduan di `docs/deployment-vps.md`)
- **Docker:** `deployment/docker-compose.yml`

## 8. Testing
- **Laravel:** 20 test, 45 assertions (PHPUnit)
- **FastAPI:** pytest (health, predict, error, CORS, preprocessing)

## 9. Dokumentasi
- `docs/api-documentation.md` — REST API docs
- `docs/postman-collection.json` — Postman collection
- `docs/flowcharts.md` — 4 diagram Mermaid
- `docs/deployment-xampp.md` — panduan dev
- `docs/deployment-vps.md` — panduan prod
- `README.md` — dokumentasi proyek

## 10. Files Utama
- `backend-laravel/` — Semua kode Laravel
- `ai-service/main.py` — FastAPI service
- `models/model.keras` — Model terlatih
- `deployment/` — Docker + Nginx config
- `Prompt.md` — Spesifikasi awal proyek
