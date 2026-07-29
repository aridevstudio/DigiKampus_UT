<x-layouts.dashboard :active="'calendar'">
@php
$user = Auth::guard('mahasiswa')->user();
$userName = $user?->name ?? 'Mahasiswa';

// Use data from controller or default to current month
$month = $currentMonth ?? now()->month;
$year = $currentYear ?? now()->year;
$monthDate = \Carbon\Carbon::createFromDate((int) $year, (int) $month, 1);
$monthName = $monthDate->translatedFormat('F Y');

$isCurrentMonthYear = ($month == now()->month && $year == now()->year);
$today = $isCurrentMonthYear ? now()->day : null;

$daysInMonth = $monthDate->daysInMonth;
$firstDayOfWeek = $monthDate->startOfMonth()->dayOfWeek;

$courseTimeline = $courseTimeline ?? collect();
$upcomingReminders = $upcomingReminders ?? collect();

$colorStyles = [
    'akademik' => [
        'badge' => 'bg-indigo-600 text-white',
        'dot' => 'bg-indigo-600',
        'bg' => 'bg-indigo-50 dark:bg-indigo-500/10',
        'border' => 'border-indigo-100 dark:border-indigo-500/20',
        'text' => 'text-indigo-700 dark:text-indigo-300',
        'label' => 'Akademik',
    ],
    'course' => [
        'badge' => 'bg-blue-600 text-white',
        'dot' => 'bg-blue-600',
        'bg' => 'bg-blue-50 dark:bg-blue-500/10',
        'border' => 'border-blue-100 dark:border-blue-500/20',
        'text' => 'text-blue-700 dark:text-blue-300',
        'label' => 'Course',
    ],
    'deadline' => [
        'badge' => 'bg-rose-600 text-white',
        'dot' => 'bg-rose-600',
        'bg' => 'bg-rose-50 dark:bg-rose-500/10',
        'border' => 'border-rose-100 dark:border-rose-500/20',
        'text' => 'text-rose-700 dark:text-rose-300',
        'label' => 'Deadline',
    ],
    'quiz' => [
        'badge' => 'bg-amber-600 text-white',
        'dot' => 'bg-amber-600',
        'bg' => 'bg-amber-50 dark:bg-amber-500/10',
        'border' => 'border-amber-100 dark:border-amber-500/20',
        'text' => 'text-amber-700 dark:text-amber-300',
        'label' => 'Quiz',
    ],
    'bootcamp' => [
        'badge' => 'bg-emerald-600 text-white',
        'dot' => 'bg-emerald-600',
        'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',
        'border' => 'border-emerald-100 dark:border-emerald-500/20',
        'text' => 'text-emerald-700 dark:text-emerald-300',
        'label' => 'Bootcamp',
    ],
    'event' => [
        'badge' => 'bg-purple-600 text-white',
        'dot' => 'bg-purple-600',
        'bg' => 'bg-purple-50 dark:bg-purple-500/10',
        'border' => 'border-purple-100 dark:border-purple-500/20',
        'text' => 'text-purple-700 dark:text-purple-300',
        'label' => 'Event',
    ],
];

// Build events array from agenda collection
$events = [];
if (isset($agenda) && count($agenda) > 0) {
    foreach ($agenda as $item) {
        $type = $item->tipe ?? 'akademik';
        $startTime = $item->waktu_mulai ?? null;
        $endTime = $item->waktu_selesai ?? null;
        $timeString = $startTime ? ($endTime ? $startTime . ' - ' . $endTime . ' WIB' : $startTime . ' WIB') : '08:00 WIB';

        $events[] = [
            'id' => (string) ($item->id_agenda ?? 'agenda-' . rand(100,999)),
            'day' => (int) $item->tanggal->day,
            'type' => $type,
            'title' => $item->judul,
            'description' => $item->deskripsi ?? 'Tidak ada catatan tambahan.',
            'course' => $item->nama_course ?? null,
            'date' => $item->tanggal->translatedFormat('l, d F Y'),
            'date_short' => $item->tanggal->translatedFormat('d F'),
            'time' => $timeString,
            'link' => $item->link ?? null,
            'source' => $item->source ?? 'Sistem',
        ];
    }
}

$upcomingEvents = $events;
$eventDays = collect($events)->pluck('day')->toArray();
@endphp

