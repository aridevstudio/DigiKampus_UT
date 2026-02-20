<x-layouts.dosen title="Progres Mahasiswa" active="progres">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Progres Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau perkembangan mahasiswa pada semua kursus yang Anda ajar.</p>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900 dark:text-white">Filter & Pencarian</h2>
            <button type="button" onclick="document.getElementById('filterForm').reset(); document.getElementById('filterForm').submit();" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                Cari/Filters
            </button>
        </div>
        
        <form id="filterForm" method="GET" action="{{ route('dosen.progres') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Pilih Kursus --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Kursus</label>
                <div class="relative">
                    <select name="course" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                        <option value="all">Semua Kursus</option>
                        @foreach($coursesForFilter ?? [] as $course)
                        <option value="{{ $course->id_course }}" {{ ($courseFilter ?? '') == $course->id_course ? 'selected' : '' }}>{{ $course->nama_course }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            {{-- Pilih Modul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Modul</label>
                <div class="relative">
                    <select name="modul" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                        <option value="all">Semua Modul</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            {{-- Status Penyelesaian --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status Penyelesaian</label>
                <div class="relative">
                    <select name="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                        <option value="all">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            {{-- Cari Mahasiswa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cari Mahasiswa</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama mahasiswa..." class="w-full px-4 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </form>
    </div>

    {{-- Progress Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kursus</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modul Terakhir</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu Akses</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enrollments ?? [] as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        {{-- Mahasiswa --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if($enrollment->mahasiswa?->profile?->foto_profile)
                                    <img src="{{ asset('storage/' . $enrollment->mahasiswa->profile->foto_profile) }}" alt="" class="w-9 h-9 rounded-full object-cover">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-sm font-semibold">
                                        {{ strtoupper(substr($enrollment->mahasiswa?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $enrollment->mahasiswa?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->mahasiswa?->profile?->nim ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Kursus --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $enrollment->course?->nama_course ?? '-' }}</p>
                        </td>
                        
                        {{-- Modul Terakhir --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $enrollment->last_module ?? 'Modul 1: Pengenalan' }}</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->last_material ?? 'Pendahuluan' }}</p>
                        </td>
                        
                        {{-- Progress --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-20 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ round($enrollment->progress ?? 0) }}%</span>
                            </div>
                        </td>
                        
                        {{-- Status --}}
                        <td class="px-5 py-4">
                            @php
                                $progress = $enrollment->progress ?? 0;
                                if ($progress >= 100) {
                                    $statusLabel = 'Selesai';
                                    $statusClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                                } elseif ($progress > 0) {
                                    $statusLabel = 'Aktif';
                                    $statusClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                } else {
                                    $statusLabel = 'Tidak Aktif';
                                    $statusClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                                }
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        
                        {{-- Waktu Akses --}}
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->updated_at?->diffForHumans() ?? '-' }}</p>
                        </td>
                        
                        {{-- Action --}}
                        <td class="px-5 py-4 text-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                </button>
                                
                                {{-- Dropdown Menu --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1" style="display: none;">
                                    
                                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Detail
                                    </a>
                                    
                                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        Kirim Pesan
                                    </a>
                                    
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    
                                    <button type="button" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus dari Kursus
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <svg class="w-14 h-14 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada mahasiswa yang terdaftar</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Mahasiswa akan muncul di sini setelah mendaftar ke kursus Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($enrollments) && $enrollments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>
</x-layouts.dosen>
