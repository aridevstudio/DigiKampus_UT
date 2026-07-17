<x-layouts.dashboard :active="'calendar'">
@php
$user = Auth::guard('mahasiswa')->user();
$userName = $user?->name ?? 'Mahasiswa';

// Use data from controller or default to current month
$month = $currentMonth ?? now()->month;
$year = $currentYear ?? now()->year;
$monthDate = \Carbon\Carbon::createFromDate($year, $month, 1);
$monthName = $monthDate->translatedFormat('F Y');

$isCurrentMonthYear = ($month == now()->month && $year == now()->year);
$today = $isCurrentMonthYear ? now()->day : null;

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
    <div class="flex items-center gap-3">
        <button onclick="openAddAgendaModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Jadwal
        </button>
        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span class="hidden sm:inline">Kembali</span>
        </a>
    </div>
</div>

<x-agenda-desktop-notification-control :agenda="$upcomingAgenda" />

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
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    @endphp
                    <div id="calendar-day-{{ $day }}" onclick="filterEvents({{ $day }}, '{{ $dateStr }}')" class="calendar-day-cell aspect-square p-1 sm:p-2 text-center relative hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg cursor-pointer transition {{ $day === $today ? 'bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30' : '' }}">
                        <span class="text-sm {{ $day === $today ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</span>
                        @if($hasEvent)
                            <div class="flex justify-center gap-0.5 mt-1 flex-wrap">
                                @foreach($dayEvents as $event)
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $eventColors[$event['type']] ?? 'blue' }}-500"></span>
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
        
        <div class="space-y-3" id="upcoming-events-list">
            @forelse($upcomingEvents as $index => $event)
                @php
                    $color = $eventColors[$event['type']] ?? 'blue';
                @endphp
                <div class="upcoming-event-item p-3 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 border border-{{ $color }}-100 dark:border-{{ $color }}-500/20" data-day="{{ $event['day'] }}">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $color }}-500 mt-1.5 flex-shrink-0"></span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $event['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event['date'] }} - {{ $event['time'] }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p id="empty-events-msg" class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">Belum ada kegiatan bulan ini.</p>
            @endforelse
            <p id="empty-filtered-msg" class="hidden text-gray-500 dark:text-gray-400 text-center py-4 text-sm">Tidak ada kegiatan pada tanggal ini.</p>
        </div>
    </div>
</div>

{{-- Add Agenda Modal --}}
<div id="addAgendaModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeAddAgendaModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative mx-3 sm:mx-0 bg-white dark:bg-[#1f2937] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full">
            <form action="{{ route('mahasiswa.calendar.store') }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Tambah Jadwal Pribadi</h3>
                        <button type="button" onclick="closeAddAgendaModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Kegiatan</label>
                            <input type="text" name="judul" required class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100" placeholder="Contoh: Belajar Javascript dasar">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Kegiatan</label>
                            <select name="tipe" required class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100">
                                <option value="webinar">Pribadi (Biru)</option>
                                <option value="deadline">Penting (Merah)</option>
                                <option value="workshop">Tugas (Kuning)</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                                <input type="date" name="tanggal" required class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam</label>
                                <input type="time" name="waktu_mulai" required class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (Opsional)</label>
                            <textarea name="deskripsi" rows="2" class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100"></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeAddAgendaModal()" class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-xl hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
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

    let activeDay = null;

    function filterEvents(day, dateStr) {
        // Toggle off if clicking the same active day
        if (activeDay === day) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
            }
            activeDay = null;
            
            // Show all events (max 3 normally, but let's just remove 'hidden' and let toggle logic handle it, actually since we hid them with JS, we just unhide all)
            const eventItems = document.querySelectorAll('.upcoming-event-item');
            let visibleCount = 0;
            eventItems.forEach(item => {
                item.classList.remove('hidden');
                visibleCount++;
            });
            
            const emptyMsg = document.getElementById('empty-events-msg');
            const emptyFilteredMsg = document.getElementById('empty-filtered-msg');
            if (emptyFilteredMsg) emptyFilteredMsg.classList.add('hidden');
            if (visibleCount === 0 && emptyMsg) {
                emptyMsg.classList.remove('hidden');
            }
            return;
        }

        // Reset old active day
        if (activeDay) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
            }
        }

        // Set new active day
        const newEl = document.getElementById('calendar-day-' + day);
        if (newEl) {
            newEl.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
        }
        
        activeDay = day;

        // Filter events
        const eventItems = document.querySelectorAll('.upcoming-event-item');
        let visibleCount = 0;
        
        eventItems.forEach(item => {
            if (parseInt(item.dataset.day) === day) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        // Toggle empty messages
        const emptyMsg = document.getElementById('empty-events-msg');
        const emptyFilteredMsg = document.getElementById('empty-filtered-msg');
        
        if (emptyMsg) emptyMsg.classList.add('hidden');
        
        if (visibleCount === 0) {
            if (emptyFilteredMsg) emptyFilteredMsg.classList.remove('hidden');
        } else {
            if (emptyFilteredMsg) emptyFilteredMsg.classList.add('hidden');
        }
    }
</script>
@endpush

</x-layouts.dashboard>

