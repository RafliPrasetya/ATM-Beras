<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATM Beras Rogojampi — Kiosk</title>
            <link rel="shortcut icon" href="{{ asset('storage/images/poli_lazismu.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- ─── BASE CONFIG untuk JavaScript ─────────────────────────────────── --}}
    {{-- Ganti dengan URL server hosting dan token mesin yang sebenarnya --}}
    <meta name="api-base-url"   content="{{ rtrim(config('app.url'), '/') }}">
    <meta name="csrf-token"     content="{{ csrf_token() }}">

    <style>
        /* ═══════════════════════════════════════════════════════════════
           DESIGN TOKENS
        ═══════════════════════════════════════════════════════════════ */
        :root {
            --bg-deep:      #050d1a;
            --bg-panel:     #0b1a2e;
            --bg-card:      #0f2040;
            --accent-gold:  #f5a623;
            --accent-green: #2ecc71;
            --accent-red:   #e74c3c;
            --accent-blue:  #3498db;
            --text-primary: #f0f4ff;
            --text-muted:   #7a90b3;
            --text-gold:    #f5c842;
            --border-dim:   rgba(245, 166, 35, 0.15);
            --border-glow:  rgba(245, 166, 35, 0.5);
            --glow-gold:    0 0 30px rgba(245, 166, 35, 0.3);
            --glow-green:   0 0 30px rgba(46, 204, 113, 0.4);
            --glow-red:     0 0 30px rgba(231, 76, 60, 0.4);
            --radius-lg:    20px;
            --radius-xl:    32px;
            --transition:   all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ═══════════════════════════════════════════════════════════════
           RESET & BASE
        ═══════════════════════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            width: 100%; height: 100%;
            overflow: hidden;
            background: var(--bg-deep);
            font-family: 'Outfit', sans-serif;
            color: var(--text-primary);
            user-select: none;
        }

        /* ═══════════════════════════════════════════════════════════════
           ANIMATED BACKGROUND
        ═══════════════════════════════════════════════════════════════ */
        .bg-orbs {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            overflow: hidden;
        }
        .bg-orbs::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            top: -200px; left: -200px;
            background: radial-gradient(circle, rgba(245,166,35,0.06) 0%, transparent 70%);
            animation: orbFloat 12s ease-in-out infinite alternate;
        }
        .bg-orbs::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            bottom: -150px; right: -150px;
            background: radial-gradient(circle, rgba(46,204,113,0.05) 0%, transparent 70%);
            animation: orbFloat 15s ease-in-out infinite alternate-reverse;
        }
        @keyframes orbFloat {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(40px, 30px) scale(1.1); }
        }

        /* ═══════════════════════════════════════════════════════════════
           MAIN LAYOUT
        ═══════════════════════════════════════════════════════════════ */
        #app {
            position: relative; z-index: 1;
            width: 100vw; height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px;
        }

        /* ── Header ── */
        .kiosk-header {
            position: fixed; top: 0; left: 0; right: 0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 40px;
            background: linear-gradient(180deg, rgba(5,13,26,0.95) 0%, transparent 100%);
            z-index: 10;
        }
        .kiosk-logo {
            display: flex; align-items: center; gap: 14px;
        }
        .kiosk-logo-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #f5a623, #e8871a);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(245,166,35,0.4);
        }
        .kiosk-logo-text h1 {
            font-size: 22px; font-weight: 800;
            background: linear-gradient(135deg, #f5c842, #f5a623);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        .kiosk-logo-text p {
            font-size: 11px; color: var(--text-muted); font-weight: 400;
            letter-spacing: 0.08em; text-transform: uppercase;
        }
        .kiosk-status {
            display: flex; align-items: center; gap: 10px;
        }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent-green);
            box-shadow: 0 0 10px rgba(46,204,113,0.8);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        .status-dot.offline { background: var(--accent-red); box-shadow: 0 0 10px rgba(231,76,60,0.8); animation: none; }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(0.85); }
        }
        .status-text { font-size: 13px; color: var(--text-muted); font-weight: 500; }

        /* ── Fullscreen Button ── */
        .fullscreen-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            margin-left: 6px;
            padding: 0;
            outline: none;
        }
        .fullscreen-btn:hover {
            background: rgba(245, 166, 35, 0.15);
            border-color: var(--accent-gold);
            color: var(--text-primary);
            box-shadow: 0 0 12px rgba(245, 166, 35, 0.25);
            transform: scale(1.05);
        }
        .fullscreen-btn svg {
            width: 15px;
            height: 15px;
        }

        /* ── Clock ── */
        .kiosk-clock {
            text-align: right;
        }
        .clock-time {
            font-size: 28px; font-weight: 700;
            background: linear-gradient(135deg, #f0f4ff, #a0b0d0);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        .clock-date {
            font-size: 12px; color: var(--text-muted); margin-top: 2px;
        }

        /* ═══════════════════════════════════════════════════════════════
           SCREENS (state machine)
        ═══════════════════════════════════════════════════════════════ */
        .screen {
            display: none; flex-direction: column;
            align-items: center; justify-content: center;
            width: 100%; max-width: 680px;
            text-align: center;
            animation: screenIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .screen.active { display: flex; }
        @keyframes screenIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Panel card shared ── */
        .panel {
            width: 100%;
            background: var(--bg-panel);
            border: 1px solid var(--border-dim);
            border-radius: var(--radius-xl);
            padding: 40px;
            backdrop-filter: blur(10px);
            position: relative; overflow: hidden;
        }
        .panel::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-gold), transparent);
            opacity: 0.4;
        }

        /* ═══════════════════════════════════════════════════════════════
           SCREEN 1 — IDLE (scan RFID)
        ═══════════════════════════════════════════════════════════════ */
        #screen-idle .panel {
            box-shadow: var(--glow-gold), 0 20px 60px rgba(0,0,0,0.5);
        }
        .rfid-animation {
            position: relative;
            width: 160px; height: 160px;
            margin: 0 auto 36px;
        }
        .rfid-ring {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(245,166,35,0.3);
            animation: rfidExpand 2.4s ease-out infinite;
        }
        .rfid-ring:nth-child(2) { animation-delay: 0.8s; }
        .rfid-ring:nth-child(3) { animation-delay: 1.6s; }
        @keyframes rfidExpand {
            0%   { transform: scale(0.6); opacity: 0.8; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        .rfid-icon {
            position: absolute; inset: 20px;
            background: linear-gradient(135deg, #1a3050, #0f2040);
            border: 2px solid rgba(245,166,35,0.4);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(245,166,35,0.2);
        }
        .rfid-icon svg { width: 56px; height: 56px; }

        .idle-title {
            font-size: 32px; font-weight: 800;
            background: linear-gradient(135deg, #f5c842, #f5a623);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        .idle-subtitle {
            font-size: 16px; color: var(--text-muted); font-weight: 400;
            line-height: 1.6;
        }
        .idle-instruction {
            margin-top: 32px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 14px 24px;
            background: rgba(245,166,35,0.06);
            border: 1px solid rgba(245,166,35,0.15);
            border-radius: 12px;
            font-size: 14px; color: var(--text-muted);
        }
        .idle-instruction .dot-blink {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent-gold);
            animation: blink 1.2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; } 50% { opacity: 0.2; }
        }

        /* ─── Error popup di atas idle ─── */
        .error-toast {
            display: none;
            position: fixed; top: 90px; left: 50%; transform: translateX(-50%);
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.4);
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 15px; font-weight: 500; color: #ff8a7a;
            z-index: 50;
            animation: toastIn 0.3s ease;
            box-shadow: 0 4px 20px rgba(231,76,60,0.2);
        }
        .error-toast.show { display: block; }
        @keyframes toastIn {
            from { opacity: 0; top: 75px; }
            to   { opacity: 1; top: 90px; }
        }

        /* ═══════════════════════════════════════════════════════════════
           SCREEN 2 — VALIDATED (pilih jumlah)
        ═══════════════════════════════════════════════════════════════ */
        #screen-validated .panel {
            box-shadow: var(--glow-green), 0 20px 60px rgba(0,0,0,0.5);
            border-color: rgba(46,204,113,0.2);
        }
        #screen-validated .panel::before {
            background: linear-gradient(90deg, transparent, var(--accent-green), transparent);
            opacity: 0.3;
        }

        .welcome-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(46,204,113,0.1);
            border: 1px solid rgba(46,204,113,0.3);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 13px; color: var(--accent-green); font-weight: 600;
            margin-bottom: 20px;
            letter-spacing: 0.04em;
        }
        .welcome-badge span { font-size: 16px; }

        .mustahik-name {
            font-size: 38px; font-weight: 800;
            background: linear-gradient(135deg, #f0f4ff, #c8d8f0);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 6px; line-height: 1.2;
        }
        .mustahik-nik {
            font-size: 14px; color: var(--text-muted);
            font-family: monospace; letter-spacing: 0.15em;
            margin-bottom: 28px;
        }

        .kuota-bar {
            background: rgba(15,32,64,0.8);
            border: 1px solid rgba(245,166,35,0.2);
            border-radius: 16px;
            padding: 20px 28px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 32px;
            gap: 20px;
        }
        .kuota-label {
            font-size: 13px; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.08em;
        }
        .kuota-value {
            font-size: 40px; font-weight: 900;
            background: linear-gradient(135deg, #f5c842, #f5a623);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        .kuota-unit {
            font-size: 18px; font-weight: 600; color: var(--text-muted);
            align-self: flex-end; padding-bottom: 4px;
        }
        .kuota-right { text-align: right; }
        .kuota-lokasi {
            font-size: 13px; color: var(--text-muted); margin-top: 4px;
        }

        /* Pilihan kg */
        .pilih-label {
            font-size: 15px; color: var(--text-muted);
            margin-bottom: 16px; font-weight: 500;
        }
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .option-btn {
            background: var(--bg-card);
            border: 2px solid var(--border-dim);
            border-radius: 14px;
            padding: 20px 12px;
            cursor: pointer;
            transition: var(--transition);
            display: flex; flex-direction: column;
            align-items: center; gap: 4px;
        }
        .option-btn:hover {
            border-color: var(--accent-gold);
            background: rgba(245,166,35,0.08);
            transform: translateY(-3px);
            box-shadow: var(--glow-gold);
        }
        .option-btn.selected {
            border-color: var(--accent-gold);
            background: rgba(245,166,35,0.12);
            box-shadow: var(--glow-gold);
        }
        .option-btn .opt-num {
            font-size: 32px; font-weight: 800;
            background: linear-gradient(135deg, #f5c842, #f5a623);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        .option-btn .opt-unit {
            font-size: 12px; color: var(--text-muted); font-weight: 500;
        }
        .option-btn .opt-key {
            margin-top: 6px;
            font-size: 10px; color: rgba(122,144,179,0.6);
            background: rgba(122,144,179,0.08);
            border: 1px solid rgba(122,144,179,0.15);
            border-radius: 4px; padding: 2px 6px;
            font-family: monospace;
        }

        .confirm-section {
            display: flex; flex-direction: column; align-items: center; gap: 10px;
        }
        .confirm-hint {
            font-size: 13px; color: var(--text-muted);
        }
        .confirm-hint kbd {
            background: rgba(245,166,35,0.1);
            border: 1px solid rgba(245,166,35,0.3);
            border-radius: 6px; padding: 2px 8px;
            font-family: monospace; color: var(--accent-gold);
        }
        .selected-info {
            font-size: 15px; color: var(--text-primary); font-weight: 600;
            min-height: 24px;
        }

        /* ═══════════════════════════════════════════════════════════════
           SCREEN 3 — PROCESSING
        ═══════════════════════════════════════════════════════════════ */
        .spinner-wrap {
            width: 100px; height: 100px;
            margin: 0 auto 32px;
            position: relative;
        }
        .spinner-ring {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
        }
        .spinner-ring:nth-child(1) {
            border-top-color: var(--accent-gold);
            animation: spin 1.2s linear infinite;
        }
        .spinner-ring:nth-child(2) {
            inset: 10px;
            border-top-color: rgba(245,166,35,0.4);
            animation: spin 1.8s linear infinite reverse;
        }
        .spinner-ring:nth-child(3) {
            inset: 22px;
            border-top-color: rgba(245,166,35,0.2);
            animation: spin 2.4s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .processing-title {
            font-size: 28px; font-weight: 700; color: var(--text-primary);
            margin-bottom: 10px;
        }
        .processing-subtitle {
            font-size: 15px; color: var(--text-muted);
        }

        /* ═══════════════════════════════════════════════════════════════
           SCREEN 4 — RESULT (sukses / gagal)
        ═══════════════════════════════════════════════════════════════ */
        .result-icon {
            width: 100px; height: 100px;
            margin: 0 auto 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 48px;
        }
        .result-icon.success {
            background: rgba(46,204,113,0.1);
            border: 2px solid rgba(46,204,113,0.4);
            box-shadow: var(--glow-green);
            animation: resultPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .result-icon.error {
            background: rgba(231,76,60,0.1);
            border: 2px solid rgba(231,76,60,0.4);
            box-shadow: var(--glow-red);
            animation: resultPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes resultPop {
            from { transform: scale(0.5); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        .result-title {
            font-size: 30px; font-weight: 800;
            margin-bottom: 10px;
        }
        .result-title.success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .result-title.error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .result-message {
            font-size: 16px; color: var(--text-muted); margin-bottom: 28px;
            line-height: 1.6;
        }

        .result-stats {
            display: flex; justify-content: center; gap: 24px;
            flex-wrap: wrap; margin-bottom: 32px;
        }
        .result-stat {
            background: rgba(15,32,64,0.8);
            border: 1px solid var(--border-dim);
            border-radius: 12px;
            padding: 14px 24px; text-align: center;
            min-width: 120px;
        }
        .result-stat-val {
            font-size: 26px; font-weight: 800;
            color: var(--text-primary); line-height: 1;
        }
        .result-stat-lbl {
            font-size: 11px; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-top: 4px;
        }

        .countdown-bar {
            width: 100%; height: 4px;
            background: rgba(255,255,255,0.08);
            border-radius: 2px;
            overflow: hidden;
        }
        .countdown-fill {
            height: 100%; width: 100%;
            background: linear-gradient(90deg, var(--accent-gold), #e8871a);
            border-radius: 2px;
            transition: width 0.1s linear;
        }
        .countdown-text {
            font-size: 12px; color: var(--text-muted);
            margin-top: 8px; text-align: center;
        }

        /* ═══════════════════════════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════════════════════════ */
        .kiosk-footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            padding: 14px 40px;
            display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(0deg, rgba(5,13,26,0.98) 0%, transparent 100%);
        }
        .footer-left {
            font-size: 12px; color: rgba(122,144,179,0.5);
        }
        .footer-right {
            font-size: 12px; color: rgba(122,144,179,0.5);
        }
        .shortcut-hint {
            font-size: 11px; color: rgba(122,144,179,0.4);
        }
        .shortcut-hint kbd {
            font-family: monospace;
            background: rgba(122,144,179,0.08);
            border: 1px solid rgba(122,144,179,0.15);
            border-radius: 4px; padding: 1px 5px;
            color: rgba(122,144,179,0.6);
        }
    </style>
</head>
<body>

<div class="bg-orbs"></div>

{{-- ── HEADER ─────────────────────────────────────────────────────── --}}
<header class="kiosk-header">
    <div class="kiosk-logo">
        <div class="kiosk-logo-icon">🌾</div>
        <div class="kiosk-logo-text">
            <h1>ATM Beras Rogojampi</h1>
            <p>Sistem Distribusi Beras Digital</p>
        </div>
    </div>

    <div class="kiosk-status" id="connection-status">
        <div class="status-dot" id="status-dot"></div>
        <span class="status-text" id="status-text">Terhubung</span>
        <button id="btn-fullscreen" class="fullscreen-btn" title="Layar Penuh" onclick="toggleFullscreen()">
            <svg id="fullscreen-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
            </svg>
        </button>
    </div>

    <div class="kiosk-clock">
        <div class="clock-time" id="clock-time">00:00:00</div>
        <div class="clock-date" id="clock-date">—</div>
    </div>
</header>

{{-- ── ERROR TOAST ─────────────────────────────────────────────────── --}}
<div class="error-toast" id="error-toast"></div>

{{-- ─── MAIN CONTENT ─────────────────────────────────────────────── --}}
<main id="app">

    {{-- ══ SCREEN 1: IDLE ══════════════════════════════════════════ --}}
    <div class="screen active" id="screen-idle">
        <div class="panel">
            <div class="rfid-animation">
                <div class="rfid-ring"></div>
                <div class="rfid-ring"></div>
                <div class="rfid-ring"></div>
                <div class="rfid-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="5" width="18" height="14" rx="2" stroke="#f5a623" stroke-width="1.5"/>
                        <path d="M7 9h10M7 12h6" stroke="#f5a623" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="17" cy="15" r="1.5" fill="#f5a623"/>
                        <path d="M19.5 12.5c1 1 1 2.5 0 3.5M21 11c2 2 2 5 0 7" stroke="#f5c842" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <h2 class="idle-title">Tempelkan Kartu<br>RFID Anda</h2>
            <p class="idle-subtitle">
                Dekatkan kartu identitas RFID ke reader<br>
                yang tersedia di samping perangkat ini
            </p>

            <div class="idle-instruction">
                <div class="dot-blink"></div>
                <span>Menunggu kartu RFID...</span>
            </div>
        </div>
    </div>

    {{-- ══ SCREEN 2: VALIDATED (pilih jumlah) ═════════════════════ --}}
    <div class="screen" id="screen-validated">
        <div class="panel">
            <div class="welcome-badge"><span>✓</span> Kartu Terverifikasi</div>

            <div class="mustahik-name" id="val-nama">—</div>
            <div class="mustahik-nik" id="val-nik">NIK: —</div>

            <div class="kuota-bar">
                <div>
                    <div class="kuota-label">Sisa Kuota Anda</div>
                    <div style="display:flex;align-items:baseline;gap:8px;margin-top:4px;">
                        <div class="kuota-value" id="val-kuota">0</div>
                        <div class="kuota-unit">kg</div>
                    </div>
                </div>
                <div class="kuota-right">
                    <div class="kuota-label">Lokasi Mesin</div>
                    <div style="margin-top:4px;font-size:14px;font-weight:500;color:var(--text-primary);" id="val-lokasi">—</div>
                </div>
            </div>

            <p class="pilih-label">Pilih jumlah beras yang ingin diambil:</p>

            <div class="options-grid" id="options-grid">
                {{-- Diisi dinamis oleh JavaScript --}}
            </div>

            <div class="confirm-section">
                <div class="selected-info" id="selected-info">Tekan angka untuk memilih jumlah</div>
                <div class="confirm-hint">
                    Tekan <kbd>Enter</kbd> untuk konfirmasi · <kbd>Backspace</kbd> untuk batal
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SCREEN 3: PROCESSING ════════════════════════════════════ --}}
    <div class="screen" id="screen-processing">
        <div class="panel">
            <div class="spinner-wrap">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="processing-title">Memproses Transaksi</div>
            <div class="processing-subtitle">Mohon tunggu, jangan cabut kartu...</div>
        </div>
    </div>

    {{-- ══ SCREEN 4: RESULT ════════════════════════════════════════ --}}
    <div class="screen" id="screen-result">
        <div class="panel">
            <div class="result-icon success" id="result-icon">✓</div>
            <div class="result-title success" id="result-title">Pengambilan Berhasil!</div>
            <div class="result-message" id="result-message">Beras sedang dikeluarkan oleh mesin.</div>

            <div class="result-stats" id="result-stats" style="display:none;">
                <div class="result-stat">
                    <div class="result-stat-val" id="stat-diambil">0</div>
                    <div class="result-stat-lbl">kg Diambil</div>
                </div>
                <div class="result-stat">
                    <div class="result-stat-val" id="stat-sisa">0</div>
                    <div class="result-stat-lbl">kg Sisa Kuota</div>
                </div>
            </div>

            <div class="countdown-bar">
                <div class="countdown-fill" id="countdown-fill"></div>
            </div>
            <div class="countdown-text" id="countdown-text">Kembali ke layar utama dalam 8 detik...</div>
        </div>
    </div>

</main>

{{-- ── FOOTER ─────────────────────────────────────────────────────── --}}
<footer class="kiosk-footer">
    <div class="footer-left">ATM Beras v1.0 · Sistem Distribusi Beras LAZISMU ROGOJAMPI</div>
    <div class="shortcut-hint">
        Input: Numpad <kbd>1</kbd>–<kbd>9</kbd> = Pilih opsi · <kbd>Enter</kbd> = Konfirmasi · <kbd>Backspace</kbd> = Batal
    </div>
    <div class="footer-right" id="machine-info">Mesin: —</div>
</footer>

<script>
/* ═══════════════════════════════════════════════════════════════════
   ATM BERAS — KIOSK CONTROLLER
   Komunikasi dengan Python script di localhost:8765
   via polling state setiap 500ms
═══════════════════════════════════════════════════════════════════ */

// ── KONFIGURASI ───────────────────────────────────────────────────
const PYTHON_LOCAL_URL = 'http://localhost:8765'; // Flask local di Raspberry Pi
const API_BASE_URL     = document.querySelector('meta[name="api-base-url"]').content;
const POLL_INTERVAL_MS = 500;    // Polling state dari Python
const RESET_DELAY_MS   = 8000;   // Waktu sebelum kembali ke idle setelah result

// Token mesin — harus diisi sesuai machine_tokens di database
// Di produksi, ini bisa diambil dari file env di Raspberry Pi
// dan di-pass ke halaman ini via custom header atau query param
const MACHINE_TOKEN = 'IXvt2v9OxyZyMkWbQOBXRmfpDbgdGtsSjQzcMww7KUCTm9AzZteL7w9GKNyTN81f'; // ← WAJIB DIISI
const MACHINE_ID    = 1;                            // ← WAJIB DIISI

// ── STATE LOCAL ───────────────────────────────────────────────────
let currentScreen    = 'idle';
let currentMustahik  = null;
let currentRfidUid   = null;
let selectedKg       = null;
let allowedOptions   = [];
let countdownTimer   = null;
let errorToastTimer  = null;
let transactionInProgress = false;  // Flag: cegah polling reset saat transaksi berjalan
let activeMachineToken = MACHINE_TOKEN; // Token aktif yang sinkron dari Python
let activeMachineId    = MACHINE_ID;    // ID mesin aktif yang sinkron dari Python
let activeRfidMode     = 'rc522';       // Mode RFID aktif: rc522 | usb | simulation

// ── DOM HELPERS ───────────────────────────────────────────────────
const screens = {
    idle:       document.getElementById('screen-idle'),
    validated:  document.getElementById('screen-validated'),
    processing: document.getElementById('screen-processing'),
    result:     document.getElementById('screen-result'),
};

function showScreen(name) {
    if (currentScreen === name) return;
    currentScreen = name;
    Object.entries(screens).forEach(([k, el]) => {
        el.classList.toggle('active', k === name);
    });
    selectedKg = null;
    updateSelectedInfo();
}

function showErrorToast(msg, durationMs = 3500) {
    const toast = document.getElementById('error-toast');
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(errorToastTimer);
    errorToastTimer = setTimeout(() => toast.classList.remove('show'), durationMs);
}

// ── JAM ───────────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('clock-time').textContent =
        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('clock-date').textContent =
        now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

// ── RENDER OPSI PILIHAN KG ────────────────────────────────────────
function renderOptions(options) {
    allowedOptions = options || [];
    const grid = document.getElementById('options-grid');
    grid.innerHTML = '';

    allowedOptions.forEach((kg, i) => {
        const btn = document.createElement('div');
        btn.className = 'option-btn';
        btn.dataset.kg = kg;
        btn.dataset.index = i;
        btn.innerHTML = `
            <div class="opt-num">${kg}</div>
            <div class="opt-unit">kilogram</div>
            <div class="opt-key">${i + 1}</div>
        `;
        btn.addEventListener('click', () => selectOption(kg, btn));
        grid.appendChild(btn);
    });
}

function selectOption(kg, btnEl) {
    document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('selected'));
    if (btnEl) btnEl.classList.add('selected');
    selectedKg = kg;
    updateSelectedInfo();
}

function updateSelectedInfo() {
    const el = document.getElementById('selected-info');
    if (selectedKg) {
        el.textContent = `✓ Dipilih: ${selectedKg} kg — tekan Enter untuk konfirmasi`;
        el.style.color = 'var(--accent-gold)';
    } else {
        el.textContent = 'Tekan angka untuk memilih jumlah';
        el.style.color = 'var(--text-muted)';
    }
}

// ── TAMPILKAN DATA MUSTAHIK ───────────────────────────────────────
function populateValidatedScreen(data) {
    document.getElementById('val-nama').textContent    = data.nama || '—';
    document.getElementById('val-nik').textContent     = `NIK: ${data.nik || '—'}`;
    document.getElementById('val-kuota').textContent   = data.sisa_kuota?.kg ?? '0';
    document.getElementById('val-lokasi').textContent  = data.machine?.lokasi || '—';
    document.getElementById('machine-info').textContent = `Mesin: ${data.machine?.kode || '—'}`;
    renderOptions(data.allowed_options || []);
}

// ── KONFIRMASI TRANSAKSI ──────────────────────────────────────────
async function konfirmasiTransaksi() {
    if (!selectedKg || !currentRfidUid) {
        console.warn('[ATM] Guard gagal:', { selectedKg, currentRfidUid });
        return;
    }

    // ← Capture ke variabel lokal SEBELUM await pertama
    // Polling resetToIdle() bisa mengubah global selectedKg di tengah async function
    const pilihKg = selectedKg;
    const rfidUid = currentRfidUid;
    transactionInProgress = true;

    const payload = {
        rfid_uid:    rfidUid,
        machine_id:  activeMachineId,
        jumlah_ambil: pilihKg,
    };

    // ← DEBUG: tampilkan payload sebelum dikirim
    console.log('[ATM] Kirim transaksi:', JSON.stringify(payload));

    showScreen('processing');

    try {
        // 1. POST ke Laravel API (menggunakan path relatif agar aman dari port mismatch)
        const resp = await fetch('/api/transaction/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${activeMachineToken}`,
            },
            body: JSON.stringify(payload),
        });

        const json = await resp.json();

        // ← DEBUG: tampilkan response lengkap
        console.log('[ATM] Response HTTP:', resp.status, JSON.stringify(json));

        if (resp.ok && json.status) {
            // 2. Perintahkan Python aktifkan motor
            try {
                await fetch(`${PYTHON_LOCAL_URL}/activate-motor`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ jumlah_kg: pilihKg }),  // ← pakai lokal
                });
            } catch (e) {
                // Motor tetap dicatat berhasil di server, log saja di console
                console.warn('Gagal hubungi motor controller:', e);
            }

            showResult({
                type:     'success',
                title:    'Pengambilan Berhasil!',
                message:  `${pilihKg} kg beras sedang dikeluarkan oleh mesin.`,  // ← lokal
                diambil:  pilihKg,                                                // ← lokal
                sisaKuota: json.data?.sisa_kuota?.kg ?? '—',
                showStats: true,
            });
        } else {
            // ← DEBUG: tampilkan detail error validasi
            console.error('[ATM] Error detail:', JSON.stringify(json.errors));
            showResult({
                type:    'error',
                title:   'Transaksi Gagal',
                message: json.message || 'Terjadi kesalahan. Silakan coba lagi.',
            });
        }

    } catch (err) {
        console.error('[ATM] Catch error:', err);
        showResult({
            type:    'error',
            title:   'Koneksi Bermasalah',
            message: 'Tidak dapat terhubung ke server. Periksa koneksi internet.',
        });
    } finally {
        transactionInProgress = false;  // Selalu reset flag setelah selesai
    }
}

