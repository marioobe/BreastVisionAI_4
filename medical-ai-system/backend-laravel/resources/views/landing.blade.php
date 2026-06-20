@extends('layouts.app')

@section('title', 'BreastVisionAI 4 — Klasifikasi USG Payudara')

@section('content')
<style>
    .hero {
        padding: 96px 0 80px;
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        gap: 56px;
        align-items: center;
    }
    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        font-family: var(--font-mono);
        font-size: 12.5px;
        letter-spacing: 0.06em;
        color: var(--teal);
        background: var(--teal-dim);
        border: 1px solid rgba(45,212,191,0.25);
        padding: 6px 12px;
        border-radius: 100px;
        margin-bottom: 28px;
    }
    .eyebrow .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--teal);
        box-shadow: 0 0 0 3px rgba(45,212,191,0.18);
    }
    h1 {
        font-family: var(--font-display);
        font-weight: 500;
        font-size: 54px;
        line-height: 1.08;
        letter-spacing: -0.01em;
        color: var(--text-primary);
        margin-bottom: 24px;
    }
    h1 em {
        font-style: italic;
        font-weight: 400;
        background: linear-gradient(95deg, var(--teal), var(--blue));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-desc {
        font-size: 16.5px;
        line-height: 1.65;
        color: var(--text-secondary);
        max-width: 480px;
        margin-bottom: 36px;
    }
    .hero-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 44px;
    }
    .btn-lg {
        font-size: 15px;
        font-weight: 600;
        color: #062220;
        background: var(--teal);
        padding: 15px 26px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 0 0 0 rgba(45,212,191,0.4);
    }
    .btn-lg:hover { background: #5EEAD4; box-shadow: 0 8px 24px -6px rgba(45,212,191,0.45); transform: translateY(-1px); }
    .btn-lg-outline {
        font-size: 14.5px;
        font-weight: 500;
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-bottom: 1px solid var(--line-bright);
        padding-bottom: 2px;
        transition: color 0.2s, border-color 0.2s;
    }
    .btn-lg-outline:hover { color: var(--text-primary); border-color: var(--text-primary); }
    .trust-row {
        display: flex;
        gap: 32px;
        padding-top: 28px;
        border-top: 1px solid var(--line);
    }
    .trust-item { display: flex; flex-direction: column; gap: 4px; }
    .trust-num {
        font-family: var(--font-mono);
        font-size: 21px;
        font-weight: 500;
        color: var(--text-primary);
    }
    .trust-label { font-size: 12.5px; color: var(--text-muted); }

    .scan-panel {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 30px 60px -30px rgba(0,0,0,0.6);
    }
    .scan-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
    }
    .scan-panel-head-title {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }
    .status-pill {
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: var(--font-mono);
        font-size: 11px;
        color: var(--teal);
    }
    .status-pill .dot { width: 5px; height: 5px; border-radius: 50%; background: var(--teal); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.35; } }

    .scan-image-area {
        position: relative;
        height: 280px;
        background: radial-gradient(ellipse at center, rgba(45,212,191,0.06) 0%, transparent 70%), var(--bg-deep);
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--line);
    }
    .scan-line {
        position: absolute;
        left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--teal), transparent);
        opacity: 0.7;
        animation: scanmove 3.2s ease-in-out infinite;
    }
    @keyframes scanmove {
        0%, 100% { top: 14%; }
        50% { top: 86%; }
    }
    .bone-svg { width: 132px; height: 220px; opacity: 0.92; }

    .scan-panel-body { padding: 22px 20px 24px; }
    .result-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .result-label { font-size: 12.5px; color: var(--text-muted); }
    .result-value {
        font-family: var(--font-mono);
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
    }
    .confidence-track {
        height: 6px;
        border-radius: 100px;
        background: rgba(148,175,209,0.12);
        overflow: hidden;
        margin-bottom: 18px;
    }
    .confidence-fill {
        height: 100%;
        width: 87%;
        border-radius: 100px;
        background: linear-gradient(90deg, var(--amber), #F2A33D);
    }
    .class-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .class-cell {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 8px;
        text-align: center;
    }
    .class-cell.active { border-color: rgba(245,180,84,0.45); background: rgba(245,180,84,0.07); }
    .class-cell-dot { width: 7px; height: 7px; border-radius: 50%; margin: 0 auto 7px; }
    .class-cell span { display: block; font-size: 11px; color: var(--text-muted); }
    .class-cell.active span { color: var(--amber); font-weight: 600; }

    .strip {
        border-top: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
        padding: 16px 0;
        background: var(--bg-panel);
    }
    .strip-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-family: var(--font-mono);
        font-size: 12.5px;
        color: var(--text-muted);
        text-align: center;
        flex-wrap: wrap;
    }
    .strip-inner strong { color: var(--text-secondary); font-weight: 500; }

    .flow { border-top: 1px solid var(--line); }
    .flow-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border-left: 1px solid var(--line);
    }
    .flow-step {
        border-right: 1px solid var(--line);
        padding: 32px 26px 36px;
        position: relative;
    }
    .flow-step-num {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--teal);
        margin-bottom: 22px;
        display: block;
    }
    .flow-step h3 {
        font-family: var(--font-body);
        font-size: 16.5px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .flow-step p {
        font-size: 13.5px;
        color: var(--text-secondary);
        line-height: 1.6;
    }
    .flow-arrow {
        position: absolute;
        right: -7px;
        top: 36px;
        width: 14px; height: 14px;
        background: var(--bg-deep);
        border: 1px solid var(--line);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .flow-arrow::after {
        content: '';
        width: 4px; height: 4px;
        border-top: 1px solid var(--teal);
        border-right: 1px solid var(--teal);
        transform: rotate(45deg) translate(-1px,1px);
    }

    .classes-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .class-card {
        background: var(--bg-panel);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 28px 26px;
        transition: border-color 0.25s, transform 0.25s;
    }
    .class-card:hover { transform: translateY(-3px); border-color: var(--line-bright); }
    .class-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
    }
    .class-indicator { width: 10px; height: 10px; border-radius: 50%; }
    .class-density-bars { display: flex; align-items: flex-end; gap: 3px; height: 28px; }
    .class-density-bars span { width: 4px; border-radius: 2px; display: block; }
    .class-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; font-family: var(--font-body); }
    .class-card p { font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; }

    .cta-section { text-align: center; padding: 110px 0; }
    .cta-section h2 { font-size: 42px; max-width: 600px; margin: 0 auto 20px; }
    .cta-section .section-sub { max-width: 480px; margin: 0 auto 40px; }
    .cta-buttons { display: flex; gap: 18px; justify-content: center; }

    @media (max-width: 900px) {
        .hero { grid-template-columns: 1fr; padding-top: 56px; }
        h1 { font-size: 38px; }
        .classes-grid { grid-template-columns: 1fr; }
        .flow-grid { grid-template-columns: 1fr; border-left: none; }
        .flow-step { border-right: none; border-bottom: 1px solid var(--line); }
        .flow-arrow { display: none; }
    }
