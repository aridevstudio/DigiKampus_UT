<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiKampus UT — Platform Pembelajaran Digital</title>
    <meta name="description" content="Platform pembelajaran digital untuk Universitas Terbuka. Akses kursus, tugas, ujian, dan sertifikat — semua di satu tempat.">
    <link rel="stylesheet" href="{{ asset('assets/css/globalFont.css') }}">
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --c-primary: #3B82F6;
            --c-primary-dark: #2563EB;
            --c-primary-light: #93C5FD;
            --c-accent: #F97316;
            --c-accent-hover: #EA580C;
            --c-bg: #F8FAFC;
            --c-bg-alt: #F1F5F9;
            --c-surface: #FFFFFF;
            --c-text: #1E293B;
            --c-text-muted: #64748B;
            --c-text-light: #94A3B8;
            --c-border: #E2E8F0;
            --c-hero-start: #EFF6FF;
            --c-hero-end: #F8FAFC;
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Work Sans', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body);
            color: var(--c-text);
            background: var(--c-bg);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-display); }

        /* ————— Hero gradient mesh ————— */
        .hero-bg {
            position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--c-hero-start) 0%, #DBEAFE 35%, var(--c-hero-end) 70%, #FFF7ED 100%);
            z-index: 0;
        }
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            opacity: 0.5;
        }
        .hero-blob-1 { width: 500px; height: 500px; background: rgba(59,130,246,0.15); top: -80px; right: -100px; }
        .hero-blob-2 { width: 400px; height: 400px; background: rgba(249,115,22,0.08); bottom: -60px; left: -60px; }
        .hero-blob-3 { width: 300px; height: 300px; background: rgba(147,197,253,0.2); top: 40%; left: 30%; }

        /* ————— Navbar ————— */
        .nav-glass {
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.6);
            border-radius: 16px;
            transition: box-shadow 0.3s ease, background 0.3s ease;
        }
        .nav-glass.scrolled {
            background: rgba(255,255,255,0.92);
            box-shadow: 0 4px 30px rgba(0,0,0,0.06);
        }

        /* ————— Animations ————— */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity:0; }
            to   { opacity:1; }
        }
        @keyframes gentleFloat {
            0%,100% { transform:translateY(0); }
            50%     { transform:translateY(-10px); }
        }
        @keyframes countPulse {
            0%   { transform:scale(1); }
            50%  { transform:scale(1.05); }
            100% { transform:scale(1); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .anim-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.16,1,0.3,1), transform 0.7s cubic-bezier(0.16,1,0.3,1);
        }
        .anim-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .float-anim { animation: gentleFloat 6s ease-in-out infinite; }

        /* ————— Hero dashboard card ————— */
        .dash-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 20px;
            box-shadow:
                0 4px 6px -1px rgba(0,0,0,0.04),
                0 20px 50px -12px rgba(59,130,246,0.12);
            overflow: hidden;
        }
        .dash-card-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* ————— Section heading ————— */
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(59,130,246,0.04));
            border: 1px solid rgba(59,130,246,0.12);
            color: var(--c-primary);
            font-family: var(--font-display);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* ————— Feature cards ————— */
        .feat-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 20px;
            padding: 28px 24px;
            transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), box-shadow 0.35s ease, border-color 0.35s ease;
            cursor: default;
        }
        .feat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(59,130,246,0.1);
            border-color: var(--c-primary-light);
        }
        .feat-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
        }
        .feat-card-highlight {
            grid-column: span 2;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dark));
            border: none;
            color: #fff;
        }
        .feat-card-highlight .feat-icon { background: rgba(255,255,255,0.2); }
        .feat-card-highlight:hover { border-color: transparent; box-shadow: 0 20px 40px -8px rgba(37,99,235,0.3); }

        /* ————— Stat cards ————— */
        .stat-strip {
            background: linear-gradient(135deg, var(--c-primary) 0%, #1D4ED8 100%);
            position: relative;
            overflow: hidden;
        }
        .stat-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .stat-number {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        /* ————— Testimonial card ————— */
        .testi-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 20px;
            padding: 28px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .testi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -8px rgba(0,0,0,0.08);
        }
        .testi-avatar {
            width: 44px; height: 44px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 15px;
            color: #fff;
        }

        /* ————— Portal cards ————— */
        .portal-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            text-decoration: none;
            color: var(--c-text);
            transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), box-shadow 0.35s ease, border-color 0.35s ease;
            display: block;
        }
        .portal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(59,130,246,0.12);
            border-color: var(--c-primary-light);
        }
        .portal-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            color: #fff;
        }
        .portal-arrow {
            display: inline-flex; align-items: center; gap: 4px;
            color: var(--c-primary);
            font-weight: 600;
            font-size: 14px;
            transition: gap 0.25s ease;
        }
        .portal-card:hover .portal-arrow { gap: 8px; }

        /* ————— CTA section ————— */
        .cta-section {
            background: linear-gradient(135deg, var(--c-primary) 0%, #1D4ED8 60%, #1E40AF 100%);
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(249,115,22,0.15), transparent 70%);
            top: -200px; right: -150px;
            pointer-events: none;
        }

        /* ————— Buttons ————— */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px;
            background: var(--c-primary);
            color: #fff;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            text-decoration: none;
            transition: background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer; border: none;
        }
        .btn-primary:hover {
            background: var(--c-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(37,99,235,0.35);
        }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px;
            background: transparent;
            color: var(--c-text);
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            border: 1.5px solid var(--c-border);
            text-decoration: none;
            transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
            cursor: pointer;
        }
        .btn-outline:hover {
            background: var(--c-bg-alt);
            border-color: var(--c-primary-light);
            transform: translateY(-2px);
        }
        .btn-white {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px;
            background: #fff;
            color: var(--c-primary);
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            text-decoration: none;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer; border: none;
        }
        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(0,0,0,0.15);
        }
        .btn-accent {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px;
            background: var(--c-accent);
            color: #fff;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            text-decoration: none;
            transition: background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer; border: none;
        }
        .btn-accent:hover {
            background: var(--c-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -6px rgba(234,88,12,0.35);
        }

        /* ————— Progress bar ————— */
        .progress-track { height: 5px; background: #E2E8F0; border-radius: 100px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 100px; transition: width 1.2s cubic-bezier(0.16,1,0.3,1); }

        /* ————— Responsive ————— */
        @media (max-width: 1024px) {
            .feat-card-highlight { grid-column: span 1; }
        }
        @media (max-width: 768px) {
            .stat-number { font-size: 1.5rem; }
            .hero-blob { display: none; }
        }

        /* ————— Reduced motion ————— */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .anim-up { opacity: 1; transform: none; }
        }

        /* ————— Misc ————— */
        .quote-mark {
            font-family: Georgia, serif;
            font-size: 48px;
            line-height: 1;
            color: var(--c-primary-light);
            opacity: 0.5;
        }
    </style>
</head>
<body>

    {{-- ═══════════ NAVBAR ═══════════ --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 px-4 sm:px-6 pt-4">
        <div class="nav-glass max-w-6xl mx-auto px-5 sm:px-6 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5" style="text-decoration:none">
                <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}" alt="DigiKampus" class="h-8" loading="eager">
                <div>
                    <span style="font-family:var(--font-display);font-weight:700;font-size:15px;color:var(--c-text)">DigiKampus</span>
                    <span style="font-size:11px;color:var(--c-text-light);margin-left:3px;font-weight:500">UT</span>
                </div>
            </a>
            <div class="hidden md:flex items-center" style="gap:32px">
                <a href="#fitur" style="font-size:14px;color:var(--c-text-muted);text-decoration:none;font-weight:500;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Fitur</a>
                <a href="#portal" style="font-size:14px;color:var(--c-text-muted);text-decoration:none;font-weight:500;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Portal</a>
                <a href="#testimoni" style="font-size:14px;color:var(--c-text-muted);text-decoration:none;font-weight:500;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Testimoni</a>
            </div>
            <a href="{{ route('mahasiswa.login') }}" class="btn-primary" style="padding:9px 20px;font-size:13px;border-radius:10px">
                Masuk
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
            </a>
        </div>
    </nav>

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="relative" style="min-height:100vh;display:flex;align-items:center;padding:120px 24px 80px;overflow:hidden">
        <div class="hero-bg"></div>
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>

        <div class="max-w-6xl mx-auto w-full relative" style="z-index:1;display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center">
            {{-- Text --}}
            <div style="max-width:640px">
                <div class="section-badge" style="animation:fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) 0.1s both">
                    <span style="width:6px;height:6px;background:var(--c-primary);border-radius:50%;display:inline-block"></span>
                    Universitas Terbuka
                </div>
                <h1 style="margin-top:20px;font-size:clamp(2.2rem,5vw,3.5rem);font-weight:800;line-height:1.1;letter-spacing:-0.02em;color:var(--c-text);animation:fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) 0.2s both">
                    Belajar lebih cerdas<br>
                    <span style="background:linear-gradient(135deg,var(--c-primary),#7C3AED);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">di era digital.</span>
                </h1>
                <p style="margin-top:20px;font-size:clamp(1rem,2vw,1.15rem);color:var(--c-text-muted);line-height:1.7;max-width:480px;animation:fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) 0.35s both">
                    Kursus, tugas, ujian, dan sertifikat — semua di satu tempat. Dibangun khusus untuk kebutuhan pembelajaran mahasiswa Universitas Terbuka.
                </p>
                <div style="margin-top:32px;display:flex;flex-wrap:wrap;gap:12px;animation:fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) 0.45s both">
                    <a href="{{ route('mahasiswa.login') }}" class="btn-accent">
                        Mulai Belajar
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </a>
                    <a href="#fitur" class="btn-outline">Lihat Fitur</a>
                </div>
            </div>

            {{-- Hero Illustration (large screens) --}}
            <div class="hidden lg:flex" style="justify-content:center;align-items:center;animation:fadeUp 0.7s cubic-bezier(0.16,1,0.3,1) 0.5s both">
                <img src="{{ asset('assets/image/auth/Illustration 1 Login Mahasiswa.png') }}" alt="DigiKampus Illustration" class="float-anim" style="width:100%;max-width:440px;object-fit:contain" loading="eager">
            </div>
        </div>
    </section>

    {{-- ═══════════ STATS STRIP ═══════════ --}}
    <section class="stat-strip" style="padding:48px 24px">
        <div class="max-w-6xl mx-auto relative" style="z-index:1;display:grid;grid-template-columns:repeat(2,1fr);gap:24px">
            @php $stats = [['2800', 'Mahasiswa Aktif', '+'], ['150', 'Kursus Tersedia', '+'], ['50', 'Dosen Pengajar', '+'], ['98', 'Kepuasan Pengguna', '%']]; @endphp
            @foreach($stats as $i => $s)
            <div class="anim-up" style="text-align:center;transition-delay:{{ $i * 80 }}ms">
                <p class="stat-number" data-count="{{ $s[0] }}" data-suffix="{{ $s[2] }}">0{{ $s[2] }}</p>
                <p style="font-size:13px;color:rgba(255,255,255,0.65);margin-top:4px;font-weight:500">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══════════ FEATURES ═══════════ --}}
    <section id="fitur" style="padding:96px 24px;background:var(--c-bg)">
        <div class="max-w-6xl mx-auto">
            <div class="anim-up" style="margin-bottom:56px">
                <div class="section-badge">Fitur Platform</div>
                <h2 style="margin-top:16px;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:var(--c-text);line-height:1.2">
                    Yang bisa kamu lakukan<br>di DigiKampus
                </h2>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px">
                @php
                $features = [
                    ['Kursus & Modul', 'Akses video pembelajaran, modul bacaan, dan materi yang dipersiapkan oleh dosen.', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25', 'highlight', '#fff'],
                    ['Quiz & Tugas', 'Kerjakan quiz interaktif dan submit tugas langsung dari dashboard.', 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z', 'normal', 'rgba(59,130,246,0.08)'],
                    ['Jadwal & Kalender', 'Lihat jadwal ujian dan kegiatan di kalender yang terintegrasi.', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5', 'normal', 'rgba(16,185,129,0.08)'],
                    ['Tracking Progres', 'Dashboard visual untuk memantau perkembangan belajar kamu.', 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z', 'normal', 'rgba(249,115,22,0.08)'],
                    ['Sertifikat', 'Raih sertifikat resmi setelah menyelesaikan kursus kamu.', 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5', 'normal', 'rgba(124,58,237,0.08)'],
                    ['Pembayaran Online', 'Bayar kursus via Transfer BCA, OVO, atau GoPay.', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z', 'normal', 'rgba(59,130,246,0.08)'],
                ];
                @endphp

                @foreach($features as $i => $f)
                <div class="feat-card {{ $f[3] === 'highlight' ? 'feat-card-highlight' : '' }} anim-up" style="transition-delay:{{ $i * 60 }}ms">
                    <div class="feat-icon" style="background:{{ $f[4] }};margin-bottom:16px">
                        <svg width="22" height="22" fill="none" stroke="{{ $f[3] === 'highlight' ? '#fff' : 'var(--c-primary)' }}" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f[2] }}"/></svg>
                    </div>
                    <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin-bottom:6px">{{ $f[0] }}</h3>
                    <p style="font-size:14px;color:{{ $f[3] === 'highlight' ? 'rgba(255,255,255,0.8)' : 'var(--c-text-muted)' }};line-height:1.6">{{ $f[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════ TESTIMONIALS ═══════════ --}}
    <section id="testimoni" style="padding:96px 24px;background:var(--c-bg-alt)">
        <div class="max-w-6xl mx-auto">
            <div class="anim-up" style="margin-bottom:56px">
                <div class="section-badge">Testimoni</div>
                <h2 style="margin-top:16px;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:var(--c-text);line-height:1.2">
                    Kata mereka tentang<br>DigiKampus
                </h2>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px">
                @php
                $testimonials = [
                    ['Siti Nurhaliza', 'Mahasiswa Informatika', 'Platform yang sangat membantu! Semua materi tersedia dan mudah diakses. Progres belajar jadi lebih terstruktur.', 'var(--c-primary)'],
                    ['Ahmad Rizky', 'Mahasiswa Manajemen', 'Fitur quiz dan tugas online-nya keren. Tidak perlu lagi kirim tugas lewat email, semua bisa dari satu tempat.', '#10B981'],
                    ['Dewi Lestari', 'Mahasiswa Akuntansi', 'Jadwal ujian dan kalender-nya sangat berguna. Saya jadi tidak pernah lupa jadwal lagi.', '#F59E0B'],
                ];
                @endphp
                @foreach($testimonials as $i => $t)
                <div class="testi-card anim-up" style="transition-delay:{{ $i * 80 }}ms">
                    <div class="quote-mark" style="margin-bottom:-8px">"</div>
                    <p style="font-size:14px;color:var(--c-text);line-height:1.7;margin-bottom:20px">{{ $t[2] }}</p>
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="testi-avatar" style="background:{{ $t[3] }}">{{ substr($t[0],0,1) }}</div>
                        <div>
                            <p style="font-family:var(--font-display);font-size:14px;font-weight:700;color:var(--c-text)">{{ $t[0] }}</p>
                            <p style="font-size:12px;color:var(--c-text-muted)">{{ $t[1] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════ PORTAL LOGIN ═══════════ --}}
    <section id="portal" style="padding:96px 24px;background:var(--c-bg)">
        <div class="max-w-6xl mx-auto">
            <div class="anim-up" style="text-align:center;margin-bottom:56px">
                <div class="section-badge">Portal Login</div>
                <h2 style="margin-top:16px;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:var(--c-text);line-height:1.2">Masuk sesuai peranmu</h2>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;max-width:840px;margin:0 auto">
                @php
                $portals = [
                    ['Mahasiswa', 'Akses kursus, tugas, quiz, dan pantau progres belajar kamu.', 'mahasiswa.login', 'linear-gradient(135deg,var(--c-primary),#2563EB)', 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5'],
                    ['Dosen', 'Kelola kursus, buat materi, dan pantau progres mahasiswa.', 'dosen.login', 'linear-gradient(135deg,#7C3AED,#5B21B6)', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                    ['Admin', 'Kelola pengguna, konfigurasi sistem, dan monitoring.', 'admin.login', 'linear-gradient(135deg,#475569,#334155)', 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z'],
                ];
                @endphp
                @foreach($portals as $i => $p)
                <a href="{{ route($p[2]) }}" class="portal-card anim-up" style="transition-delay:{{ $i * 80 }}ms">
                    <div class="portal-icon" style="background:{{ $p[3] }}">
                        <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $p[4] }}"/></svg>
                    </div>
                    <h3 style="font-family:var(--font-display);font-size:17px;font-weight:700;margin-bottom:6px">{{ $p[0] }}</h3>
                    <p style="font-size:13px;color:var(--c-text-muted);line-height:1.6;margin-bottom:16px">{{ $p[1] }}</p>
                    <span class="portal-arrow">
                        Masuk
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════ CTA ═══════════ --}}
    <section class="cta-section" style="padding:80px 24px">
        <div class="max-w-2xl mx-auto text-center relative anim-up" style="z-index:1">
            <h2 style="font-family:var(--font-display);font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#fff;margin-bottom:12px;line-height:1.2">Siap mulai belajar?</h2>
            <p style="font-size:15px;color:rgba(255,255,255,0.7);margin-bottom:32px;line-height:1.6">Bergabung dengan ribuan mahasiswa UT yang sudah merasakan kemudahan belajar digital.</p>
            <a href="{{ route('mahasiswa.login') }}" class="btn-white">
                Mulai Sekarang
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
            </a>
        </div>
    </section>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer style="border-top:1px solid var(--c-border);padding:32px 24px;background:var(--c-surface)">
        <div class="max-w-6xl mx-auto" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;font-size:13px;color:var(--c-text-muted)">
            <div style="display:flex;align-items:center;gap:8px">
                <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}" alt="Logo" style="height:20px;opacity:0.4" loading="lazy">
                <span>© {{ date('Y') }} DigiKampus UT</span>
            </div>
            <div style="display:flex;gap:24px">
                <a href="#" style="color:var(--c-text-muted);text-decoration:none;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Tentang</a>
                <a href="#" style="color:var(--c-text-muted);text-decoration:none;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Kontak</a>
                <a href="#" style="color:var(--c-text-muted);text-decoration:none;transition:color 0.2s" onmouseover="this.style.color='var(--c-primary)'" onmouseout="this.style.color='var(--c-text-muted)'">Privasi</a>
            </div>
        </div>
    </footer>

    {{-- ═══════════ SCRIPTS ═══════════ --}}
    <script>
        // ——— Scroll reveal ———
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.anim-up').forEach(el => observer.observe(el));

        // ——— Navbar scroll effect ———
        const nav = document.getElementById('navbar');
        const navGlass = nav.querySelector('.nav-glass');
        window.addEventListener('scroll', () => {
            navGlass.classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });

        // ——— Smooth scroll for anchor links ———
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // ——— Counter animation for stats ———
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count);
                    const suffix = el.dataset.suffix || '';
                    const duration = 1800;
                    const start = performance.now();

                    function update(now) {
                        const elapsed = now - start;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease out cubic
                        const eased = 1 - Math.pow(1 - progress, 3);
                        const current = Math.round(eased * target);
                        el.textContent = current.toLocaleString('id-ID') + suffix;
                        if (progress < 1) requestAnimationFrame(update);
                    }
                    requestAnimationFrame(update);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        document.querySelectorAll('.stat-number').forEach(el => counterObserver.observe(el));

        // ——— Responsive stat grid ———
        const statGrid = document.querySelector('.stat-strip .max-w-6xl');
        if (statGrid && window.innerWidth >= 768) {
            statGrid.style.gridTemplateColumns = 'repeat(4, 1fr)';
        }
    </script>
</body>
</html>