// ── TAMPILKAN RESULT SCREEN ───────────────────────────────────────
function showResult({ type = 'success', title, message, diambil, sisaKuota, showStats = false }) {
    const iconEl    = document.getElementById('result-icon');
    const titleEl   = document.getElementById('result-title');
    const msgEl     = document.getElementById('result-message');
    const statsEl   = document.getElementById('result-stats');
    const fillEl    = document.getElementById('countdown-fill');
    const countEl   = document.getElementById('countdown-text');

    iconEl.textContent  = type === 'success' ? '✓' : '✕';
    iconEl.className    = `result-icon ${type}`;
    titleEl.textContent = title;
    titleEl.className   = `result-title ${type}`;
    msgEl.textContent   = message;

    if (showStats && type === 'success') {
        document.getElementById('stat-diambil').textContent = diambil ?? '—';
        document.getElementById('stat-sisa').textContent    = sisaKuota ?? '—';
        statsEl.style.display = 'flex';
    } else {
        statsEl.style.display = 'none';
    }

    showScreen('result');

    // Countdown bar
    const TOTAL = RESET_DELAY_MS;
    let remaining = TOTAL;
    clearInterval(countdownTimer);
    fillEl.style.width = '100%';

    countdownTimer = setInterval(() => {
        remaining -= 100;
        fillEl.style.width = `${(remaining / TOTAL) * 100}%`;
        const sec = Math.ceil(remaining / 1000);
        countEl.textContent = `Kembali ke layar utama dalam ${sec} detik...`;

        if (remaining <= 0) {
            clearInterval(countdownTimer);
            resetToIdle();
        }
    }, 100);
}