</style>

<div class="wrap">
    <section class="hero" style="border:none; padding-bottom:0;">
        <div>
            <span class="eyebrow"><span class="dot"></span>BUSI · MOBILENETV2 · TRANSFER LEARNING</span>
            <h1>Klasifikasi USG<br>payudara, <em>cepat</em><br>dan akurat.</h1>
            <p class="hero-desc">
                Unggah hasil ultrasonografi (USG) payudara, dan sistem akan mengklasifikasikan jaringan ke dalam kategori Benign, Malignant, atau Normal — dalam hitungan detik, tanpa perlu login.
            </p>
            <div class="hero-actions">
                <a href="{{ route('pasien.form') }}" class="btn-lg">
                    Unggah Gambar USG
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="#062220" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="#flow" class="btn-lg-outline">Lihat cara kerjanya</a>
            </div>
            <div class="trust-row">
                <div class="trust-item">
                    <span class="trust-num">3 Kelas</span>
                    <span class="trust-label">Benign · Malignant · Normal</span>
                </div>
                <div class="trust-item">
                    <span class="trust-num">&lt; 5 detik</span>
                    <span class="trust-label">Waktu rata-rata analisis</span>
                </div>
                <div class="trust-item">
                    <span class="trust-num">224×224</span>
                    <span class="trust-label">Resolusi input citra</span>
                </div>
            </div>
        </div>

        <div class="scan-panel">
            <div class="scan-panel-head">
                <span class="scan-panel-head-title">CITRA_USG_PAYUDARA_01.PNG</span>
                <span class="status-pill"><span class="dot"></span>MENGANALISIS</span>
            </div>
            <div class="scan-image-area">
                <div class="scan-line"></div>
                <svg width="100" height="160" viewBox="0 0 100 160" fill="none">
                    <circle cx="50" cy="80" r="38" stroke="#2DD4BF" stroke-width="1.2" opacity="0.15"/>
                    <circle cx="50" cy="80" r="26" stroke="#2DD4BF" stroke-width="1.2" opacity="0.3"/>
                    <circle cx="50" cy="80" r="14" stroke="#2DD4BF" stroke-width="1.2" opacity="0.6"/>
                    <circle cx="50" cy="80" r="4" fill="#2DD4BF" opacity="0.8"/>
                    <path d="M16 34 Q30 22 50 30 Q70 38 84 26" stroke="#2DD4BF" stroke-width="1.2" opacity="0.35" fill="none"/>
                    <path d="M16 46 Q30 34 50 42 Q70 50 84 38" stroke="#2DD4BF" stroke-width="1.2" opacity="0.25" fill="none"/>
                    <path d="M16 58 Q30 46 50 54 Q70 62 84 50" stroke="#2DD4BF" stroke-width="1.2" opacity="0.15" fill="none"/>
                    <path d="M16 102 Q30 114 50 106 Q70 98 84 110" stroke="#2DD4BF" stroke-width="1.2" opacity="0.25" fill="none"/>
                    <path d="M16 114 Q30 126 50 118 Q70 110 84 122" stroke="#2DD4BF" stroke-width="1.2" opacity="0.15" fill="none"/>
                    <line x1="14" y1="130" x2="86" y2="130" stroke="#2DD4BF" stroke-width="1" opacity="0.2"/>
                    <line x1="14" y1="134" x2="86" y2="134" stroke="#2DD4BF" stroke-width="1" opacity="0.2"/>
                </svg>
            </div>
            <div class="scan-panel-body">
                <div class="result-row">
                    <span class="result-label">Prediksi Awal</span>
                    <span class="result-value" style="color:var(--amber);">BENIGN</span>
                </div>
                <div class="result-row" style="margin-bottom:8px;">
                    <span class="result-label">Tingkat Keyakinan</span>
                    <span class="result-value">87.3%</span>
                </div>
                <div class="confidence-track"><div class="confidence-fill"></div></div>
                <div class="class-grid">
                    <div class="class-cell active">
                        <span class="class-cell-dot" style="background:var(--amber);"></span>
                        <span>Benign</span>
                    </div>
                    <div class="class-cell">
                        <span class="class-cell-dot" style="background:var(--rose);"></span>
                        <span>Malignant</span>
                    </div>
                    <div class="class-cell">
                        <span class="class-cell-dot" style="background:#34D399;"></span>
                        <span>Normal</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="strip" style="margin-top:80px;">
    <div class="wrap">
        <div class="strip-inner">
            <span>⚠</span>
            <span><strong>Bukan diagnosis medis resmi.</strong> Hasil prediksi adalah alat bantu klasifikasi awal — konsultasikan selalu dengan dokter atau radiolog untuk diagnosis akhir.</span>
        </div>
    </div>
