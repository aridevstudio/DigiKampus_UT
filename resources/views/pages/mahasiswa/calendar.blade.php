<x-layouts.dashboard :active="'calendar'">
@php
$user = Auth::guard('mahasiswa')->user();
$userName = $user?->name ?? 'Mahasiswa';

// Current month data
$currentMonth = now()->month;
$currentYear = now()->year;
$monthName = now()->translatedFormat('F Y');
$today = now()->day;
$daysInMonth = now()->daysInMonth;
$firstDayOfWeek = now()->startOfMonth()->dayOfWeek;

// Dummy events data
$events = [
    ['day' => 4, 'type' => 'webinar', 'title' => 'Webinar Akuntansi', 'date' => '10 Juni', 'time' => '10.00 WIB'],
    ['day' => 16, 'type' => 'workshop', 'title' => 'Workshop DKV', 'date' => '11 Desember', 'time' => '10.00 WIB'],
    ['day' => 18, 'type' => 'workshop', 'title' => 'Workshop Python', 'date' => '18 Desember', 'time' => '14.00 WIB'],
    ['day' => 22, 'type' => 'deadline', 'title' => 'Deadline Kelas Python', 'date' => '15 Desember', 'time' => '09.00 WIB'],
];

$eventDays = collect($events)->pluck('day')->toArray();

$eventColors = [
    'webinar' => 'blue',
    'workshop' => 'green',
    'deadline' => 'yellow',
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
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-1">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $monthName }}</span>
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
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Deadline Kelas</span>
            </div>
        </div>
    </div>

    {{-- Upcoming Events Section --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 h-fit">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Kegiatan yang Akan Datang</h2>
        
        <div class="space-y-3">
            {{-- First 3 events (always visible) --}}
            @foreach(collect($events)->take(3) as $event)
            <div class="p-3 rounded-xl bg-{{ $eventColors[$event['type']] }}-50 dark:bg-{{ $eventColors[$event['type']] }}-500/10 border border-{{ $eventColors[$event['type']] }}-100 dark:border-{{ $eventColors[$event['type']] }}-500/20">
                <div class="flex items-start gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $eventColors[$event['type']] }}-500 mt-1.5 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['title'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event['date'] }} - {{ $event['time'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Hidden extra events (shown on click) --}}
            @if(count($events) > 3)
            <div id="extra-events" class="hidden space-y-3">
                @foreach(collect($events)->skip(3) as $event)
                <div class="p-3 rounded-xl bg-{{ $eventColors[$event['type']] }}-50 dark:bg-{{ $eventColors[$event['type']] }}-500/10 border border-{{ $eventColors[$event['type']] }}-100 dark:border-{{ $eventColors[$event['type']] }}-500/20">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $eventColors[$event['type']] }}-500 mt-1.5 flex-shrink-0"></span>
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

        @if(count($events) > 3)
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