// ── RESET KE IDLE ─────────────────────────────────────────────────
async function resetToIdle() {
    currentMustahik = null;
    currentRfidUid  = null;
    selectedKg      = null;
    rfidBuffer      = ''; // Clear buffer pembacaan USB RFID
    showScreen('idle');

    // Beritahu Python untuk reset state-nya juga
    try {
        await fetch(`${PYTHON_LOCAL_URL}/reset`, { method: 'POST' });
    } catch (_) {}
}

let rfidBuffer = '';
let rfidTimeout = null;

// ── INPUT KEYBOARD (Numpad & USB RFID Reader) ──────────────────────
document.addEventListener('keydown', (e) => {
    // 1. Baca input USB RFID Reader (Keyboard Emulator) jika di layar idle
    if (currentScreen === 'idle') {
        // USB Reader mengetik angka 0-9
        if (e.key.match(/^[0-9]$/)) {
            clearTimeout(rfidTimeout);
            rfidBuffer += e.key;
            // Reset buffer jika ada jeda pengetikan lebih dari 400ms (mencegah ketikan manual lambat)
            rfidTimeout = setTimeout(() => { rfidBuffer = ''; }, 400);
        } else if (e.key === 'Enter' && rfidBuffer.length >= 4) {
            const scannedUid = rfidBuffer;
            rfidBuffer = '';
            clearTimeout(rfidTimeout);
            
            console.log('[ATM] Kartu terbaca via USB Reader, UID:', scannedUid);
            // Panggil fungsi validasi langsung dari browser
            validateUsbRfid(scannedUid);
        }
        return;
    }

    if (currentScreen !== 'validated') return;

    // Angka 1–9 → pilih opsi ke-n
    const numMatch = e.key.match(/^[1-9]$/);
    if (numMatch) {
        const idx = parseInt(e.key) - 1;
        if (idx < allowedOptions.length) {
            const kg  = allowedOptions[idx];
            const btn = document.querySelector(`.option-btn[data-index="${idx}"]`);
            selectOption(kg, btn);
        }
        return;
    }

    // Enter → konfirmasi
    if (e.key === 'Enter' && selectedKg) {
        konfirmasiTransaksi();
        return;
    }

    // Escape → batalkan dan kembali ke idle
    if (e.key === 'Backspace') {
        resetToIdle();
    }
});

