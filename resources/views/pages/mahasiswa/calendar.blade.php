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

$colorStyles = [
    'webinar' => [
        'badge' => 'bg-blue-500 text-white',
        'dot' => 'bg-blue-500',
        'bg' => 'bg-blue-50 dark:bg-blue-500/10',
        'border' => 'border-blue-100 dark:border-blue-500/20',
        'text' => 'text-blue-700 dark:text-blue-300',
        'label' => 'Webinar / Sesi Online',
    ],
    'workshop' => [
        'badge' => 'bg-emerald-500 text-white',
        'dot' => 'bg-emerald-500',
        'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',
        'border' => 'border-emerald-100 dark:border-emerald-500/20',
        'text' => 'text-emerald-700 dark:text-emerald-300',
        'label' => 'Workshop / Live Class',
    ],
    'deadline' => [
        'badge' => 'bg-rose-500 text-white',
        'dot' => 'bg-rose-500',
        'bg' => 'bg-rose-50 dark:bg-rose-500/10',
        'border' => 'border-rose-100 dark:border-rose-500/20',
        'text' => 'text-rose-700 dark:text-rose-300',
        'label' => 'Deadline Tugas',
    ],
    'quiz' => [
        'badge' => 'bg-amber-500 text-white',
        'dot' => 'bg-amber-500',
        'bg' => 'bg-amber-50 dark:bg-amber-500/10',
        'border' => 'border-amber-100 dark:border-amber-500/20',
        'text' => 'text-amber-700 dark:text-amber-300',
        'label' => 'Quiz',
    ],
];

// Build events from agenda (from controller)
$events = [];
if (isset($agenda) && count($agenda) > 0) {
    foreach ($agenda as $item) {
        $type = $item->tipe ?? 'webinar';
        $startTime = $item->waktu_mulai ?? null;
        $endTime = $item->waktu_selesai ?? null;
        $timeString = $startTime ? ($endTime ? $startTime . ' - ' . $endTime . ' WIB' : $startTime . ' WIB') : '09:00 WIB';

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
            'source' => $item->source ?? 'agenda',
        ];
    }
}

$upcomingEvents = $events;
$eventDays = collect($events)->pluck('day')->toArray();
@endphp

{{-- Header Section --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Semua Jadwal Kegiatan Kamu</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Pantau jadwal live session bootcamp, deadline tugas, dan agenda belajar Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openAddAgendaModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition text-sm font-medium shadow-sm">
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

{{-- Alert Flashes --}}
@if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
        {{ session('success') }}
    </div>
@endif

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
            <a href="{{ route('mahasiswa.calendar') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-600/50 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 transition">
                Hari Ini
            </a>
        </div>

        {{-- Calendar Grid --}}
        <div class="overflow-x-auto">
            {{-- Days Header --}}
            <div class="grid grid-cols-7 gap-1 mb-2 min-w-[500px]">
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                <div class="text-center text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-400 py-2">
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
                    <div class="aspect-square p-2 text-center opacity-40">
                        <span class="text-gray-400 dark:text-gray-600 text-sm">{{ $startFrom + $i }}</span>
                    </div>
                @endfor
                
                {{-- Current month days --}}
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
                                    @php $style = $colorStyles[$event['type']] ?? $colorStyles['webinar']; @endphp
                                    <span class="w-2 h-2 rounded-full {{ $style['dot'] }}"></span>
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
                    <div class="aspect-square p-2 text-center opacity-40">
                        <span class="text-gray-400 dark:text-gray-600 text-sm">{{ $i }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Webinar / Sesi Online</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Workshop / Live Class</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Deadline Tugas</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Quiz</span>
            </div>
        </div>
    </div>

    {{-- Upcoming Events Section --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/50 h-fit">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Kegiatan Bulan Ini</h2>
            <span id="active-filter-badge" class="hidden text-xs bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 font-semibold px-2.5 py-0.5 rounded-full"></span>
        </div>

        {{-- Type Filter Pills --}}
        <div class="flex flex-wrap gap-1.5 mb-4">
            <button type="button" onclick="filterByType('semua')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white transition" data-type="semua">Semua</button>
            <button type="button" onclick="filterByType('webinar')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-blue-100 transition" data-type="webinar">Webinar</button>
            <button type="button" onclick="filterByType('workshop')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-emerald-100 transition" data-type="workshop">Workshop</button>
            <button type="button" onclick="filterByType('deadline')" class="type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-rose-100 transition" data-type="deadline">Deadline</button>
        </div>
        
        <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1" id="upcoming-events-list">
            @forelse($upcomingEvents as $index => $event)
                @php
                    $style = $colorStyles[$event['type']] ?? $colorStyles['webinar'];
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
                <p id="empty-events-msg" class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">Belum ada kegiatan bulan ini.</p>
            @endforelse
            <p id="empty-filtered-msg" class="hidden text-gray-500 dark:text-gray-400 text-center py-6 text-sm">Tidak ada kegiatan pada tanggal/filter ini.</p>
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
                                <option value="webinar">Pribadi / Sesi Online (Biru)</option>
                                <option value="deadline">Penting / Deadline (Merah)</option>
                                <option value="workshop">Tugas / Workshop (Kuning)</option>
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

{{-- Event Detail Modal --}}
<div id="eventDetailModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeEventDetailModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative mx-3 sm:mx-0 bg-white dark:bg-[#1f2937] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full p-6">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <span id="detail-event-type-badge" class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full mb-2 bg-blue-500 text-white"></span>
                    <h3 id="detail-event-title" class="text-xl font-bold text-gray-900 dark:text-gray-50"></h3>
                </div>
                <button type="button" onclick="closeEventDetailModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                <div id="detail-event-course-container" class="hidden">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Kursus / Bootcamp</p>
                    <p id="detail-event-course" class="font-bold text-blue-600 dark:text-blue-400"></p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Waktu & Tanggal</p>
                    <p id="detail-event-date-time" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Catatan / Deskripsi</p>
                    <p id="detail-event-description" class="leading-relaxed bg-slate-50 dark:bg-gray-800/60 p-3 rounded-xl mt-1 text-gray-700 dark:text-gray-200"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <button type="button" onclick="closeEventDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-xl">Tutup</button>
                <a id="detail-event-link" href="#" class="hidden px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Buka Pembelajaran</a>
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
        typeBadge.innerText = (event.type || 'webinar').toUpperCase();

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
                btn.className = 'type-filter-btn px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-blue-100 transition';
            }
        });

        applyFilters();
    }

    function filterEvents(day, dateStr) {
        if (activeDay === day) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
            }
            activeDay = null;
            applyFilters();
            return;
        }

        if (activeDay) {
            const oldEl = document.getElementById('calendar-day-' + activeDay);
            if (oldEl) {
                oldEl.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
            }
        }

        const newEl = document.getElementById('calendar-day-' + day);
        if (newEl) {
            newEl.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2', 'dark:ring-offset-[#1f2937]');
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
            if (emptyFilteredMsg) emptyFilteredMsg.classList.add('hidden');
        }
    }
</script>
@endpush

</x-layouts.dashboard>
