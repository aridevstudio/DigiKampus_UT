<x-layouts.dashboard :active="'bootcamp-saya'">
@php
    $user = Auth::guard('mahasiswa')->user();

    // Default image
    $defaultImage = 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=400&h=250&fit=crop';
@endphp

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 14.15v4.1A2.25 2.25 0 0118 20.5H6a2.25 2.25 0 01-2.25-2.25v-4.1m16.5 0A2.25 2.25 0 0018 11.9H6a2.25 2.25 0 00-2.25 2.25m16.5 0v-2.9A2.25 2.25 0 0018 9H6a2.25 2.25 0 00-2.25 2.25v2.9M9 9V5.75A2.25 2.25 0 0111.25 3.5h1.5A2.25 2.25 0 0115 5.75V9" />
            </svg>
        </div>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Bootcamp Saya</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Kelola dan pantau progress bootcamp Anda</p>
        </div>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- Left Content (3 columns) --}}
    <div class="lg:col-span-3 space-y-6">

        {{-- Sort --}}
        <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up delay-100">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-4 py-2 rounded-full text-sm font-medium bg-blue-500 text-white">
                    Semua <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-white/20">{{ $enrollments->total() }}</span>
                </span>
            </div>

            {{-- Sort Dropdown --}}
            <div class="relative">
                <select onchange="window.location.href='{{ route('mahasiswa.bootcamp-saya') }}?sort=' + this.value"
                        class="appearance-none bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2 pr-8 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="terbaru" {{ $selectedSort === 'terbaru' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                    <option value="progress" {{ $selectedSort === 'progress' ? 'selected' : '' }}>Urutkan: Progress</option>
                    <option value="nama" {{ $selectedSort === 'nama' ? 'selected' : '' }}>Urutkan: Nama</option>
                </select>
                <svg class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        {{-- Bootcamp Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up delay-200">
            @forelse($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
                $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
                $progress = intval($enrollment->progress ?? 0);
                $certificateEligible = (bool) ($course->sertifikat ?? false)
                    && (($enrollment->status ?? null) === 'selesai' || $progress >= 100);

                // Status badge
                if ($progress >= 100) {
                    $statusLabel = 'Selesai';
                    $statusClass = 'bg-green-500';
                } elseif ($progress > 0) {
                    $statusLabel = 'Sedang Berlangsung';
                    $statusClass = 'bg-blue-500';
                } elseif ($course->tanggal_webinar && $course->tanggal_webinar->isFuture()) {
                    $statusLabel = 'Akan Dimulai';
                    $statusClass = 'bg-yellow-500';
                } else {
                    $statusLabel = 'Terdaftar';
                    $statusClass = 'bg-indigo-500';
                }

                $dateLabel = $course->tanggal_webinar ? $course->tanggal_webinar->format('d M Y') : null;
            @endphp
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover-lift">
                {{-- Bootcamp Image --}}
                <div class="relative h-40">
                    <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 {{ $statusClass }} text-white text-xs font-medium px-3 py-1 rounded-full">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Bootcamp Info --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-1">{{ $course->nama_course }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">{{ $course->kode_course }}</p>

                    @if($dateLabel)
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span>{{ $dateLabel }}</span>
                    </div>
                    @endif

                    {{-- Progress --}}
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Progress Bootcamp</span>
                        <span class="font-medium {{ $progress >= 100 ? 'text-green-500' : 'text-blue-500' }}">{{ $progress }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-3">
                        <div class="h-full {{ $progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }} rounded-full transition-all" style="width: {{ $progress }}%"></div>
                    </div>

                    {{-- Rating --}}
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= ($course->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="px-4 pb-4 space-y-2">
                    <a href="{{ route('mahasiswa.bootcamp-learn', $course->id_course) }}"
                       class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl font-medium text-sm transition">
                        {{ $progress >= 100 ? 'Lihat Bootcamp' : 'Lanjutkan Belajar' }}
                    </a>
                    @if($certificateEligible)
                    <a href="{{ route('mahasiswa.bootcamp-learn', ['id' => $course->id_course, 'certificate' => 'download']) }}#course-certificate-panel"
                       class="block w-full text-center bg-emerald-500 hover:bg-emerald-600 text-white py-2 rounded-xl font-medium text-sm transition">
                        Download Sertifikat
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 14.15v4.1A2.25 2.25 0 0118 20.5H6a2.25 2.25 0 01-2.25-2.25v-4.1m16.5 0A2.25 2.25 0 0018 11.9H6a2.25 2.25 0 00-2.25 2.25m16.5 0v-2.9A2.25 2.25 0 0018 9H6a2.25 2.25 0 00-2.25 2.25v2.9M9 9V5.75A2.25 2.25 0 0111.25 3.5h1.5A2.25 2.25 0 0115 5.75V9" />
                </svg>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Bootcamp</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Anda belum terdaftar di bootcamp apapun.</p>
                <a href="{{ route('mahasiswa.bootcamp') }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-xl font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Jelajahi Bootcamp
                </a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($enrollments->hasPages())
        <div class="flex items-center justify-between animate-fade-in-up delay-300">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $enrollments->firstItem() ?? 0 }} dari {{ $enrollments->total() }} bootcamp
            </p>
            <div class="flex items-center gap-2">
                @if($enrollments->onFirstPage())
                <span class="px-4 py-2 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                    &lt; Sebelumnya
                </span>
                @else
                <a href="{{ $enrollments->previousPageUrl() }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    &lt; Sebelumnya
                </a>
                @endif

                @if($enrollments->hasMorePages())
                <a href="{{ $enrollments->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    Berikutnya &gt;
                </a>
                @else
                <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-600 rounded-lg text-sm cursor-not-allowed">
                    Berikutnya &gt;
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-6">
        {{-- Info Card --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-5 animate-fade-in-up delay-100">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-3">Tentang Bootcamp</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                Bootcamp adalah program intensif berbasis jadwal dengan mentor profesional. Pantau jadwal dan progress Anda secara berkala untuk hasil maksimal.
            </p>
        </div>

        {{-- CTA --}}
        <div class="bg-gradient-to-br from-blue-50 to-emerald-50 dark:from-blue-500/10 dark:to-emerald-500/10 rounded-2xl border border-blue-100 dark:border-blue-700/30 p-5 animate-fade-in-up delay-200">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-2">Cari Bootcamp Baru?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Jelajahi katalog bootcamp dan tiket event aktif.</p>
            <a href="{{ route('mahasiswa.bootcamp') }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Lihat Katalog Bootcamp
            </a>
        </div>
    </div>
</div>

</x-layouts.dashboard>
