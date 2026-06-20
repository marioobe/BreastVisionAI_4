@extends('layouts.app')

@section('title', 'Detail Prediksi — BreastVisionAI 4')

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

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .card {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }
    .card-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
        font-weight: 600;
        font-size: 14px;
    }
    .card-body { padding: 20px; }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--line);
        font-size: 13.5px;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: var(--text-muted); }
    .detail-value { font-weight: 500; text-align: right; }

    .pred-image {
        width: 100%;
        max-height: 360px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: var(--bg-deep);
    }

    .prob-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .prob-item:last-child { margin-bottom: 0; }
    .prob-label {
        width: 100px;
        font-size: 13px;
        font-weight: 500;
    }
    .prob-track {
        flex: 1;
        height: 10px;
        border-radius: 100px;
        background: rgba(148,175,209,0.1);
        overflow: hidden;
    }
    .prob-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 0.5s;
    }
    .prob-pct {
        font-family: var(--font-mono);
        font-size: 13px;
        color: var(--text-muted);
        width: 60px;
        text-align: right;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
    }
    .back-link:hover { color: var(--text-secondary); }

    @media (max-width: 860px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="wrap">
    <section class="admin-section">
        <div class="admin-head">
            <h2>Detail Prediksi</h2>
            <a href="{{ route('admin.predictions') }}" style="font-size:13px; color:var(--text-muted);">← Kembali</a>
        </div>

        <div class="detail-grid">
            <div class="card">
                <div class="card-head">Citra USG</div>
                <div class="card-body">
                    @if($prediction->image_path)
                        <img src="{{ asset('storage/' . $prediction->image_path) }}" class="pred-image" alt="Citra USG">
                    @else
                        <div style="padding:48px; text-align:center; color:var(--text-muted); font-size:13px;">Tidak ada gambar</div>
                    @endif
                </div>
            </div>

            <div>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head">Hasil Prediksi</div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Prediksi</span>
                            <span class="detail-value" style="color: {{ $prediction->prediction == 'Normal' ? '#34D399' : ($prediction->prediction == 'Benign' ? 'var(--amber)' : 'var(--rose)') }};">
                                {{ $prediction->prediction }}
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Confidence</span>
                            <span class="detail-value" style="font-family:var(--font-mono);">{{ $prediction->confidence }}%</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Waktu Analisis</span>
                            <span class="detail-value" style="font-family:var(--font-mono);">{{ $prediction->analysis_time ?? '—' }} detik</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Model</span>
                            <span class="detail-value">{{ $prediction->aiModel?->name ?? '—' }} v{{ $prediction->aiModel?->version ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal</span>
                            <span class="detail-value" style="font-size:12.5px;">{{ $prediction->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Disclaimer</span>
                            <span class="detail-value">{{ $prediction->consent_approved ? 'Disetujui' : 'Tidak' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">Probabilitas Semua Kelas</div>
                    <div class="card-body">
                        @php
                            $colors = ['var(--amber)', 'var(--rose)', '#34D399'];
                            $i = 0;
                            $total = $prediction->probabilities->sum('probability') ?: 1;
                        @endphp
                        @forelse($prediction->probabilities as $prob)
                            @php $pct = round($prob->probability, 1); @endphp
                            <div class="prob-item">
                                <span class="prob-label" style="color: {{ $colors[$i] ?? 'var(--text-muted)' }};">{{ $prob->class_name }}</span>
                                <div class="prob-track">
                                    <div class="prob-fill" style="width: {{ $pct }}%; background: {{ $colors[$i] ?? 'var(--text-muted)' }};"></div>
                                </div>
                                <span class="prob-pct">{{ $pct }}%</span>
                            </div>
                            @php $i++; @endphp
                        @empty
                            <div style="color:var(--text-muted); font-size:13px;">Tidak ada data probabilitas.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card" style="margin-top:20px;">
                    <div class="card-head">Data Pasien</div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Nama</span>
                            <span class="detail-value">{{ $prediction->patient->name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">NIK</span>
                            <span class="detail-value" style="font-family:var(--font-mono);">{{ $prediction->patient->nik }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Jenis Kelamin</span>
                            <span class="detail-value">{{ $prediction->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal Lahir</span>
                            <span class="detail-value">{{ $prediction->patient->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">No. HP</span>
                            <span class="detail-value">{{ $prediction->patient->phone ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value" style="font-size:12px;">{{ $prediction->patient->email ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.predictions.destroy', $prediction->id) }}" method="POST" style="margin-top:20px;" onsubmit="return confirm('Hapus data prediksi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:10px 20px; background:transparent; border:1px solid rgba(242,115,122,0.3); color:var(--rose); border-radius:7px; cursor:pointer; font-size:13px;">Hapus Data Prediksi</button>
                </form>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="back-link">← Kembali ke Panel Admin</a>
    </section>
</div>
@endsection
