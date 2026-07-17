<x-layouts.dashboard :active="'home'">
@php
$user = Auth::guard('mahasiswa')->user();
$userName = $user?->name ?? 'Mahasiswa';
$firstName = explode(' ', $userName)[0];

// Use data from controller (with fallback to 0 if not set)
$totalProgressValue = max(0, min(100, (int) ($totalProgress ?? 0)));
$activeCoursesCount = $kursusAktif ?? 0;
$completedCoursesCount = $kursusSelesai ?? 0;
$inProgressCoursesCount = $kursusSedangDipelajari ?? 0;
$pendingCoursesCount = $kursusTertunda ?? 0;
$continueLearningHref = $continueLearningUrl ?? route('mahasiswa.get-courses');

// Gradients for course cards
$gradients = [
    'from-purple-600 to-purple-400',
    'from-teal-500 to-cyan-400',
    'from-yellow-400 to-orange-400',
    'from-rose-500 to-pink-400',
    'from-indigo-500 to-blue-400',
];

// Build courses array from enrolledCourses (from controller) — hanya kursus reguler
// karena controller sudah memfilter berdasarkan kategori/tipe_event.
$courses = [];
if (isset($enrolledCourses) && count($enrolledCourses) > 0) {
    foreach ($enrolledCourses as $index => $enrollment) {
        $course = $enrollment->course;
        $courseProgress = (float) ($enrollment->progress ?? 0);
        $courseStatus = $courseProgress >= 100
            ? 'Selesai'
            : ($courseProgress > 0 ? 'Sedang Berlangsung' : 'Belum Dimulai');
        $courses[] = [
            'id' => $course->id_course ?? $enrollment->id_course ?? null,
            'name' => $course->nama_course ?? 'Kursus',
            'code' => $course->kode_course ?? '',
            'progress' => $courseProgress,
            'status' => $courseStatus,
            'gradient' => $gradients[$index % count($gradients)],
            'dosen' => $course->dosen->name ?? 'Dosen',
            'thumbnail' => $course->thumbnail ?? null,
        ];
    }
}

// Build events array dari $eventEnrollments (bootcamp/webinar/workshop/seminar)
// — terpisah dari $courses karena flow-nya berbeda (link SS Zoom/materi per sesi
// vs modul kursus statis), dan progress dihitung via attendance verification.
$events = [];
if (isset($eventEnrollments) && count($eventEnrollments) > 0) {
    foreach ($eventEnrollments as $index => $enrollment) {
        $course = $enrollment->course;
        $tipeEvent = strtoupper($course->tipe_event ?? 'BOOTCAMP');
        $modeEvent = strtolower((string) ($course->mode_event ?? 'online'));
        $modeLabel = match ($modeEvent) {
            'offline' => 'Offline',
            'hybrid' => 'Hybrid',
            'onsite' => 'On-site',
            default => 'Online',
        };
        $modeClass = match ($modeEvent) {
            'offline', 'onsite' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'hybrid' => 'bg-violet-100 text-violet-700 ring-violet-200',
            default => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        };
        $tipeClass = match (strtolower($course->tipe_event ?? 'bootcamp')) {
            'webinar' => 'bg-sky-100 text-sky-700 ring-sky-200',
            'workshop' => 'bg-pink-100 text-pink-700 ring-pink-200',
            'seminar' => 'bg-orange-100 text-orange-700 ring-orange-200',
            default => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
        };
        $events[] = [
            'id' => $course->id_course ?? $enrollment->id_course ?? null,
            'name' => $course->nama_course ?? 'Event',
            'code' => $course->kode_course ?? '',
            'type' => $tipeEvent,
            'mode_label' => $modeLabel,
            'mode_class' => $modeClass,
            'type_class' => $tipeClass,
            'dosen' => $course->dosen->name ?? 'Dosen',
            'thumbnail' => $course->thumbnail ?? null,
            'gradient' => $gradients[$index % count($gradients)],
        ];
    }
}

// Build news array from controller data
$newsItems = [];
if (isset($news) && count($news) > 0) {
    foreach ($news as $item) {
        $newsItems[] = [
            'title' => $item->judul,
            'time' => $item->tanggal_publish->diffForHumans(),
            'icon' => match($item->kategori ?? 'info') {
                'deadline' => 'alert',
                'event', 'webinar' => 'calendar',
                default => 'document'
            },
            'color' => match($item->kategori ?? 'info') {
                'deadline' => 'rose',
                'event', 'webinar' => 'blue',
                default => 'green'
            },
        ];
    }
}

