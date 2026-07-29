<x-layouts.dashboard :active="'bootcamp'">
@php
    $defaultImage = 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&h=520&fit=crop';
    $searchQuery = $searchQuery ?? request('search');
    $selectedFilter = $selectedFilter ?? 'semua';
    $enrolledCourseIds = $enrolledCourseIds ?? [];

    $filterTabs = [
        ['key' => 'semua', 'label' => 'Semua Event'],
        ['key' => 'bootcamp', 'label' => 'Bootcamp'],
        ['key' => 'webinar', 'label' => 'Webinar'],
        ['key' => 'workshop', 'label' => 'Workshop'],
    ];
@endphp

<div class="mb-6 rounded-3xl border border-blue-100 bg-gradient-to-br from-blue-50 via-white to-emerald-50 p-6 shadow-sm dark:border-gray-700/50 dark:from-[#172033] dark:via-[#1f2937] dark:to-[#12251f]">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-white dark:bg-blue-500">Bootcamp & Tiket</span>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-gray-50 sm:text-3xl">Bootcamp dan tiket event aktif</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                Pilih bootcamp atau tiket event yang sudah dibuka admin, lihat detailnya, lalu lanjutkan ke keranjang dan pembayaran.
            </p>
        </div>
        <form method="GET" action="{{ route('mahasiswa.bootcamp') }}" class="relative w-full lg:w-96">
            @if($selectedFilter !== 'semua')
                <input type="hidden" name="filter" value="{{ $selectedFilter }}">
            @endif
            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input name="search" value="{{ $searchQuery }}" placeholder="Cari bootcamp atau event..." class="w-full rounded-2xl border border-white/70 bg-white/90 py-3 pl-12 pr-4 text-sm text-gray-700 shadow-sm outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-900/80 dark:text-gray-100 dark:focus:ring-blue-500/20">
        </form>
    </div>

    {{-- Filter Tabs --}}
    <div class="mt-5 flex flex-wrap gap-2">
        @foreach($filterTabs as $tab)
            <a href="{{ route('mahasiswa.bootcamp', array_filter(['filter' => $tab['key'] !== 'semua' ? $tab['key'] : null, 'search' => $searchQuery])) }}"
               class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-[0.14em] transition
               {{ $selectedFilter === $tab['key']
                   ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                   : 'border border-gray-200 bg-white text-gray-500 hover:border-blue-300 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-400 dark:hover:text-blue-300' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>

@if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-300">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
    @forelse($bootcampCourses as $course)
        @php
            $thumbnailPath = $course->thumbnail ? preg_replace('#^/?storage/#i', '', $course->thumbnail) : null;
            $thumbnail = $thumbnailPath ? asset('storage/' . ltrim($thumbnailPath, '/')) : $defaultImage;
            $basePrice = max(0, (float) ($course->harga ?? 0));
            $discountPercent = max(0, min(100, (float) ($course->diskon ?? 0)));
            $finalPrice = $basePrice > 0 && $discountPercent > 0 ? $basePrice * (100 - $discountPercent) / 100 : $basePrice;
            $dateLabel = $course->tanggal_webinar ? $course->tanggal_webinar->format('d M Y') : 'Jadwal menyusul';
            $startTime = $course->jam_mulai_webinar ? substr((string) $course->jam_mulai_webinar, 0, 5) : null;
            $endTime = $course->jam_selesai_webinar ? substr((string) $course->jam_selesai_webinar, 0, 5) : null;
            $timeLabel = $startTime ? ($endTime ? $startTime . ' - ' . $endTime : $startTime) : 'Jam menyusul';
            $isEnrolled = in_array($course->id_course, $enrolledCourseIds);

            // ── tipe_event differentiation (business rules) ──
            // Tipe-event baru pakai kolom Course.tipe_event (lebih granular dari kategori='tiket' lama).
            $tipeEventRaw = trim((string) ($course->tipe_event ?? ''));
            if ($tipeEventRaw === '') {
                // Legacy bootcamp tanpa tipe_event terisi → fallback ke kategori='tiket' = Bootcamp.
                $tipeEventLabel = 'Bootcamp';
                $isKategoriTiket = strtolower((string) ($course->kategori ?? '')) === 'tiket';
                $effectiveTipeEvent = $isKategoriTiket ? 'bootcamp' : 'lain';
            } else {
                $effectiveTipeEvent = strtolower($tipeEventRaw);
            }
            $tipeEventLabel = match ($effectiveTipeEvent) {
                'seminar' => 'Seminar',
                'webinar' => 'Webinar',
                'workshop' => 'Workshop',
                'bootcamp' => 'Bootcamp',
                default => 'Event',
            };

            // Mode_event badge
            $modeEventRaw = strtolower((string) ($course->mode_event ?? ''));
            $isOnlineEvent = in_array($modeEventRaw, ['online', ''], true)
                && $effectiveTipeEvent !== 'seminar';
            $isOfflineEvent = in_array($modeEventRaw, ['offline', 'onsite', 'hybrid'], true)
                || $effectiveTipeEvent === 'seminar';
            $modeBadgeLabel = $isOfflineEvent ? 'Offline' : 'Online';
            $modeBadgeColor = $isOfflineEvent
                ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300';

            // Sesi count (1 sesi untuk seminar/webinar/workshop; multi-sesi untuk bootcamp)
            $isBootcampCard = $effectiveTipeEvent === 'bootcamp';
            $sesiCountLabel = $isBootcampCard ? 'Multi-Sesi' : '1 Sesi';
            $sesiCountBg = $isBootcampCard
                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                : 'bg-slate-100 text-slate-700 dark:bg-slate-700/60 dark:text-slate-300';

            // Seminar/webinar hanya menjual daya tampung peserta. Enrollment
            // aktif dan reservasi checkout yang belum expired sama-sama memakai slot.
            $isCapacityOnlyEvent = in_array($effectiveTipeEvent, ['seminar', 'webinar'], true);
            $kapasitasMax = (int) ($course->kapasitas_maksimal ?? 0);
            if ($kapasitasMax <= 0 && $isCapacityOnlyEvent) {
                $kapasitasMax = (int) ($course->kuota_peserta ?? 0);
            }
            $slotTerisiCounter = $isCapacityOnlyEvent
                ? (int) (($course->active_enrollments_count ?? 0) + ($course->active_seat_reservations_count ?? 0))
                : (int) ($course->slot_terisi ?? 0);
            $slotsRemaining = $kapasitasMax > 0 ? max(0, $kapasitasMax - $slotTerisiCounter) : null;
            $isSoldOut = $isCapacityOnlyEvent && $slotsRemaining === 0;
            $slotsLabel = $slotsRemaining !== null
                ? "{$slotsRemaining} dari {$kapasitasMax} slot tersisa"
                : 'Tidak dibatasi';
        @endphp

        <article class="group overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl dark:border-gray-700/50 dark:bg-[#1f2937]">
            <a href="{{ route('mahasiswa.bootcamp-detail', $course->id_course) }}" class="block">
                <div class="relative h-44 overflow-hidden bg-slate-900">
                    <img src="{{ $thumbnail }}" alt="{{ $course->nama_course }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.src='{{ $defaultImage }}'">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/5 to-transparent"></div>
                    @if($isEnrolled)
                        <span class="absolute left-4 top-4 rounded-full bg-emerald-500 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-white">Terdaftar</span>
                    @else
                        <span class="absolute left-4 top-4 rounded-full bg-blue-500 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-white">Open</span>
                    @endif
                    {{-- Tipe-event chip (stack di kanan atas thumbnail) --}}
                    <span class="absolute right-4 top-4 inline-flex items-center gap-1 rounded-full {{ $sesiCountBg }} px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.16em] shadow-sm">
                        {{ $tipeEventLabel }} &middot; {{ $sesiCountLabel }}
                    </span>
                    <span class="absolute bottom-4 left-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-800">{{ $course->kode_course }}</span>
                </div>
            </a>

            <div class="space-y-4 p-5">
                <div>
                    <a href="{{ route('mahasiswa.bootcamp-detail', $course->id_course) }}" class="line-clamp-2 text-lg font-bold text-gray-900 transition hover:text-blue-600 dark:text-gray-50 dark:hover:text-blue-300">
                        {{ $course->nama_course }}
                    </a>
                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $course->deskripsi ?? 'Deskripsi program belum tersedia.' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-slate-50 p-3 dark:bg-gray-800/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">Jadwal</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $dateLabel }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3 dark:bg-gray-800/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">Waktu</p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $timeLabel }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3 dark:bg-gray-800/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">{{ $isCapacityOnlyEvent ? 'Slot tersisa' : 'Kuota' }}</p>
                        <p class="mt-1 font-semibold {{ $isSoldOut ? 'text-rose-600 dark:text-rose-300' : 'text-gray-800 dark:text-gray-100' }}">{{ $isCapacityOnlyEvent ? $slotsLabel : ($course->kuota_peserta ? number_format($course->kuota_peserta) . ' kursi' : 'Terbatas') }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-3 dark:bg-gray-800/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">Mentor</p>
                        <p class="mt-1 truncate font-semibold text-gray-800 dark:text-gray-100">{{ $course->dosen?->name ?? 'Tim mentor' }}</p>
                    </div>
                </div>

                <div class="flex items-end justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-700/50">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-400">Harga</p>
                        @if($finalPrice > 0)
                            <p class="mt-1 text-xl font-black text-blue-600 dark:text-blue-300">Rp {{ number_format($finalPrice, 0, ',', '.') }}</p>
                        @else
                            <p class="mt-1 text-xl font-black text-emerald-600 dark:text-emerald-300">Gratis</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('mahasiswa.bootcamp-detail', $course->id_course) }}" class="rounded-2xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Detail</a>
                        @if($isEnrolled)
                            <a href="{{ route('mahasiswa.bootcamp-learn', $course->id_course) }}" class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                                Mulai
                            </a>
                        @elseif($isSoldOut)
                            <button type="button" disabled aria-disabled="true" class="cursor-not-allowed rounded-2xl bg-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                Penuh
                            </button>
                        @else
                            <form action="{{ route('mahasiswa.cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id_course }}">
                                <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                                    Beli
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-[#1f2937]">
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                Belum ada bootcamp atau tiket event yang tersedia untuk dibeli
            </p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Semua event yang tersedia mungkin sudah Anda ikuti, atau coba ubah kata kunci pencarian Anda.
            </p>
            <a href="{{ route('mahasiswa.bootcamp-saya') }}" class="mt-5 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white">Lihat Bootcamp Saya</a>
        </div>
    @endforelse
</div>
</x-layouts.dashboard>
