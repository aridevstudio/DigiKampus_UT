<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Soft Dark Mode Background - Subtle gradient */
        .dark body {
            background: #111827;
        }
        
        /* Softer card backgrounds for dark mode */
        .dark .card-soft {
            background: linear-gradient(145deg, #1f2937 0%, #1a1f2e 100%);
        }

        /* === GACOR ANIMATIONS === */
        
        /* Fade In Up Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Fade In Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Scale In Animation */
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Slide In Left Animation */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Pulse Glow Animation */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 20px 5px rgba(59, 130, 246, 0.2); }
        }
        
        /* Bounce Animation */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        /* Animation Classes */
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.4s ease-out forwards;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.4s ease-out forwards;
        }
        
        /* Stagger Delay Classes */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        
        /* Hover Lift Effect */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
        }
        .dark .hover-lift:hover {
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.4);
        }
        
        /* Hover Scale Effect */
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        /* Hover Glow Effect */
        .hover-glow {
            transition: box-shadow 0.3s ease;
        }
        .hover-glow:hover {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        
        /* Button Pulse on Hover */
        .btn-pulse:hover {
            animation: pulseGlow 1.5s infinite;
        }
        
        /* Icon Bounce on Hover */
        .icon-bounce:hover {
            animation: bounce 0.6s ease infinite;
        }
        
        /* Progress Bar Animation */
        @keyframes progressFill {
            from { width: 0; }
        }
        .animate-progress {
            animation: progressFill 1s ease-out forwards;
        }
        
        /* Shimmer Loading Effect */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        /* Card appear animation on scroll */
        .card-animate {
            opacity: 0;
            animation: fadeInUp 0.5s ease-out forwards;
        }
        
        /* Notification dot pulse */
        @keyframes notifPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }
        .notif-pulse {
            animation: notifPulse 2s ease-in-out infinite;
        }

        #mhs-main-content {
            overflow-x: auto;
        }

        #mhs-layout-main {
            min-width: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        #sidebar {
            width: 16rem;
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out, background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        #sidebar nav a > span.sidebar-label {
            max-width: 200px;
            opacity: 1;
            display: inline-block;
            transition: max-width 0.3s ease-in-out, opacity 0.2s ease-in-out, margin 0.3s ease-in-out;
        }

        #sidebar .sidebar-user-meta {
            max-width: 150px;
            opacity: 1;
            transition: max-width 0.3s ease-in-out, opacity 0.2s ease-in-out, margin 0.3s ease-in-out;
        }

        /* --- Collapsed State Styles (Desktop Collapsed or Tablet Default) --- */
        
        /* Tablet default collapse (768px to 1023px) */
        @media (min-width: 768px) and (max-width: 1023px) {
            #sidebar {
                width: 5rem !important;
            }
            #mhs-layout-main {
                margin-left: 5rem !important;
            }
            #sidebar .sidebar-logo-wrap {
                justify-content: center !important;
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            #sidebar .sidebar-logo-image {
                height: 2rem !important;
            }
            #sidebar .sidebar-user-wrap {
                justify-content: center !important;
            }
            #sidebar .sidebar-user-meta,
            #sidebar nav a > span.sidebar-label {
                max-width: 0 !important;
                opacity: 0 !important;
                overflow: hidden !important;
                pointer-events: none !important;
                margin-left: 0 !important;
            }
            #sidebar nav {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            #sidebar nav a {
                justify-content: center !important;
                width: 3rem !important; /* w-12 (48px) */
                height: 3rem !important; /* h-12 (48px) */
                padding: 0 !important;
                border-radius: 1rem !important; /* rounded-2xl (16px) */
            }
            
            /* CSS Tooltip for Collapsed Sidebar */
            #sidebar nav a {
                position: relative;
            }
            #sidebar nav a::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%) translateX(10px);
                background: #1f2937;
                color: #fff;
                padding: 0.375rem 0.625rem;
                border-radius: 0.375rem;
                font-size: 0.75rem;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.2s ease-in-out;
                z-index: 50;
                pointer-events: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            #sidebar nav a:hover::after {
                opacity: 1;
                visibility: visible;
                transform: translateY(-50%) translateX(15px);
            }
        }

        /* Desktop collapsed (min-width: 1024px) */
        @media (min-width: 1024px) {
            body.mhs-sidebar-collapsed #sidebar {
                width: 5rem !important;
            }
            body.mhs-sidebar-collapsed #mhs-layout-main {
                margin-left: 5rem !important;
            }
            body.mhs-sidebar-collapsed #sidebar .sidebar-logo-wrap {
                justify-content: center !important;
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            body.mhs-sidebar-collapsed #sidebar .sidebar-logo-image {
                height: 2rem !important;
            }
            body.mhs-sidebar-collapsed #sidebar .sidebar-user-wrap {
                justify-content: center !important;
            }
            body.mhs-sidebar-collapsed #sidebar .sidebar-user-meta,
            body.mhs-sidebar-collapsed #sidebar nav a > span.sidebar-label {
                max-width: 0 !important;
                opacity: 0 !important;
                overflow: hidden !important;
                pointer-events: none !important;
                margin-left: 0 !important;
            }
            body.mhs-sidebar-collapsed #sidebar nav {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            body.mhs-sidebar-collapsed #sidebar nav a {
                justify-content: center !important;
                width: 3rem !important; /* w-12 (48px) */
                height: 3rem !important; /* h-12 (48px) */
                padding: 0 !important;
                border-radius: 1rem !important; /* rounded-2xl (16px) */
            }

            /* CSS Tooltip for Collapsed Sidebar */
            body.mhs-sidebar-collapsed #sidebar nav a {
                position: relative;
            }
            body.mhs-sidebar-collapsed #sidebar nav a::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%) translateX(10px);
                background: #1f2937;
                color: #fff;
                padding: 0.375rem 0.625rem;
                border-radius: 0.375rem;
                font-size: 0.75rem;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.2s ease-in-out;
                z-index: 50;
                pointer-events: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            body.mhs-sidebar-collapsed #sidebar nav a:hover::after {
                opacity: 1;
                visibility: visible;
                transform: translateY(-50%) translateX(15px);
            }
        }

        #mhs-main-content .mhs-data-table {
            width: 100%;
            min-width: 0;
            table-layout: auto;
        }

        #mhs-main-content [data-table-scroll-wrapper='true'] {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 640px) {
            #mhs-main-content {
                padding: 0.75rem;
            }

            #mhs-main-content h1 {
                font-size: 1.35rem;
                line-height: 1.25;
            }

            #mhs-main-content .mhs-responsive-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                gap: 0.75rem;
            }

            #mhs-main-content .mhs-responsive-toolbar > * {
                margin-left: 0 !important;
                min-width: 0;
                width: 100%;
                max-width: none !important;
                flex: 1 1 100%;
            }

            #mhs-main-content .mhs-data-table th,
            #mhs-main-content .mhs-data-table td {
                padding: 0.625rem 0.75rem !important;
            }

            #mhs-main-content .mhs-data-table {
                width: max-content;
                min-width: max(100%, 560px);
            }

            #mhs-main-content .mhs-data-table th {
                font-size: 0.7rem;
                letter-spacing: 0.04em;
            }

            #mhs-main-content .mhs-data-table td {
                font-size: 0.75rem;
            }
        }

        @media (min-width: 641px) and (max-width: 1023px) {
            #mhs-main-content .mhs-data-table {
                width: max-content;
                min-width: max(100%, 640px);
            }

            #mhs-main-content .mhs-data-table th,
            #mhs-main-content .mhs-data-table td {
                padding: 0.7rem 0.9rem !important;
            }

            #mhs-main-content .mhs-data-table th {
                font-size: 0.75rem;
                letter-spacing: 0.03em;
            }

            #mhs-main-content .mhs-data-table td {
                font-size: 0.8125rem;
            }
        }

        #mhs-main-content #chartContainer,
        #mhs-main-content .mhs-chart-container,
        #mhs-main-content .chart-container {
            height: clamp(180px, 30vw, 280px);
        }

        .mhs-cs-widget {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 60;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .mhs-cs-panel {
            width: min(22rem, calc(100vw - 2rem));
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        }

        .mhs-cs-launcher {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .mhs-cs-dismiss {
            position: absolute;
            top: -0.55rem;
            left: -0.55rem;
            z-index: 80;
            display: inline-flex;
            height: 2rem;
            width: 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 1px solid rgba(148, 163, 184, 0.42);
            background: rgba(255, 255, 255, 0.98);
            color: #334155;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
            transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        }

        .mhs-cs-dismiss:hover {
            transform: scale(1.05);
            background: #ffffff;
            color: #0f172a;
        }

        .dark .mhs-cs-dismiss {
            border-color: rgba(100, 116, 139, 0.5);
            background: rgba(31, 41, 55, 0.96);
            color: #cbd5e1;
        }

        .dark .mhs-cs-dismiss:hover {
            background: rgba(17, 24, 39, 0.98);
            color: #f8fafc;
        }

        @media (max-width: 640px) {
            .mhs-cs-widget {
                right: 0.75rem;
                bottom: 0.75rem;
            }
        }

        /* Ensure SweetAlert confirm button stays visible without hover */
        .swal2-popup .swal2-actions .swal2-confirm {
            background-color: #3b82f6 !important;
            border: 1px solid #3b82f6 !important;
            color: #ffffff !important;
        }

        .swal2-popup .swal2-actions .swal2-confirm:hover,
        .swal2-popup .swal2-actions .swal2-confirm:active {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }

    </style>
</head>
<body class="bg-gray-50 dark:bg-[#111827]">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 md:hidden hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <x-dashboard.sidebar :active="$active ?? 'home'" />

        {{-- Main Content --}}
        <div id="mhs-layout-main" class="flex-1 flex flex-col md:ml-20 lg:ml-64">
            {{-- Header --}}
            <x-dashboard.header />

            {{-- Page Content --}}
            <main id="mhs-main-content" class="flex-1 p-4 sm:p-6">
                <x-sweetalert />

                {{ $slot }}
            </main>
        </div>
    </div>

    @unless(request()->routeIs('mahasiswa.chat'))
    <div class="mhs-cs-widget" id="mhs-cs-widget">
        <div id="mhs-cs-panel" class="mhs-cs-panel hidden mb-3 bg-white dark:bg-[#1f2937] p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Live Chat CS</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tim support siap bantu 24/7.</p>
                </div>
                <button type="button" onclick="toggleMahasiswaCsWidget(false)" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-3 rounded-xl bg-blue-50 px-3 py-2 text-xs text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    QA & Support
                </span>
                <p class="mt-1">Pertanyaan akan dicek ke FAQ terlebih dahulu. Jika belum cocok, tiket akan diteruskan ke admin.</p>
            </div>
            <div id="mhs-cs-messages" class="mt-3 space-y-3 rounded-2xl bg-gray-50 p-3 dark:bg-gray-900/50">
                <div class="max-w-[85%] rounded-2xl rounded-bl-md bg-white px-3 py-2 text-xs text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                    Halo, tulis pertanyaan Anda terkait pembayaran, kelas, tugas, atau kendala dashboard.
                </div>
            </div>
            <form class="mt-3 flex items-end gap-2" onsubmit="submitMahasiswaCsMessage(event)">
                <div class="flex-1">
                    <label for="mhs-cs-input" class="sr-only">Pesan ke CS</label>
                    <textarea id="mhs-cs-input" rows="2" placeholder="Tulis pesan untuk tim support..." class="w-full resize-none rounded-2xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-blue-500 px-4 text-sm font-semibold text-white transition hover:bg-blue-600">
                    Kirim
                </button>
            </form>
            <a href="{{ route('mahasiswa.support') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                Buka Halaman Support
            </a>
        </div>

        <div class="mhs-cs-launcher">
            <button id="mhs-cs-dismiss" type="button" class="mhs-cs-dismiss" aria-label="Tutup Chat CS" title="Tutup Chat CS">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <button id="mhs-cs-trigger" type="button" onclick="toggleMahasiswaCsWidget()" class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Chat CS
            </button>
        </div>
    </div>
    @endunless

    @vite('resources/js/app.js')
    
    <script>
        const MHS_DESKTOP_SIDEBAR_KEY = 'mhs-desktop-sidebar-state';

        function syncMahasiswaDesktopSidebar() {
            const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
            const isCollapsed = localStorage.getItem(MHS_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            document.body.classList.toggle('mhs-sidebar-collapsed', isDesktop && isCollapsed);

            document.querySelectorAll('[data-sidebar-desktop-toggle]').forEach((button) => {
                const collapseIcon = button.querySelector('[data-sidebar-toggle-collapse]');
                const expandIcon = button.querySelector('[data-sidebar-toggle-expand]');
                const buttonLabel = isCollapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar';

                button.setAttribute('title', buttonLabel);
                button.setAttribute('aria-label', buttonLabel);
                button.setAttribute('aria-pressed', isCollapsed ? 'true' : 'false');

                if (collapseIcon) collapseIcon.classList.toggle('hidden', isCollapsed);
                if (expandIcon) expandIcon.classList.toggle('hidden', !isCollapsed);
            });
        }

        function toggleDesktopSidebar() {
            const isCollapsed = localStorage.getItem(MHS_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            localStorage.setItem(MHS_DESKTOP_SIDEBAR_KEY, isCollapsed ? 'expanded' : 'collapsed');
            syncMahasiswaDesktopSidebar();
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
            }
        }

        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const lightIcon = document.getElementById('theme-light-icon');
            const darkIcon = document.getElementById('theme-dark-icon');
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            }
        }

        // Initialize theme on page load
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const lightIcon = document.getElementById('theme-light-icon');
            const darkIcon = document.getElementById('theme-dark-icon');
            
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                if (lightIcon) lightIcon.classList.remove('hidden');
                if (darkIcon) darkIcon.classList.add('hidden');
            }
        })();

        function showAppAlert(message, icon = 'info', title = 'Informasi', options = {}) {
            const text = typeof message === 'string' ? message : String(message ?? '');
            if (window.Swal && typeof window.Swal.fire === 'function') {
                const isToast = Boolean(options.toast);
                return window.Swal.fire({
                    icon,
                    title,
                    text,
                    toast: isToast,
                    position: isToast ? (options.position || 'top-end') : 'center',
                    timer: isToast ? (options.timer || 2600) : undefined,
                    timerProgressBar: isToast ? true : undefined,
                    showConfirmButton: isToast ? false : (options.showConfirmButton ?? true),
                    showCancelButton: options.showCancelButton ?? false,
                    showDenyButton: options.showDenyButton ?? false,
                    confirmButtonText: options.confirmButtonText || 'Oke',
                    cancelButtonText: options.cancelButtonText || 'Batal',
                    denyButtonText: options.denyButtonText || 'Tidak',
                    buttonsStyling: !isToast,
                    customClass: {
                        container: 'font-inter',
                        confirmButton: isToast ? '' : 'bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors'
                    }
                });
            }

            const nativeAlert = window.__nativeAlert || window.alert.bind(window);
            return nativeAlert(text);
        }

        (function patchNativeAlertToSweetAlert() {
            if (window.__alertPatchedToSweetAlert) return;
            window.__nativeAlert = window.__nativeAlert || window.alert.bind(window);
            window.alert = (message) => showAppAlert(message, 'warning', 'Perhatian', { toast: true });
            window.__alertPatchedToSweetAlert = true;
        })();

        // Notification toggle - placeholder for pages without notification view
        function toggleNotificationView() {
            const notificationView = document.getElementById('notification-view');
            if (notificationView) {
                // If notification view exists (dashboard), toggle it
                const dashboardContent = document.getElementById('dashboard-content');
                const calendarView = document.getElementById('calendar-view');
                
                if (calendarView) calendarView.classList.add('hidden');
                
                if (notificationView.classList.contains('hidden')) {
                    if (dashboardContent) dashboardContent.classList.add('hidden');
                    notificationView.classList.remove('hidden');
                } else {
                    if (dashboardContent) dashboardContent.classList.remove('hidden');
                    notificationView.classList.add('hidden');
                }
            } else {
                // If no notification view, redirect to dashboard with notification open
                window.location.href = '{{ route("mahasiswa.dashboard") }}?view=notifications';
            }
        }

        function ensureResponsiveMahasiswaTables() {
            const tables = document.querySelectorAll('#mhs-main-content table');
            const isSmallScreen = window.matchMedia('(max-width: 640px)').matches;
            const isTabletScreen = window.matchMedia('(min-width: 641px) and (max-width: 1023px)').matches;

            const getAutoMinWidth = () => {
                if (isSmallScreen) return '560px';
                if (isTabletScreen) return '640px';
                return '';
            };

            tables.forEach((table) => {
                table.classList.add('mhs-data-table', 'responsive-data-table');

                if (table.closest('.overflow-x-auto')) {
                    const wrapper = table.closest('.overflow-x-auto');
                    if (wrapper) {
                        wrapper.classList.add('responsive-table');
                    }

                    const autoMinWidth = getAutoMinWidth();
                    if (autoMinWidth && !table.className.includes('min-w-')) {
                        if (table.dataset.autoMinWidth === 'true' || !table.style.minWidth) {
                            table.style.minWidth = autoMinWidth;
                            table.dataset.autoMinWidth = 'true';
                        }
                    } else if (table.dataset.autoMinWidth === 'true') {
                        table.style.minWidth = '';
                        delete table.dataset.autoMinWidth;
                    }

                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'overflow-x-auto responsive-table';
                wrapper.setAttribute('data-table-scroll-wrapper', 'true');

                const parent = table.parentNode;
                if (!parent) {
                    return;
                }

                parent.insertBefore(wrapper, table);
                wrapper.appendChild(table);

                const autoMinWidth = getAutoMinWidth();
                if (autoMinWidth && !table.className.includes('min-w-')) {
                    table.style.minWidth = autoMinWidth;
                    table.dataset.autoMinWidth = 'true';
                }
            });
        }

        function initMahasiswaFileSizeGuards() {
            const fileInputs = document.querySelectorAll('#mhs-main-content input[type="file"][data-max-size-mb]');
            const isFileOversize = (inputEl) => {
                const maxSizeMb = Number(inputEl.dataset.maxSizeMb || 0);
                if (!maxSizeMb || !inputEl.files || inputEl.files.length === 0) return null;

                const maxBytes = maxSizeMb * 1024 * 1024;
                const oversizedFile = Array.from(inputEl.files).find((file) => file.size > maxBytes);
                if (!oversizedFile) return null;

                return { maxSizeMb, oversizedFile };
            };

            fileInputs.forEach((input) => {
                if (input.dataset.maxSizeBound === 'true') return;

                input.addEventListener('change', () => {
                    const invalid = isFileOversize(input);
                    if (invalid) {
                        alert(`File "${invalid.oversizedFile.name}" melebihi batas ${invalid.maxSizeMb}MB.`);
                        input.value = '';
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });

                input.dataset.maxSizeBound = 'true';
            });

            const forms = document.querySelectorAll('#mhs-main-content form');
            forms.forEach((form) => {
                if (form.dataset.sizeGuardSubmitBound === '1') return;
                form.dataset.sizeGuardSubmitBound = '1';

                form.addEventListener('submit', (event) => {
                    const formFileInputs = form.querySelectorAll('input[type="file"][data-max-size-mb]');
                    for (const input of formFileInputs) {
                        const invalid = isFileOversize(input);
                        if (!invalid) continue;

                        event.preventDefault();
                        alert(`File "${invalid.oversizedFile.name}" melebihi batas ${invalid.maxSizeMb}MB.`);
                        input.value = '';
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                        return;
                    }
                });
            });

            if (!document.body.dataset.mahasiswaFileGuardCaptureBound) {
                document.addEventListener('change', (event) => {
                    const input = event.target;
                    if (!(input instanceof HTMLInputElement)) return;
                    if (!input.matches('#mhs-main-content input[type="file"][data-max-size-mb]')) return;

                    const invalid = isFileOversize(input);
                    if (!invalid) return;

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    alert(`File "${invalid.oversizedFile.name}" melebihi batas ${invalid.maxSizeMb}MB.`);
                    input.value = '';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }, true);

                document.body.dataset.mahasiswaFileGuardCaptureBound = '1';
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                syncMahasiswaDesktopSidebar();
                ensureResponsiveMahasiswaTables();
                initMahasiswaFileSizeGuards();
            });
        } else {
            syncMahasiswaDesktopSidebar();
            ensureResponsiveMahasiswaTables();
            initMahasiswaFileSizeGuards();
        }

        window.addEventListener('resize', () => {
            syncMahasiswaDesktopSidebar();
            ensureResponsiveMahasiswaTables();
            initMahasiswaFileSizeGuards();
        });

        function toggleMahasiswaCsWidget(forceOpen = null) {
            const panel = document.getElementById('mhs-cs-panel');
            const widget = document.getElementById('mhs-cs-widget');
            if (!panel) return;
            if (widget && widget.classList.contains('hidden')) return;

            const shouldOpen = forceOpen === null ? panel.classList.contains('hidden') : forceOpen;
            if (shouldOpen) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }

        function dismissMahasiswaCsWidget(event) {
            event?.stopPropagation();
            const widget = document.getElementById('mhs-cs-widget');
            const panel = document.getElementById('mhs-cs-panel');

            if (panel) {
                panel.classList.add('hidden');
            }

            if (widget) {
                widget.style.display = 'none';
            }
        }

        document.getElementById('mhs-cs-dismiss')?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            dismissMahasiswaCsWidget(event);
        });

        function appendMahasiswaCsMessage(message, type = 'user') {
            const container = document.getElementById('mhs-cs-messages');
            if (!container) return;

            const bubble = document.createElement('div');
            bubble.className = type === 'user'
                ? 'ml-auto max-w-[85%] rounded-2xl rounded-br-md bg-blue-500 px-3 py-2 text-xs text-white shadow-sm'
                : 'max-w-[85%] rounded-2xl rounded-bl-md bg-white px-3 py-2 text-xs text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300';
            bubble.textContent = message;
            container.appendChild(bubble);
            container.scrollTop = container.scrollHeight;
        }

        function submitMahasiswaCsMessage(event) {
            event.preventDefault();

            const input = document.getElementById('mhs-cs-input');
            if (!input) return;

            const message = input.value.trim();
            if (!message) return;

            appendMahasiswaCsMessage(message, 'user');
            input.value = '';
            appendMahasiswaCsMessage('Sedang memeriksa FAQ...');

            fetch(@json(route('mahasiswa.support.ask', [], false)), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: (() => {
                    const formData = new FormData();
                    formData.append('subject', 'Pertanyaan dari widget support');
                    formData.append('question', message);
                    return formData;
                })(),
            })
                .then(async (response) => {
                    const payload = await response.json();
                    if (!response.ok || !payload.success) {
                        throw new Error(payload.message || 'Gagal menghubungi support.');
                    }

                    const container = document.getElementById('mhs-cs-messages');
                    if (container && container.lastElementChild) {
                        container.removeChild(container.lastElementChild);
                    }

                    if (payload.resolved) {
                        appendMahasiswaCsMessage('FAQ terkait ditemukan: ' + payload.data.answer);
                    } else {
                        appendMahasiswaCsMessage('Pertanyaan Anda belum ada di FAQ dan sudah diteruskan ke admin. Ticket ID: #' + payload.data.ticket_id);
                    }
                })
                .catch((error) => {
                    const container = document.getElementById('mhs-cs-messages');
                    if (container && container.lastElementChild) {
                        container.removeChild(container.lastElementChild);
                    }
                    appendMahasiswaCsMessage(error.message || 'Terjadi kesalahan saat mengirim pertanyaan.');
                });
        }

        document.addEventListener('click', (event) => {
            const widget = document.getElementById('mhs-cs-widget');
            const panel = document.getElementById('mhs-cs-panel');
            if (!widget || !panel) return;
            if (panel.classList.contains('hidden')) return;
            if (!widget.contains(event.target)) {
                panel.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
