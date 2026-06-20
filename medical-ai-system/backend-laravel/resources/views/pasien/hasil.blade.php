@extends('layouts.app')

@section('title', 'Hasil Analisis — BreastVisionAI 4')

@section('content')
<style>
    .result-section { padding: 64px 0 100px; }
    .result-card {
        max-width: 720px;
        margin: 0 auto;
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
    }
    .result-header {
        padding: 32px 32px 24px;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .result-header h2 {
        font-family: var(--font-display);
        font-weight: 500;
        font-size: 24px;
        margin-bottom: 4px;
    }
    .result-header .date {
        font-size: 12.5px;
        color: var(--text-muted);
        font-family: var(--font-mono);
    }
    .result-badge {
        font-family: var(--font-mono);
        font-size: 13px;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 100px;
    }
    .result-badge.normal { color: #34D399; background: rgba(52,211,153,0.12); border: 1px solid rgba(52,211,153,0.3); }
    .result-badge.osteopenia { color: var(--amber); background: rgba(245,180,84,0.12); border: 1px solid rgba(245,180,84,0.3); }
    .result-badge.osteoporosis { color: var(--rose); background: rgba(242,115,122,0.12); border: 1px solid rgba(242,115,122,0.3); }

    .result-body { padding: 32px; }
    .result-image-box {
        background: var(--bg-deep);
        border-radius: 12px;
        border: 1px solid var(--line);
        overflow: hidden;
        margin-bottom: 32px;
        text-align: center;
    }
    .result-image-box img {
        max-width: 100%;
        max-height: 360px;
        display: block;
        margin: 0 auto;
    }
    .result-image-box .img-label {
        padding: 10px 16px;
        font-family: var(--font-mono);
        font-size: 11.5px;
        color: var(--text-muted);
        border-top: 1px solid var(--line);
    }

    .result-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 32px;
    }
    .result-item {
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 18px;
    }
    .result-item-label { font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
    .result-item-value {
        font-family: var(--font-mono);
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .prob-section { margin-bottom: 32px; }
    .prob-section h3 {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 14px;
    }
    .prob-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 0;
        border-bottom: 1px solid var(--line);
    }
    .prob-row:last-child { border-bottom: none; }
    .prob-class {
        width: 120px;
        font-size: 14px;
        font-weight: 500;
    }
    .prob-track {
        flex: 1;
        height: 8px;
        border-radius: 100px;
        background: rgba(148,175,209,0.1);
        overflow: hidden;
    }
    .prob-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 0.8s ease;
    }
    .prob-pct {
        font-family: var(--font-mono);
        font-size: 13px;
        color: var(--text-muted);
        width: 48px;
        text-align: right;
    }

    .result-actions {
        display: flex;
        gap: 14px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--line);
    }
    .btn-pdf {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        padding: 12px 22px;
        border: 1px solid var(--line-bright);
        border-radius: 9px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        cursor: pointer;
    }
    .btn-pdf:hover { border-color: var(--text-secondary); color: var(--text-primary); }
    .btn-new {
        font-size: 14px;
        font-weight: 600;
        color: #062220;
        background: var(--teal);
        padding: 12px 22px;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-new:hover { background: #5EEAD4; transform: translateY(-1px); }

    .patient-info {
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }
    .patient-info-item label { font-size: 11px; color: var(--text-muted); display: block; margin-bottom: 2px; }
    .patient-info-item span { font-size: 13.5px; color: var(--text-primary); }

    @media (max-width: 600px) {
        .result-header { flex-direction: column; align-items: flex-start; gap: 16px; }
        .result-grid { grid-template-columns: 1fr; }
        .result-actions { flex-direction: column; }
        .result-body { padding: 20px; }
    }
</style>

<div class="wrap">
    <section class="result-section">
        <div class="result-card">
            <div class="result-header">
                <div>
                    <h2>Hasil Analisis</h2>
                    <span class="date">{{ $prediction->created_at->format('d M Y · H:i') }} WIB</span>
                </div>
                <span class="result-badge {{ strtolower($prediction->prediction) }}">
                    {{ strtoupper($prediction->prediction) }}
                </span>
            </div>

            <div class="result-body">
                <div class="result-image-box">
                    <img src="{{ asset('storage/' . $prediction->image_path) }}" alt="Citra Rontgen">
                    <div class="img-label">CITRA_RONTGEN_LUTUT</div>
                </div>

                <div class="patient-info">
                    <div class="patient-info-item">
                        <label>Nama</label>
                        <span>{{ $prediction->patient->name }}</span>
                    </div>
                    <div class="patient-info-item">
                        <label>NIK</label>
                        <span>{{ $prediction->patient->nik }}</span>
                    </div>
                    <div class="patient-info-item">
                        <label>Jenis Kelamin</label>
                        <span>{{ $prediction->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="patient-info-item">
                        <label>Usia</label>
                        <span>{{ \Carbon\Carbon::parse($prediction->patient->date_of_birth)->age }} tahun</span>
                    </div>
                </div>

                <div class="result-grid">
                    <div class="result-item">
                        <div class="result-item-label">Prediksi</div>
                        <div class="result-item-value" style="color: {{ $prediction->prediction == 'Normal' ? '#34D399' : ($prediction->prediction == 'Benign' ? 'var(--amber)' : 'var(--rose)') }};">
                            {{ $prediction->prediction }}
                        </div>
                    </div>
                    <div class="result-item">
                        <div class="result-item-label">Tingkat Keyakinan</div>
                        <div class="result-item-value">{{ $prediction->confidence }}%</div>
                    </div>
                    @if($prediction->aiModel)
                    <div class="result-item">
                        <div class="result-item-label">Model AI</div>
                        <div class="result-item-value" style="font-size:15px;">{{ $prediction->aiModel->name }} v{{ $prediction->aiModel->version }}</div>
                    </div>
                    @endif
                    @if($prediction->analysis_time)
                    <div class="result-item">
                        <div class="result-item-label">Waktu Analisis</div>
                        <div class="result-item-value">{{ $prediction->analysis_time }} ms</div>
                    </div>
                    @endif
                </div>

                <div class="prob-section">
                    <h3>Distribusi Probabilitas</h3>
                    @foreach($prediction->probabilities as $i => $prob)
                        @php
                            $colors = ['#34D399', 'var(--amber)', 'var(--rose)'];
                            $color = $colors[$i] ?? 'var(--text-muted)';
                        @endphp
                        <div class="prob-row">
                            <span class="prob-class" style="color: {{ $color }};">
                                {{ $prob->class_name }}
                                @if($prob->class_name == $prediction->prediction)
                                    ←
                                @endif
                            </span>
                            <div class="prob-track">
                                <div class="prob-fill" style="width: {{ $prob->probability }}%; background: {{ $color }};"></div>
                            </div>
                            <span class="prob-pct">{{ $prob->probability }}%</span>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:24px; padding:14px 18px; background:rgba(242,115,122,0.06); border:1px solid rgba(242,115,122,0.2); border-radius:10px; font-size:12px; color:var(--text-muted); line-height:1.6;">
                    ⚠ <strong style="color:var(--text-secondary);">Bukan diagnosis medis resmi.</strong> Hasil prediksi ini adalah alat bantu deteksi awal berbasis kecerdasan buatan dan tidak dapat dijadikan sebagai diagnosis medis final. Hasil prediksi dapat mengandung kesalahan. Untuk mendapatkan diagnosis yang akurat, konsultasikan dengan dokter atau tenaga medis profesional yang berkompeten.
                </div>

                <div class="result-actions">
                    <a href="{{ route('pasien.form') }}" class="btn-new">
                        Pemeriksaan Baru
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="#062220" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <button class="btn-pdf" onclick="window.print()">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M4 5V2h5l3 3v9H4v-3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 11H2v-2a2 2 0 012-2h8a2 2 0 012 2v2h-2" stroke="currentColor" stroke-width="1.4"/></svg>
                        Export PDF
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
