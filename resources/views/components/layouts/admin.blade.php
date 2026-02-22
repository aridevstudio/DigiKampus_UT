<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Admin' }} - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .dark body { background: #111827; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#111827]">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

        {{-- Admin Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="h-16 flex items-center px-4 border-b border-gray-200 dark:border-gray-700">
                <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}" alt="SALUT" class="h-10 object-contain">
            </div>
            
            {{-- User Info --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                @php
                    $adminUser = Auth::guard('admin')->user();
                    $adminPhoto = $adminUser?->profile?->foto_profile;
                    $adminName = $adminUser->name ?? 'Admin';
                @endphp
                <div class="flex items-center gap-3">
                    @if($adminPhoto)
                        <img src="{{ asset('storage/' . $adminPhoto) }}" alt="{{ $adminName }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ substr($adminName, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $adminName }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
            
            {{-- Nav Links --}}
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'dashboard' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.dosen') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'dosen' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-medium">Kelola Dosen</span>
                </a>
                
                <a href="{{ route('admin.mahasiswa') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'mahasiswa' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-medium">Kelola Mahasiswa</span>
                </a>
                
                <a href="{{ route('admin.kursus') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'kursus' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="font-medium">Kelola Kursus</span>
                </a>
                
                <a href="{{ route('admin.pengumuman') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'pengumuman' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span class="font-medium">Pengumuman</span>
                </a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col lg:ml-64">
            {{-- Header --}}
            <header class="sticky top-0 z-30 bg-white dark:bg-[#1f2937] border-b border-gray-100 dark:border-gray-700/50 px-4 sm:px-6 py-3">
                <div class="flex items-center justify-between">
                    {{-- Left Side: Hamburger Menu (mobile) --}}
                    <div class="flex items-center gap-3">
                        {{-- Hamburger Menu Button --}}
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                        
                        {{-- Mobile Logo --}}
                        <img 
                            src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}" 
                            alt="SALUT Logo" 
                            class="h-8 object-contain lg:hidden"
                        >
                    </div>

                    {{-- Right Side --}}
                    <div class="flex items-center gap-2 sm:gap-4">
                        {{-- Online Status --}}
                        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <span class="w-2 h-2 bg-green-500 animate-pulse rounded-full"></span>
                            <span>Online</span>
                        </div>

                        {{-- Action Icons --}}
                        <div class="flex items-center gap-1 sm:gap-2">
                            {{-- Notifications --}}
                            <div class="relative" id="notifContainer">
                                <button onclick="toggleNotifications()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    <span id="notifBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center px-1 hidden">0</span>
                                </button>

                                {{-- Notification Dropdown --}}
                                <div id="notifDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-[#1f2937] rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/50 hidden z-50">
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" id="notifSubtitle">Memuat...</p>
                                        </div>
                                        <button onclick="markAllRead()" class="text-xs text-blue-500 hover:text-blue-600 font-medium" id="markAllBtn" style="display:none;">Tandai Semua</button>
                                    </div>
                                    <div id="notifList" class="max-h-72 overflow-y-auto">
                                        <div class="px-4 py-6 text-center text-sm text-gray-400">
                                            <svg class="w-6 h-6 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            Memuat...
                                        </div>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700/50 text-center">
                                        <a href="{{ route('admin.dashboard') }}" class="text-xs text-blue-500 hover:text-blue-600 font-medium">Lihat Dashboard</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Dark/Light Mode Toggle --}}
                            <button id="theme-toggle" onclick="toggleTheme()" class="hidden md:flex p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                                <svg id="theme-light-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <svg id="theme-dark-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                            </button>
                        </div>

                        {{-- User Avatar --}}
                        @php
                            $navAdminUser = Auth::guard('admin')->user();
                            $navAdminName = $navAdminUser->name ?? 'Admin';
                            $navAdminPhoto = $navAdminUser?->profile?->foto_profile;
                        @endphp
                        <div class="relative group">
                            @if($navAdminPhoto)
                            <button class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden">
                                <img src="{{ asset('storage/' . $navAdminPhoto) }}" alt="{{ $navAdminName }}" class="w-full h-full object-cover">
                            </button>
                            @else
                            <button class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs sm:text-sm">
                                {{ strtoupper(substr($navAdminName, 0, 2)) }}
                            </button>
                            @endif
                            
                            {{-- Dropdown Menu --}}
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-[#1f2937] rounded-lg shadow-lg border border-gray-100 dark:border-gray-700/50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="py-2">
                                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">Profile</a>
                                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">Settings</a>
                                    <hr class="my-1 border-gray-100 dark:border-gray-700/50">
                                    <form action="{{ route('admin.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6">
                {{-- Global Flash Messages --}}
                @if($errors->any())
                <div id="globalValidationAlert" class="fixed top-4 right-4 z-[60] max-w-md bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <div class="flex-1">
                            <p class="font-semibold text-sm mb-1">Data gagal disimpan:</p>
                            <ul class="text-xs space-y-0.5 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.closest('#globalValidationAlert').remove()" class="ml-2 shrink-0">&times;</button>
                    </div>
                </div>
                <script>setTimeout(() => document.getElementById('globalValidationAlert')?.remove(), 8000);</script>
                @endif

                @if(session('success'))
                <div id="globalSuccessAlert" class="fixed top-4 right-4 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
                </div>
                <script>setTimeout(() => document.getElementById('globalSuccessAlert')?.remove(), 5000);</script>
                @endif

                @if(session('error'))
                <div id="globalErrorAlert" class="fixed top-4 right-4 z-[60] bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
                </div>
                <script>setTimeout(() => document.getElementById('globalErrorAlert')?.remove(), 5000);</script>
                @endif

                @if(session('info'))
                <div id="globalInfoAlert" class="fixed top-4 right-4 z-[60] bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('info') }}
                    <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
                </div>
                <script>setTimeout(() => document.getElementById('globalInfoAlert')?.remove(), 5000);</script>
                @endif

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
                if (lightIcon) lightIcon.classList.add('hidden');
                if (darkIcon) darkIcon.classList.remove('hidden');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                if (lightIcon) lightIcon.classList.remove('hidden');
                if (darkIcon) darkIcon.classList.add('hidden');
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
            } else if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                if (lightIcon) lightIcon.classList.add('hidden');
                if (darkIcon) darkIcon.classList.remove('hidden');
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
                if (lightIcon) lightIcon.classList.remove('hidden');
                if (darkIcon) darkIcon.classList.add('hidden');
            }
        })();
        // Notifications
        let notifLoaded = false;

        function toggleNotifications() {
            const dropdown = document.getElementById('notifDropdown');
            const isHidden = dropdown.classList.contains('hidden');
            
            if (isHidden) {
                dropdown.classList.remove('hidden');
                if (!notifLoaded) {
                    loadNotifications();
                }
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function updateBadge(count) {
            const badge = document.getElementById('notifBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function loadNotifications() {
            fetch('/admin/notifications')
                .then(r => r.json())
                .then(data => {
                    notifLoaded = true;
                    const list = document.getElementById('notifList');
                    const subtitle = document.getElementById('notifSubtitle');
                    const markAllBtn = document.getElementById('markAllBtn');

                    updateBadge(data.count);
                    subtitle.textContent = data.count > 0 ? data.count + ' belum dibaca' : 'Semua sudah dibaca';
                    markAllBtn.style.display = data.count > 0 ? 'inline' : 'none';
                    
                    if (data.items.length === 0) {
                        list.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada notifikasi</div>';
                        return;
                    }
                    
                    let html = '';
                    data.items.forEach(item => {
                        const iconMap = {
                            'student': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                            'teacher': '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                            'youtube': '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>',
                            'import': '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>',
                            'success': '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                            'warning': '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
                            'info': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        };
                        const iconSvg = iconMap[item.icon] || iconMap['info'];
                        const unreadBg = item.is_read ? '' : 'bg-blue-50/50 dark:bg-blue-500/5';
                        const unreadDot = item.is_read ? '' : '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>';
                        const clickAction = item.link ? `window.location.href='${item.link}'` : (item.is_read ? '' : `markRead(${item.id})`);
                        const cursor = (item.link || !item.is_read) ? 'cursor-pointer' : '';

                        html += `<div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-start gap-3 transition ${unreadBg} ${cursor}" ${clickAction ? `onclick="${clickAction}"` : ''} data-notif-id="${item.id}">`;
                        html += '<div class="mt-0.5 flex-shrink-0">' + iconSvg + '</div>';
                        html += '<div class="flex-1 min-w-0"><p class="text-sm text-gray-700 dark:text-gray-300 truncate font-medium">' + item.message + '</p>';
                        if (item.detail) html += '<p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">' + item.detail + '</p>';
                        html += '<p class="text-xs text-gray-400 mt-0.5">' + item.time + '</p></div>' + unreadDot + '</div>';
                    });
                    list.innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('notifList').innerHTML = '<div class="px-4 py-6 text-center text-sm text-red-400">Gagal memuat notifikasi</div>';
                });
        }

        function markRead(id) {
            fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                const el = document.querySelector(`[data-notif-id="${id}"]`);
                if (el) {
                    el.classList.remove('bg-blue-50/50', 'dark:bg-blue-500/5');
                    const dot = el.querySelector('.w-2.h-2.bg-blue-500');
                    if (dot) dot.remove();
                }
                // Update count
                fetch('/admin/notifications/count').then(r => r.json()).then(data => {
                    updateBadge(data.count);
                    document.getElementById('notifSubtitle').textContent = data.count > 0 ? data.count + ' belum dibaca' : 'Semua sudah dibaca';
                    document.getElementById('markAllBtn').style.display = data.count > 0 ? 'inline' : 'none';
                });
            });
        }

        function markAllRead() {
            fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                updateBadge(0);
                document.getElementById('notifSubtitle').textContent = 'Semua sudah dibaca';
                document.getElementById('markAllBtn').style.display = 'none';
                // Reload list
                notifLoaded = false;
                loadNotifications();
            });
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notifContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('notifDropdown').classList.add('hidden');
            }
        });

        // Auto-check for unread count on page load
        fetch('/admin/notifications/count').then(r => r.json()).then(data => {
            updateBadge(data.count);
        }).catch(() => {});
    </script>
    
    @stack('scripts')
</body>
</html>
