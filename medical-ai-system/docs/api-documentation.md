# Dokumentasi REST API — OsteoScan AI

**Base URL (Development):** `http://127.0.0.1:8000`

---

## Daftar Endpoint

### Public
| Method | Endpoint | Auth | Deskripsi |
|:------:|:---------|:----:|:----------|
| POST | `/api/login` | ❌ | Login admin |
| POST | `/api/predict` | ✅ | Prediksi gambar (API) |
| GET | `/api/models` | ✅ | Lihat semua model |
| POST | `/api/models` | ✅ | Upload model baru |
| PUT | `/api/models/{id}` | ✅ | Edit model |
| DELETE | `/api/models/{id}` | ✅ | Hapus model |
| POST | `/api/models/{id}/activate` | ✅ | Aktifkan model |
| GET | `/api/predictions` | ✅ | Lihat prediksi (filterable) |
| GET | `/api/predictions/{id}` | ✅ | Detail prediksi |
| DELETE | `/api/predictions/{id}` | ✅ | Hapus prediksi |
| GET | `/api/dashboard` | ✅ | Statistik dashboard |

### Web (Frontend)
| Method | Endpoint | Deskripsi |
|:------:|:---------|:----------|
| GET | `/` | Halaman Beranda |
| GET | `/pemeriksaan` | Form biodata + upload |
| POST | `/pemeriksaan` | Submit form → redirect hasil |
| GET | `/hasil/{id}` | Halaman hasil analisis |
| GET | `/login` | Halaman login admin |
| POST | `/login` | Proses login admin |
| POST | `/logout` | Logout admin |
| GET | `/dashboard` | Dashboard admin |

---

## Authorization

Semua endpoint API (kecuali `/api/login`) membutuhkan **Bearer Token** yang didapat dari login.

```
Authorization: Bearer <token>
```

---

## 1. POST /api/login

Login admin dan mendapatkan token.

**Request:**
```json
{
    "email": "admin@medical-ai.com",
    "password": "password"
}
```

**Response (200):**
```json
{
    "message": "Login berhasil",
    "token": "1|abc123...",
    "admin": {
        "id": 1,
        "name": "Admin Utama",
        "email": "admin@medical-ai.com"
    }
}
```

**Response (422 - gagal):**
```json
{
    "message": "Email atau password salah.",
    "errors": { "email": ["Email atau password salah."] }
}
```

---

## 2. GET /api/models