// ── VALIDASI USB RFID ──────────────────────────────────────────────
async function validateUsbRfid(uid) {
    showScreen('processing');
    try {
        const resp = await fetch(`${API_BASE_URL}/api/rfid/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${activeMachineToken}`,
            },
            body: JSON.stringify({ rfid_uid: uid, machine_id: activeMachineId }),
        });

        const json = await resp.json();

        if (resp.ok && json.status) {
            currentMustahik = json.data;
            currentRfidUid  = uid;
            populateValidatedScreen(json.data);
            showScreen('validated');
            
            // Sinkronkan state ke Python Flask agar Python juga tahu kita di step pilih_jumlah
            await fetch(`${PYTHON_LOCAL_URL}/state-sync`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    step: "pilih_jumlah",
                    rfid_uid: uid,
                    mustahik: json.data
                })
            }).catch(() => {});
        } else {
            showScreen('idle');
            showErrorToast(`⚠ ${json.message || 'Kartu tidak terdaftar'}`);
        }
    } catch (err) {
        showScreen('idle');
        showErrorToast('⚠ Gagal menghubungi server validasi');
        console.error('[ATM] Error validasi USB RFID:', err);
    }
}

// ── POLLING STATE DARI PYTHON (localhost:8765) ────────────────────
let pollFailCount  = 0;
const MAX_FAIL     = 5;

