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
            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
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
    </script>
    
    @stack('scripts')
</body>
</html>