</div>

<div class="wrap">
    <section id="flow" class="flow" style="border-bottom:1px solid var(--line);">
        <div class="section-head">
            <span class="section-eyebrow">cara kerja</span>
            <h2>Empat langkah, satu jawaban awal.</h2>
            <p class="section-sub">Dari gambar USG sampai hasil klasifikasi, prosesnya dirancang sesederhana mungkin — tidak perlu login, tidak perlu instalasi.</p>
        </div>
        <div class="flow-grid">
            <div class="flow-step">
                <span class="flow-step-num">01</span>
                <h3>Unggah Citra</h3>
                <p>Seret gambar USG payudara atau pilih dari perangkatmu. Format JPG/PNG didukung.</p>
                <div class="flow-arrow"></div>
            </div>
            <div class="flow-step">
                <span class="flow-step-num">02</span>
                <h3>Setujui Disclaimer</h3>
                <p>Konfirmasi bahwa kamu memahami hasil ini bersifat bantu-deteksi, bukan diagnosis final.</p>
                <div class="flow-arrow"></div>
            </div>
            <div class="flow-step">
                <span class="flow-step-num">03</span>
                <h3>Model Menganalisis</h3>
                <p>MobileNetV2 membaca pola tekstur dan struktur jaringan payudara dari citra USG dalam hitungan detik.</p>
                <div class="flow-arrow"></div>
            </div>
            <div class="flow-step">
                <span class="flow-step-num">04</span>
                <h3>Lihat Hasil</h3>
                <p>Dapatkan klasifikasi beserta tingkat keyakinan, lengkap dengan rekomendasi langkah lanjutan.</p>
            </div>
        </div>
    </section>

    <section id="classes">
        <div class="section-head">
            <span class="section-eyebrow">kategori hasil</span>
            <h2>Tiga kemungkinan klasifikasi.</h2>
            <p class="section-sub">Model membaca pola tekstur dan echogenicity jaringan payudara dari citra USG dan mengelompokkannya ke salah satu dari tiga kategori berikut.</p>
        </div>
        <div class="classes-grid">
            <div class="class-card">
                <div class="class-card-top">
                    <span class="class-indicator" style="background:var(--amber);"></span>
                    <div class="class-density-bars">
                        <span style="height:70%; background:var(--amber);"></span>
                        <span style="height:55%; background:var(--amber);"></span>
                        <span style="height:65%; background:var(--amber);"></span>
                        <span style="height:45%; background:var(--amber);"></span>
                    </div>
                </div>
                <h3>Benign</h3>
                <p>Jaringan menunjukkan pola jinak — tidak terindikasi keganasan. Meski demikian, pemantauan rutin tetap dianjurkan.</p>
            </div>
            <div class="class-card">
                <div class="class-card-top">
                    <span class="class-indicator" style="background:var(--rose);"></span>
                    <div class="class-density-bars">
                        <span style="height:40%; background:var(--rose);"></span>
                        <span style="height:25%; background:var(--rose);"></span>
                        <span style="height:35%; background:var(--rose);"></span>
                        <span style="height:20%; background:var(--rose);"></span>
                    </div>
                </div>
                <h3>Malignant</h3>
                <p>Terdeteksi pola mencurigakan yang mengindikasikan kemungkinan keganasan. Disarankan untuk segera melakukan pemeriksaan lanjutan.</p>
            </div>
            <div class="class-card">
                <div class="class-card-top">
                    <span class="class-indicator" style="background:#34D399;"></span>
                    <div class="class-density-bars">
                        <span style="height:100%; background:#34D399;"></span>
                        <span style="height:90%; background:#34D399;"></span>
                        <span style="height:95%; background:#34D399;"></span>
                        <span style="height:85%; background:#34D399;"></span>
                    </div>
                </div>
                <h3>Normal</h3>
                <p>Jaringan payudara tampak normal tanpa indikasi massa atau kelainan struktur yang signifikan pada citra USG.</p>
            </div>
        </div>
    </section>

    <section id="scan" class="cta-section">
        <span class="section-eyebrow" style="display:flex; justify-content:center;">mulai sekarang</span>
        <h2>Satu unggahan, satu langkah<br>lebih dekat ke kepastian.</h2>
        <p class="section-sub">Gratis, tanpa login, dan hasil langsung muncul di hadapanmu.</p>
        <div class="cta-buttons">
            <a href="{{ route('pasien.form') }}" class="btn-lg">Unggah Gambar USG Sekarang</a>
        </div>
    </section>
</div>
@endsection
