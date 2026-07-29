<x-layouts.dashboard :active="'forum'">
<div class="w-full max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-5 lg:py-8 space-y-6">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center gap-1 hover:text-blue-500 transition font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Forum Komunitas
                </a>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">Buat Topik Baru</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Buat Topik Diskusi Baru</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai diskusi, ajukan pertanyaan, atau bagikan wawasan dengan mahasiswa & pengajar lainnya.</p>
        </div>
        <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-xs self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Batal
        </a>
    </div>

    {{-- Validation Error Alert --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl flex items-start gap-3 text-sm text-red-700 dark:text-red-300 font-medium">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div class="space-y-0.5">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        </div>
    @endif

    {{-- Main Form Card (Dashboard Professional Structure) --}}
    <form method="POST" action="{{ route('mahasiswa.forum.store') }}" class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm overflow-hidden" x-data="{ selectedCat: '{{ old('category_id') }}', charCount: {{ strlen(old('isi', '')) }} }">
        @csrf

        {{-- Form Content Area --}}
        <div class="p-5 sm:p-7 lg:p-8 space-y-6">

            {{-- BAGIAN 1: Grid 2 Kolom (Kategori & Judul Topik) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Kolom Kiri: Kategori Topik --}}
                <div class="lg:col-span-5 space-y-2">
                    <label for="category_id" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                        Kategori Topik <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="category_id" name="category_id" x-model="selectedCat" required class="w-full px-4 py-3 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Kategori Diskusi --</option>
                            @foreach($kategoriList as $cat)
                                <option value="{{ $cat->id_forum_category }}" {{ old('category_id') == $cat->id_forum_category ? 'selected' : '' }}>
                                    {{ $cat->nama }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pilih kategori yang paling sesuai dengan topik pertanyaan Anda.</p>
                    @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Kolom Kanan: Judul Topik --}}
                <div class="lg:col-span-7 space-y-2">
                    <label for="judul" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                        Judul Topik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" required maxlength="200" placeholder="Tuliskan judul topik diskusi yang spesifik dan jelas..." value="{{ old('judul') }}" class="w-full px-4 py-3 text-sm sm:text-base rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Gunakan kata kunci sejelas mungkin</span>
                        <span>Maks. 200 karakter</span>
                    </div>
                    @error('judul')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- BAGIAN 2: Editor Isi Diskusi (Besar & Memenuhi Lebar Card) --}}
            <div class="space-y-2 pt-2">
                <div class="flex items-center justify-between">
                    <label for="isi" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                        Isi Diskusi <span class="text-red-500">*</span>
                    </label>
                    {{-- Format Toolbar Helper --}}
                    <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        <button type="button" onclick="insertFormatting('**', '**')" class="px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition font-bold" title="Cetak Tebal">B</button>
                        <button type="button" onclick="insertFormatting('*', '*')" class="px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition italic" title="Cetak Miring">I</button>
                        <button type="button" onclick="insertFormatting('`', '`')" class="px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition font-mono" title="Kode Inline">&lt;/&gt;</button>
                        <button type="button" onclick="insertFormatting('- ', '')" class="px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Daftar Bullet">• List</button>
                        <button type="button" onclick="insertFormatting('> ', '')" class="px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Kutipan">“ Quote</button>
                    </div>
                </div>

                <textarea id="isi" name="isi" rows="12" maxlength="8000" required x-on:input="charCount = $el.value.length" placeholder="Jelaskan secara rinci konteks masalah, pertanyaan, atau ide diskusi Anda. Anda dapat menggunakan format text di atas..." class="w-full px-4 py-3.5 text-sm sm:text-base rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y min-h-[300px] lg:min-h-[380px] transition font-sans leading-relaxed">{{ old('isi') }}</textarea>
                
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-1">
                    <span>💡 Tips: Diskusi dengan penjelasan lengkap mendapatkan 3x lebih banyak tanggapan dari rekan mahasiswa.</span>
                    <span x-text="charCount + '/8000 karakter'"></span>
                </div>
                @error('isi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- BAGIAN 3: Fitur & Opsi Tambahan (Grid Cards Horizontal) --}}
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700/50 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-400">Opsi & Pengaturan Tambahan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- Card Opsi 1: Upload File --}}
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700/70 bg-gray-50/50 dark:bg-gray-800/40 space-y-2">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span>Upload Lampiran (Opsional)</span>
                        </div>
                        <input type="file" disabled class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-not-allowed opacity-60">
                        <p class="text-[11px] text-gray-400">Fitur lampiran file dokumen/gambar tersedia untuk diskusi tertentu.</p>
                    </div>

                    {{-- Card Opsi 2: Notifikasi Balasan --}}
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700/70 bg-gray-50/50 dark:bg-gray-800/40 space-y-2">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span>Notifikasi Email</span>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer pt-1">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Kirim notifikasi saat ada balasan</span>
                        </label>
                    </div>

                    {{-- Card Opsi 3: Etika Komunitas --}}
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700/70 bg-gray-50/50 dark:bg-gray-800/40 space-y-1.5">
                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Etika Akademik</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jaga kesantunan, hormati pendapat, dan bantu sesama civitas akademika.</p>
                    </div>

                </div>
            </div>

        </div>

        {{-- BAGIAN 4: Footer Action Bar (Sticky / Right Alignment) --}}
        <div class="px-5 sm:px-7 lg:px-8 py-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700/50 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
                Pastikan kolom bernoda <span class="text-red-500">*</span> terisi sebelum memposting.
            </p>
            <div class="flex flex-col-reverse sm:flex-row items-center gap-3">
                <a href="{{ route('mahasiswa.forum') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Batal
                </a>
                <button type="submit" :disabled="!selectedCat" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Posting Topik
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function insertFormatting(prefix, suffix) {
        const textarea = document.getElementById('isi');
        if (!textarea) return;

        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        const replacement = prefix + (selectedText || 'teks') + suffix;

        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
        textarea.setSelectionRange(start + prefix.length, start + prefix.length + (selectedText || 'teks').length);
    }
</script>
@endpush
</x-layouts.dashboard>
