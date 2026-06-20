# Flowchart — Sistem Klasifikasi Citra Medis AI

---

## 1. Flowchart Sistem (Arsitektur)

```mermaid
flowchart TB
    User["👤 Pengguna (Browser)"]
    Admin["🔐 Admin (Browser)"]

    subgraph Frontend ["Frontend (Blade View)"]
        Landing["Halaman Beranda"]
        Form["Form Biodata + Upload"]
        Hasil["Halaman Hasil"]
        Dashboard["Dashboard Admin"]
        Login["Login Admin"]
    end

    subgraph Backend ["Backend Laravel"]
        Web["Web Routes"]
        API["API Routes"]
        Validation["Form Request Validation"]
        PredService["PredictionService"]
        AiService["AiService (Guzzle HTTP Client)"]
        Auth["Sanctum Auth"]
    end

    subgraph Database ["Database MySQL"]
        Patients["patients"]
        Predictions["predictions"]
        Probabilities["prediction_probabilities"]
        AiModels["ai_models"]
        Admins["admins"]
        ActivityLogs["activity_logs"]
    end

    subgraph AIService ["AI Service (FastAPI - Python)"]
        FastAPI["FastAPI Server :8000"]
        Preprocess["Preprocessing 224x224"]
        Model["MobileNetV2 Model"]
    end

    User --> Landing
    User --> Form
    Form --> Web
    Web --> Validation
    Validation --> PredService
    PredService --> AiService
    AiService --> FastAPI
    FastAPI --> Preprocess
    Preprocess --> Model
    Model --> FastAPI
    FastAPI --> AiService
    PredService --> Patients
    PredService --> Predictions
    PredService --> Probabilities
    PredService --> AiModels
    Web --> Hasil

    Admin --> Login
    Login --> Auth
    Auth --> Dashboard
    Dashboard --> API
    API --> Predictions
    API --> AiModels
    API --> Admins
```

---

## 2. Flowchart User

```mermaid
flowchart TD
    Start(["Mulai"]) --> Landing["Halaman Beranda"]
    Landing --> FlowInfo["Lihat Cara Kerja"]
    Landing --> ClassInfo["Lihat Kategori Hasil"]
    Landing --> BtnMulai["Klik 'Mulai Pemeriksaan'"]

    BtnMulai --> Form["Form Biodata Pasien"]
    Form --> IsiData["Isi NIK, Nama, Gender, Tgl Lahir"]
    IsiData --> UploadGambar["Upload Foto Rontgen (JPG/PNG)"]
    UploadGambar --> Preview{"Preview OK?"}
    Preview -->|Tidak| UploadGambar
    Preview -->|Ya| Disclaimer["Centang Checkbox Disclaimer"]
    Disclaimer --> TombolAktif{"Tombol Aktif?"}
    TombolAktif -->|Disabled| Disclaimer
    TombolAktif -->|Enabled| KlikAnalisis["Klik 'Mulai Analisis'"]

    KlikAnalisis --> Kirim["Kirim ke Backend Laravel"]
    Kirim --> Validasi{"Validasi Data"}
    Validasi -->|Gagal| Error["Tampilkan Error"]
    Error --> Form
    Validasi -->|Sukses| Simpan["Simpan Pasien (FirstOrCreate)"]
    Simpan --> SimpanGambar["Simpan Gambar ke Storage"]
    SimpanGambar --> PanggilAI["Panggil AI Service via Guzzle"]
    PanggilAI --> Prediksi["FastAPI Preprocess + Predict"]
    Prediksi --> Return["Return JSON"]
    Return --> SimpanHasil["Simpan Hasil ke DB"]
    SimpanHasil --> Tampilkan["Redirect ke Halaman Hasil"]
    Tampilkan --> Hasil["Lihat: Prediksi, Confidence, Probabilitas"]
    Hasil --> PDF["Export PDF"]
    Hasil --> Baru["Pemeriksaan Baru"]
    Baru --> Form
    PDF --> Selesai(["Selesai"])
    Hasil --> Selesai
```

---

## 3. Flowchart Admin

