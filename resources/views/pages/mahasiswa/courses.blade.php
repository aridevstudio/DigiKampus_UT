<x-layouts.dashboard :active="'courses'">
@php
    $user = Auth::guard('mahasiswa')->user();
    
    // Default image
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop';
    
    // Status badge colors
    $statusColors = [
        'aktif' => 'bg-blue-500',
        'selesai' => 'bg-green-500',
        'akan_dimulai' => 'bg-yellow-500',
    ];
@endphp

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Kursus Saya</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Kelola dan pantau progress pembelajaran Anda</p>
        </div>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    
    {{-- Left Content (3 columns) --}}
    <div class="lg:col-span-3 space-y-6">
        
        {{-- Filters and Sort --}}
        <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up delay-100">
            {{-- Type Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('mahasiswa.courses', ['tipe' => 'all', 'sort' => $selectedSort]) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedTipe === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    All <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs {{ $selectedTipe === 'all' ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $allCount }}</span>
                </a>
                @foreach(['webinar' => 'Webinar', 'tiket' => 'Tiket', 'kursus' => 'Mata Kuliah'] as $key => $label)
                <a href="{{ route('mahasiswa.courses', ['tipe' => $key, 'sort' => $selectedSort]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedTipe === $key ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $label }} <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs {{ $selectedTipe === $key ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }}">{{ $typeCounts[$key] ?? 0 }}</span>
                </a>
                @endforeach
            </div>
            
            {{-- Sort Dropdown --}}
            <div class="relative">
                <select onchange="window.location.href='{{ route('mahasiswa.courses') }}?tipe={{ $selectedTipe }}&sort=' + this.value"
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
        
        {{-- Course Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up delay-200">
            @forelse($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
                $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
                $progress = intval($enrollment->progress ?? 0);
                
                // Status badge
                if ($progress >= 100) {
                    $statusLabel = 'Selesai';
                    $statusClass = 'bg-green-500';
                } elseif ($progress > 0) {
                    $statusLabel = 'Sedang Berlangsung';
                    $statusClass = 'bg-blue-500';
                } else {
                    $statusLabel = 'Akan Dimulai';
                    $statusClass = 'bg-yellow-500';
                }
            @endphp
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover-lift">
                {{-- Course Image --}}
                <div class="relative h-40">
                    <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="w-full h-full object-cover">
                    <span class="absolute top-3 right-3 {{ $statusClass }} text-white text-xs font-medium px-3 py-1 rounded-full">
                        {{ $statusLabel }}
                    </span>
                </div>
                
                {{-- Course Info --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-1">{{ $course->nama_course }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">{{ $course->kode_course }}</p>
                    
                    {{-- Progress --}}
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Progress</span>
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
                <div class="px-4 pb-4">
                    <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" 
                       class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl font-medium text-sm transition">
                        {{ $progress >= 100 ? 'Lihat Kursus' : 'Lanjutkan Belajar' }}
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Kursus</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Anda belum terdaftar di kursus apapun.</p>
                <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-xl font-medium text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Jelajahi Kursus
                </a>
            </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($enrollments->hasPages())
        <div class="flex items-center justify-between animate-fade-in-up delay-300">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $enrollments->firstItem() ?? 0 }} dari {{ $enrollments->total() }} kursus
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
        
        {{-- Target Mingguan --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-5 animate-fade-in-up delay-100">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Target Mingguan</h3>
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400 text-sm">Selesaikan {{ $weeklyTarget['total'] }} modul</span>
                <span class="text-blue-500 font-medium">{{ $weeklyTarget['completed'] }}/{{ $weeklyTarget['total'] }}</span>
            </div>
            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-3">
                <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($weeklyTarget['completed'] / max($weeklyTarget['total'], 1)) * 100 }}%"></div>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-xs">
                @if($weeklyTarget['completed'] >= $weeklyTarget['total'])
                    🎉 Target mingguan tercapai!
                @else
                    {{ $weeklyTarget['total'] - $weeklyTarget['completed'] }} modul lagi untuk mencapai target minggu ini!
                @endif
            </p>
        </div>
        
        {{-- Rekomendasi --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-5 animate-fade-in-up delay-200">
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Rekomendasi untuk Anda</h3>
            <div class="space-y-4">
                @forelse($recommendedCourses as $course)
                @php
                    $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
                @endphp
                <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" class="flex items-center gap-3 group">
                    <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-800 dark:text-gray-100 text-sm group-hover:text-blue-500 transition line-clamp-1">{{ $course->nama_course }}</h4>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $course->kode_course }}</p>
                        <div class="flex items-center gap-0.5 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= ($course->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                </a>
                @empty
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Tidak ada rekomendasi</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

</x-layouts.dashboard>
