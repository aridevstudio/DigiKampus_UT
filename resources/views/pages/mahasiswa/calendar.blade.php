<x-layouts.dashboard :active="'calendar'">
@php
$user = Auth::guard('mahasiswa')->user();
$userName = $user?->name ?? 'Mahasiswa';

// Use data from controller or default to current month
$month = $currentMonth ?? now()->month;
$year = $currentYear ?? now()->year;
$monthDate = \Carbon\Carbon::createFromDate($year, $month, 1);
$monthName = $monthDate->translatedFormat('F Y');
$today = now()->day;
$daysInMonth = $monthDate->daysInMonth;
$firstDayOfWeek = $monthDate->startOfMonth()->dayOfWeek;

// Build events from agenda (from controller)
$events = [];
if (isset($agenda) && count($agenda) > 0) {
    foreach ($agenda as $item) {
        $events[] = [
            'day' => $item->tanggal->day,
            'type' => $item->tipe ?? 'webinar',
            'title' => $item->judul,
            'date' => $item->tanggal->translatedFormat('d F'),
            'time' => $item->waktu_mulai ?? '09.00 WIB',
        ];
    }
}

// Build upcoming events from controller
$upcomingEvents = [];
if (isset($upcomingAgenda) && count($upcomingAgenda) > 0) {
    foreach ($upcomingAgenda as $item) {
        $upcomingEvents[] = [
            'day' => $item->tanggal->day,
            'type' => $item->tipe ?? 'webinar',
            'title' => $item->judul,
            'date' => $item->tanggal->translatedFormat('d F'),
            'time' => $item->waktu_mulai ?? '09.00 WIB',
        ];
    }
}

$eventDays = collect($events)->pluck('day')->toArray();

$eventColors = [
    'webinar' => 'blue',
    'workshop' => 'green',
    'deadline' => 'rose',
    'quiz' => 'yellow',
];
@endphp

{{-- Header Section --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Semua Jadwal Kegiatan Kamu</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Lihat semua jadwal webinar, deadline kursus, dan workshop yang sudah dijadwalkan.</p>
    </div>
    <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Dashboard
    </a>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Calendar Section --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
        {{-- Calendar Header --}}
        @php
            $prevMonth = $month == 1 ? 12 : $month - 1;
            $prevYear = $month == 1 ? $year - 1 : $year;
            $nextMonth = $month == 12 ? 1 : $month + 1;
            $nextYear = $month == 12 ? $year + 1 : $year;
        @endphp
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('mahasiswa.calendar', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="flex items-center gap-1">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $monthName }}</span>
                </div>
                <a href="{{ route('mahasiswa.calendar', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
            <a href="{{ route('mahasiswa.calendar') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 transition">
                Hari Ini
            </a>
        </div>

        {{-- Calendar Grid --}}
        <div class="overflow-x-auto">
            {{-- Days Header --}}
            <div class="grid grid-cols-7 gap-1 mb-2 min-w-[500px]">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">
                    {{ $day }}
                </div>
                @endforeach
            </div>

            {{-- Calendar Days --}}
            <div class="grid grid-cols-7 gap-1 min-w-[500px]">
                {{-- Previous month days --}}
                @php
                    $prevMonthDays = now()->subMonth()->daysInMonth;
                    $startFrom = $prevMonthDays - $firstDayOfWeek + 1;
                @endphp
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="aspect-square p-2 text-center">
                        <span class="text-gray-300 dark:text-gray-600 text-sm">{{ $startFrom + $i }}</span>
                    </div>
                @endfor
                
                {{-- Current month days --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dayEvents = collect($events)->where('day', $day);
                        $hasEvent = $dayEvents->count() > 0;
                    @endphp
                    <div class="aspect-square p-1 sm:p-2 text-center relative hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg cursor-pointer transition {{ $day === $today ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}">
                        <span class="text-sm {{ $day === $today ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</span>
                        @if($hasEvent)
                            <div class="flex justify-center gap-0.5 mt-1">
                                @foreach($dayEvents as $event)
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $eventColors[$event['type']] }}-500"></span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor

                {{-- Next month days --}}
                @php
                    $remainingCells = 42 - ($firstDayOfWeek + $daysInMonth);
                    if ($remainingCells > 7) $remainingCells -= 7;
                @endphp
                @for($i = 1; $i <= $remainingCells; $i++)
                    <div class="aspect-square p-2 text-center">
                        <span class="text-gray-300 dark:text-gray-600 text-sm">{{ $i }}</span>
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
                <span class="text-sm text-gray-600 dark:text-gray-400">Deadline</span>
            </div>
        </div>
    </div>

    {{-- Upcoming Events Section --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 h-fit">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Kegiatan Bulan Ini</h2>
        
        <div class="space-y-3">
            @forelse($upcomingEvents as $index => $event)
                @php
                    $color = $eventColors[$event['type']] ?? 'blue';
                @endphp
                @if($index < 3)
                <div class="p-3 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 border border-{{ $color }}-100 dark:border-{{ $color }}-500/20">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500 mt-1.5 flex-shrink-0"></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event['date'] }} - {{ $event['time'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <p class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">Belum ada kegiatan bulan ini.</p>
            @endforelse

            {{-- Hidden extra events (shown on click) --}}
            @if(count($upcomingEvents) > 3)
            <div id="extra-events" class="hidden space-y-3">
                @foreach(collect($upcomingEvents)->skip(3) as $event)
                    @php
                        $color = $eventColors[$event['type']] ?? 'blue';
                    @endphp
                <div class="p-3 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 border border-{{ $color }}-100 dark:border-{{ $color }}-500/20">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500 mt-1.5 flex-shrink-0"></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event['date'] }} - {{ $event['time'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if(count($upcomingEvents) > 3)
        <button onclick="toggleExtraEvents()" id="toggle-events-btn" class="block w-full text-center text-blue-500 hover:text-blue-600 text-sm font-medium mt-4 transition">
            Lihat Semua
        </button>
        <script>
            function toggleExtraEvents() {
                const extraEvents = document.getElementById('extra-events');
                const toggleBtn = document.getElementById('toggle-events-btn');
                
                if (extraEvents.classList.contains('hidden')) {
                    extraEvents.classList.remove('hidden');
                    toggleBtn.textContent = 'Sembunyikan';
                } else {
                    extraEvents.classList.add('hidden');
                    toggleBtn.textContent = 'Lihat Semua';
                }
            }
        </script>
        @endif
    </div>
</div>

</x-layouts.dashboard>

