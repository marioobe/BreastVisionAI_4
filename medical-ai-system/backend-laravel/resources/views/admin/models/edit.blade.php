@extends('layouts.app')

@section('title', 'Edit Model AI — BreastVisionAI 4')

@section('content')
<style>
    .admin-section { padding: 48px 0 100px; }
    .admin-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }
    .admin-head h2 { font-family: var(--font-display); font-weight: 500; font-size: 28px; }
    .card {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }
    .card-head {
        padding: 18px 22px;
        border-bottom: 1px solid var(--line);
        font-weight: 600;
        font-size: 15px;
    }
    .card-body { padding: 22px; }
    .alert {
        background: rgba(45,212,191,0.1);
        border: 1px solid rgba(45,212,191,0.25);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        color: var(--teal);
        margin-bottom: 20px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        font-size: 12px;
        font-family: var(--font-mono);
        color: var(--text-secondary);
        margin-bottom: 6px;
        letter-spacing: 0.02em;
    }
    .form-group input {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 7px;
        color: var(--text-primary);
        font-family: var(--font-body);
        font-size: 13.5px;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-group input:focus { border-color: var(--teal); }
    .btn-submit {
        padding: 10px 22px;
        background: var(--teal);
        color: #062220;
        font-weight: 600;
        font-size: 13.5px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #5EEAD4; }
    .btn-cancel {
        padding: 10px 22px;
        background: transparent;
        color: var(--text-secondary);
        font-size: 13.5px;
        border: 1px solid var(--line-bright);
        border-radius: 7px;
        cursor: pointer;
        text-decoration: none;
        margin-left: 10px;
    }
    .btn-cancel:hover { color: var(--text-primary); }
    @media (max-width: 860px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="wrap">
    <section class="admin-section">
        <div class="admin-head">
            <h2>Edit Model: {{ $model->name }}</h2>
            <a href="{{ route('admin.models') }}" style="font-size:13px; color:var(--text-muted);">← Kembali</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-head">Edit Model</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.models.update', $model->id) }}">
                    @csrf @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Model</label>
                            <input type="text" name="name" value="{{ old('name', $model->name) }}" required>
                            @error('name') <span style="color:var(--rose); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Versi</label>
                            <input type="text" name="version" value="{{ old('version', $model->version) }}" required>
                            @error('version') <span style="color:var(--rose); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Akurasi (%)</label>
                            <input type="number" name="accuracy" step="0.01" min="0" max="100" value="{{ old('accuracy', $model->metrics['accuracy'] ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Loss</label>
                            <input type="number" name="loss" step="0.0001" min="0" value="{{ old('loss', $model->metrics['loss'] ?? '') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    <a href="{{ route('admin.models') }}" class="btn-cancel">Batal</a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