// Build calendar events array from agenda (from controller)
// PENTING: Pakai variabel $calendarEvents, BUKAN $events, supaya tidak menimpa
// data bootcamp/event enrollment yang sudah dibangun di atas ($events dari
// $eventEnrollments). Bug sebelumnya: $events di-overwrite oleh agenda →
// section "Event yang Sedang Kamu Ikuti" merender data kalender (struktur
// salah: day/label/color) alih-alih data bootcamp (id/name/type/mode_label).
$calendarEvents = [];
if (isset($agenda) && count($agenda) > 0) {
    foreach ($agenda as $item) {
        $calendarEvents[] = [
            'day' => $item->tanggal->day,
            'color' => match($item->tipe ?? 'webinar') {
                'deadline' => 'rose',
                'workshop' => 'green',
                'quiz' => 'yellow',
                default => 'blue'
            },
            'label' => $item->judul,
        ];
    }
}
@endphp

{{-- Main Dashboard Content --}}
<div id="dashboard-content">

{{-- Welcome Section --}}
<div class="mb-4 sm:mb-6 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Selamat datang, {{ $firstName }} 👋</h1>
</div>

{{-- Perkembangan Belajarmu Section --}}
<div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 mb-4 sm:mb-6 animate-fade-in-up delay-100 hover-lift">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100">Perkembangan Belajarmu</h2>
            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm">Pantau sejauh mana progres belajar kamu di SALUT.</p>
        </div>
        <a href="{{ $continueLearningHref }}" class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-medium text-xs sm:text-sm transition w-full sm:w-auto btn-pulse">
            Lanjutkan Belajar
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-center">
        {{-- Donut Chart --}}
        <div class="relative w-36 h-36 sm:w-48 sm:h-48 flex-shrink-0">
            <canvas id="progressChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">Kamu sedang</p>
                <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">mempelajari {{ $inProgressCoursesCount }} dari {{ $activeCoursesCount }}</p>
                <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">kursus aktif</p>
            </div>
        </div>

        {{-- Progress Stats --}}
        <div class="flex-1 w-full space-y-4">
            {{-- Total Progress Bar --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 dark:text-gray-300 text-sm">Total kemajuan</span>
                    <span class="text-blue-500 font-bold">{{ $totalProgressValue }}%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalProgressValue }}%"></div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-3 gap-2 sm:gap-4 mt-4 sm:mt-6">
                <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                    <p class="text-lg sm:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $activeCoursesCount }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs">Kursus Aktif</p>
                </div>
                <div class="bg-green-50 dark:bg-green-500/10 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                    <p class="text-lg sm:text-2xl font-bold text-green-600 dark:text-green-400">{{ $completedCoursesCount }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs">Selesai</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-500/10 rounded-lg sm:rounded-xl p-2 sm:p-4 text-center">
                    <p class="text-lg sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCoursesCount }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-[10px] sm:text-xs">Tertunda</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Kursus yang Sedang Kamu Ikuti Section --}}
