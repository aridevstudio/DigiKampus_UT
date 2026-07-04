<x-layouts.dashboard :active="'bootcamp-saya'">
@php
    $user = Auth::guard('mahasiswa')->user();
    $defaultBanner = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=300&fit=crop';
@endphp

<div x-data="{ 
    activeTab: '{{ request('tab', 'overview') }}',
    changeTab(tab) {
        this.activeTab = tab;
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    }
}" class="pb-12">
    
    {{-- Course Header Banner --}}
    <div class="relative rounded-3xl overflow-hidden mb-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/10">
        <div class="absolute inset-0 bg-black/25"></div>
        @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->nama_course }}" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-30">
        @else
            <img src="{{ $defaultBanner }}" alt="{{ $course->nama_course }}" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-30">
        @endif
        
        <div class="relative p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 z-10">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md text-white border border-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Verified Bootcamp Student
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">{{ $course->nama_course }}</h1>
                <p class="text-blue-100/90 text-sm sm:text-base line-clamp-2">{{ $course->deskripsi }}</p>
                
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 pt-2 text-xs sm:text-sm text-blue-50/90">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Mentor: {{ $course->dosen?->name ?: 'Instructor' }}
                    </span>
                    @if($course->tanggal_webinar)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Jadwal: {{ $course->tanggal_webinar->format('d M Y') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Circular progress --}}
            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 self-start md:self-auto">
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <svg class="absolute w-full h-full transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="rgba(255,255,255,0.15)" stroke-width="5" fill="transparent"/>
                        <circle cx="32" cy="32" r="28" stroke="white" stroke-width="5" fill="transparent"
                                stroke-dasharray="175.92"
                                stroke-dashoffset="{{ 175.92 - (175.92 * $progressPercent) / 100 }}"/>
                    </svg>
                    <span class="text-sm font-bold">{{ $progressPercent }}%</span>
                </div>
                <div>
                    <div class="text-xs text-blue-200">Progress Belajar</div>
                    <div class="text-sm font-bold text-white">Bootcamp Terverifikasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid: Tabs Menu & Content Panel --}}
    <div class="flex flex-col lg:flex-row gap-6">
        
        {{-- Responsive Left Sidebar Tab List --}}
        <div class="w-full lg:w-64 flex-shrink-0">
            {{-- Tablet/Desktop Vertical Navigation --}}
            <div class="hidden md:flex flex-col gap-1 bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm sticky top-20">
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-3 mb-2">Menu Bootcamp</p>
                
                @php
                    $tabsList = [
                        ['id' => 'overview', 'name' => 'Overview', 'icon' => 'home'],
                        ['id' => 'modules', 'name' => 'Module Belajar', 'icon' => 'academic-cap'],
                        ['id' => 'assignments', 'name' => 'Assignment', 'icon' => 'clipboard-document-list'],
                        ['id' => 'live-class', 'name' => 'Live Class', 'icon' => 'video-camera'],
                        ['id' => 'quiz', 'name' => 'Quiz', 'icon' => 'pencil-square'],
                        ['id' => 'forum', 'name' => 'Forum Diskusi', 'icon' => 'chat-bubble-left-right'],
                        ['id' => 'announcement', 'name' => 'Pengumuman', 'icon' => 'megaphone'],
                        ['id' => 'progress', 'name' => 'Progress Anda', 'icon' => 'chart-bar'],
                        ['id' => 'final-project', 'name' => 'Final Project', 'icon' => 'briefcase'],
                        ['id' => 'certificate', 'name' => 'Sertifikat', 'icon' => 'academic-cap'],
                    ];
                @endphp

                @foreach($tabsList as $t)
                    <button 
                        @click="changeTab('{{ $t['id'] }}')" 
                        :class="activeTab === '{{ $t['id'] }}' ? 'bg-blue-500 text-white font-semibold shadow-md shadow-blue-500/20' : 'text-gray-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-slate-700/30 hover:text-slate-900 dark:hover:text-white'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200 text-left"
                    >
                        @switch($t['icon'])
                            @case('home')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                                @break
                            @case('academic-cap')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M12 21v-8.25" /></svg>
                                @break
                            @case('clipboard-document-list')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                @break
                            @case('video-camera')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                @break
                            @case('pencil-square')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                @break
                            @case('chat-bubble-left-right')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                                @break
                            @case('megaphone')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.68-.15-1.32-.47-1.85-.92L5 11.5M10.34 15.84c.64-.14 1.25-.43 1.77-.85l5.34-4.27M10.34 15.84V21M17.45 10.725c.34-.14.65-.36.91-.64l3.19-3.41a1 1 0 00-.73-1.68h-4.9c-.34 0-.66.12-.91.34l-3.19 3.41a1 1 0 00.73 1.68h4.9z" /></svg>
                                @break
                            @case('chart-bar')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013 18.75v-5.625zM10.125 9.75c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-9zM17.25 5.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v13.125c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V5.625z" /></svg>
                                @break
                            @case('briefcase')
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.1A2.25 2.25 0 0118 20.5H6a2.25 2.25 0 01-2.25-2.25v-4.1m16.5 0A2.25 2.25 0 0018 11.9H6a2.25 2.25 0 00-2.25 2.25m16.5 0v-2.9A2.25 2.25 0 0018 9H6a2.25 2.25 0 00-2.25 2.25v2.9M9 9V5.75A2.25 2.25 0 0111.25 3.5h1.5A2.25 2.25 0 0115 5.75V9" /></svg>
                                @break
                        @endswitch
                        <span>{{ $t['name'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Mobile Side-Scrollable Horizontal Tabs List --}}
            <div class="flex md:hidden overflow-x-auto gap-2 pb-2 -mx-4 px-4 scrollbar-none sticky top-[72px] bg-gray-50 dark:bg-[#111827] z-20 py-2">
                @foreach($tabsList as $t)
                    <button 
                        @click="changeTab('{{ $t['id'] }}')" 
                        :class="activeTab === '{{ $t['id'] }}' ? 'bg-blue-500 text-white font-semibold' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-700/50'"
                        class="flex-shrink-0 px-4 py-2.5 rounded-full text-xs transition-all duration-200"
                    >
                        {{ $t['name'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Content Panel --}}
        <div class="flex-1 min-w-0">
            
            {{-- 1. OVERVIEW TAB --}}
            <div x-show="activeTab === 'overview'" class="space-y-6 animate-fade-in">
                {{-- Dashboard summary ringkasan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    {{-- Progress --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-500/10 text-blue-500 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Progress Saya</div>
                            <div class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $progressPercent }}% Selesai</div>
                        </div>
                    </div>
                    
                    {{-- Next Live Class --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Live Class Terdekat</div>
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">
                                @if($dashboardStats['next_class'])
                                    {{ $dashboardStats['next_class']['start_time']->format('d M H:i') }}
                                @else
                                    Belum ada jadwal
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Assignment Deadline --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Tugas Terdekat</div>
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">
                                @if($dashboardStats['closest_deadline'])
                                    {{ $dashboardStats['closest_deadline']->diffForHumans() }}
                                @else
                                    Semua tugas disubmit
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Announcement --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Info Terkini</div>
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">
                                @if($dashboardStats['latest_announcement'])
                                    {{ $dashboardStats['latest_announcement']->judul }}
                                @else
                                    Tidak ada pengumuman
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Last Opened Modul Quick Access --}}
                @if($dashboardStats['last_module'])
                <div class="bg-blue-500/5 dark:bg-blue-500/10 border border-blue-500/20 p-6 rounded-3xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 uppercase tracking-wider">Lanjutkan Belajar</span>
                        <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mt-2">{{ $dashboardStats['last_module']['title'] }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $dashboardStats['last_module']['description'] }}</p>
                    </div>
                    <button @click="changeTab('modules')" class="bg-blue-500 hover:bg-blue-600 text-white font-medium text-sm px-6 py-3 rounded-2xl transition shadow-lg shadow-blue-500/15">
                        Buka Modul
                    </button>
                </div>
                @endif

                {{-- Mentor Card --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Deskripsi Bootcamp</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $course->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                        
                        @if($course->persyaratan)
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700/50">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-2">Prasyarat Peserta</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->persyaratan }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-500/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-extrabold text-2xl mb-4">
                            {{ strtoupper(substr($course->dosen?->name ?: 'MT', 0, 2)) }}
                        </div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $course->dosen?->name ?: 'Mentor Utama' }}</h4>
                        <span class="text-xs text-gray-400 mt-0.5">Dosen / Mentor Utama</span>
                        <hr class="w-full my-4 border-gray-100 dark:border-gray-700/50">
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Mentor berpengalaman yang akan memandu Anda secara interaktif selama sesi Live Class dan melakukan review tugas pengerjaan proyek akhir.</p>
                    </div>
                </div>
            </div>

            {{-- 2. MODULE BELAJAR TAB --}}
            <div x-show="activeTab === 'modules'" class="space-y-6 animate-fade-in">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Modul Pembelajaran</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Status rilis modul diatur dalam mode: <span class="font-semibold text-blue-500 uppercase">{{ $moduleMode }}</span></p>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($modules as $mId => $m)
                        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm overflow-hidden">
                            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/50">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $m['title'] }}</h4>
                                        
                                        {{-- Module Status Badges --}}
                                        @if($m['status'] === 'Locked')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                                Locked
                                            </span>
                                        @elseif($m['status'] === 'Completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                Completed
                                            </span>
                                        @elseif($m['status'] === 'In Progress')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                                In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                                Available
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">{{ $m['description'] }}</p>
                                </div>
                                
                                <div class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ $m['completed_count'] }} / {{ $m['total_count'] }} Aktivitas Selesai
                                </div>
                            </div>
                            
                            {{-- Module Materials List --}}
                            @if($m['status'] === 'Locked')
                                <div class="p-6 text-center space-y-2 bg-gray-50/20 dark:bg-gray-900/10">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Modul ini Terkunci</p>
                                    <p class="text-xs text-gray-400 max-w-sm mx-auto">{{ $m['lock_reason'] }}</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    @foreach($m['materials'] as $mat)
                                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-700/10 transition">
                                            <div class="flex items-center gap-3 min-w-0">
                                                {{-- Icon based on material type --}}
                                                <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                                    @switch($mat['type'])
                                                        @case('video')
                                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            @break
                                                        @case('tugas')
                                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            @break
                                                        @case('kuis')
                                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                            @break
                                                        @default
                                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    @endswitch
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate block">{{ $mat['title'] }}</span>
                                                    <span class="text-[10px] text-gray-400 capitalize">{{ $mat['type'] }}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                @if($mat['is_completed'])
                                                    <span class="p-1 text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-500/10 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    </span>
                                                @else
                                                    @if($mat['type'] === 'tugas')
                                                        <button @click="changeTab('assignments')" class="text-xs bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-xl font-medium transition">
                                                            Kerjakan
                                                        </button>
                                                    @elseif($mat['type'] === 'kuis')
                                                        <button @click="changeTab('quiz')" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-xl font-medium transition">
                                                            Mulai Kuis
                                                        </button>
                                                    @else
                                                        <form method="POST" action="{{ route('mahasiswa.material.complete', ['id' => $mat['id']]) }}">
                                                            @csrf
                                                            <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-xl font-medium transition">
                                                                Selesai
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. ASSIGNMENT TAB --}}
            <div x-show="activeTab === 'assignments'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Assignment / Tugas Bootcamp</h3>
                    <p class="text-xs text-gray-400 mt-1">Selesaikan seluruh tugas agar syarat kelulusan sertifikat terpenuhi.</p>
                </div>

                <div class="space-y-6">
                    @foreach($assignments as $a)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $a['title'] }}</h4>
                                    <p class="text-xs text-gray-400">Deadline: <span class="font-semibold text-rose-500">{{ $a['deadline']->format('d M Y, H:i') }}</span> ({{ $a['countdown'] }})</p>
                                </div>
                                
                                {{-- Status Badge --}}
                                @php
                                    $badgeColor = 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400';
                                    if(Str::contains($a['status'], 'Disetujui')) $badgeColor = 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400';
                                    elseif(Str::contains($a['status'], 'Menunggu Review')) $badgeColor = 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400';
                                    elseif(Str::contains($a['status'], 'Perlu Revisi')) $badgeColor = 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400';
                                    elseif(Str::contains($a['status'], 'Terlambat')) $badgeColor = 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }} self-start sm:self-auto">
                                    {{ $a['status'] }}
                                </span>
                            </div>

                            <div class="p-4 bg-gray-50/50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-800 text-sm space-y-2">
                                <p class="font-bold text-gray-700 dark:text-gray-300">Deskripsi & Instruksi:</p>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-xs">{{ $a['description'] }}</p>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-xs">{{ $a['instructions'] }}</p>
                            </div>

                            {{-- File Attachment from Mentor --}}
                            @if($a['lampiran_path'])
                                <a href="{{ asset('storage/' . $a['lampiran_path']) }}" download class="inline-flex items-center gap-2 text-xs font-bold text-blue-500 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Dokumen Pendukung
                                </a>
                            @endif

                            {{-- Upload and History Section --}}
                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/50 space-y-4">
                                @if($a['submission'])
                                    <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <span class="text-xs text-gray-400 block">Tugas yang Anda Kumpulkan:</span>
                                            <a href="{{ asset('storage/' . $a['submission']->file_path) }}" download class="text-sm font-semibold text-blue-500 hover:underline truncate block">
                                                {{ $a['submission']->original_file_name ?: 'File Tugas' }}
                                            </a>
                                            <span class="text-[10px] text-gray-400">Diupload pada: {{ $a['submission']->submitted_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        
                                        @if(in_array($a['submission']->status, ['submitted', 'draft', 'revision', 'perlu_revisi'], true))
                                            {{-- Allow Replacement --}}
                                            <div x-data="{ openUpload: false }">
                                                <button @click="openUpload = !openUpload" class="text-xs border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-xl transition">
                                                    Ganti File Tugas
                                                </button>
                                                
                                                <div x-show="openUpload" class="mt-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl shadow-sm">
                                                    <form method="POST" action="{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $a['material_id']]) }}" enctype="multipart/form-data" class="space-y-4">
                                                        @csrf
                                                        <input type="file" name="file" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        <textarea name="catatan" rows="2" placeholder="Catatan opsional..." class="w-full text-xs border rounded-xl p-2.5 dark:bg-gray-900 dark:border-gray-700"></textarea>
                                                        <button type="submit" class="bg-blue-500 text-white text-xs font-semibold px-4 py-2 rounded-xl">Kirim Pengganti</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Mentor Feedback --}}
                                    @if($a['submission']->catatan_dosen)
                                        <div class="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl space-y-1">
                                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Feedback Mentor:</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $a['submission']->catatan_dosen }}</p>
                                        </div>
                                    @endif
                                @else
                                    {{-- Upload area --}}
                                    @if(now()->lessThan($a['deadline']) || $a['allow_late'])
                                        <form method="POST" action="{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $a['material_id']]) }}" enctype="multipart/form-data" class="space-y-4">
                                            @csrf
                                            <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 rounded-2xl text-center space-y-2">
                                                <input type="file" name="file" required class="mx-auto block text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                <p class="text-[10px] text-gray-400">Format yang diterima: PDF, ZIP (Maksimal 10 MB)</p>
                                            </div>
                                            <textarea name="catatan" rows="2" placeholder="Catatan ke mentor..." class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
                                            <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-medium text-xs px-6 py-2.5 rounded-xl transition shadow-lg shadow-blue-500/15">
                                                Submit Tugas
                                            </button>
                                        </form>
                                    @else
                                        <div class="bg-rose-500/5 border border-rose-500/20 p-4 rounded-2xl text-center text-xs font-semibold text-rose-500">
                                            Tenggat Waktu Lewat (Penerimaan File Ditutup)
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 4. LIVE CLASS TAB --}}
            <div x-show="activeTab === 'live-class'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Live Class / Sesi Online</h3>
                    <p class="text-xs text-gray-400 mt-1">Interaksi tatap muka langsung bersama mentor lewat media Zoom / Meet.</p>
                </div>

                <div class="space-y-4">
                    @foreach($liveClasses as $lc)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="space-y-2">
                                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $lc['title'] }}</h4>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $lc['start_time']->format('d M Y, H:i') }} - {{ $lc['end_time']->format('H:i') }} WIB
                                    </span>
                                    <span>•</span>
                                    <span>Mentor: {{ $lc['mentor'] }}</span>
                                </div>
                                <span class="text-xs text-blue-500 font-bold block">{{ $lc['countdown'] }}</span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3">
                                @if($lc['is_active'])
                                    <a href="{{ $lc['link'] }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-medium text-xs px-6 py-3 rounded-xl transition shadow-lg shadow-green-500/15">
                                        Join Sesi Live
                                    </a>
                                @else
                                    <button disabled class="bg-gray-100 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500 font-medium text-xs px-6 py-3 rounded-xl cursor-not-allowed">
                                        Join Sesi Live
                                    </button>
                                @endif

                                @if($lc['recording_url'])
                                    <a href="{{ $lc['recording_url'] }}" target="_blank" class="border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 font-medium text-xs px-6 py-3 rounded-xl transition">
                                        Lihat Rekaman
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 5. QUIZ TAB --}}
            <div x-show="activeTab === 'quiz'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Kuis Evaluasi Modul</h3>
                    <p class="text-xs text-gray-400 mt-1">Uji kemampuan akademis Anda pada setiap segmen kuis modul belajar.</p>
                </div>

                <div class="space-y-4">
                    @foreach($quizzes as $q)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="space-y-2">
                                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $q['title'] }}</h4>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400">
                                    <span>{{ $q['question_count'] }} Pertanyaan</span>
                                    <span>•</span>
                                    <span>Durasi: {{ $q['duration'] }} Menit</span>
                                    <span>•</span>
                                    <span>Min. Kelulusan: {{ $q['passing_score'] }}%</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Skor Terbaik: <span class="font-bold text-gray-800 dark:text-gray-100">{{ $q['score'] }}%</span> ({{ $q['attempts'] }} Kali Percobaan)
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                @if($q['attempts'] > 0)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $q['status'] === 'Lulus' ? 'bg-green-50 text-green-600 dark:bg-green-500/10' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10' }}">
                                        {{ $q['status'] }}
                                    </span>
                                @endif
                                
                                @if($q['attempts'] === 0)
                                    <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $q['id_quiz']]) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium text-xs px-5 py-2.5 rounded-xl transition">
                                        Mulai Kuis
                                    </a>
                                @else
                                    {{-- Reset session to allow retake --}}
                                    <form method="POST" action="{{ route('mahasiswa.quiz-reset', ['courseId' => $course->id_course, 'quizId' => $q['id_quiz']]) }}" onsubmit="event.preventDefault(); fetch(this.action, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => window.location.href = '{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $q['id_quiz']]) }}')">
                                        @csrf
                                        <button type="submit" class="border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 font-medium text-xs px-5 py-2.5 rounded-xl transition">
                                            Ulangi Kuis
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 6. FORUM DISKUSI TAB --}}
            <div x-show="activeTab === 'forum'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Forum Khusus Bootcamp</h3>
                        <p class="text-xs text-gray-400 mt-1">Grup diskusi privat untuk bertanya kendala materi khusus bootcamp ini.</p>
                    </div>
                    
                    {{-- Alpine data to open inline create form --}}
                    <div x-data="{ openForm: false }" class="w-full sm:w-auto">
                        <button @click="openForm = !openForm" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-medium text-xs px-5 py-2.5 rounded-xl transition">
                            Tambah Diskusi Baru
                        </button>
                        
                        {{-- Create discussion form modal/card --}}
                        <div x-show="openForm" class="mt-4 bg-white dark:bg-gray-850 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-lg space-y-4">
                            <form method="POST" action="{{ route('mahasiswa.bootcamp.forum.topic.store', ['id' => $course->id_course]) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Judul Diskusi</label>
                                    <input type="text" name="judul" required placeholder="Tulis subjek pertanyaan Anda..." class="w-full text-xs border rounded-xl p-3 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-blue-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Isi Detail</label>
                                    <textarea name="isi" rows="4" required placeholder="Jelaskan pertanyaan atau error yang Anda alami..." class="w-full text-xs border rounded-xl p-3 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-blue-500/20"></textarea>
                                </div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition">Kirim Topik</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Topics List --}}
                <div class="space-y-4">
                    @forelse($forumTopics as $t)
                        @php
                            // Strip [Bootcamp #id] prefix from display title
                            $cleanTitle = preg_replace('/^\[Bootcamp\s*#\d+\]\s*/i', '', $t->judul);
                        @endphp
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm flex items-center justify-between gap-6">
                            <div class="space-y-1 min-w-0">
                                <a href="{{ route('mahasiswa.forum.show', ['slug' => $t->slug]) }}" class="text-sm font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-500 transition block truncate">
                                    {{ $cleanTitle }}
                                </a>
                                <p class="text-xs text-gray-400">Ditulis oleh: {{ $t->author?->name ?: 'Anonim' }} • {{ $t->created_at->diffForHumans() }}</p>
                            </div>
                            
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                {{ $t->published_comments_count }} Balasan
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm text-center text-gray-500 dark:text-gray-400">
                            Belum ada topik diskusi khusus pada bootcamp ini. Mulailah diskusi pertama!
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 7. ANNOUNCEMENT TAB --}}
            <div x-show="activeTab === 'announcement'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Pengumuman & Update</h3>
                    <p class="text-xs text-gray-400 mt-1">Informasi terkini, pengumuman mentor, atau perubahan jadwal penting.</p>
                </div>

                <div class="space-y-4">
                    @forelse($announcements as $ann)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $ann->judul }}</h4>
                                <span class="text-xs text-gray-400">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                            <hr class="border-gray-100 dark:border-gray-700/50">
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $ann->konten }}</p>
                            <div class="text-[10px] text-gray-400">Oleh: {{ $ann->dosen?->name ?: 'Admin' }}</div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm text-center text-gray-500 dark:text-gray-400">
                            Belum ada pengumuman resmi yang diterbitkan untuk saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 8. PROGRESS TAB --}}
            <div x-show="activeTab === 'progress'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Analitik Pembelajaran Anda</h3>
                    <p class="text-xs text-gray-400 mt-1">Pantau performa nilai kuis, tugas dikumpulkan, dan pencapaian Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-4">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider text-gray-400">Statistik Modul & Tugas</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Total Progress</span>
                                <span class="font-bold text-gray-800 dark:text-gray-100">{{ $progressPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700/50 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                                <div>
                                    <span class="text-[10px] text-gray-400 block font-semibold uppercase">Rata-Rata Nilai Kuis</span>
                                    <span class="text-lg font-black text-gray-800 dark:text-gray-100">{{ round($avgQuizScore, 1) }}%</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 block font-semibold uppercase">Proyek Akhir</span>
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $finalProject ? $finalProject['status'] : 'Tidak Ada' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-4">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider text-gray-400">Pencapaian Prasyarat Sertifikat</h4>
                        <ul class="space-y-2 text-xs">
                            <li class="flex items-center gap-2">
                                <span class="p-0.5 rounded-full {{ $certificateChecklist['modules_completed'] ? 'bg-green-100 text-green-600' : 'bg-rose-100 text-rose-600' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">Semua aktivitas modul pembelajaran selesai</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="p-0.5 rounded-full {{ $certificateChecklist['assignments_completed'] ? 'bg-green-100 text-green-600' : 'bg-rose-100 text-rose-600' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">Seluruh tugas kelas (assignment) disubmit</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="p-0.5 rounded-full {{ $certificateChecklist['final_project_passed'] ? 'bg-green-100 text-green-600' : 'bg-rose-100 text-rose-600' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">Proyek Akhir disetujui / lulus</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="p-0.5 rounded-full {{ $certificateChecklist['score_met'] ? 'bg-green-100 text-green-600' : 'bg-rose-100 text-rose-600' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">Nilai kuis di atas ambang batas kelulusan (>= 70)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 9. FINAL PROJECT TAB --}}
            <div x-show="activeTab === 'final-project'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Proyek Akhir (Final Project)</h3>
                    <p class="text-xs text-gray-400 mt-1">Unggah berkas proyek akhir Anda sebagai instrumen kelulusan utama.</p>
                </div>

                @if(!$finalProject)
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm text-center text-gray-500 dark:text-gray-400">
                        Bootcamp ini tidak mensyaratkan pengerjaan proyek akhir terpisah.
                    </div>
                @else
                    @if(!$finalProject['prerequisites_met'])
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm text-center space-y-4">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">Proyek Akhir Masih Terkunci</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">Selesaikan seluruh aktivitas materi modul pembelajaran prasyarat terlebih dahulu untuk membuka akses unggah proyek akhir.</p>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $finalProject['title'] }}</h4>
                                    <span class="text-xs text-gray-400">Unggah portofolio terbaik Anda untuk direview langsung oleh mentor utama.</span>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 self-start sm:self-auto">
                                    {{ $finalProject['status'] }}
                                </span>
                            </div>

                            <div class="p-4 bg-gray-50/50 dark:bg-gray-900/20 rounded-2xl border border-gray-100 dark:border-gray-800 text-sm space-y-2">
                                <p class="font-bold text-gray-700 dark:text-gray-300">Deskripsi Proyek Akhir:</p>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-xs">{{ $finalProject['description'] }}</p>
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/50 space-y-4">
                                @if($finalProject['submission'])
                                    <div class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <span class="text-xs text-gray-400 block">Proyek yang Anda Kumpulkan:</span>
                                            <a href="{{ asset('storage/' . $finalProject['submission']->file_path) }}" download class="text-sm font-semibold text-blue-500 hover:underline truncate block">
                                                {{ $finalProject['submission']->original_file_name ?: 'File Proyek Akhir' }}
                                            </a>
                                            <span class="text-[10px] text-gray-400">Diupload pada: {{ $finalProject['submission']->submitted_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        
                                        @if(in_array($finalProject['submission']->status, ['submitted', 'draft', 'revision', 'perlu_revisi'], true))
                                            <div x-data="{ openUpload: false }">
                                                <button @click="openUpload = !openUpload" class="text-xs border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 px-4 py-2 rounded-xl transition">
                                                    Ganti File Proyek
                                                </button>
                                                
                                                <div x-show="openUpload" class="mt-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-2xl shadow-sm">
                                                    <form method="POST" action="{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $finalProject['material_id']]) }}" enctype="multipart/form-data" class="space-y-4">
                                                        @csrf
                                                        <input type="file" name="file" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        <button type="submit" class="bg-blue-500 text-white text-xs font-semibold px-4 py-2 rounded-xl">Kirim Pengganti</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($finalProject['submission']->catatan_dosen)
                                        <div class="bg-amber-500/5 dark:bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl space-y-1">
                                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Tanggapan Reviewer:</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $finalProject['submission']->catatan_dosen }}</p>
                                        </div>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $finalProject['material_id']]) }}" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 rounded-2xl text-center space-y-2">
                                            <input type="file" name="file" required class="mx-auto block text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            <p class="text-[10px] text-gray-400">Lampirkan file PDF / ZIP berisi pengerjaan proyek akhir Anda.</p>
                                        </div>
                                        <textarea name="catatan" rows="2" placeholder="Catatan opsional..." class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 dark:bg-gray-900"></textarea>
                                        <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white font-medium text-xs px-6 py-2.5 rounded-xl transition shadow-lg shadow-blue-500/15">
                                            Kumpulkan Proyek Akhir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- 10. CERTIFICATE TAB --}}
            <div x-show="activeTab === 'certificate'" class="space-y-6 animate-fade-in">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Sertifikat Kelulusan</h3>
                    <p class="text-xs text-gray-400 mt-1">Unduh bukti kelulusan formal Anda jika seluruh prasyarat telah terpenuhi.</p>
                </div>

                @if($certificateEligible && $issuedCertificate)
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm text-center space-y-6">
                        <div class="w-16 h-16 bg-green-50 dark:bg-green-500/10 text-green-500 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">Selamat! Anda Dinyatakan Lulus</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">Anda telah memenuhi seluruh parameter pembelajaran. Sertifikat digital kelulusan telah diterbitkan secara resmi.</p>
                            <p class="text-xs font-semibold text-gray-400">No Sertifikat: {{ $issuedCertificate['nomor_sertifikat'] }}</p>
                        </div>
                        
                        <div class="pt-4">
                            <a href="{{ route('mahasiswa.courses') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium text-xs px-6 py-3 rounded-xl transition shadow-lg shadow-blue-500/15 inline-block">
                                Download Sertifikat (PDF)
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-sm space-y-4">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100">Prasyarat Belum Terpenuhi</h4>
                        <p class="text-xs text-gray-400">Silakan selesaikan parameter berikut agar sertifikat digital Anda dapat diterbitkan:</p>
                        
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center gap-3 text-xs">
                                <span class="p-1 rounded-full {{ $certificateChecklist['modules_completed'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="{{ $certificateChecklist['modules_completed'] ? 'text-gray-500 line-through' : 'text-gray-700 dark:text-gray-300 font-semibold' }}">Semua materi modul pembelajaran ditandai selesai</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="p-1 rounded-full {{ $certificateChecklist['assignments_completed'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="{{ $certificateChecklist['assignments_completed'] ? 'text-gray-500 line-through' : 'text-gray-700 dark:text-gray-300 font-semibold' }}">Mengumpulkan seluruh tugas (assignment) kelas</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="p-1 rounded-full {{ $certificateChecklist['final_project_passed'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="{{ $certificateChecklist['final_project_passed'] ? 'text-gray-500 line-through' : 'text-gray-700 dark:text-gray-300 font-semibold' }}">Menyelesaikan Proyek Akhir dengan status Lulus</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="p-1 rounded-full {{ $certificateChecklist['score_met'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="{{ $certificateChecklist['score_met'] ? 'text-gray-500 line-through' : 'text-gray-700 dark:text-gray-300 font-semibold' }}">Rata-rata nilai kuis mencapai batas minimum (70%)</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
</x-layouts.dashboard>
