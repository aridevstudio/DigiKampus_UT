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

        #admin-main-content {
            overflow-x: hidden;
            width: 100%;
        }

        #admin-layout-main {
            min-width: 0;
            transition: margin-left 0.3s ease;
        }

        #sidebar {
            transition: transform 0.3s ease, width 0.3s ease;
        }

        @media (min-width: 1024px) {
            body.admin-sidebar-collapsed #sidebar {
                width: 5.25rem;
            }

            body.admin-sidebar-collapsed #admin-layout-main {
                margin-left: 5.25rem;
            }

            body.admin-sidebar-collapsed #sidebar .sidebar-logo-wrap {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            body.admin-sidebar-collapsed #sidebar .sidebar-logo-image {
                height: 2rem;
            }

            body.admin-sidebar-collapsed #sidebar .sidebar-user-wrap {
                justify-content: center;
            }

            body.admin-sidebar-collapsed #sidebar .sidebar-user-meta,
            body.admin-sidebar-collapsed #sidebar nav a > span:last-child {
                width: 0;
                opacity: 0;
                overflow: hidden;
                pointer-events: none;
            }

            body.admin-sidebar-collapsed #sidebar nav {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            body.admin-sidebar-collapsed #sidebar nav a {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        #admin-main-content .admin-data-table {
            width: 100%;
            min-width: 0;
            table-layout: auto;
        }

        #admin-main-content [data-table-scroll-wrapper='true'] {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }


        @media (max-width: 639px) {
            #admin-main-content {
                padding: 0.75rem;
            }

            #admin-main-content h1 {
                font-size: 1.25rem;
                line-height: 1.25;
            }

            #admin-main-content h1 + p {
                font-size: 0.75rem;
            }

            #notifDropdown {
                width: min(20rem, calc(100vw - 1rem));
                right: 0;
            }

            #admin-main-content .admin-data-table th,
            #admin-main-content .admin-data-table td {
                padding: 0.625rem 0.75rem !important;
            }

            #admin-main-content .admin-data-table {
                width: max-content;
                min-width: max(100%, 560px);
            }

            #admin-main-content .admin-mobile-list {
                width: 100% !important;
                min-width: 100% !important;
                table-layout: fixed;
            }

            #admin-main-content .admin-data-table th {
                font-size: 0.7rem;
                letter-spacing: 0.04em;
            }

            #admin-main-content .admin-data-table td {
                font-size: 0.75rem;
            }

            #admin-main-content .admin-responsive-modal-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.625rem;
            }

            #admin-main-content .admin-responsive-modal-actions > * {
                width: 100%;
            }

            #admin-main-content [class*='fixed inset-0 z-50'] .relative.w-full {
                max-height: calc(100dvh - 1rem);
            }

            #admin-main-content .admin-responsive-pagination {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            #admin-main-content .admin-responsive-actions {
                display: flex;
                flex-wrap: wrap;
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            #admin-main-content .admin-data-table {
                width: max-content;
                min-width: max(100%, 640px);
            }

            #admin-main-content .admin-data-table th,
            #admin-main-content .admin-data-table td {
                padding: 0.7rem 0.9rem !important;
            }

            #admin-main-content .admin-data-table th {
                font-size: 0.75rem;
                letter-spacing: 0.03em;
            }

            #admin-main-content .admin-data-table td {
                font-size: 0.8125rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#111827]">
    <div class="flex min-h-screen">
        {{-- Mobile Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

        {{-- Admin Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 flex w-64 flex-col overflow-hidden bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="sidebar-logo-wrap h-16 flex items-center px-4 border-b border-gray-200 dark:border-gray-700 transition-all duration-300">
                <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}?v={{ @filemtime(public_path('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png')) }}" alt="SALUT" class="sidebar-logo-image h-10 object-contain transition-all duration-300">
            </div>
            
            {{-- User Info --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                @php
                    $adminUser = Auth::guard('admin')->user();
                    $adminPhoto = $adminUser?->profile?->foto_profile;
                    $adminName = $adminUser->name ?? 'Admin';
                @endphp
                <div class="sidebar-user-wrap flex items-center gap-3 transition-all duration-300">
                    @if($adminPhoto)
                        <img src="{{ asset('storage/' . $adminPhoto) }}" alt="{{ $adminName }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ substr($adminName, 0, 1) }}
                        </div>
                    @endif
                    <div class="sidebar-user-meta transition-all duration-300">
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $adminName }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
            
            {{-- Nav Links --}}
            <nav class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'dashboard' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.dosen') }}" title="Kelola Dosen" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'dosen' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-medium">Kelola Dosen</span>
                </a>
                
                <a href="{{ route('admin.mahasiswa') }}" title="Kelola Mahasiswa" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'mahasiswa' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-medium">Kelola Mahasiswa</span>
                </a>
                
                <a href="{{ route('admin.kursus') }}" title="Kelola Kursus" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'kursus' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="font-medium">Kelola Kursus</span>
                </a>

                <a href="{{ route('admin.bootcamp-tiket') }}" title="Bootcamp & Tiket" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'bootcamp' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8V6a4 4 0 10-8 0v2m-3 3h14l-1 8a2 2 0 01-2 2H8a2 2 0 01-2-2l-1-8zm5 4h4" />
                    </svg>
                    <span class="font-medium">Bootcamp & Tiket</span>
                </a>
                
                <a href="{{ route('admin.prodi') }}" title="Kelola Prodi" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'prodi' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="font-medium">Kelola Prodi</span>
                </a>

                <a href="{{ \Illuminate\Support\Facades\Route::has('admin.kategori') ? route('admin.kategori') : '#' }}" title="Kelola Kategori" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ (($active ?? '') == 'kategori' || request()->routeIs('admin.kategori')) ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10v10H7V7zm-4 4h4m10 0h4M11 3v4m0 10v4" />
                    </svg>
                    <span class="font-medium">Kelola Kategori</span>
                </a>

                <a href="{{ \Illuminate\Support\Facades\Route::has('admin.sertifikasi') ? route('admin.sertifikasi') : '#' }}" title="Sertifikasi Otomatis" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ (($active ?? '') == 'sertifikasi' || request()->routeIs('admin.sertifikasi')) ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9c0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="font-medium">Sertifikasi Otomatis</span>
                </a>
                
                <a href="{{ route('admin.pengumuman') }}" title="Pengumuman" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'pengumuman' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span class="font-medium">Pengumuman</span>
                </a>

                @php
                    $forumManagementActive = in_array(($active ?? ''), ['forum-kategori', 'forum-topik', 'forum-komentar'], true) || request()->routeIs('admin.forum-kategori*') || request()->routeIs('admin.forum-topik*') || request()->routeIs('admin.forum-komentar*');
                    $forumCategoryRoute = \Illuminate\Support\Facades\Route::has('admin.forum-kategori') ? route('admin.forum-kategori') : '#';
                @endphp
                <div x-data="{ open: {{ $forumManagementActive ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open" title="Forum Management" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg {{ $forumManagementActive ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v3l-4-3H9a2 2 0 01-2-2v-1m10-9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v3l4-3h2a2 2 0 002-2V7z" />
                        </svg>
                        <span class="font-medium flex-1 text-left">Forum Management</span>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-1 border-l border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ $forumCategoryRoute }}" title="Kategori Forum" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ ($active ?? '') == 'forum-kategori' || request()->routeIs('admin.forum-kategori*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10v10H7V7zM3 11h4m10 0h4" />
                            </svg>
                            <span>Kategori</span>
                        </a>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.forum-topik') ? route('admin.forum-topik') : '#' }}" title="Moderasi Topik" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ ($active ?? '') == 'forum-topik' || request()->routeIs('admin.forum-topik*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01" />
                            </svg>
                            <span>Topik</span>
                        </a>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.forum-komentar') ? route('admin.forum-komentar') : '#' }}" title="Moderasi Komentar" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition {{ ($active ?? '') == 'forum-komentar' || request()->routeIs('admin.forum-komentar*') ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Moderasi</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.support-tickets') }}" title="Tiket Support" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'support-tickets' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span class="font-medium">Tiket Support</span>
                </a>

                <a href="{{ route('admin.chat') }}" title="Manajemen Chat" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'chat' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.963 9.963 0 01-4.518-1.078L3 20l1.149-3.064A7.963 7.963 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="font-medium">Manajemen Chat</span>
                </a>

                <a href="{{ route('admin.voucher') }}" title="Voucher" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'voucher' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 010 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 010-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span class="font-medium">Voucher</span>
                </a>

                <a href="{{ route('admin.finance-report') }}" title="Finance Report" class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ ($active ?? '') == 'finance-report' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 13l3-3 3 2 4-5" />
                    </svg>
                    <span class="font-medium">Finance Report</span>
                </a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div id="admin-layout-main" class="flex-1 flex flex-col lg:ml-64">
            {{-- Header --}}
            <header class="sticky top-0 z-30 bg-white dark:bg-[#1f2937] border-b border-gray-100 dark:border-gray-700/50 px-3 sm:px-6 py-3">
                <div class="flex items-center justify-between gap-3">
                    {{-- Left Side: Hamburger Menu (mobile) --}}
                    <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
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
                            class="h-8 max-w-[132px] shrink-0 object-contain lg:hidden"
                        >
                        <div class="hidden min-w-0 leading-none sm:block lg:hidden">
                            <div class="truncate text-sm font-bold text-gray-800 dark:text-gray-100">DigiKampus</div>
                            <div class="mt-0.5 inline-flex rounded-full bg-blue-50 px-1.5 py-0.5 text-[9px] font-semibold leading-none text-blue-500 dark:bg-blue-500/10 dark:text-blue-300">UT</div>
                        </div>
                    </div>

                    {{-- Right Side --}}
                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-4">
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
                                    <span id="notifBadge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-green-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center px-1 hidden">0</span>
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
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
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
            <main id="admin-main-content" class="flex-1 p-4 sm:p-6">
                <x-sweetalert />

                {{ $slot }}
            </main>
        </div>
    </div>

    @vite('resources/js/app.js')
    
    <script>
        const ADMIN_DESKTOP_SIDEBAR_KEY = 'admin-desktop-sidebar-state';

        function syncAdminDesktopSidebar() {
            const isDesktop = window.matchMedia('(min-width: 1024px)').matches;
            const isCollapsed = localStorage.getItem(ADMIN_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            document.body.classList.toggle('admin-sidebar-collapsed', isDesktop && isCollapsed);

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
            const isCollapsed = localStorage.getItem(ADMIN_DESKTOP_SIDEBAR_KEY) === 'collapsed';
            localStorage.setItem(ADMIN_DESKTOP_SIDEBAR_KEY, isCollapsed ? 'expanded' : 'collapsed');
            syncAdminDesktopSidebar();
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

        function initAdminFileSizeGuards() {
            const fileInputs = document.querySelectorAll('#admin-main-content input[type="file"][data-max-size-mb]');

            const isFileOversize = (inputEl) => {
                const maxMb = Number(inputEl.dataset.maxSizeMb || 0);
                if (!maxMb || !inputEl.files || inputEl.files.length === 0) {
                    return null;
                }

                const maxBytes = maxMb * 1024 * 1024;
                const oversize = Array.from(inputEl.files).find((f) => f.size > maxBytes);
                if (!oversize) return null;

                return { maxMb, oversize };
            };

            fileInputs.forEach((input) => {
                if (input.dataset.sizeGuardBound === '1') return;
                input.dataset.sizeGuardBound = '1';

                input.addEventListener('change', function () {
                    const invalid = isFileOversize(this);
                    if (!invalid) return;

                    alert(`File terlalu besar. Maksimal ${invalid.maxMb}MB per file.`);
                    this.value = '';
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });

            const forms = document.querySelectorAll('#admin-main-content form');
            forms.forEach((form) => {
                if (form.dataset.sizeGuardSubmitBound === '1') return;
                form.dataset.sizeGuardSubmitBound = '1';

                form.addEventListener('submit', (event) => {
                    const formFileInputs = form.querySelectorAll('input[type="file"][data-max-size-mb]');
                    for (const input of formFileInputs) {
                        const invalid = isFileOversize(input);
                        if (!invalid) continue;

                        event.preventDefault();
                        alert(`File "${invalid.oversize.name}" terlalu besar. Maksimal ${invalid.maxMb}MB.`);
                        input.value = '';
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                        return;
                    }
                });
            });

            if (!document.body.dataset.adminFileGuardCaptureBound) {
                document.addEventListener('change', (event) => {
                    const input = event.target;
                    if (!(input instanceof HTMLInputElement)) return;
                    if (!input.matches('#admin-main-content input[type="file"][data-max-size-mb]')) return;

                    const invalid = isFileOversize(input);
                    if (!invalid) return;

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    alert(`File "${invalid.oversize.name}" terlalu besar. Maksimal ${invalid.maxMb}MB.`);
                    input.value = '';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }, true);

                document.body.dataset.adminFileGuardCaptureBound = '1';
            }
        }
        // Notifications
        const ADMIN_LOCAL_NOTIFICATION_KEY = 'admin-local-notifications';
        let notifLoaded = false;

        function readLocalNotifications() {
            try {
                const raw = localStorage.getItem(ADMIN_LOCAL_NOTIFICATION_KEY);
                const items = raw ? JSON.parse(raw) : [];
                return Array.isArray(items) ? items : [];
            } catch (error) {
                return [];
            }
        }

        function writeLocalNotifications(items) {
            try {
                localStorage.setItem(ADMIN_LOCAL_NOTIFICATION_KEY, JSON.stringify(items));
            } catch (error) {
                // Ignore storage failures and keep UI functional.
            }
        }

        function getLocalUnreadCount() {
            return readLocalNotifications().filter((item) => !item.is_read).length;
        }

        function getUnreadCount(items) {
            return items.filter((item) => !item.is_read).length;
        }

        function mergeNotifications(serverData = {}) {
            const serverItems = Array.isArray(serverData.items) ? serverData.items : [];
            const localItems = readLocalNotifications();
            const items = [...localItems, ...serverItems];

            return {
                items,
                count: getUnreadCount(items),
            };
        }

        function renderNotificationState(data) {
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
                    'support': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>',
                    'youtube': '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>',
                    'import': '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>',
                    'success': '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'warning': '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
                    'info': '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                };
                const iconSvg = iconMap[item.icon] || iconMap['info'];
                const unreadBg = item.is_read ? '' : 'bg-blue-50/50 dark:bg-blue-500/5';
                const unreadDot = item.is_read ? '' : '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>';
                const canClick = Boolean(item.link) || !item.is_read;
                const cursor = canClick ? 'cursor-pointer' : '';
                const encodedLink = item.link ? encodeURIComponent(item.link) : '';

                html += `<div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-start gap-3 transition ${unreadBg} ${cursor}" data-notif-id="${item.id}" data-notif-read="${item.is_read ? '1' : '0'}" data-notif-link="${encodedLink}">`;
                html += '<div class="mt-0.5 flex-shrink-0">' + iconSvg + '</div>';
                html += '<div class="flex-1 min-w-0"><p class="text-sm text-gray-700 dark:text-gray-300 truncate font-medium">' + item.message + '</p>';
                if (item.detail) html += '<p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">' + item.detail + '</p>';
                html += '<p class="text-xs text-gray-400 mt-0.5">' + item.time + '</p></div>' + unreadDot + '</div>';
            });
            list.innerHTML = html;

            list.querySelectorAll('[data-notif-id]').forEach((notifEl) => {
                notifEl.addEventListener('click', () => {
                    handleNotificationClick(notifEl);
                });
            });
        }

        function refreshNotificationCounter() {
            return fetch('/admin/notifications/count')
                .then(r => r.json())
                .then(data => {
                    const total = (data.count || 0) + getLocalUnreadCount();
                    updateBadge(total);
                    const subtitle = document.getElementById('notifSubtitle');
                    const markAllBtn = document.getElementById('markAllBtn');

                    if (subtitle) {
                        subtitle.textContent = total > 0 ? total + ' belum dibaca' : 'Semua sudah dibaca';
                    }

                    if (markAllBtn) {
                        markAllBtn.style.display = total > 0 ? 'inline' : 'none';
                    }
                })
                .catch(() => {
                    const total = getLocalUnreadCount();
                    updateBadge(total);
                });
        }

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
                    renderNotificationState(mergeNotifications(data));
                })
                .catch(() => {
                    notifLoaded = true;
                    const localOnly = mergeNotifications({ items: [] });
                    if (localOnly.items.length > 0) {
                        renderNotificationState(localOnly);
                        return;
                    }
                    document.getElementById('notifList').innerHTML = '<div class="px-4 py-6 text-center text-sm text-red-400">Gagal memuat notifikasi</div>';
                });
        }

        function handleNotificationClick(notifEl) {
            const id = notifEl.getAttribute('data-notif-id');
            const isRead = notifEl.getAttribute('data-notif-read') === '1';
            const encodedLink = notifEl.getAttribute('data-notif-link') || '';
            const link = encodedLink ? decodeURIComponent(encodedLink) : '';

            if (!id) return;

            if (!isRead) {
                markRead(id, { skipCounterRefresh: Boolean(link) })
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

        function markRead(id, options = {}) {
            const { skipCounterRefresh = false } = options;

            if (String(id).startsWith('local-')) {
                const nextItems = readLocalNotifications().map((item) => (
                    String(item.id) === String(id) ? { ...item, is_read: true } : item
                ));
                writeLocalNotifications(nextItems);

                const el = document.querySelector(`[data-notif-id="${id}"]`);
                if (el) {
                    el.classList.remove('bg-blue-50/50', 'dark:bg-blue-500/5');
                    const dot = el.querySelector('.w-2.h-2.bg-blue-500');
                    if (dot) dot.remove();
                    el.setAttribute('data-notif-read', '1');
                }

                if (skipCounterRefresh) {
                    return Promise.resolve();
                }

                return refreshNotificationCounter();
            }

            return fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                const el = document.querySelector(`[data-notif-id="${id}"]`);
                if (el) {
                    el.classList.remove('bg-blue-50/50', 'dark:bg-blue-500/5');
                    const dot = el.querySelector('.w-2.h-2.bg-blue-500');
                    if (dot) dot.remove();
                    el.setAttribute('data-notif-read', '1');
                }

                if (skipCounterRefresh) return;

                return refreshNotificationCounter();
            });
        }

        function markAllRead() {
            const localItems = readLocalNotifications().map((item) => ({ ...item, is_read: true }));
            writeLocalNotifications(localItems);

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
            }).catch(() => {
                updateBadge(0);
                document.getElementById('notifSubtitle').textContent = 'Semua sudah dibaca';
                document.getElementById('markAllBtn').style.display = 'none';
                notifLoaded = false;
                loadNotifications();
            });
        }

        window.pushAdminNotification = function pushAdminNotification(payload = {}) {
            const nextItems = readLocalNotifications();
            nextItems.unshift({
                id: `local-${Date.now()}`,
                icon: payload.icon || 'info',
                message: payload.message || 'Ada pembaruan baru.',
                detail: payload.detail || '',
                time: payload.time || 'Baru saja',
                link: payload.link || '',
                is_read: false,
            });
            writeLocalNotifications(nextItems.slice(0, 20));
            notifLoaded = false;
            refreshNotificationCounter();

            const dropdown = document.getElementById('notifDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        };

        refreshNotificationCounter();

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

        // Ensure all admin tables remain scrollable on small screens.
        function ensureResponsiveAdminTables() {
            const tables = document.querySelectorAll('#admin-main-content table');
            const isSmallScreen = window.matchMedia('(max-width: 639px)').matches;
            const isTabletScreen = window.matchMedia('(min-width: 640px) and (max-width: 1023px)').matches;

            const getAutoMinWidth = () => {
                if (isSmallScreen) return '560px';
                if (isTabletScreen) return '640px';
                return '';
            };

            tables.forEach((table) => {
                const isResponsiveListTable =
                    table.classList.contains('responsive-data-table') ||
                    table.classList.contains('admin-mobile-list') ||
                    table.closest('.responsive-table');

                if (!isResponsiveListTable) {
                    return;
                }

                table.classList.add('admin-data-table', 'responsive-data-table');

                // Build per-cell labels from headers so mobile card rows remain readable.
                const headerCells = Array.from(table.querySelectorAll('thead th'));
                if (headerCells.length > 0) {
                    const labels = headerCells.map((th) => (th.textContent || '').trim() || 'Kolom');
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach((row) => {
                        const cells = row.querySelectorAll('td');
                        cells.forEach((cell, index) => {
                            if (!cell.dataset.label) {
                                cell.dataset.label = labels[index] || 'Detail';
                            }
                        });
                    });
                }

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
                } else if (table.dataset.autoMinWidth === 'true') {
                    table.style.minWidth = '';
                    delete table.dataset.autoMinWidth;
                }
            });
        }

        function ensureAdminResponsiveToolbars() {
            const toolbars = document.querySelectorAll("#admin-main-content form[method='GET'], #admin-main-content form[method='get']");
            toolbars.forEach((form) => {
                form.classList.add('admin-toolbar-responsive');
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                syncAdminDesktopSidebar();
                ensureResponsiveAdminTables();
                ensureAdminResponsiveToolbars();
                initAdminFileSizeGuards();
            });
        } else {
            syncAdminDesktopSidebar();
            ensureResponsiveAdminTables();
            ensureAdminResponsiveToolbars();
            initAdminFileSizeGuards();
        }

        window.addEventListener('resize', () => {
            syncAdminDesktopSidebar();
            ensureResponsiveAdminTables();
            ensureAdminResponsiveToolbars();
            initAdminFileSizeGuards();
        });
    </script>
    
    @stack('scripts')
</body>
</html>
