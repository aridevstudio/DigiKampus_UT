<x-layouts.dosen title="Progres Mahasiswa" active="progres">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Progres Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau perkembangan mahasiswa pada semua kursus yang Anda ajar.</p>
    </div>
    @if(request('source') === 'bootcamp')
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
        <p class="font-semibold">Mode Delivery Bootcamp</p>
        <p class="mt-1">Konteks batch: {{ request('batch', '-') }}.</p>
    </div>
    @endif

    {{-- Summary Stats --}}
    @if(($totalEnrollments ?? 0) > 0)
    <div class="grid responsive-grid-stats-6 gap-4 mb-6">
        @php
            $stats = [
                ['label' => 'Total Enrollment', 'count' => $totalEnrollments ?? 0, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'blue'],
                ['label' => 'Sedang Aktif', 'count' => $aktifCount ?? 0, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Selesai', 'count' => $selesaiCount ?? 0, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
                ['label' => 'Tidak Aktif', 'count' => $tidakAktifCount ?? 0, 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'gray'],
                ['label' => 'Rata-rata Progress', 'count' => ($avgProgress ?? 0) . '%', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'purple'],
                ['label' => 'Rata-rata Nilai', 'count' => ($avgNilai ?? 0) . '%', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'color' => 'rose'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-{{ $stat['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xl font-bold text-gray-900 dark:text-white leading-none">{{ $stat['count'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $stat['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-5 mb-6">
        <form id="filterForm" method="GET" action="{{ route('dosen.progres') }}" x-data="{ isLoading: false }" @submit="isLoading = true">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm">Filter & Pencarian</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dosen.progres') }}" class="px-3 py-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">Reset</a>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-blue-500/25">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Terapkan
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Pilih Kursus --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Pilih Kursus</label>
                    <div class="relative">
                        <select name="course" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                            <option value="all">Semua Kursus</option>
                            @foreach($coursesForFilter ?? [] as $course)
                            <option value="{{ $course->id_course }}" {{ ($courseFilter ?? '') == $course->id_course ? 'selected' : '' }}>{{ $course->nama_course }}</option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Pilih Prodi --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Program Studi</label>
                    <div class="relative">
                        <select name="prodi" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                            <option value="all">Semua Prodi</option>
                            @foreach($jurusanList ?? [] as $jurusan)
                            <option value="{{ $jurusan->id_jurusan }}" {{ ($jurusanFilter ?? '') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Status Penyelesaian --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Status Penyelesaian</label>
                    <div class="relative">
                        <select name="status" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                            <option value="all">Semua Status</option>
                            <option value="aktif" {{ ($statusFilter ?? 'all') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ ($statusFilter ?? 'all') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="tidak_aktif" {{ ($statusFilter ?? 'all') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Cari Mahasiswa --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Cari Mahasiswa</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama mahasiswa..." class="w-full px-3.5 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Progress Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kursus</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hidden lg:table-cell">Modul Terakhir</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hidden md:table-cell">Waktu Akses</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/30">
                    @forelse($enrollments ?? [] as $enrollment)
                    @php
                        $progress = round($enrollment->progress ?? 0);
                        if ($progress >= 100) {
                            $statusLabel = 'Selesai';
                            $statusDot = 'bg-green-500';
                            $statusBg = 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800/30';
                            $barColor = 'from-green-400 to-emerald-500';
                            $barBg = 'bg-green-100 dark:bg-green-900/20';
                        } elseif ($progress > 0) {
                            $statusLabel = 'Aktif';
                            $statusDot = 'bg-blue-500';
                            $statusBg = 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30';
                            $barColor = 'from-blue-400 to-indigo-500';
                            $barBg = 'bg-blue-100 dark:bg-blue-900/20';
                        } else {
                            $statusLabel = 'Tidak Aktif';
                            $statusDot = 'bg-gray-400';
                            $statusBg = 'bg-gray-50 text-gray-500 border-gray-200 dark:bg-gray-700/50 dark:text-gray-400 dark:border-gray-600';
                            $barColor = 'from-gray-300 to-gray-400';
                            $barBg = 'bg-gray-100 dark:bg-gray-700/50';
                        }
                    @endphp
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/5 transition-colors duration-150">
                        {{-- Mahasiswa --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                @if($enrollment->mahasiswa?->profile?->foto_profile)
                                    <img src="{{ asset('storage/' . $enrollment->mahasiswa->profile->foto_profile) }}" alt="" class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                        {{ strtoupper(substr($enrollment->mahasiswa?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $enrollment->mahasiswa?->name ?? 'Unknown' }}</p>
                                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono">{{ $enrollment->mahasiswa?->profile?->nomor_induk ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Kursus --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg border border-gray-100 dark:border-gray-600/30 max-w-[180px] truncate">
                                <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                {{ $enrollment->course?->nama_course ?? '-' }}
                            </span>
                        </td>
                        
                        {{-- Modul Terakhir --}}
                        <td class="px-5 py-3.5 hidden lg:table-cell">
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $enrollment->last_module ?? 'Modul 1: Pengenalan' }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $enrollment->last_material ?? 'Pendahuluan' }}</p>
                        </td>
                        
                        {{-- Progress --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-16 sm:w-24 h-2 {{ $barBg }} rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $barColor }} rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tabular-nums min-w-[36px]">{{ $progress }}%</span>
                            </div>
                        </td>
                        
                        {{-- Nilai Akumulasi --}}
                        <td class="px-5 py-3.5">
                            @if($enrollment->quiz_score && $enrollment->quiz_score['quiz_count'] > 0)
                                @php
                                    $nilai = $enrollment->quiz_score['persentase'];
                                    $nilaiColor = $nilai >= 80 ? 'text-green-600 dark:text-green-400' : ($nilai >= 60 ? 'text-blue-600 dark:text-blue-400' : ($nilai >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400'));
                                    $nilaiBg = $nilai >= 80 ? 'bg-green-50 dark:bg-green-900/20' : ($nilai >= 60 ? 'bg-blue-50 dark:bg-blue-900/20' : ($nilai >= 40 ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-red-50 dark:bg-red-900/20'));
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold {{ $nilaiColor }} {{ $nilaiBg }}">
                                        {{ $nilai }}%
                                    </span>
                                    <span class="text-[10px] text-gray-400">{{ $enrollment->quiz_score['quiz_count'] }} kuis</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">â€”</span>
                            @endif
                        </td>
                        
                        {{-- Status --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg border {{ $statusBg }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        
                        {{-- Waktu Akses --}}
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $enrollment->updated_at?->diffForHumans() ?? '-' }}</p>
                        </td>
                        
                        {{-- Action --}}
                        <td class="px-5 py-3.5 text-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" type="button" class="p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                </button>
                                
                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 z-50 mt-1 w-44 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1" style="display: none;">
                                    <a href="{{ route('dosen.kursus.progres', $enrollment->course->id_course ?? '#') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Detail
                                    </a>
                                    <a href="{{ route('dosen.pesan') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Kirim Pesan
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold text-sm">Belum ada mahasiswa yang terdaftar</p>
                                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1 max-w-xs">Mahasiswa akan muncul di sini setelah mendaftar ke kursus Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(isset($enrollments) && $enrollments->hasPages())
        <div class="px-5 py-3.5 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $enrollments->firstItem() ?? 0 }}</span>-<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $enrollments->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $enrollments->total() }}</span> data
            </p>
            <div class="flex items-center gap-1.5">
                {{ $enrollments->onEachSide(1)->links() }}
            </div>
        </div>
        @endif
    </div>
</x-layouts.dosen>
