<x-layouts.dashboard :active="'favorites'">

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Kursus Favorit</h1>
    <p class="text-gray-500 dark:text-gray-400">Kursus yang kamu simpan untuk nanti</p>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-xl">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl">
    {{ session('error') }}
</div>
@endif

{{-- Favorites Grid --}}
@if($favorites->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($favorites as $favorite)
    @php $course = $favorite->course; @endphp
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover:shadow-lg transition animate-fade-in-up">
        {{-- Course Image --}}
        <div class="relative aspect-video">
            <img src="{{ $course->thumbnail ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=225&fit=crop' }}" 
                 alt="{{ $course->nama_course }}" 
                 class="w-full h-full object-cover">
            <span class="absolute top-3 right-3 bg-rose-500 text-white text-xs px-2 py-1 rounded-lg">
                ❤️ Favorit
            </span>
        </div>
        
        {{-- Course Info --}}
        <div class="p-4">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1 truncate">{{ $course->nama_course }}</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2 line-clamp-2">{{ $course->deskripsi ?? 'Deskripsi kursus' }}</p>
            
            {{-- Instructor --}}
            <div class="flex items-center gap-2 mb-3 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ $course->dosen->name ?? 'Instructor' }}</span>
            </div>
            
            {{-- Price --}}
            <div class="mb-3">
                @if($course->harga == 0)
                <span class="text-green-500 font-bold">Gratis</span>
                @else
                <span class="text-blue-600 dark:text-blue-400 font-bold">Rp {{ number_format($course->harga, 0, ',', '.') }}</span>
                @endif
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" 
                   class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl font-medium transition text-sm">
                    Lihat Detail
                </a>
                <form action="{{ route('mahasiswa.favorite.remove', $course->id_course) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-red-500 hover:border-red-500 rounded-xl transition" title="Hapus dari Favorit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
{{-- Empty State --}}
<div class="text-center py-16 animate-fade-in-up">
    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </div>
    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Favorit</h3>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Kamu belum menyimpan kursus apapun ke favorit</p>
    <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-medium transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Jelajahi Kursus
    </a>
</div>
@endif

</x-layouts.dashboard>