<div class="mb-4 sm:mb-6 animate-fade-in-up delay-200">
    <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mb-3 sm:mb-4">Kursus yang Sedang Kamu Ikuti</h2>

    @if(count($courses) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($courses as $index => $course)
        <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700/50 hover-lift animate-scale-in" style="animation-delay: {{ ($index + 1) * 100 }}ms;">
            <div class="h-32 sm:h-36 bg-gradient-to-br {{ $course['gradient'] }} flex items-center justify-center p-4">
                @if($course['thumbnail'])
                <img src="{{ asset('storage/' . $course['thumbnail']) }}" alt="{{ $course['name'] }}" class="w-full h-full object-cover">
                @else
                <div class="text-white text-center">
                    <p class="text-base sm:text-lg font-bold">{{ $course['name'] }}</p>
                    @if($course['code'])
                    <p class="text-sm opacity-80">{{ $course['code'] }}</p>
                    @endif
                </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1 text-sm sm:text-base">{{ $course['name'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mb-3">{{ $course['status'] }} − {{ round($course['progress']) }}%</p>
                <div class="w-full h-2 rounded-full overflow-hidden mb-4 bg-blue-100 dark:bg-blue-500/20">
                    <div class="h-full rounded-full bg-blue-500 animate-progress" style="width: {{ $course['progress'] }}%;"></div>
                </div>
                <a href="{{ !empty($course['id']) ? route('mahasiswa.course-learn', $course['id']) : route('mahasiswa.get-courses') }}" class="inline-flex items-center justify-center text-blue-500 hover:text-blue-600 text-xs sm:text-sm font-medium border border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition hover-scale">
                    Lanjutkan Kursus
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-8 text-center border border-gray-100 dark:border-gray-700/50">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400 mb-4">Kamu belum mengikuti kursus apapun.</p>
        <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition">
            Jelajahi Kursus
        </a>
    </div>
    @endif
</div>

{{-- Event yang Sedang Kamu Ikuti Section (terpisah dari kursus) --}}
{{-- Tipe: bootcamp / webinar / workshop / seminar; Mode: online / offline / hybrid. --}}
@if(count($events) > 0)
<div class="mb-4 sm:mb-6 animate-fade-in-up delay-250">
    <div class="flex items-center justify-between mb-3 sm:mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100">Event yang Sedang Kamu Ikuti</h2>
        <a href="{{ route('mahasiswa.bootcamp-saya') }}" class="text-blue-500 hover:text-blue-600 text-xs sm:text-sm font-medium transition hover-scale">Lihat Semua</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($events as $index => $event)
        <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700/50 hover-lift animate-scale-in" style="animation-delay: {{ ($index + 1) * 100 }}ms;">
            <div class="h-32 sm:h-36 bg-gradient-to-br {{ $event['gradient'] }} relative flex items-center justify-center p-4">
                @if($event['thumbnail'])
                <img src="{{ asset('storage/' . $event['thumbnail']) }}" alt="{{ $event['name'] }}" class="w-full h-full object-cover">
                @else
                <div class="text-white text-center">
                    <p class="text-base sm:text-lg font-bold">{{ $event['name'] }}</p>
                    @if($event['code'])
                    <p class="text-sm opacity-80">{{ $event['code'] }}</p>
                    @endif
                </div>
                @endif
                {{-- Tipe event chip pojok kiri-atas --}}
                <span class="absolute top-3 left-3 inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold ring-1 ring-inset {{ $event['type_class'] }}">{{ $event['type'] }}</span>
                {{-- Mode chip pojok kanan-atas --}}
                <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold ring-1 ring-inset {{ $event['mode_class'] }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        @if(strtolower($event['mode_label']) === 'Offline' || strtolower($event['mode_label']) === 'On-site')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        @endif
                    </svg>
                    {{ $event['mode_label'] }}
                </span>
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1 text-sm sm:text-base">{{ $event['name'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mb-3">Oleh {{ $event['dosen'] }}</p>
                <a href="{{ !empty($event['id']) ? route('mahasiswa.bootcamp-learn', $event['id']) : route('mahasiswa.bootcamp-saya') }}" class="inline-flex items-center justify-center text-amber-600 hover:text-amber-700 text-xs sm:text-sm font-medium border border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition hover-scale">
                    Buka Event
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Bottom Section: News & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 animate-fade-in-up delay-300">
    {{-- Berita dan Pengumuman --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 hover-lift">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-gray-100">Berita dan Pengumuman</h2>
            <a href="{{ route('mahasiswa.news') }}" class="text-blue-500 hover:text-blue-600 text-xs sm:text-sm font-medium transition hover-scale">Lihat Semua</a>
        </div>
        
        <div class="space-y-4">
            @forelse($newsItems as $item)
            <div class="flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 -mx-2 px-2 py-2 rounded-lg transition cursor-pointer">
                <div class="w-10 h-10 rounded-lg bg-{{ $item['color'] }}-100 dark:bg-{{ $item['color'] }}-500/20 flex items-center justify-center flex-shrink-0">
                    @if($item['icon'] === 'alert')
                    <svg class="w-5 h-5 text-{{ $item['color'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    @elseif($item['icon'] === 'calendar')
                    <svg class="w-5 h-5 text-{{ $item['color'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-{{ $item['color'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-gray-800 dark:text-gray-100 text-sm font-medium truncate">{{ $item['title'] }}</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $item['time'] }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">Belum ada berita terbaru.</p>
            @endforelse
        </div>
    </div>

    {{-- Agenda Kegiatan Kamu --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 hover-lift">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 dark:text-gray-100">Agenda Kegiatan Kamu</h2>
            <a href="{{ route('mahasiswa.calendar') }}" class="text-blue-500 hover:text-blue-600 text-xs sm:text-sm font-medium transition hover-scale">Lihat Semua Jadwal</a>
        </div>

        <x-agenda-desktop-notification-control :agenda="$agenda" />
        
        {{-- Mini Calendar (default view) --}}
        <div id="mini-calendar" class="mb-4">
            {{-- Calendar Header --}}
            <div class="grid grid-cols-7 gap-1 text-center text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mb-2">
                <span>Min</span>
                <span>Sen</span>
                <span>Sel</span>
                <span>Rab</span>
                <span>Kam</span>
                <span>Jum</span>
                <span>Sab</span>
            </div>
            
            {{-- Calendar Days --}}
            @php
                $today = now()->day;
                $daysInMonth = now()->daysInMonth;
                $firstDayOfWeek = now()->startOfMonth()->dayOfWeek;
                $eventDays = collect($calendarEvents)->pluck('day')->toArray();
            @endphp
            <div class="grid grid-cols-7 gap-1 text-center text-xs sm:text-sm">
                {{-- Empty cells for days before month starts --}}
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="py-1.5"></div>
                @endfor
                
                {{-- Days of the month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $event = collect($calendarEvents)->firstWhere('day', $day);
                    @endphp
                    <div class="py-1.5 rounded-lg cursor-pointer
                        @if($day === $today)
                            bg-blue-500 text-white
                        @elseif($event)
                            bg-{{ $event['color'] }}-500 text-white
                        @else
                            text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50
                        @endif
                    ">
                        {{ $day }}
                    </div>
                @endfor
            </div>
        </div>

        {{-- Event Legend --}}
        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100 dark:border-gray-700/50">
            @foreach($calendarEvents as $event)
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-{{ $event['color'] }}-500"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $event['label'] }} ({{ $event['day'] }})</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div> {{-- End dashboard-content --}}

{{-- Full Calendar View (hidden by default) --}}
<div id="calendar-view" class="hidden">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Semua Jadwal Kegiatan Kamu</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Lihat semua jadwal webinar, deadline kursus, dan workshop yang sudah dijadwalkan.</p>
        </div>
        <button onclick="toggleCalendarView()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Dashboard
        </button>
    </div>

    {{-- Main Content: 2 Column Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
        {{-- Calendar Section (Left - 2 columns) --}}
        <div class="md:col-span-2 lg:col-span-2 bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
            {{-- Calendar Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-1">
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ now()->translatedFormat('F Y') }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
                <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 transition">
                    Hari Ini
                </button>
            </div>

            {{-- Calendar Grid --}}
            <div class="overflow-x-auto">
                <div class="grid grid-cols-7 gap-1 mb-2 min-w-[420px]">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">{{ $day }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-1 min-w-[420px]">
                    @php
                        $calToday = now()->day;
                        $calDaysInMonth = now()->daysInMonth;
                        $calFirstDay = now()->startOfMonth()->dayOfWeek;
                        $calPrevMonthDays = now()->subMonth()->daysInMonth;
                        $calStartFrom = $calPrevMonthDays - $calFirstDay + 1;
                    @endphp
                    
                    @for($i = 0; $i < $calFirstDay; $i++)
                        <div class="aspect-square p-2 text-center">
                            <span class="text-gray-300 dark:text-gray-600 text-sm">{{ $calStartFrom + $i }}</span>
                        </div>
                    @endfor
                    
                    @for($day = 1; $day <= $calDaysInMonth; $day++)
                        @php
                            $calEvent = collect($calendarEvents)->firstWhere('day', $day);
                        @endphp
                        <div class="aspect-square p-1 sm:p-2 text-center relative hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg cursor-pointer {{ $day === $calToday ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}">
                            <span class="text-sm {{ $day === $calToday ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</span>
                            @if($calEvent)
                                <div class="flex justify-center gap-0.5 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $calEvent['color'] }}-500"></span>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-4 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Webinar</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Workshop</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Deadline Kelas</span>
                </div>
            </div>
        </div>

        {{-- Upcoming Events Section (Right - 1 column) --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 h-fit">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Kegiatan yang Akan Datang</h2>
            
            <div class="space-y-3">
                {{-- First 3 events --}}
                @foreach(collect($calendarEvents)->take(3) as $event)
                <div class="p-3 rounded-xl bg-{{ $event['color'] }}-50 dark:bg-{{ $event['color'] }}-500/10 border border-{{ $event['color'] }}-100 dark:border-{{ $event['color'] }}-500/20">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $event['color'] }}-500 mt-1.5 flex-shrink-0"></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tanggal {{ $event['day'] }} {{ now()->translatedFormat('F') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Extra events (hidden by default) --}}
                @if(count($calendarEvents) > 3)
                <div id="extra-calendar-events" class="hidden space-y-3">
                    @foreach(collect($calendarEvents)->skip(3) as $event)
                    <div class="p-3 rounded-xl bg-{{ $event['color'] }}-50 dark:bg-{{ $event['color'] }}-500/10 border border-{{ $event['color'] }}-100 dark:border-{{ $event['color'] }}-500/20">
                        <div class="flex items-start gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-{{ $event['color'] }}-500 mt-1.5 flex-shrink-0"></span>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['label'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tanggal {{ $event['day'] }} {{ now()->translatedFormat('F') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            @if(count($calendarEvents) > 3)
            <button onclick="toggleExtraCalendarEvents()" id="toggle-extra-events-btn" class="block w-full text-center text-blue-500 hover:text-blue-600 text-sm font-medium mt-4 transition">
                Lihat Semua
            </button>
            @endif
        </div>
    </div>
</div>  {{-- End calendar-view --}}

{{-- Notification View (hidden by default) --}}
<div id="notification-view" class="hidden">
    @php
        $dashboardNotifications = collect($dashboardNotifications ?? []);
        $dashboardNotificationUnreadCount = (int) ($dashboardNotificationUnreadCount ?? 0);
        $notificationIconStyle = static function (?string $color, float $alpha = 0.14): string {
            $safeColor = is_string($color) && preg_match('/^#?[0-9a-fA-F]{3,6}$/', trim($color))
                ? ('#' . ltrim(trim($color), '#'))
                : '#3B82F6';

            $hex = ltrim($safeColor, '#');
            if (strlen($hex) === 3) {
                $hex = collect(str_split($hex))->map(fn ($char) => $char . $char)->implode('');
            }

            if (strlen($hex) !== 6) {
                return 'background: rgba(59, 130, 246, ' . $alpha . '); color: #3B82F6;';
            }

            [$r, $g, $b] = [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];

            return 'background: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . '); color: #' . $hex . ';';
        };
    @endphp

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Notifikasi Kamu</h1>
            <span class="text-blue-500">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </span>
            @if($dashboardNotificationUnreadCount > 0)
                <span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-medium text-white">{{ $dashboardNotificationUnreadCount }}</span>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('mahasiswa.notification') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                Buka Halaman Notifikasi
            </a>
            <button onclick="toggleNotificationView()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Dashboard
            </button>
        </div>
    </div>

    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Lihat semua pemberitahuan terbaru seputar kursus, jadwal, dan aktivitas belajar kamu.</p>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
        {{-- Notifications List (Left - 2 columns) --}}
        <div class="md:col-span-2 lg:col-span-2 space-y-4">
            {{-- Search Bar --}}
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input 
                    type="text" 
                    placeholder="Cari notifikasi..." 
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-700/50 rounded-xl bg-white dark:bg-[#1f2937] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            {{-- Notification Items --}}
            @forelse($dashboardNotifications as $notif)
                <div class="notification-item bg-white dark:bg-[#1f2937] rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50 hover:shadow-md transition" data-category="{{ $notif['tipe'] }}" data-unread="{{ $notif['is_read'] ? 'false' : 'true' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="{{ $notificationIconStyle($notif['icon_color'] ?? null) }}">
                            @if(($notif['tipe'] ?? null) === 'kursus_pembelajaran')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            @elseif(($notif['tipe'] ?? null) === 'jadwal_ujian')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            @elseif(($notif['tipe'] ?? null) === 'pencapaian')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start gap-2">
                                <h3 class="text-sm text-gray-800 dark:text-gray-100 {{ ($notif['is_read'] ?? false) ? 'font-medium' : 'font-bold' }}">{{ $notif['judul'] }}</h3>
                                @if(($notif['action_variant'] ?? null) === 'certificate')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-800">
                                        Sertifikat Siap
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $notif['konten'] }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if(!empty($notif['action_url']))
                                    <a href="{{ $notif['action_url'] }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v10m0 0l-4-4m4 4l4-4M5 19h14" />
                                        </svg>
                                        {{ $notif['action_label'] ?? 'Buka' }}
                                    </a>
                                @endif
                                <a href="{{ route('mahasiswa.notification') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-3.5 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    Kelola Notifikasi
                                </a>
                            </div>
                            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ $notif['waktu_relatif'] }}</p>
                        </div>

                        @if(!($notif['is_read'] ?? false))
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-[#1f2937] dark:text-gray-400">
                    Belum ada notifikasi terbaru.
                </div>
            @endforelse
        </div>

        {{-- Filter Panel (Right - 1 column) --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700/50 h-fit">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Filter Notifikasi</h2>
            
            <div class="space-y-2">
                <button onclick="setActiveFilter(this)" data-filter="all" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium">
                    Semua Notifikasi
                </button>
                <button onclick="setActiveFilter(this)" data-filter="unread" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                    Belum Dibaca
                </button>
                <button onclick="setActiveFilter(this)" data-filter="kursus_pembelajaran" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                    Kursus & Pembelajaran
                </button>
                <button onclick="setActiveFilter(this)" data-filter="jadwal_ujian" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                    Jadwal & Ujian
                </button>
                <button onclick="setActiveFilter(this)" data-filter="pencapaian" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                    Pencapaian
                </button>
            </div>

            <button onclick="applyFilter()" class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium transition">
                Terapkan Filter
            </button>
        </div>
    </div>
</div>

<script>
    function toggleCalendarView() {
        const dashboardContent = document.getElementById('dashboard-content');
        const calendarView = document.getElementById('calendar-view');
        const notificationView = document.getElementById('notification-view');
        
        // Hide notification view if visible
        notificationView.classList.add('hidden');
        
        if (calendarView.classList.contains('hidden')) {
            dashboardContent.classList.add('hidden');
            calendarView.classList.remove('hidden');
        } else {
            dashboardContent.classList.remove('hidden');
            calendarView.classList.add('hidden');
        }
    }

    function toggleNotificationView() {
        const dashboardContent = document.getElementById('dashboard-content');
        const calendarView = document.getElementById('calendar-view');
        const notificationView = document.getElementById('notification-view');
        
        // Hide calendar view if visible
        calendarView.classList.add('hidden');
        
        if (notificationView.classList.contains('hidden')) {
            dashboardContent.classList.add('hidden');
            notificationView.classList.remove('hidden');
        } else {
            dashboardContent.classList.remove('hidden');
            notificationView.classList.add('hidden');
        }
    }

    function toggleExtraCalendarEvents() {
        const extraEvents = document.getElementById('extra-calendar-events');
        const toggleBtn = document.getElementById('toggle-extra-events-btn');
        
        if (extraEvents.classList.contains('hidden')) {
            extraEvents.classList.remove('hidden');
            toggleBtn.textContent = 'Sembunyikan';
        } else {
            extraEvents.classList.add('hidden');
            toggleBtn.textContent = 'Lihat Semua';
        }
    }

    function setActiveFilter(button) {
        // Remove active state from all filter buttons
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white', 'font-medium');
            btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
        });
        
        // Add active state to clicked button
        button.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
        button.classList.add('bg-blue-500', 'text-white', 'font-medium');
    }

    function applyFilter() {
        // Get currently selected filter
        const activeBtn = document.querySelector('.filter-btn.bg-blue-500');
        const filterType = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        
        // Get all notification items
        const notifications = document.querySelectorAll('.notification-item');
        
        notifications.forEach(notif => {
            const category = notif.getAttribute('data-category');
            const isUnread = notif.getAttribute('data-unread') === 'true';
            
            let shouldShow = false;
            
            if (filterType === 'all') {
                shouldShow = true;
            } else if (filterType === 'unread') {
                shouldShow = isUnread;
            } else {
                shouldShow = category === filterType;
            }
            
            if (shouldShow) {
                notif.classList.remove('hidden');
            } else {
                notif.classList.add('hidden');
            }
        });
    }
</script>

@push('scripts')
<script>
    // Donut Chart with real data from backend
    const ctx = document.getElementById('progressChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Dalam Progress'],
            datasets: [{
                data: [{{ $totalProgressValue }}, {{ 100 - $totalProgressValue }}],
                backgroundColor: [
                    '#3B82F6', // Blue
                    isDark ? '#374151' : '#E5E7EB'
                ],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });
</script>
@endpush

</x-layouts.dashboard>
