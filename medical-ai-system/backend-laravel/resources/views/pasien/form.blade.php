@extends('layouts.app')

@section('title', 'Pemeriksaan — BreastVisionAI 4')
@section('nav-active', 'active')

@section('content')
<style>
    .form-section { padding: 64px 0 100px; }
    .form-card {
        max-width: 680px;
        margin: 0 auto;
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 40px;
    }
    .form-card h2 {
        font-family: var(--font-display);
        font-weight: 500;
        font-size: 28px;
        margin-bottom: 8px;
    }
    .form-card .section-sub { margin-bottom: 32px; }

    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    .form-group label .required { color: var(--rose); margin-left: 2px; }
    .form-control {
        width: 100%;
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 12px 14px;
        font-family: var(--font-body);
        font-size: 14px;
        color: var(--text-primary);
        transition: border-color 0.2s;
        outline: none;
    }
    .form-control:focus { border-color: var(--teal); }
    .form-control::placeholder { color: var(--text-muted); }
    select.form-control { cursor: pointer; }
    select.form-control option { background: var(--bg-panel); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .upload-area {
        position: relative;
        border: 2px dashed var(--line);
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.25s, background 0.25s;
    }
    .upload-area:hover { border-color: var(--teal); background: var(--teal-dim); }
    .upload-area.has-image { border-style: solid; border-color: var(--teal); padding: 20px; }
    .upload-icon { margin-bottom: 12px; }
    .upload-text { font-size: 14px; color: var(--text-secondary); }
    .upload-text strong { color: var(--teal); }
    .upload-hint { font-size: 12px; color: var(--text-muted); margin-top: 8px; }
    .upload-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        display: none;
    }
    .upload-area.has-image .upload-icon,
    .upload-area.has-image .upload-text,
    .upload-area.has-image .upload-hint { display: none; }
    .upload-area.has-image .upload-preview { display: block; margin: 0 auto; }
    .upload-area input[type="file"] { display: none; }

    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin: 24px 0;
        padding: 16px;
        background: rgba(242, 115, 122, 0.05);
        border: 1px solid rgba(242, 115, 122, 0.2);
        border-radius: 10px;
    }
    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: var(--teal);
        flex-shrink: 0;
        cursor: pointer;
    }
    .checkbox-group label {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }
    .btn-submit {
        font-size: 15px;
        font-weight: 600;
        color: #062220;
        background: var(--teal);
        padding: 14px 28px;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-submit:hover { background: #5EEAD4; transform: translateY(-1px); }
    .btn-submit:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none;
    }
    .btn-cancel {
        font-size: 14px;
        color: var(--text-secondary);
        padding: 14px 24px;
        border: 1px solid var(--line-bright);
        border-radius: 9px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel:hover { color: var(--text-primary); border-color: var(--text-secondary); }

    .error-text { color: var(--rose); font-size: 12.5px; margin-top: 4px; display: none; }
    .form-control.error { border-color: var(--rose); }
    .is-invalid { border-color: var(--rose) !important; }
    .invalid-feedback { color: var(--rose); font-size: 12px; margin-top: 4px; }

    @media (max-width: 600px) {
        .form-card { padding: 24px; }
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
    }
</style>

<div class="wrap">
    <section class="form-section">
        <div class="form-card">
            <span class="section-eyebrow">pemeriksaan</span>
            <h2>Data Pasien &amp; Unggah Citra</h2>
            <p class="section-sub">Lengkapi data diri dan unggah gambar USG payudara untuk memulai analisis.</p>

            @if ($errors->any())
                <div style="background: rgba(242,115,122,0.1); border: 1px solid rgba(242,115,122,0.3); border-radius: 10px; padding: 16px; margin-bottom: 24px;">
                    <ul style="list-style: none; font-size: 13px; color: var(--rose);">
                        @foreach ($errors->all() as $error)
                            <li style="margin-bottom:4px;">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pasien.predict') }}" method="POST" enctype="multipart/form-data" id="predictForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="nik">NIK <span class="required">*</span></label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan" maxlength="20" required>
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Sesuai KTP" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin <span class="required">*</span></label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">— Pilih —</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" maxlength="20">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Foto Rontgen <span class="required">*</span></label>
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('image').click()">
                        <div class="upload-icon">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#5E7090" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div class="upload-text">
                            Seret gambar USG ke sini, atau <strong>pilih file</strong>
                        </div>
                        <div class="upload-hint">Format: JPG, JPEG, PNG — Maks: 10MB</div>
                        <img class="upload-preview" id="preview" alt="Preview">
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png" required>
                    </div>
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="error-text" id="fileError">File tidak valid. Gunakan format JPG/JPEG/PNG maksimal 10MB.</div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="disclaimer" name="disclaimer" required>
                    <label for="disclaimer">
                        Saya memahami bahwa hasil analisis ini bersifat <strong>alat bantu deteksi awal</strong> dan <strong>bukan diagnosis medis resmi</strong>. Saya akan berkonsultasi dengan dokter atau radiolog untuk diagnosis akhir.
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
                        Mulai Analisis
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="#062220" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <a href="{{ route('landing') }}" class="btn-cancel">Kembali</a>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
    const fileInput = document.getElementById('image');
    const uploadArea = document.getElementById('uploadArea');
    const preview = document.getElementById('preview');
    const disclaimer = document.getElementById('disclaimer');
    const submitBtn = document.getElementById('submitBtn');
    const fileError = document.getElementById('fileError');

    function validateFile(file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 10 * 1024 * 1024;
        if (!file) return false;
        if (!validTypes.includes(file.type)) return false;
        if (file.size > maxSize) return false;
        return true;
    }

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        fileError.style.display = 'none';
        if (!file) {
            uploadArea.classList.remove('has-image');
            preview.src = '';
            return;
        }
        if (!validateFile(file)) {
            fileError.style.display = 'block';
            this.value = '';
            uploadArea.classList.remove('has-image');
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            uploadArea.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    });

    function checkForm() {
        const fileOk = fileInput.files.length > 0 && validateFile(fileInput.files[0]);
        submitBtn.disabled = !(fileOk && disclaimer.checked);
    }

    fileInput.addEventListener('change', checkForm);
    disclaimer.addEventListener('change', checkForm);

    document.getElementById('predictForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = 'Menganalisis...';
    });
</script>
@endpush
@endsection