Menampilkan semua model AI.

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "MobileNetV2 BUSI",
            "version": "1.0.0",
            "path": "ai-models/model.keras",
            "metrics": { "accuracy": 0.94, "loss": 0.18 },
            "is_active": true,
            "uploaded_by": 1,
            "created_at": "2026-06-20T10:00:00Z",
            "uploader": { "id": 1, "name": "Admin Utama" }
        }
    ]
}
```

---

## 3. POST /api/models

Menambahkan model AI baru.

**Request (multipart/form-data):**
| Field | Tipe | Required | Deskripsi |
|:------|:----:|:--------:|:----------|
| `name` | string | ✅ | Nama model |
| `version` | string | ✅ | Versi model |
| `model_file` | file | ✅ | File .keras atau .h5 (max 200MB) |
| `metrics` | string | ❌ | JSON string (opsional) |

**Response (201):**
```json
{
    "message": "Model berhasil ditambahkan",
    "data": { ... }
}
```

---

## 4. PUT /api/models/{id}

Mengupdate data model (tanpa file).

**Request (JSON):**
```json
{
    "name": "Nama Baru",
    "version": "1.1.0",
    "metrics": "{\"accuracy\": 0.95}"
}
```

**Response (200):**
```json
{
    "message": "Model berhasil diperbarui",
    "data": { ... }
}
```

---

## 5. DELETE /api/models/{id}

Menghapus model.

**Response (200):**
```json
{
    "message": "Model berhasil dihapus"
}
```

---

## 6. POST /api/models/{id}/activate

Mengaktifkan model (model lain otomatis nonaktif).

**Response (200):**
```json
{
    "message": "Model berhasil diaktifkan",
    "data": { ... }
}
```

---

## 7. POST /api/predict

Melakukan prediksi gambar (via API).

**Request (multipart/form-data):**
| Field | Tipe | Required | Deskripsi |
|:------|:----:|:--------:|:----------|
| `image` | file | ✅ | Gambar JPG/JPEG/PNG (max 10MB) |
| `nik` | string | ✅ | NIK pasien |
| `name` | string | ✅ | Nama pasien |
| `gender` | string | ✅ | L / P |
| `date_of_birth` | date | ✅ | YYYY-MM-DD |
| `address` | string | ❌ | Alamat |
| `phone` | string | ❌ | No telepon |
| `email` | email | ❌ | Email |
| `consent_approved` | boolean | ❌ | Status persetujuan |

**Response (200):**
```json
{
    "message": "Prediksi berhasil",
    "data": {
        "id": 1,
        "patient_id": 1,
        "ai_model_id": 1,
        "image_path": "predictions/1/abc.jpg",
        "prediction": "Malignant",
        "confidence": "96.73",
        "consent_approved": true,
        "analysis_time": 320.50,
        "created_at": "2026-06-20T10:00:00Z",
        "patient": {
            "id": 1,
            "nik": "1234567890123456",
            "name": "Pasien Contoh",
            "gender": "P",
            "date_of_birth": "1990-01-15"
        },
        "probabilities": [
            { "class_name": "Benign", "probability": 1.52 },
            { "class_name": "Malignant", "probability": 96.73 },
            { "class_name": "Normal", "probability": 1.75 }
        ],
        "ai_model": {
            "id": 1,
            "name": "MobileNetV2 BUSI",
            "version": "1.0.0"
        }
    }
}
```

---

## 8. GET /api/predictions

Menampilkan prediksi dengan filter.

**Query Parameters:**
| Parameter | Tipe | Deskripsi |
|:----------|:----:|:----------|
| `search` | string | Cari berdasarkan nama/NIK pasien |
| `prediction` | string | Filter kelas (Normal/Osteopenia/Osteoporosis) |
| `date_from` | date | Filter tanggal awal |
| `date_to` | date | Filter tanggal akhir |
| `per_page` | int | Item per halaman (default: 15) |

**Response (200):**
```json
{
    "data": [...],
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "last_page": 4
}
```

---

## 9. GET /api/predictions/{id}

Detail prediksi.

**Response (200):**
```json
{
    "data": {
        "id": 1,
        "patient": {...},
        "probabilities": [...],
        "ai_model": {...}
    }
}
```

---

## 10. DELETE /api/predictions/{id}

Hapus prediksi.

**Response (200):**
```json
{
    "message": "Data prediksi berhasil dihapus"
}
```

---

## 11. GET /api/dashboard

Statistik dashboard admin.

**Response (200):**
```json
{
    "data": {
        "total_predictions": 150,
        "total_patients": 120,
        "total_models": 3,
        "active_model": {
            "id": 1,
            "name": "MobileNetV2 BUSI",
            "version": "1.0.0"
        },
        "predictions_by_class": {
            "Normal": 50,
            "Osteopenia": 60,
            "Osteoporosis": 40
        },
        "today_predictions": 5,
        "recent_predictions": [...]
    }
}
```

---

## 12. AI Service (FastAPI) Endpoints

### GET /health

Cek status service.

**Response (200):**
```json
{
    "status": "ok",
    "model_loaded": true,
    "classes": ["Benign", "Malignant", "Normal"]
}
```

### POST /predict

Prediksi dari FastAPI langsung.

**Request:** `multipart/form-data` dengan field `file` (image)

**Response (200):**
```json
{
    "prediction": "Malignant",
    "confidence": 96.73,
    "probabilities": {
        "Benign": 1.52,
        "Malignant": 96.73,
        "Normal": 1.75
    }
}
```

---

## Error Codes

| Kode | Deskripsi |
|:----:|:----------|
| 200 | Sukses |
| 201 | Berhasil dibuat |
| 302 | Redirect (web) |
| 400 | Bad request (file type invalid) |
| 401 | Unauthenticated (token tidak valid) |
| 422 | Validation error |
| 500 | Internal server error |
| 503 | Service unavailable (model not loaded) |

---

© 2026 — OsteoScan AI — Proyek Akademik Sistem Klasifikasi Citra Medis