async function pollPythonState() {
    try {
        const resp = await fetch(`${PYTHON_LOCAL_URL}/state`, {
            signal: AbortSignal.timeout(2000)
        });

        if (!resp.ok) throw new Error('non-200');

        const state = await resp.json();
        pollFailCount = 0;

        // Update indikator koneksi
        document.getElementById('status-dot').classList.remove('offline');
        document.getElementById('status-text').textContent = 'Terhubung';

        // Sinkronisasi state dari Python
        handlePythonState(state);

    } catch (err) {
        pollFailCount++;
        if (pollFailCount >= MAX_FAIL) {
            document.getElementById('status-dot').classList.add('offline');
            document.getElementById('status-text').textContent = 'Tidak Terhubung ke Pi';
        }
    }
}

function handlePythonState(state) {
    // Sinkronkan token dan ID mesin secara dinamis dari Python
    if (state.machine_token) {
        activeMachineToken = state.machine_token;
    }
    if (state.machine_id) {
        activeMachineId = state.machine_id;
    }
    if (state.rfid_mode) {
        activeRfidMode = state.rfid_mode;
    }

    // Python bilang: RFID sudah divalidasi, tampilkan info mustahik
    if (state.step === 'pilih_jumlah' && currentScreen === 'idle' && state.mustahik) {
        currentMustahik = state.mustahik;
        currentRfidUid  = state.rfid_uid;
        populateValidatedScreen(state.mustahik);
        showScreen('validated');
    }

    // Python bilang: error saat scan RFID
    if (state.error && currentScreen === 'idle') {
        showErrorToast(`⚠ ${state.error}`);
    }

    // Python bilang: kembali ke idle — TAPI jangan reset jika transaksi sedang berjalan
    if (state.step === 'scan_rfid'
        && currentScreen !== 'idle'
        && currentScreen !== 'result'
        && !transactionInProgress) {      // ← guard: jangan interrupt transaksi aktif
        resetToIdle();
    }
}

