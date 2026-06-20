@extends('layouts.app')

@section('title', 'Kelola Model AI — BreastVisionAI 4')

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
        margin-bottom: 24px;
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
    .form-group.full { grid-column: 1 / -1; }
    .form-group label {
        display: block;
        font-size: 12px;
        font-family: var(--font-mono);
        color: var(--text-secondary);
        margin-bottom: 6px;
        letter-spacing: 0.02em;
    }
    .form-group input, .form-group select {
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
    .form-group input::file-selector-button {
        background: var(--bg-panel-light);
        border: 1px solid var(--line-bright);
        border-radius: 5px;
        color: var(--text-secondary);
        padding: 5px 10px;
        font-size: 12px;
        cursor: pointer;
        margin-right: 10px;
    }
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

    table { width: 100%; border-collapse: collapse; }
    th {
        text-align: left;
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--text-muted);
        padding: 12px 14px;
        border-bottom: 1px solid var(--line);
        letter-spacing: 0.05em;
    }
    td {
        padding: 12px 14px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--line);
    }
    tr:last-child td { border-bottom: none; }
    .badge-active {
        font-size: 10.5px;
        font-family: var(--font-mono);
        color: var(--teal);
        background: var(--teal-dim);
        padding: 3px 8px;
        border-radius: 100px;
    }
    .badge-inactive {
        font-size: 10.5px;
        font-family: var(--font-mono);
        color: var(--text-muted);
        background: rgba(148,175,209,0.08);
        padding: 3px 8px;
        border-radius: 100px;
    }
    .action-link {
        font-size: 12px;
        font-family: var(--font-mono);
        color: var(--text-secondary);
        text-decoration: none;
        margin-right: 12px;
        transition: color 0.15s;
    }
    .action-link:hover { color: var(--teal); }
    .action-link.danger:hover { color: var(--rose); }

    .inline-form { display: inline; }

    @media (max-width: 860px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="wrap">
    <section class="admin-section">
        <div class="admin-head">
            <h2>Kelola Model AI</h2>
            <a href="{{ route('dashboard') }}" style="font-size:13px; color:var(--text-muted);">← Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-head">+ Tambah Model Baru</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.models.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Model</label>
                            <input type="text" name="name" placeholder="MobileNetV2-BUSI" required>
                            @error('name') <span style="color:var(--rose); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Versi</label>
                            <input type="text" name="version" placeholder="1.0.0" required>
                            @error('version') <span style="color:var(--rose); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Akurasi (%)</label>
                            <input type="number" name="accuracy" step="0.01" min="0" max="100" placeholder="96.73">
                        </div>
                        <div class="form-group">
                            <label>Loss</label>
                            <input type="number" name="loss" step="0.0001" min="0" placeholder="0.1234">
                        </div>
                        <div class="form-group full">
                            <label>File Model (.keras / .h5)</label>
                            <input type="file" name="model_file" accept=".keras,.h5" required>
                            @error('model_file') <span style="color:var(--rose); font-size:11px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Upload Model</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-head">Daftar Model</div>
            <div class="card-body" style="padding:0;">
                @if($models->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--text-muted); font-size:14px;">
                        Belum ada model. Upload model pertama di atas.
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Versi</th>
                                <th>Akurasi</th>
                                <th>Loss</th>
                                <th>Status</th>
                                <th>Penggunaan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($models as $model)
                            <tr>
                                <td style="font-weight:500;">{{ $model->name }}</td>
                                <td style="font-family:var(--font-mono); font-size:12.5px;">v{{ $model->version }}</td>
                                <td style="font-family:var(--font-mono); font-size:12.5px;">
                                    {{ $model->metrics['accuracy'] ?? '-' }}
                                    @if(isset($model->metrics['accuracy'])) % @endif
                                </td>
                                <td style="font-family:var(--font-mono); font-size:12.5px;">
                                    {{ $model->metrics['loss'] ?? '-' }}
                                </td>
                                <td>
                                    @if($model->is_active)
                                        <span class="badge-active">AKTIF</span>
                                    @else
                                        <span class="badge-inactive">NONAKTIF</span>
                                    @endif
                                </td>
                                <td style="font-family:var(--font-mono); font-size:12.5px;">{{ $model->predictions_count }}</td>
                                <td style="font-size:12px; color:var(--text-muted);">{{ $model->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.models.edit', $model->id) }}" class="action-link">Edit</a>
                                    @if(!$model->is_active)
                                        <form action="{{ route('admin.models.activate', $model->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="action-link" style="background:none; border:none; cursor:pointer; font-family:var(--font-mono); font-size:12px; color:var(--text-secondary); padding:0;">Aktifkan</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.models.destroy', $model->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Hapus model {{ $model->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-link danger" style="background:none; border:none; cursor:pointer; font-family:var(--font-mono); font-size:12px; color:var(--text-secondary); padding:0;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
