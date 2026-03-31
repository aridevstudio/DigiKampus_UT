<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Dosen' }} - SALUT</title>
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

        #dosen-main-content {
            overflow-x: auto;
        }

        #dosen-layout-main {
            min-width: 0;
            transition: margin-left 0.3s ease;
        }

        #sidebar {
            transition: transform 0.3s ease, width 0.3s ease;
        }

        @media (min-width: 1024px) {
            body.dosen-sidebar-collapsed #sidebar {
                width: 5.25rem;
            }

            body.dosen-sidebar-collapsed #dosen-layout-main {
                margin-left: 5.25rem;
            }

            body.dosen-sidebar-collapsed #sidebar .sidebar-logo-wrap {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            body.dosen-sidebar-collapsed #sidebar .sidebar-logo-image {
                height: 2rem;
            }

            body.dosen-sidebar-collapsed #sidebar .sidebar-user-wrap {
                justify-content: center;
            }

            body.dosen-sidebar-collapsed #sidebar .sidebar-user-meta,
            body.dosen-sidebar-collapsed #sidebar nav a > span:last-child {
                width: 0;
                opacity: 0;
                overflow: hidden;
                pointer-events: none;
            }

            body.dosen-sidebar-collapsed #sidebar nav {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            body.dosen-sidebar-collapsed #sidebar nav a {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        #dosen-main-content .dosen-data-table {
            width: 100%;
            min-width: 0;
            table-layout: auto;
        }

        #dosen-main-content [data-table-scroll-wrapper='true'] {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 1024px) {
            #dosen-main-content .dosen-responsive-toolbar {
                row-gap: 0.75rem;
            }
            #dosen-main-content .dosen-responsive-toolbar > .w-px {
                display: none;
            }
        }

        @media (max-width: 640px) {
            #dosen-main-content {
                padding: 0.75rem;
            }

            #dosen-main-content h1 {
                font-size: 1.35rem;
                line-height: 1.25;
            }

            #dosen-main-content .dosen-responsive-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                gap: 0.75rem;
            }

            #dosen-main-content .dosen-responsive-toolbar > * {
                margin-left: 0 !important;
                min-width: 0;
                width: 100%;
                max-width: none !important;
                flex: 1 1 100%;
            }

            #dosen-main-content .dosen-responsive-toolbar > .w-px {
                display: none;
            }

            #dosen-main-content .dosen-data-table th,
            #dosen-main-content .dosen-data-table td {
                padding: 0.625rem 0.75rem !important;
            }

            #dosen-main-content .dosen-data-table {
                width: max-content;
                min-width: max(100%, 560px);
            }

            #dosen-main-content .dosen-data-table th {
                font-size: 0.7rem;
                letter-spacing: 0.04em;
            }

            #dosen-main-content .dosen-data-table td {
                font-size: 0.75rem;
            }

            #dosen-main-content .dosen-responsive-modal-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.625rem;
            }

            #dosen-main-content .dosen-responsive-modal-actions > * {
                width: 100%;
            }

            #dosen-main-content .dosen-responsive-pagination {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }

        @media (min-width: 641px) and (max-width: 1023px) {
            #dosen-main-content .dosen-data-table {
                width: max-content;
                min-width: max(100%, 640px);
            }

            #dosen-main-content .dosen-data-table th,
            #dosen-main-content .dosen-data-table td {
                padding: 0.7rem 0.9rem !important;
            }

            #dosen-main-content .dosen-data-table th {
                font-size: 0.75rem;
                letter-spacing: 0.03em;
            }

            #dosen-main-content .dosen-data-table td {
                font-size: 0.8125rem;
            }
        }

        .dosen-cs-widget {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 60;
        }

        .dosen-cs-panel {
            width: min(22rem, calc(100vw - 2rem));
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        }

        @media (max-width: 640px) {
            .dosen-cs-widget {
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

        {{-- Dosen Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 flex w-64 flex-col overflow-hidden bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="sidebar-logo-wrap h-16 flex items-center px-4 border-b border-gray-200 dark:border-gray-700 transition-all duration-300">
                <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}?v={{ @filemtime(public_path('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png')) }}" alt="SALUT" class="sidebar-logo-image h-10 object-contain transition-all duration-300">
            </div>
            
            {{-- User Info --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="sidebar-user-wrap flex items-center gap-3 transition-all duration-300">
                    @php
                        $dosenUser = Auth::guard('dosen')->user();
                        $dosenPhoto = $dosenUser?->profile?->foto_profile;
                    @endphp
                    @if($dosenPhoto)
                        <img src="{{ asset('storage/' . $dosenPhoto) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::guard('dosen')->user()->name ?? 'D', 0, 1) }}
                        </div>
                    @endif
                    <div class="sidebar-user-meta transition-all duration-300">
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ Auth::guard('dosen')->user()->name ?? 'Dosen' }}</p>
                        <p class="text-xs text-gray-500">Dosen</p>
                    </div>
                </div>
            </div>
            
            {{-- Nav Links --}}
            <nav class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 space-y-2">
                <a href="{{ route('dosen.dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'dashboard' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('dosen.kursus') }}" title="Kursus Saya" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'kursus-saya' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="font-medium">Kursus Saya</span>
                </a>

                <a href="{{ route('dosen.bootcamp') }}" title="Bootcamp Saya" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'bootcamp' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8V6a4 4 0 10-8 0v2m-3 3h14l-1 8a2 2 0 01-2 2H8a2 2 0 01-2-2l-1-8zm5 4h4" />
                    </svg>
                    <span class="font-medium">Bootcamp Saya</span>
                </a>
                
                <a href="{{ route('dosen.progres') }}" title="Progres Mahasiswa" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'progres' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="font-medium">Progres Mahasiswa</span>
                </a>

                <a href="{{ route('dosen.nilai') }}" title="Mengelola Nilai" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'nilai' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-3m4 6H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">Mengelola Nilai</span>
                </a>
                
                <a href="{{ route('dosen.pesan') }}" title="Pesan" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'pesan' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span class="font-medium">Pesan</span>
                </a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div id="dosen-layout-main" class="flex-1 flex flex-col lg:ml-64">
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

                        <button type="button" onclick="toggleDesktopSidebar()" data-sidebar-desktop-toggle class="hidden lg:inline-flex p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition" title="Ciutkan sidebar">
                            <svg data-sidebar-toggle-collapse class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            <svg data-sidebar-toggle-expand class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        
                        {{-- Mobile Logo --}}
                        <img 
                            src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}?v={{ @filemtime(public_path('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png')) }}" 
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
                            <div class="relative" id="dosenNotifContainer">
                                <button onclick="toggleDosenNotifications()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    <span id="dosenNotifBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center px-1 hidden">0</span>
                                </button>

                                {{-- Notification Dropdown --}}
                                <div id="dosenNotifDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-[#1f2937] rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/50 hidden z-50">
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" id="dosenNotifSubtitle">Memuat...</p>
                                        </div>
                                        <button onclick="dosenMarkAllRead()" class="text-xs text-blue-500 hover:text-blue-600 font-medium" id="dosenMarkAllBtn" style="display:none;">Tandai Semua</button>
                                    </div>
                                    <div id="dosenNotifList" class="max-h-72 overflow-y-auto">
                                        <div class="px-4 py-6 text-center text-sm text-gray-400">
                                            <svg class="w-6 h-6 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            Memuat...
                                        </div>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700/50 text-center">
                                        <a href="{{ route('dosen.dashboard') }}" class="text-xs text-blue-500 hover:text-blue-600 font-medium">Lihat Dashboard</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Messages --}}
                            <a href="{{ route('dosen.pesan') }}" class="hidden sm:flex p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition relative">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                </svg>
                                <span id="dosenMsgBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center px-1 hidden">0</span>
                            </a>

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
                            $navDosenUser = Auth::guard('dosen')->user();
                            $navDosenName = $navDosenUser->name ?? 'Dosen';
                            $navDosenPhoto = $navDosenUser?->profile?->foto_profile;
                        @endphp
                        <div class="relative group">
                            @if($navDosenPhoto)
                            <button class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden">
                                <img src="{{ asset('storage/' . $navDosenPhoto) }}" alt="{{ $navDosenName }}" class="w-full h-full object-cover">
                            </button>
                            @else
                            <button class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs sm:text-sm">
                                {{ strtoupper(substr($navDosenName, 0, 2)) }}
                            </button>
                            @endif
                            
                            {{-- Dropdown Menu --}}
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-[#1f2937] rounded-lg shadow-lg border border-gray-100 dark:border-gray-700/50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="py-2">
                                    <a href="{{ route('dosen.profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">Profile</a>
                                    <hr class="my-1 border-gray-100 dark:border-gray-700/50">
                                    <form action="{{ route('dosen.logout') }}" method="POST">
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
            <main id="dosen-main-content" class="flex-1 p-4 sm:p-6">
                <x-sweetalert />

                {{ $slot }}
            </main>
        </div>
    </div>

    <div class="dosen-cs-widget" id="dosen-cs-widget">
        <div id="dosen-cs-panel" class="dosen-cs-panel hidden mb-3 bg-white dark:bg-[#1f2937] p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Live Chat CS</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Butuh bantuan terkait dashboard dosen?</p>
                </div>
                <button type="button" onclick="toggleDosenCsWidget(false)" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700">
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
                <p class="mt-1">Support ini tetap berada di bubble popup dan tidak masuk ke chat dosen-mahasiswa.</p>
            </div>
            <div id="dosen-cs-messages" class="mt-3 space-y-3 rounded-2xl bg-gray-50 p-3 dark:bg-gray-900/50">
                <div class="max-w-[85%] rounded-2xl rounded-bl-md bg-white px-3 py-2 text-xs text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                    Halo dosen, jika ada kendala materi, jadwal, atau penilaian, silakan kirim lewat popup ini.
                </div>
            </div>
            <form class="mt-3 flex items-end gap-2" onsubmit="submitDosenCsMessage(event)">
                <div class="flex-1">
                    <label for="dosen-cs-input" class="sr-only">Pesan ke CS</label>
                    <textarea id="dosen-cs-input" rows="2" placeholder="Tulis pesan untuk support..." class="w-full resize-none rounded-2xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-blue-500 px-4 text-sm font-semibold text-white transition hover:bg-blue-600">
                    Kirim
                </button>
            </form>
        </div>

        <button id="dosen-cs-trigger" type="button" onclick="toggleDosenCsWidget()" class="inline-flex items-center gap-2 rounded-full bg-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            Chat CS
        </button>
    </div>

    @vite('resources/js/app.js')
    
    <script>
        const DOSEN_DESKTOP_SIDEBAR_KEY = 'dosen-desktop-sidebar-state';

        function syncDosenDesktopSidebar() {
            const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
            const isCollapsed = localStorage.getItem(DOSEN_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            document.body.classList.toggle('dosen-sidebar-collapsed', isDesktop && isCollapsed);

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
            const isCollapsed = localStorage.getItem(DOSEN_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            localStorage.setItem(DOSEN_DESKTOP_SIDEBAR_KEY, isCollapsed ? 'expanded' : 'collapsed');
            syncDosenDesktopSidebar();
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

        syncDosenDesktopSidebar();
        window.addEventListener('resize', syncDosenDesktopSidebar);

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

        function ensureResponsiveDosenTables() {
            const tables = document.querySelectorAll('#dosen-main-content table');
            const isSmallScreen = window.matchMedia('(max-width: 640px)').matches;
            const isTabletScreen = window.matchMedia('(min-width: 641px) and (max-width: 1023px)').matches;

            const getAutoMinWidth = () => {
                if (isSmallScreen) return '560px';
                if (isTabletScreen) return '640px';
                return '';
            };

            tables.forEach((table) => {
                table.classList.add('dosen-data-table', 'responsive-data-table');

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

        function initDosenFileSizeGuards() {
            const fileInputs = document.querySelectorAll('#dosen-main-content input[type="file"][data-max-size-mb]');
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

            const forms = document.querySelectorAll('#dosen-main-content form');
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

            if (!document.body.dataset.dosenFileGuardCaptureBound) {
                document.addEventListener('change', (event) => {
                    const input = event.target;
                    if (!(input instanceof HTMLInputElement)) return;
                    if (!input.matches('#dosen-main-content input[type="file"][data-max-size-mb]')) return;

                    const invalid = isFileOversize(input);
                    if (!invalid) return;

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    alert(`File "${invalid.oversizedFile.name}" melebihi batas ${invalid.maxSizeMb}MB.`);
                    input.value = '';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }, true);

                document.body.dataset.dosenFileGuardCaptureBound = '1';
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                ensureResponsiveDosenTables();
                initDosenFileSizeGuards();
            });
        } else {
            ensureResponsiveDosenTables();
            initDosenFileSizeGuards();
        }

        window.addEventListener('resize', () => {
            ensureResponsiveDosenTables();
            initDosenFileSizeGuards();
        });
    </script>

    <script>
        // ==========================================
        // Dosen Notifications (bell icon dropdown)
        // ==========================================
        let dosenNotifLoaded = false;

        function toggleDosenNotifications() {
            const dropdown = document.getElementById('dosenNotifDropdown');
            const isHidden = dropdown.classList.contains('hidden');
            
            if (isHidden) {
                dropdown.classList.remove('hidden');
                if (!dosenNotifLoaded) {
                    loadDosenNotifications();
                }
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function updateDosenNotifBadge(count) {
            const badge = document.getElementById('dosenNotifBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function loadDosenNotifications() {
            fetch('/dosen/notifications')
                .then(r => r.json())
                .then(data => {
                    dosenNotifLoaded = true;
                    const list = document.getElementById('dosenNotifList');
                    const subtitle = document.getElementById('dosenNotifSubtitle');
                    const markAllBtn = document.getElementById('dosenMarkAllBtn');

                    updateDosenNotifBadge(data.count);
                    subtitle.textContent = data.count > 0 ? data.count + ' belum dibaca' : 'Semua sudah dibaca';
                    markAllBtn.style.display = data.count > 0 ? 'inline' : 'none';
                    
                    if (data.items.length === 0) {
                        list.innerHTML = '<div class="px-4 py-6 text-center text-sm text-gray-400"><svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>Tidak ada notifikasi</div>';
                        return;
                    }
                    
                    const iconMap = {
                        'pesan': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>',
                        'enrollment': '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>',
                        'kursus': '<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                        'tugas': '<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
                        'calendar': '<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                        'success': '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'warning': '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                        'info': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                    };

                    let html = '';
                    data.items.forEach(item => {
                        const iconSvg = iconMap[item.icon] || iconMap['info'];
                        const unreadBg = item.is_read ? '' : 'bg-blue-50/50 dark:bg-blue-500/5';
                        const unreadDot = item.is_read ? '' : '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>';
                        const canClick = Boolean(item.link) || !item.is_read;
                        const cursor = canClick ? 'cursor-pointer' : '';
                        const encodedLink = item.link ? encodeURIComponent(item.link) : '';

                        html += `<div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-start gap-3 transition ${unreadBg} ${cursor}" data-dosen-notif-id="${item.id}" data-dosen-notif-read="${item.is_read ? '1' : '0'}" data-dosen-notif-link="${encodedLink}">`;
                        html += '<div class="mt-0.5 flex-shrink-0">' + iconSvg + '</div>';
                        html += '<div class="flex-1 min-w-0"><p class="text-sm text-gray-700 dark:text-gray-300 truncate font-medium">' + item.message + '</p>';
                        if (item.detail) html += '<p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">' + item.detail + '</p>';
                        html += '<p class="text-xs text-gray-400 mt-0.5">' + item.time + '</p></div>' + unreadDot + '</div>';
                    });
                    list.innerHTML = html;

                    list.querySelectorAll('[data-dosen-notif-id]').forEach((notifEl) => {
                        notifEl.addEventListener('click', () => {
                            handleDosenNotificationClick(notifEl);
                        });
                    });
                })
                .catch(() => {
                    document.getElementById('dosenNotifList').innerHTML = '<div class="px-4 py-6 text-center text-sm text-red-400">Gagal memuat notifikasi</div>';
                });
        }

        function handleDosenNotificationClick(notifEl) {
            const id = notifEl.getAttribute('data-dosen-notif-id');
            const isRead = notifEl.getAttribute('data-dosen-notif-read') === '1';
            const encodedLink = notifEl.getAttribute('data-dosen-notif-link') || '';
            const link = encodedLink ? decodeURIComponent(encodedLink) : '';

            if (!id) return;

            if (!isRead) {
                dosenMarkRead(id, { skipCounterRefresh: Boolean(link) })
                    .finally(() => {
                        if (link) {
                            window.location.href = link;
                        }
                    });
                return;
            }

            if (link) {
                window.location.href = link;
            }
        }

        function dosenMarkRead(id, options = {}) {
            const { skipCounterRefresh = false } = options;

            return fetch(`/dosen/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                const el = document.querySelector(`[data-dosen-notif-id="${id}"]`);
                if (el) {
                    el.classList.remove('bg-blue-50/50', 'dark:bg-blue-500/5');
                    const dot = el.querySelector('.w-2.h-2.bg-blue-500');
                    if (dot) dot.remove();
                    el.setAttribute('data-dosen-notif-read', '1');
                }

                if (skipCounterRefresh) return;

                fetch('/dosen/notifications/count').then(r => r.json()).then(data => {
                    updateDosenNotifBadge(data.count);
                    document.getElementById('dosenNotifSubtitle').textContent = data.count > 0 ? data.count + ' belum dibaca' : 'Semua sudah dibaca';
                    document.getElementById('dosenMarkAllBtn').style.display = data.count > 0 ? 'inline' : 'none';
                });
            });
        }

        function dosenMarkAllRead() {
            fetch('/dosen/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                updateDosenNotifBadge(0);
                document.getElementById('dosenNotifSubtitle').textContent = 'Semua sudah dibaca';
                document.getElementById('dosenMarkAllBtn').style.display = 'none';
                dosenNotifLoaded = false;
                loadDosenNotifications();
            });
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('dosenNotifContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('dosenNotifDropdown').classList.add('hidden');
            }
        });

        // ==========================================
        // Unread counts on page load + polling
        // ==========================================
        function loadDosenHeaderCounts() {
            // Notification count
            fetch('/dosen/notifications/count').then(r => r.json()).then(data => {
                updateDosenNotifBadge(data.count);
            }).catch(() => {});

            // Message unread count
            fetch('/dosen/messages/unread-count').then(r => r.json()).then(data => {
                const badge = document.getElementById('dosenMsgBadge');
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }).catch(() => {});
        }

        // Load counts on page load
        loadDosenHeaderCounts();

        // Poll every 30 seconds for fresh counts
        setInterval(loadDosenHeaderCounts, 30000);

        function toggleDosenCsWidget(forceOpen = null) {
            const panel = document.getElementById('dosen-cs-panel');
            if (!panel) return;

            const shouldOpen = forceOpen === null ? panel.classList.contains('hidden') : forceOpen;
            if (shouldOpen) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }

        function appendDosenCsMessage(message, type = 'user') {
            const container = document.getElementById('dosen-cs-messages');
            if (!container) return;

            const bubble = document.createElement('div');
            bubble.className = type === 'user'
                ? 'ml-auto max-w-[85%] rounded-2xl rounded-br-md bg-blue-500 px-3 py-2 text-xs text-white shadow-sm'
                : 'max-w-[85%] rounded-2xl rounded-bl-md bg-white px-3 py-2 text-xs text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300';
            bubble.textContent = message;
            container.appendChild(bubble);
            container.scrollTop = container.scrollHeight;
        }

        function submitDosenCsMessage(event) {
            event.preventDefault();

            const input = document.getElementById('dosen-cs-input');
            if (!input) return;

            const message = input.value.trim();
            if (!message) return;

            appendDosenCsMessage(message, 'user');
            input.value = '';

            window.setTimeout(() => {
                appendDosenCsMessage('Pesan support diterima. Bubble ini tetap terpisah dari inbox mahasiswa dan saat ini masih frontend preview.');
            }, 550);
        }

        document.addEventListener('click', (event) => {
            const widget = document.getElementById('dosen-cs-widget');
            const panel = document.getElementById('dosen-cs-panel');
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

