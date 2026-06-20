@extends('layouts.app')

@section('title', 'Dashboard Admin — BreastVisionAI 4')

@section('content')
<style>
    .dash-section { padding: 48px 0 100px; }
    .dash-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 40px;
    }
    .dash-head h2 {
        font-family: var(--font-display);
        font-weight: 500;
        font-size: 28px;
        margin-bottom: 4px;
    }
    .dash-head .date { font-size: 13px; color: var(--text-muted); font-family: var(--font-mono); }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 40px;
    }
    .stat-card {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 22px;
    }
    .stat-card-label { font-size: 12.5px; color: var(--text-muted); margin-bottom: 8px; }
    .stat-card-value {
        font-family: var(--font-mono);
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .stat-card-sub { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

    .dash-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 20px;
        margin-bottom: 40px;
    }
    .dash-panel {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }
    .dash-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--line);
    }
    .dash-panel-head h3 { font-size: 15px; font-weight: 600; }
    .dash-panel-head a {
        font-size: 12.5px;
        color: var(--teal);
        font-family: var(--font-mono);
    }
    .dash-panel-body { padding: 8px 0; }

    .model-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 22px;
        border-bottom: 1px solid var(--line);
    }
    .model-row:last-child { border-bottom: none; }
    .model-info { display: flex; flex-direction: column; gap: 2px; }
    .model-name { font-size: 14px; font-weight: 500; }
    .model-meta { font-size: 12px; color: var(--text-muted); }
    .model-actions { display: flex; gap: 8px; }
    .btn-sm {
        font-size: 11.5px;
        font-family: var(--font-mono);
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid var(--line-bright);
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-sm:hover { border-color: var(--text-secondary); color: var(--text-primary); }
    .btn-sm.primary { color: #062220; background: var(--teal); border-color: var(--teal); }
    .btn-sm.primary:hover { background: #5EEAD4; }
    .btn-sm.danger { color: var(--rose); border-color: rgba(242,115,122,0.3); }
    .btn-sm.danger:hover { border-color: var(--rose); background: rgba(242,115,122,0.1); }

    .prediction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 22px;
        border-bottom: 1px solid var(--line);
        font-size: 13px;
    }
    .prediction-row:last-child { border-bottom: none; }
    .pred-patient { display: flex; flex-direction: column; }
    .pred-patient-name { font-weight: 500; }
    .pred-patient-date { font-size: 11.5px; color: var(--text-muted); }
    .pred-class {
        font-family: var(--font-mono);
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 100px;
    }
    .pred-class.normal { color: #34D399; background: rgba(52,211,153,0.1); }
    .pred-class.benign { color: var(--amber); background: rgba(245,180,84,0.1); }
    .pred-class.malignant { color: var(--rose); background: rgba(242,115,122,0.1); }

    .class-distrib {
        padding: 22px;
    }
    .class-distrib-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .class-distrib-item:last-child { margin-bottom: 0; }
    .class-distrib-label {
        width: 100px;
        font-size: 13px;
    }
    .class-distrib-track {
        flex: 1;
        height: 10px;
        border-radius: 100px;
        background: rgba(148,175,209,0.1);
        overflow: hidden;
    }
    .class-distrib-fill {
        height: 100%;
        border-radius: 100px;
    }
    .class-distrib-pct {
        font-family: var(--font-mono);
        font-size: 13px;
        color: var(--text-muted);
        width: 48px;
        text-align: right;
    }

    .recent-section {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }

    .btn-add {
        font-size: 12.5px;
        font-family: var(--font-mono);
        padding: 6px 14px;
        border-radius: 6px;
        color: #062220;
        background: var(--teal);
        border: none;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-add:hover { background: #5EEAD4; }

    @media (max-width: 900px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .dash-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 500px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="wrap">
    <section class="dash-section">
        <div class="dash-head">
            <div>
                <h2>Dashboard Admin</h2>
                <span class="date">{{ now()->format('d M Y') }}</span>
            </div>
            <div style="display:flex; gap:12px;">
                <span style="font-size:13px; color:var(--text-secondary);">{{ auth('admin')->user()->name }}</span>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-card-label">Total Prediksi</div>
                <div class="stat-card-value">{{ $totalPredictions }}</div>
                <div class="stat-card-sub">{{ $todayPredictions }} hari ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Total Pasien</div>
                <div class="stat-card-value">{{ $totalPatients }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Total Model</div>
                <div class="stat-card-value">{{ $totalModels }}</div>
                <div class="stat-card-sub">
                    @if($activeModel)
                        Aktif: {{ $activeModel->name }} v{{ $activeModel->version }}
                    @else
                        Tidak ada model aktif
                    @endif
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Prediksi per Kelas</div>
                <div class="stat-card-value" style="font-size:20px;">
                    @foreach($predictionsByClass as $class => $count)
                        <span style="display:block; font-size:14px; font-family:var(--font-body); font-weight:400; color:var(--text-secondary);">
                            {{ $class }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Total Penggunaan Model</div>
                <div class="stat-card-value">{{ $modelUsage?->predictions_count ?? 0 }}</div>
                <div class="stat-card-sub">
                    @if($activeModel)
                        Model: {{ $activeModel->name }} v{{ $activeModel->version }}
                    @else
                        Tidak ada model aktif
                    @endif
                </div>
            </div>
        </div>

        <div class="dash-grid">
            <div class="dash-panel">
                <div class="dash-panel-head">
                    <h3>Model AI</h3>
                    <a href="{{ route('admin.models') }}">+ Tambah</a>
                </div>
                <div class="dash-panel-body">
                    @forelse($models as $model)
                        <div class="model-row">
                            <div class="model-info">
                                <span class="model-name">{{ $model->name }}
                                    @if($model->is_active)
                                        <span style="color:var(--teal); font-size:11px; font-family:var(--font-mono);"> (AKTIF)</span>
                                    @endif
                                </span>
                                <span class="model-meta">
                                    v{{ $model->version }} ·
                                    @if($model->metrics && isset($model->metrics['accuracy']))
                                        Akurasi: {{ $model->metrics['accuracy'] }}% ·
                                    @endif
                                    @if($model->metrics && isset($model->metrics['loss']))
                                        Loss: {{ $model->metrics['loss'] }} ·
                                    @endif
                                    {{ $model->predictions_count }} prediksi ·
                                    {{ $model->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <div class="model-actions">
                                <a href="{{ route('admin.models.edit', $model->id) }}" class="btn-sm">Edit</a>
                                @if(!$model->is_active)
                                    <form action="{{ route('admin.models.activate', $model->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn-sm primary">Aktifkan</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.models.destroy', $model->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus model ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div style="padding:32px; text-align:center; color:var(--text-muted); font-size:14px;">
                            Belum ada model AI. Unggah model pertama.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="dash-panel">
                <div class="dash-panel-head">
                    <h3>Distribusi Kelas</h3>
                </div>
                <div class="class-distrib">
                    @php
                        $total = array_sum($predictionsByClass->toArray()) ?: 1;
                        $colors = ['#34D399', 'var(--amber)', 'var(--rose)'];
                        $i = 0;
                    @endphp
                    @foreach($predictionsByClass as $class => $count)
                        @php $pct = round(($count / $total) * 100, 1); @endphp
                        <div class="class-distrib-item">
                            <span class="class-distrib-label" style="color: {{ $colors[$i] ?? 'var(--text-muted)' }};">
                                {{ $class }}
                            </span>
                            <div class="class-distrib-track">
                                <div class="class-distrib-fill" style="width: {{ $pct }}%; background: {{ $colors[$i] ?? 'var(--text-muted)' }};"></div>
                            </div>
                            <span class="class-distrib-pct">{{ $pct }}%</span>
                        </div>
                        @php $i++; @endphp
                    @endforeach
                </div>
            </div>
        </div>

        <div class="recent-section">
            <div class="dash-panel-head">
                <h3>Prediksi Terbaru</h3>
                <a href="{{ route('admin.predictions') }}">Lihat Semua</a>
            </div>
            <div class="dash-panel-body">
                @forelse($recentPredictions as $pred)
                    <div class="prediction-row">
                        <div class="pred-patient">
                            <span class="pred-patient-name">{{ $pred->patient->name }}</span>
                            <span class="pred-patient-date">{{ $pred->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <span class="pred-class {{ strtolower($pred->prediction) }}">
                            {{ $pred->prediction }} · {{ $pred->confidence }}%
                        </span>
                        <a href="{{ route('admin.predictions.show', $pred->id) }}" style="font-size:11px; font-family:var(--font-mono); color:var(--text-muted); text-decoration:none; margin-left:10px;">Detail</a>
                    </div>
                @empty
                    <div style="padding:32px; text-align:center; color:var(--text-muted); font-size:14px;">
                        Belum ada data prediksi.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
