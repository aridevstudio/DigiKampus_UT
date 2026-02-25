<x-layouts.dosen title="Dashboard" active="dashboard">
    {{-- Welcome Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang Kembali, {{ $dosen->name ?? 'Dosen' }}!</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Berikut ringkasan aktivitas pengajaran Anda hari ini</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Kursus --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover-lift">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCourses ?? 0 }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Kursus</p>
        </div>
        
        {{-- Mahasiswa Terdaftar --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover-lift">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMahasiswa ?? 0 }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Mahasiswa Terdaftar</p>
        </div>
        
        {{-- Rata-rata Progres --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover-lift">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $avgProgress ?? 0 }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Progres</p>
        </div>
        
        {{-- Sesi Terdekat --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover-lift">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($upcomingSchedules ?? []) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sesi Terdekat</p>
        </div>
    </div>

    {{-- Kursus yang Kamu Kelola --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Kursus yang Kamu Kelola</h2>
            <a href="{{ route('dosen.kursus.buat') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kursus Baru
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($coursesData ?? [] as $index => $course)
            @php
                $colors = [
                    ['from-blue-500', 'to-blue-600'],
                    ['from-green-500', 'to-green-600'],
                    ['from-purple-500', 'to-purple-600'],
                ];
                $colorSet = $colors[$index % 3];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden hover-lift">
                <div class="h-32 bg-gradient-to-br {{ $colorSet[0] }} {{ $colorSet[1] }} p-5 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $course['nama'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $course['mahasiswa_count'] }} Mahasiswa terdaftar</p>
                    
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Progres Rata-rata</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $course['progress_avg'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-gradient-to-r {{ $colorSet[0] }} {{ $colorSet[1] }} rounded-full" style="width: {{ $course['progress_avg'] }}%"></div>
                    </div>
                    
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('dosen.kursus.edit', $course['id']) }}" class="flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-sm font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition text-center">
                            Kelola Kursus & Modul
                        </a>
                        <a href="{{ route('dosen.kursus.preview', $course['id']) }}" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition" title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Belum ada kursus yang Anda kelola</p>
                <a href="{{ route('dosen.kursus.buat') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Kursus Pertama
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Bottom Section: Progress & Schedule --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Progres Mahasiswa Terbaru --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Progres Mahasiswa Terbaru</h3>
            
            <div class="space-y-4">
                @forelse($recentProgress ?? [] as $progress)
                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    @if($progress['foto'])
                        <img src="{{ asset('storage/' . $progress['foto']) }}" alt="{{ $progress['nama'] }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($progress['nama'], 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $progress['nama'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $progress['course'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $progress['updated'] }}</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Progres</span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $progress['progress'] }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $progress['progress'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas progres terbaru</p>
                </div>
                @endforelse
            </div>
            
            @if(count($recentProgress ?? []) > 0)
            <a href="{{ route('dosen.progres') }}" class="inline-flex items-center gap-2 mt-4 text-blue-500 hover:text-blue-600 text-sm font-medium">
                Lihat Semua Progres
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @endif
        </div>
        
        {{-- Jadwal Mengajar Terdekat --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900 dark:text-white">Jadwal Mengajar Terdekat</h3>
                <button
                    type="button"
                    onclick="openScheduleModal()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Jadwal
                </button>
            </div>
            
            <div class="space-y-4">
                @forelse($upcomingSchedules ?? [] as $index => $schedule)
                @php
                    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500'];
                    $bgColor = $colors[$index % 3];
                @endphp
                <div class="flex gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg {{ $bgColor }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $schedule['course'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $schedule['tanggal'] }}</p>
                        <div class="flex items-center gap-1 mt-1 text-xs text-gray-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $schedule['waktu'] }}
                        </div>
                        @if(!empty($schedule['id']))
                            <a href="{{ route('dosen.kursus.detail', $schedule['id']) }}" class="inline-flex items-center gap-1 mt-2 text-blue-500 hover:text-blue-600 text-xs font-medium">
                                Lihat Detail Jadwal
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada jadwal terdekat</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Add Schedule Modal --}}
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeScheduleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeScheduleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form id="scheduleForm" class="p-6" onsubmit="return submitScheduleForm(event)">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Jadwal Mengajar</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Kursus <span class="text-red-500">*</span></label>
                            <select id="scheduleCourse" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Memuat daftar kursus...</option>
                            </select>
                            <p id="scheduleCourseHint" class="mt-1 text-xs text-gray-500 dark:text-gray-400 hidden"></p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" id="scheduleDate" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                <select id="scheduleType" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    <option value="webinar">Webinar</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="deadline">Deadline</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Waktu Mulai</label>
                                <input type="time" id="scheduleStartTime" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Waktu Selesai</label>
                                <input type="time" id="scheduleEndTime" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Sesi (opsional)</label>
                            <input type="text" id="scheduleTitle" placeholder="Contoh: Live Mentoring Mingguan" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi (opsional)</label>
                            <textarea id="scheduleDescription" rows="3" placeholder="Catatan untuk sesi mengajar..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" id="scheduleSubmitBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const scheduleModal = document.getElementById('scheduleModal');
        const scheduleForm = document.getElementById('scheduleForm');
        const scheduleCourseSelect = document.getElementById('scheduleCourse');
        const scheduleSubmitBtn = document.getElementById('scheduleSubmitBtn');
        const scheduleCourseHint = document.getElementById('scheduleCourseHint');
        const scheduleDateInput = document.getElementById('scheduleDate');
        const scheduleCsrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? @json(csrf_token());
        let scheduleCoursesLoaded = false;

        function setDefaultScheduleDate() {
            if (!scheduleDateInput) return;
            if (scheduleDateInput.value) return;

            const today = new Date();
            scheduleDateInput.value = today.toISOString().slice(0, 10);
        }

        async function loadScheduleCourses() {
            if (scheduleCoursesLoaded || !scheduleCourseSelect) return;

            scheduleCourseSelect.innerHTML = '<option value="">Memuat daftar kursus...</option>';
            scheduleCourseSelect.disabled = true;

            try {
                const response = await fetch('/dosen/api/courses?sort=nama&per_page=100', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();

                const courses = data?.data?.courses ?? [];
                if (!response.ok) {
                    throw new Error(data?.message || 'Gagal memuat daftar kursus.');
                }

                if (!Array.isArray(courses) || courses.length === 0) {
                    scheduleCourseSelect.innerHTML = '<option value="">Belum ada kursus tersedia</option>';
                    scheduleCourseHint.textContent = 'Buat kursus terlebih dahulu sebelum menambahkan jadwal.';
                    scheduleCourseHint.classList.remove('hidden');
                    return;
                }

                const options = ['<option value="">-- Pilih Kursus --</option>'];
                courses.forEach((course) => {
                    options.push(`<option value="${course.id}">${course.nama}</option>`);
                });

                scheduleCourseSelect.innerHTML = options.join('');
                scheduleCoursesLoaded = true;
                scheduleCourseHint.classList.add('hidden');
            } catch (error) {
                scheduleCourseSelect.innerHTML = '<option value="">Gagal memuat kursus</option>';
                scheduleCourseHint.textContent = error.message || 'Terjadi kesalahan saat memuat kursus.';
                scheduleCourseHint.classList.remove('hidden');
            } finally {
                scheduleCourseSelect.disabled = false;
            }
        }

        function openScheduleModal() {
            if (!scheduleModal) return;

            scheduleModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setDefaultScheduleDate();
            loadScheduleCourses();
        }

        function closeScheduleModal() {
            if (!scheduleModal) return;

            scheduleModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            scheduleForm?.reset();
            setDefaultScheduleDate();
        }

        function extractScheduleErrorMessage(data) {
            if (data?.errors && typeof data.errors === 'object') {
                const firstKey = Object.keys(data.errors)[0];
                const firstError = firstKey ? data.errors[firstKey]?.[0] : null;
                if (firstError) return firstError;
            }

            return data?.message || 'Terjadi kesalahan saat menyimpan jadwal.';
        }

        async function submitScheduleForm(event) {
            event.preventDefault();

            const courseId = scheduleCourseSelect?.value;
            const tanggal = document.getElementById('scheduleDate')?.value;
            const waktuMulai = document.getElementById('scheduleStartTime')?.value;
            const waktuSelesai = document.getElementById('scheduleEndTime')?.value;
            const judul = document.getElementById('scheduleTitle')?.value?.trim();
            const deskripsi = document.getElementById('scheduleDescription')?.value?.trim();
            const tipe = document.getElementById('scheduleType')?.value || 'webinar';

            if (!courseId) {
                alert('Silakan pilih kursus terlebih dahulu.');
                return false;
            }

            if (!tanggal) {
                alert('Tanggal jadwal wajib diisi.');
                return false;
            }

            const payload = {
                id_course: Number(courseId),
                tanggal,
                tipe,
            };

            if (judul) payload.judul = judul;
            if (deskripsi) payload.deskripsi = deskripsi;
            if (waktuMulai) payload.waktu_mulai = waktuMulai;
            if (waktuSelesai) payload.waktu_selesai = waktuSelesai;

            scheduleSubmitBtn.disabled = true;
            scheduleSubmitBtn.textContent = 'Menyimpan...';

            try {
                const response = await fetch('/dosen/api/schedules', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': scheduleCsrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                if (!response.ok || !data?.success) {
                    throw new Error(extractScheduleErrorMessage(data));
                }

                closeScheduleModal();
                alert('Jadwal mengajar berhasil ditambahkan.');
                window.location.reload();
            } catch (error) {
                alert(error.message || 'Gagal menyimpan jadwal mengajar.');
            } finally {
                scheduleSubmitBtn.disabled = false;
                scheduleSubmitBtn.textContent = 'Simpan Jadwal';
            }

            return false;
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && scheduleModal && !scheduleModal.classList.contains('hidden')) {
                closeScheduleModal();
            }
        });
    </script>
    @endpush
</x-layouts.dosen>
