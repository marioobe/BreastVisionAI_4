# ROLE

Bertindaklah sebagai Senior AI Engineer, Machine Learning Architect, Software Architect, Senior Laravel Developer, Senior FastAPI Developer, Database Architect, UI/UX Designer, dan Dosen Pembimbing Skripsi yang memiliki pengalaman membangun sistem klasifikasi citra medis berbasis Deep Learning.

Tugas Anda adalah membantu saya merancang, menganalisis, dan menghasilkan implementasi lengkap sebuah sistem AI berbasis web dari nol hingga siap dipresentasikan untuk UAS/Skripsi.

---

# LATAR BELAKANG

Saya sedang mengembangkan sistem klasifikasi citra medis berbasis Artificial Intelligence.

Dataset yang digunakan:

Breast Ultrasound Images Dataset (BUSI)

https://www.kaggle.com/datasets/aryashah2k/breast-ultrasound-images-dataset

Kategori klasifikasi:

* Benign
* Malignant
* Normal

Model yang akan digunakan:

MobileNetV2

Referensi dosen saya menggunakan:

MobileNetV2 + SVM

Namun saya TIDAK ingin menggunakan SVM.

Saya ingin menggunakan:

MobileNetV2 + Transfer Learning + Dense Layer + Softmax

Output model:

model.keras

Framework AI:

TensorFlow
Keras

---

# TUJUAN SISTEM

Saya ingin membuat website yang dapat menerima gambar ultrasound dari pengguna dan melakukan klasifikasi otomatis menggunakan model AI.

Website harus mampu:

1. Upload gambar medis.
2. Menjalankan inferensi AI.
3. Menampilkan hasil klasifikasi.
4. Menampilkan confidence score.
5. Menampilkan probabilitas seluruh kelas.
6. Menyimpan riwayat prediksi.
7. Dikelola oleh admin.

---

# KONSEP USER

SAYA TIDAK INGIN MENGGUNAKAN LOGIN USER.

Pengguna tidak perlu membuat akun.

Sebagai gantinya pengguna wajib mengisi biodata sederhana sebelum melakukan analisis.

Data yang diisi:

* Nama Lengkap
* Umur
* Jenis Kelamin
* Nomor HP (Opsional)
* Email (Opsional)

Data tersebut akan digunakan sebagai identitas pasien.

---

# DISCLAIMER WAJIB

Sebelum tombol "Mulai Analisis" aktif, pengguna wajib mencentang checkbox persetujuan.

Isi disclaimer:

"Saya memahami bahwa hasil analisis yang diberikan oleh sistem ini merupakan prediksi yang dihasilkan oleh kecerdasan buatan (Artificial Intelligence) dan tidak dapat dijadikan sebagai diagnosis medis final. Hasil prediksi dapat mengandung kesalahan. Untuk mendapatkan diagnosis yang akurat, saya disarankan untuk berkonsultasi dengan dokter atau tenaga medis profesional yang berkompeten."

Ketentuan:

* Tombol Mulai Analisis disabled secara default.
* Tombol aktif hanya setelah checkbox dicentang.
* Status persetujuan harus tersimpan di database.
* Disclaimer harus muncul juga pada hasil cetak PDF.

---

# TEKNOLOGI WAJIB

Frontend:

* HTML5
* CSS3
* Bootstrap 5
* JavaScript

Backend:

* Laravel 12

Database:

* MySQL

AI Service:

* Python
* FastAPI

Machine Learning:

* TensorFlow
* Keras
* MobileNetV2

Model Format:

* .keras

---

# ARSITEKTUR SISTEM

Saya ingin menggunakan arsitektur Microservice.

Alur sistem:

Pasien

↓

Isi Biodata

↓

Upload Gambar

↓

Centang Disclaimer

↓

Klik Mulai Analisis

↓

Laravel Backend

↓

FastAPI AI Service

↓

Load Model Aktif (.keras)

↓

Prediksi

↓

Return JSON

↓

Laravel

↓

Simpan Database

↓

Tampilkan Hasil

↓

Cetak PDF

Buatkan diagram arsitektur lengkap.

---

# STRUKTUR FOLDER PROJECT

Buatkan struktur folder profesional dan scalable.

Contoh:

medical-ai-system/

├── backend-laravel/
│
├── ai-service/
│
├── models/
│
├── storage/
│
├── database/
│
├── docs/
│
└── deployment/

Jelaskan fungsi setiap folder.

---

# DESAIN DATABASE

Buatkan ERD lengkap.

Tabel:

patients

models

predictions

prediction_probabilities

activity_logs

admins

---

# DETAIL TABEL

patients

* id
* nama
* umur
* jenis_kelamin
* no_hp
* email
* created_at

models

* id
* model_name
* version
* accuracy
* loss
* file_path
* is_active
* uploaded_at

predictions

* id
* patient_id
* model_id
* image_path
* predicted_class
* confidence
* consent_approved
* analysis_time
* created_at

prediction_probabilities

* id
* prediction_id
* class_name
* probability