// ── FULLSCREEN TOGGLE LOGIC ────────────────────────────────────────
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(() => {
            updateFullscreenIcon(true);
        }).catch(err => {
            console.error(`Gagal mengaktifkan fullscreen: ${err.message}`);
        });
    } else {
        document.exitFullscreen().then(() => {
            updateFullscreenIcon(false);
        }).catch(err => {
            console.error(`Gagal keluar dari fullscreen: ${err.message}`);
        });
    }
}

function updateFullscreenIcon(isFullscreen) {
    const icon = document.getElementById('fullscreen-icon');
    if (!icon) return;
    
    if (isFullscreen) {
        // Exit fullscreen icon (arrows pointing inward)
        icon.innerHTML = `<path d="M4 14h6v6m0-6l-7 7m17-7h-6v6m0-6l7 7M4 10h6V4m0 6L3 3m17 7h-6V4m0 6l7-7"/>`;
    } else {
        // Fullscreen icon (arrows pointing outward)
        icon.innerHTML = `<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>`;
    }
}

// Deteksi perubahan fullscreen manual (misal tekan F11)
document.addEventListener('fullscreenchange', () => {
    updateFullscreenIcon(!!document.fullscreenElement);
});

// Mulai polling
setInterval(pollPythonState, POLL_INTERVAL_MS);
pollPythonState(); // Panggil langsung saat load
</script>
</body>
</html>