<div class="w-full space-y-6 animate-fade-in">

    {{-- Header Section (Desain Existing Dipelihara) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 text-xs font-bold uppercase tracking-wider">
                    Learning Calendar & Activity Tracker
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Jadwal & Activity Tracker Pembelajaran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau jadwal perkuliahan, timeline course, deadline tugas, kuis, dan sesi live bootcamp secara real-time (WIB).</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openAddAgendaModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition text-sm font-bold shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Agenda Pribadi
            </button>
            <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-semibold shadow-xs">
                Dashboard
            </a>
        </div>
    </div>

    {{-- Desktop Agenda Notification Component Existing --}}
    <x-agenda-desktop-notification-control :agenda="$upcomingAgenda" />

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl flex items-center gap-2.5 text-sm text-emerald-700 dark:text-emerald-300 font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- 1. COURSE TIMELINE SECTION (Integrasi Data Enrollment Real) --}}
    @if(count($courseTimeline) > 0)
        <section class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 sm:p-6 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>🚀 Course Timeline (Sedang Diikuti)</span>
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ count($courseTimeline) }} Course Aktif</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($courseTimeline as $courseItem)
                    <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-700/70 bg-gray-50/50 dark:bg-gray-800/50 space-y-3 flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                                    {{ $courseItem['is_bootcamp'] ? 'Bootcamp' : 'Kursus' }}
                                </span>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ $courseItem['progress'] }}% Selesai</span>
                            </div>
                            <h3 class="font-bold text-sm text-gray-900 dark:text-white line-clamp-1">{{ $courseItem['nama_course'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pengajar: {{ $courseItem['dosen_name'] }}</p>
                        </div>

                        <div class="space-y-2">
                            <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all" style="width: {{ $courseItem['progress'] }}%"></div>
                            </div>
                            <a href="{{ $courseItem['learn_url'] }}" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-blue-600 dark:text-blue-300 hover:bg-blue-50 transition">
                                Lanjut Belajar ➔
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 2. URGENT DEADLINE REMINDER BOX (7 Hari Ke Depan) --}}
    @if(count($upcomingReminders) > 0)
        <div class="p-4 sm:p-5 rounded-2xl border border-rose-200 dark:border-rose-800/40 bg-gradient-to-r from-rose-50/80 via-white to-amber-50/80 dark:from-rose-950/20 dark:via-gray-800 dark:to-amber-950/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-xs">
                    ⏰
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Upcoming Deadline & Reminders (7 Hari Ke Depan)</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">Ada {{ count($upcomingReminders) }} agenda/deadline terdekat yang memerlukan perhatian Anda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                @foreach($upcomingReminders as $rem)
                    @php $remStyle = $colorStyles[$rem->tipe] ?? $colorStyles['akademik']; @endphp
                    <div class="px-3 py-2 rounded-xl {{ $remStyle['bg'] }} border {{ $remStyle['border'] }} text-xs min-w-[160px] flex-shrink-0 cursor-pointer" onclick="openEventDetailModal({{ json_encode($rem) }})">
                        <span class="font-bold block truncate {{ $remStyle['text'] }}">{{ $rem->judul }}</span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 block mt-0.5">📅 {{ $rem->tanggal->translatedFormat('d F') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 3. MAIN CALENDAR GRID & ACTIVITY LIST --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Calendar Grid Component --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-7 shadow-xs border border-gray-100 dark:border-gray-700/50">
            @php
                $prevMonth = $month == 1 ? 12 : $month - 1;
                $prevYear = $month == 1 ? $year - 1 : $year;
                $nextMonth = $month == 12 ? 1 : $month + 1;
                $nextYear = $month == 12 ? $year + 1 : $year;
            @endphp
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <a href="{{ route('mahasiswa.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition" title="Bulan Sebelumnya">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-gray-800 dark:text-gray-100 text-base sm:text-lg">{{ $monthName }}</span>
                    </div>
                    <a href="{{ route('mahasiswa.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition" title="Bulan Berikutnya">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
                <a href="{{ route('mahasiswa.calendar') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 transition">
                    Hari Ini
                </a>
            </div>

            {{-- Days Header --}}
            <div class="overflow-x-auto">
                <div class="grid grid-cols-7 gap-1 mb-2 min-w-[480px]">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="text-center text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-400 py-2">
                        {{ $day }}
                    </div>
                    @endforeach
                </div>

                {{-- Month Grid Days --}}
                <div class="grid grid-cols-7 gap-1 min-w-[480px]">
                    @php
                        $prevMonthDays = now()->subMonth()->daysInMonth;
                        $startFrom = $prevMonthDays - $firstDayOfWeek + 1;
                    @endphp
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="aspect-square p-2 text-center opacity-30">
                            <span class="text-gray-400 text-sm">{{ $startFrom + $i }}</span>
                        </div>
                    @endfor
                    
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dayEvents = collect($events)->where('day', $day);
                            $hasEvent = $dayEvents->count() > 0;
                            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        @endphp
                        <div id="calendar-day-{{ $day }}" onclick="filterEvents({{ $day }}, '{{ $dateStr }}')" class="calendar-day-cell aspect-square p-1 sm:p-2 text-center relative hover:bg-blue-50/50 dark:hover:bg-blue-500/10 rounded-xl cursor-pointer transition {{ $day === $today ? 'bg-blue-50 dark:bg-blue-500/15 border-2 border-blue-500 dark:border-blue-400' : 'border border-transparent' }}">
                            <span class="text-sm font-semibold {{ $day === $today ? 'font-black text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</span>
                            @if($hasEvent)
                                <div class="flex justify-center gap-1 mt-1 flex-wrap">
                                    @foreach($dayEvents as $event)
                                        @php $style = $colorStyles[$event['type']] ?? $colorStyles['akademik']; @endphp
                                        <span class="w-2 h-2 rounded-full {{ $style['dot'] }}"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endfor

                    @php
                        $remainingCells = 42 - ($firstDayOfWeek + $daysInMonth);
                        if ($remainingCells > 7) $remainingCells -= 7;
                    @endphp
                    @for($i = 1; $i <= $remainingCells; $i++)
                        <div class="aspect-square p-2 text-center opacity-30">
                            <span class="text-gray-400 text-sm">{{ $i }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Legend Kategori Warna --}}
            <div class="flex flex-wrap gap-4 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/50 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Akademik</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Course</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Deadline</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Quiz</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Bootcamp</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Event</span>
                </div>
            </div>
        </div>

        {{-- Activity List & Category Pills Filter --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-xs border border-gray-100 dark:border-gray-700/50 h-fit space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Aktivitas Bulan Ini</h2>
                <span id="active-filter-badge" class="hidden text-xs bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 font-semibold px-2.5 py-0.5 rounded-full"></span>
            </div>

            {{-- Filter Kategori Pills (Sesuai Spesifikasi Prompt) --}}
            <div class="flex flex-wrap gap-1.5">
                <button type="button" onclick="filterByType('semua')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white transition" data-type="semua">Semua</button>
                <button type="button" onclick="filterByType('akademik')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-indigo-100 transition" data-type="akademik">Akademik</button>
                <button type="button" onclick="filterByType('course')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-blue-100 transition" data-type="course">Course</button>
                <button type="button" onclick="filterByType('deadline')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-rose-100 transition" data-type="deadline">Deadline</button>
                <button type="button" onclick="filterByType('bootcamp')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-emerald-100 transition" data-type="bootcamp">Bootcamp</button>
                <button type="button" onclick="filterByType('event')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-purple-100 transition" data-type="event">Event</button>
            </div>
            
            {{-- List Aktivitas --}}
            <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1" id="upcoming-events-list">
                @forelse($upcomingEvents as $index => $event)
                    @php
                        $style = $colorStyles[$event['type']] ?? $colorStyles['akademik'];
                    @endphp
                    <div class="upcoming-event-item p-3.5 rounded-2xl {{ $style['bg'] }} border {{ $style['border'] }} cursor-pointer transition hover:shadow-md" 
                         data-day="{{ $event['day'] }}"
                         data-type="{{ $event['type'] }}"
                         onclick="openEventDetailModal({{ json_encode($event) }})">
                        <div class="flex items-start gap-3">
                            <span class="w-2.5 h-2.5 rounded-full {{ $style['dot'] }} mt-1.5 flex-shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-gray-900 dark:text-gray-50 text-sm truncate">{{ $event['title'] }}</p>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $style['text'] }}">{{ $style['label'] }}</span>
                                </div>
                                @if($event['course'])
                                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mt-0.5 truncate">{{ $event['course'] }}</p>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <span>📅 {{ $event['date_short'] }}</span>
                                    <span>⏰ {{ $event['time'] }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p id="empty-events-msg" class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">Belum ada aktivitas pembelajaran bulan ini.</p>
                @endforelse
                <p id="empty-filtered-msg" class="hidden text-gray-500 dark:text-gray-400 text-center py-6 text-sm">Tidak ada aktivitas pada tanggal/kategori ini.</p>
            </div>
        </div>
    </div>

</div>

{{-- Add Agenda Modal (Fungsi Agenda Pribadi Existing Tetap Utuh) --}}
<div id="addAgendaModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddAgendaModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative mx-3 sm:mx-0 bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full">
            <form action="{{ route('mahasiswa.calendar.store') }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Tambah Agenda Pribadi</h3>
                        <button type="button" onclick="closeAddAgendaModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Agenda</label>
                            <input type="text" name="judul" required class="w-full bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100" placeholder="Contoh: Belajar modul Javascript & kuis">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori Agenda</label>
                            <select name="tipe" required class="w-full bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                                <option value="webinar">Event / Sesi Online (Biru)</option>
                                <option value="deadline">Assignment / Tugas (Merah)</option>
                                <option value="workshop">Bootcamp / Workshop (Kuning)</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                                <input type="date" name="tanggal" required class="w-full bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Jam</label>
                                <input type="time" name="waktu_mulai" required class="w-full bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan</label>
                            <textarea name="deskripsi" rows="2" class="w-full bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-100"></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeAddAgendaModal()" class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-sm">
                        Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Event Detail Modal (Sesuai Spesifikasi Prompt) --}}