activity_logs

* id
* action
* description
* created_at

admins

* id
* name
* email
* password

---

# FITUR ADMIN

Dashboard

Menampilkan:

* Total Pasien
* Total Prediksi
* Total Model
* Model Aktif
* Total Penggunaan Model

---

CRUD MODEL AI

Admin dapat:

* Upload model .keras
* Edit model
* Hapus model
* Aktifkan model

Ketentuan:

Hanya boleh ada satu model aktif.

Jika model baru diaktifkan maka model lama otomatis nonaktif.

---

MONITORING MODEL

Admin dapat melihat:

* Nama model
* Versi model
* Akurasi
* Loss
* Jumlah penggunaan
* Tanggal upload

---

MANAJEMEN HASIL PREDIKSI

Admin dapat:

* Melihat seluruh hasil prediksi
* Melihat gambar
* Melihat confidence
* Melihat probabilitas semua kelas
* Menghapus data

---

# FITUR USER

Halaman Beranda

* Informasi sistem
* Cara penggunaan
* Disclaimer

---

Form Biodata

* Nama
* Umur
* Jenis Kelamin
* Nomor HP
* Email

---

Upload Gambar

* Preview gambar
* Validasi ukuran file
* Validasi format file

Format:

* JPG
* JPEG
* PNG

---

Hasil Analisis

Menampilkan:

* Nama Pasien
* Tanggal Analisis
* Nama Model
* Kategori Prediksi
* Confidence Score
* Probabilitas Semua Kelas

Contoh:

Prediksi:

Malignant

Confidence:

96.73%

Probabilitas:

Benign = 1.52%

Malignant = 96.73%

Normal = 1.75%

---

EXPORT PDF

PDF harus berisi:

* Biodata pasien
* Gambar
* Hasil prediksi
* Confidence
* Nama model
* Disclaimer AI

---

# DESAIN API

Buatkan dokumentasi REST API lengkap.

Endpoint:

POST /api/predict

GET /api/models

POST /api/models

PUT /api/models/{id}

DELETE /api/models/{id}

POST /api/models/{id}/activate

GET /api/predictions

GET /api/predictions/{id}

DELETE /api/predictions/{id}

GET /api/dashboard

---

# LAYANAN FASTAPI

Buatkan service FastAPI yang:

* Load model aktif
* Preprocessing gambar
* Prediksi
* Return JSON

Contoh response:

{
"prediction": "Malignant",
"confidence": 96.73,
"probabilities": {
"Benign": 1.52,
"Malignant": 96.73,
"Normal": 1.75
}
}

---

# MODEL DEEP LEARNING

Karena referensi dosen menggunakan MobileNetV2 + SVM, saya ingin menggantinya menjadi MobileNetV2 + Dense + Softmax.

Buatkan:

1. Data Augmentation
2. Transfer Learning
3. Fine Tuning
4. EarlyStopping
5. ReduceLROnPlateau
6. ModelCheckpoint
7. TensorBoard
8. Save model .keras

Arsitektur:

Input Layer

↓

MobileNetV2

↓

GlobalAveragePooling2D

↓

Dense(256)

↓

Dropout(0.5)

↓

Dense(128)

↓

Dropout(0.3)

↓

Dense(3, Softmax)

---

# HASIL TRAINING

Simpan:

* model.keras
* history.json
* class_names.json

---

# INTEGRASI LARAVEL DAN FASTAPI

Berikan:

* Laravel Controller
* Service Layer
* HTTP Client
* FastAPI Endpoint
* Request Validation
* Error Handling

Lengkap dengan kode.

---

# DESAIN UI/UX

Buatkan:

1. Wireframe Dashboard Admin
2. Wireframe Halaman User
3. Layout Mobile
4. Layout Desktop
5. Warna
6. Typography
7. Komponen Bootstrap

---

# FLOWCHART

Buatkan:

1. Flowchart Sistem
2. Flowchart User
3. Flowchart Admin
4. Flowchart Prediksi AI

---

# DEPLOYMENT

Berikan panduan:

Development:

* XAMPP
* Laravel
* FastAPI
* MySQL

Production:

* VPS Ubuntu
* Nginx
* Laravel
* FastAPI
* MySQL

Docker:

* docker-compose.yml

---

# ANALISIS AKADEMIK

Buatkan:

* Latar Belakang
* Rumusan Masalah
* Tujuan Penelitian
* Manfaat Penelitian
* Metodologi
* BAB I
* BAB II
* BAB III
* BAB IV
* BAB V

yang sesuai dengan sistem ini.

---

# ROADMAP PENGERJAAN

Buat roadmap dari awal sampai selesai.

Minggu 1:
Dataset dan Training

Minggu 2:
FastAPI

Minggu 3:
Laravel

Minggu 4:
Integrasi

Minggu 5:
Testing

Minggu 6:
Deployment dan Dokumentasi

Berikan jawaban dalam format yang sangat detail, profesional, dan siap digunakan sebagai blueprint proyek UAS/Skripsi.
