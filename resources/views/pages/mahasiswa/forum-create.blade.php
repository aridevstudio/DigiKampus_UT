<x-layouts.dashboard :active="'forum'">
<div class="max-w-3xl mx-auto px-3 sm:px-4 lg:px-6 py-5 lg:py-8 space-y-5">
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center gap-1 hover:text-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Forum Komunitas
        </a>
        <span class="text-gray-300">/</span>
        <span>Buat Topik</span>
    </nav>

    <header class="space-y-1.5">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Buat Topik Baru</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Pilih kategori, tulis judul yang menarik, dan jelaskan isi diskusi secara jelas.</p>
    </header>

    @if($errors->any())
        <div class="p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl flex items-start gap-2 text-sm text-red-700 dark:text-red-300 font-medium">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div class="space-y-0.5">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('mahasiswa.forum.store') }}" class="space-y-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6" x-data="{ selectedCat: null }">
        @csrf

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Kategori <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pilih satu kategori yang paling sesuai dengan topik Anda.</p>
            @if($kategoriList->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
                    @foreach($kategoriList as $cat)
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $cat->id_forum_category }}" x-model="selectedCat" class="sr-only peer" required>
                            <div class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-500/10 hover:border-blue-300 transition flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->warna }};"></span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $cat->nama }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            @else
                <p class="text-xs text-amber-600 dark:text-amber-400">Belum ada kategori aktif. Hubungi admin.</p>
            @endif
        </div>

        <div class="space-y-2">
            <label for="judul" class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Judul Topik <span class="text-red-500">*</span></label>
            <input type="text" id="judul" name="judul" required maxlength="200" placeholder="Contoh: Cara mulai skripsi setelah libur panjang?" value="{{ old('judul') }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Buat judul sejelas mungkin agar mahasiswa lain tertarik membaca.</p>
            @error('judul')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2">
            <label for="isi" class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Isi Diskusi <span class="text-red-500">*</span></label>
            <textarea id="isi" name="isi" rows="8" maxlength="8000" required placeholder="Jelaskan konteks, pertanyaan, atau topik diskusi Anda..." class="w-full px-3.5 py-2.5 text-sm rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y min-h-[180px]">{{ old('isi') }}</textarea>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Maks 8000 karakter. Hindari informasi pribadi sensitif.</p>
            @error('isi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="pt-3 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row sm:justify-end gap-2">
            <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm rounded-xl text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
            <button type="submit" :disabled="!selectedCat" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold shadow-md shadow-blue-500/20 hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Posting Topik
            </button>
        </div>
    </form>
</div>
</x-layouts.dashboard>