<div id="eventDetailModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeEventDetailModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative mx-3 sm:mx-0 bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full p-6">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <span id="detail-event-type-badge" class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full mb-2 bg-blue-600 text-white"></span>
                    <h3 id="detail-event-title" class="text-xl font-bold text-gray-900 dark:text-gray-50"></h3>
                </div>
                <button type="button" onclick="closeEventDetailModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3.5 text-sm text-gray-600 dark:text-gray-300">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Sumber Aktivitas</p>
                    <p id="detail-event-source" class="font-semibold text-gray-800 dark:text-gray-200 capitalize"></p>
                </div>

                <div id="detail-event-course-container" class="hidden">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Mata Kuliah / Bootcamp</p>
                    <p id="detail-event-course" class="font-bold text-blue-600 dark:text-blue-400"></p>
                </div>

                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Waktu & Tanggal (WIB)</p>
                    <p id="detail-event-date-time" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                </div>

                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Catatan / Deskripsi Aktivitas</p>
                    <p id="detail-event-description" class="leading-relaxed bg-slate-50 dark:bg-gray-700/50 p-3 rounded-xl mt-1 text-gray-700 dark:text-gray-200"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <button type="button" onclick="closeEventDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-xl">Tutup</button>
                <a id="detail-event-link" href="#" class="hidden px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Buka Halaman Terkait ➔</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddAgendaModal() {
        document.getElementById('addAgendaModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeAddAgendaModal() {
        document.getElementById('addAgendaModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openEventDetailModal(event) {
        document.getElementById('detail-event-title').innerText = event.title;
        document.getElementById('detail-event-date-time').innerText = event.date + ' • ' + event.time;
        document.getElementById('detail-event-description').innerText = event.description || 'Tidak ada catatan tambahan.';

        const typeBadge = document.getElementById('detail-event-type-badge');
        typeBadge.innerText = (event.type || 'akademik').toUpperCase();

        const sourceEl = document.getElementById('detail-event-source');
        sourceEl.innerText = (event.source || 'sistem').replace('_', ' ');

        const courseContainer = document.getElementById('detail-event-course-container');
        if (event.course) {
            document.getElementById('detail-event-course').innerText = event.course;
            courseContainer.classList.remove('hidden');
        } else {
            courseContainer.classList.add('hidden');
        }

        const linkBtn = document.getElementById('detail-event-link');
        if (event.link) {
            linkBtn.href = event.link;
            linkBtn.classList.remove('hidden');
        } else {
            linkBtn.classList.add('hidden');
        }

        document.getElementById('eventDetailModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEventDetailModal() {
        document.getElementById('eventDetailModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    let activeDay = null;
    let activeType = 'semua';

    function filterByType(type) {
        activeType = type;
        
        document.querySelectorAll('.type-filter-btn').forEach(btn => {
            if (btn.dataset.type === type) {
                btn.className = 'type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white transition';
            } else {
                btn.className = 'type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-blue-100 transition';
            }
        });

        applyFilters();
    }

    function filterEvents(day, dateStr) {
        if (activeDay === day) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-gray-800');
            }
            activeDay = null;
            applyFilters();
            return;
        }

        if (activeDay) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-gray-800');
            }
        }

        const newEl = document.getElementById('calendar-day-' + day);
        if (newEl) {
            newEl.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-gray-800');
        }
        
        activeDay = day;
        applyFilters();
    }

    function applyFilters() {
        const eventItems = document.querySelectorAll('.upcoming-event-item');
        let visibleCount = 0;

        eventItems.forEach(item => {
            const matchesDay = (activeDay === null || parseInt(item.dataset.day) === activeDay);
            const matchesType = (activeType === 'semua' || item.dataset.type === activeType);

            if (matchesDay && matchesType) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const emptyMsg = document.getElementById('empty-events-msg');
        const emptyFilteredMsg = document.getElementById('empty-filtered-msg');
        
        if (emptyMsg) emptyMsg.classList.add('hidden');
        if (visibleCount === 0) {
            if (emptyFilteredMsg) emptyFilteredMsg.classList.remove('hidden');
        } else {
            if (emptyFilteredMsg) emptyFilteredMsg.classList.hidden = true;
        }
    }
</script>
@endpush

</x-layouts.dashboard>