```mermaid
flowchart TD
    Start(["Mulai"]) --> LoginPage["Halaman Login Admin"]
    LoginPage --> Input["Input Email & Password"]
    Input --> Auth{"Validasi Login"}
    Auth -->|Gagal| Error["Tampilkan Error"]
    Error --> LoginPage
    Auth -->|Sukses| Dashboard["Dashboard Admin"]

    Dashboard --> Stats["Lihat Statistik"]
    Dashboard --> Models["Manajemen Model AI"]
    Dashboard --> Predictions["Lihat Prediksi Terbaru"]
    Dashboard --> Logout["Logout"]

    subgraph CRUD ["CRUD Model AI"]
        Tambah["Tambah Model Baru"]
        Edit["Edit Model"]
        Aktifkan["Aktifkan Model"]
        Hapus["Hapus Model"]
        Tambah --> Upload["Upload .keras + Nama + Versi"]
        Upload --> Simpan["Simpan ke DB"]
        Aktifkan --> NonaktifLama{"Nonaktifkan Model Lama"}
        NonaktifLama -->|Ya| Update["Set is_active=true"]
        Hapus --> Confirm{"Konfirmasi Hapus"}
        Confirm -->|Ya| Delete["Hapus dari DB"]
    end

    Models --> Tambah
    Models --> Edit
    Models --> Aktifkan
    Models --> Hapus

    Predictions --> ViewList["Lihat Daftar Prediksi"]
    ViewList --> Filter["Filter: Kelas, Tanggal"]
    ViewList --> Detail["Lihat Detail Prediksi"]
    Detail --> HapusPred["Hapus Data Prediksi"]

    Logout --> Selesai(["Selesai"])
```

---

## 4. Flowchart Prediksi AI

```mermaid
flowchart TD
    Start(["Request Masuk"]) --> Validate["Validasi Request"]
    Validate -->|Invalid| Reject["Return 422 Validation Error"]
    Validate -->|Valid| FindPatient["Patient::firstOrCreate(nik)"]
    FindPatient --> StoreImage["Simpan Gambar ke storage/app/public/predictions/{id}/"]
    StoreImage --> GetActiveModel["Cari Model Aktif"]
    GetActiveModel --> StartTimer["Catat Waktu Mulai (microtime)"]
    StartTimer --> AiService["Panggil AiService.predict()"]
    AiService --> Guzzle["HTTP POST ke FastAPI :8000/predict"]
    Guzzle --> FastAPI["FastAPI Menerima File"]
    FastAPI --> ValidateType{"File JPG/PNG?"}
    ValidateType -->|Bukan| Error400["Return 400 Invalid Type"]
    ValidateType -->|Ya| Read["Baca File Bytes"]
    Read --> Preprocess["Preprocessing:"]
    Preprocess --> Resize["Resize ke 224x224"]
    Resize --> Convert["Convert ke Array (float32)"]
    Convert --> Expand["Expand Dimensi (batch)"]
    Expand --> PreprocessInput["mobilenet_v2.preprocess_input()"]
    PreprocessInput --> Predict["model.predict()"]
    Predict --> GetResult["Ambil ArgMax"]
    GetResult --> MapClass["Map Index ke Class Name"]
    MapClass --> CalcConfidence["Hitung Confidence (%)"]
    CalcConfidence --> CalcProbabilities["Hitung Probabilitas Semua Kelas"]
    CalcProbabilities --> ReturnJSON["Return JSON:"]
    ReturnJSON --> JsonFormat["{prediction, confidence, probabilities}"]

    JsonFormat --> LaravelReceive["Laravel Menerima Response"]
    LaravelReceive --> StopTimer["Catat Waktu Selesai -> analysis_time (ms)"]
    StopTimer --> SavePrediction["Simpan ke Tabel predictions"]
    SavePrediction --> SaveProbabilities["Simpan ke prediction_probabilities"]
    SaveProbabilities --> LoadRelations["Load relasi: patient, probabilities, aiModel"]
    LoadRelations --> ReturnResult["Return Prediction Model"]
    ReturnResult --> Selesai(["Selesai"])
```

---

© 2026 — Proyek Akademik Sistem Klasifikasi Citra Medis AI
