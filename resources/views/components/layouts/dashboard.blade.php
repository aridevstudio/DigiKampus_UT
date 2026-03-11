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
        }

        .mhs-cs-panel {
            width: min(22rem, calc(100vw - 2rem));
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        }

        @media (max-width: 640px) {
            .mhs-cs-widget {
                right: 0.75rem;
                bottom: 0.75rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#111827]">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <x-dashboard.sidebar :active="$active ?? 'home'" />

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col lg:ml-64">
            {{-- Header --}}
            <x-dashboard.header />

            {{-- Page Content --}}
            <main id="mhs-main-content" class="flex-1 p-4 sm:p-6">
                <x-sweetalert />

                {{ $slot }}
            </main>
        </div>
    </div>

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
                    CS Online
                </span>
                <p class="mt-1">Klik tombol di bawah untuk mulai chat dengan CS.</p>
            </div>
            <a href="{{ route('mahasiswa.chat') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-500 px-3 py-2.5 text-sm font-medium text-white transition hover:bg-blue-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Buka Live Chat
            </a>
        </div>

        <button id="mhs-cs-trigger" type="button" onclick="toggleMahasiswaCsWidget()" class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Chat CS
        </button>
    </div>

    @vite('resources/js/app.js')
    
    <script>
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
                    showConfirmButton: isToast ? false : true,
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
                ensureResponsiveMahasiswaTables();
                initMahasiswaFileSizeGuards();
            });
        } else {
            ensureResponsiveMahasiswaTables();
            initMahasiswaFileSizeGuards();
        }

        window.addEventListener('resize', () => {
            ensureResponsiveMahasiswaTables();
            initMahasiswaFileSizeGuards();
        });

        function toggleMahasiswaCsWidget(forceOpen = null) {
            const panel = document.getElementById('mhs-cs-panel');
            if (!panel) return;

            const shouldOpen = forceOpen === null ? panel.classList.contains('hidden') : forceOpen;
            if (shouldOpen) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
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
