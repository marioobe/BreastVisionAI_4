@extends('layouts.app')

@section('title', 'Hasil Prediksi — BreastVisionAI 4')

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

    .alert {
        background: rgba(45,212,191,0.1);
        border: 1px solid rgba(45,212,191,0.25);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        color: var(--teal);
        margin-bottom: 20px;
    }

    /* filter bar */
    .filter-bar {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label {
        font-size: 10.5px;
        font-family: var(--font-mono);
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }
    .filter-group input,
    .filter-group select {
        padding: 8px 10px;
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 6px;
        color: var(--text-primary);
        font-family: var(--font-body);
        font-size: 13px;
        outline: none;
        min-width: 140px;
    }
    .filter-group input:focus,
    .filter-group select:focus { border-color: var(--teal); }
    .btn-filter {
        padding: 8px 16px;
        background: var(--teal);
        color: #062220;
        font-weight: 600;
        font-size: 13px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-filter:hover { background: #5EEAD4; }
    .btn-reset {
        padding: 8px 16px;
        background: transparent;
        color: var(--text-secondary);
        font-size: 13px;
        border: 1px solid var(--line-bright);
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-reset:hover { color: var(--text-primary); }

    /* table */
    .table-wrap {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    th {
        text-align: left;
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--text-muted);
        padding: 14px 16px;
        border-bottom: 1px solid var(--line);
        letter-spacing: 0.05em;
        white-space: nowrap;
    }
    td {
        padding: 14px 16px;
        font-size: 13px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
    }
    tr:last-child td { border-bottom: none; }
    tr:hover { background: rgba(148,175,209,0.02); }

    .thumb-img {
        width: 48px; height: 48px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid var(--line);
        display: block;
    }
    .pred-badge {
        font-family: var(--font-mono);
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 100px;
        display: inline-block;
    }
    .pred-badge.benign { color: var(--amber); background: rgba(245,180,84,0.1); }
    .pred-badge.malignant { color: var(--rose); background: rgba(242,115,122,0.1); }
    .pred-badge.normal { color: #34D399; background: rgba(52,211,153,0.1); }

    .patient-name { font-weight: 500; }
    .patient-nik { font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono); }

    .action-link {
        font-size: 12px;
        font-family: var(--font-mono);
        color: var(--text-secondary);
        text-decoration: none;
        margin-right: 10px;
        transition: color 0.15s;
    }
    .action-link:hover { color: var(--teal); }
    .action-link.danger:hover { color: var(--rose); }

    /* pagination */
    .pagination-wrap {
        padding: 16px 22px;
        border-top: 1px solid var(--line);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12.5px;
        color: var(--text-muted);
    }
    .pagination-wrap nav { display: inline-flex; gap: 4px; }
    .pagination-wrap nav a,
    .pagination-wrap nav span {
        padding: 5px 10px;
        border: 1px solid var(--line);
        border-radius: 5px;
        font-size: 12px;
        color: var(--text-secondary);
        text-decoration: none;
    }
    .pagination-wrap nav a:hover { border-color: var(--teal); color: var(--teal); }
    .pagination-wrap nav span[aria-current] {
        background: var(--teal);
        color: #062220;
        border-color: var(--teal);
    }

    @media (max-width: 900px) {
        .filter-bar { flex-direction: column; }
        .filter-group input, .filter-group select { min-width: 100%; }
        .table-wrap { overflow-x: auto; }
    }
</style>

<div class="wrap">
    <section class="admin-section">
        <div class="admin-head">
            <h2>Hasil Prediksi</h2>
            <a href="{{ route('dashboard') }}" style="font-size:13px; color:var(--text-muted);">← Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.predictions') }}">
            <div class="filter-bar">
                <div class="filter-group">
                    <label>Cari</label>
                    <input type="text" name="search" placeholder="Nama / NIK" value="{{ request('search') }}">
                </div>
                <div class="filter-group">
                    <label>Kelas</label>
                    <select name="prediction">
                        <option value="">Semua</option>
                        <option value="Benign" @selected(request('prediction') == 'Benign')>Benign</option>
                        <option value="Malignant" @selected(request('prediction') == 'Malignant')>Malignant</option>
                        <option value="Normal" @selected(request('prediction') == 'Normal')>Normal</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <button type="submit" class="btn-filter">Filter</button>
                <a href="{{ route('admin.predictions') }}" class="btn-reset">Reset</a>
            </div>
        </form>

        <div class="table-wrap">
            @if($predictions->isEmpty())
                <div style="padding:48px; text-align:center; color:var(--text-muted); font-size:14px;">
                    Tidak ada data prediksi.
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Pasien</th>
                            <th>Prediksi</th>
                            <th>Confidence</th>
                            <th>Model</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($predictions as $pred)
                        <tr>
                            <td>
                                @if($pred->image_path)
                                    <img src="{{ asset('storage/' . $pred->image_path) }}" class="thumb-img" alt="Citra USG" loading="lazy">
                                @else
                                    <span style="color:var(--text-muted); font-size:11px;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="patient-name">{{ $pred->patient->name }}</span>
                                <span class="patient-nik">{{ $pred->patient->nik }}</span>
                            </td>
                            <td>
                                <span class="pred-badge {{ strtolower($pred->prediction) }}">{{ $pred->prediction }}</span>
                            </td>
                            <td style="font-family:var(--font-mono); font-size:12.5px;">{{ $pred->confidence }}%</td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $pred->aiModel?->name ?? '—' }}</td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $pred->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.predictions.show', $pred->id) }}" class="action-link">Detail</a>
                                <form action="{{ route('admin.predictions.destroy', $pred->id) }}" method="POST" class="inline-form" style="display:inline;" onsubmit="return confirm('Hapus prediksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-link danger" style="background:none; border:none; cursor:pointer; font-family:var(--font-mono); font-size:12px; color:var(--text-secondary); padding:0;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrap">
                    <span>Menampilkan {{ $predictions->firstItem() }}-{{ $predictions->lastItem() }} dari {{ $predictions->total() }}</span>
                    {{ $predictions->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
