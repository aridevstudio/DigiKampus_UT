<x-layouts.dosen title="Buat Kursus Baru" active="buat-kursus">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Buat Kursus Baru</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Lengkapi informasi berikut untuk membuat kursus baru Anda.</p>
        </div>

        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                {{-- 1. Informasi Dasar Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informasi Dasar Kursus</h2>
                    
                    <div class="space-y-5">
                        {{-- Judul --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Kursus</label>
                            <input type="text" name="nama_course" value="{{ old('nama_course') }}" required placeholder="Masukkan judul kursus" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Kode Kursus (Hidden/Auto-generated idea? Or user input?) Keeping user input for now but maybe less prominent --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kode Kursus (Unik)</label>
                            <input type="text" name="kode_course" value="{{ old('kode_course') }}" required placeholder="Contoh: CS-101 (Harus Unik)" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                             @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Kursus</label>
                            <textarea name="deskripsi" rows="4" placeholder="Jelaskan tentang kursus Anda..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori Kursus</label>
                                <select name="id_jurusan" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($jurusans ?? [] as $jurusan)
                                    <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Tingkat Kesulitan --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tingkat Kesulitan</label>
                                <select name="level" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none">
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                    <option value="Menengah" {{ old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                    <option value="Mahir" {{ old('level') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Estimasi Waktu --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Estimasi Waktu Belajar (Jam)</label>
                                <input type="number" name="estimasi_waktu" value="{{ old('estimasi_waktu') }}" min="0" placeholder="20" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>

                            {{-- Thumbnail --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Thumbnail Kursus</label>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="thumbnail-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewThumbnail(this)">
                                    <div class="flex flex-col items-center justify-center w-full h-[52px] px-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition text-center overflow-hidden">
                                        <div id="file-placeholder" class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                            <span class="text-sm">Klik untuk upload atau drag & drop</span>
                                        </div>
                                        <p id="file-name" class="text-sm text-gray-800 dark:text-gray-200 font-medium truncate w-full hidden"></p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 pl-1">PNG, JPG hingga 2MB (Opsional)</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Struktur Modul Awal --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Struktur Modul Awal</h2>
                        <button type="button" disabled class="px-4 py-2 bg-blue-500/50 cursor-not-allowed text-white text-sm font-medium rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Modul Pertama
                        </button>
                    </div>
                    
                    <div class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-700/30">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada modul</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Anda dapat menambahkan modul setelah membuat kursus.</p>
                    </div>
                </div>

                {{-- 3. Pengaturan Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Pengaturan Kursus</h2>
                    
                    <div class="space-y-6">
                        {{-- Status & Akses Publik --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 transition">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Status Kursus</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aktif atau simpan sebagai draft</p>
                                <input type="hidden" name="status" id="status_input" value="draft">
                            </div>
                            <!-- Toggle is managed via submit buttons at the bottom, visual here only? No, let's make it functional if needed, 
                                 but user design shows "Simpan Sebagai Draft" vs "Buat Kursus". 
                                 So this setting might just be "Default Status". Let's stick to the toggle design. -->
                            <!-- Since the prompt image shows a toggle for "Status Kursus", let's implement a toggle that updates a hidden input -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="status_toggle" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                         <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 transition">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Akses Publik</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dapat dilihat oleh semua mahasiswa</p>
                            </div>
                             <input type="hidden" name="akses_publik" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="akses_publik" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 transition">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Sertifikat Penyelesaian</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Berikan sertifikat setelah selesai</p>
                            </div>
                            <input type="hidden" name="sertifikat" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sertifikat" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 4. Pricing & Akses Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Pricing & Akses Kursus</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-end mb-4">
                            <span class="mr-3 text-sm font-medium text-gray-900 dark:text-gray-300">Kursus Gratis</span>
                             <!-- Hidden inputs to handle logic -->
                             <input type="hidden" name="tipe" id="tipe_input" value="berbayar">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="gratis_toggle" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div id="pricing_fields" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Harga Kursus (Rp)</label>
                                <input type="number" name="harga" id="harga_input" value="{{ old('harga') }}" min="0" placeholder="0" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Diskon (%)</label>
                                <input type="number" name="diskon" id="diskon_input" value="{{ old('diskon') }}" min="0" max="100" placeholder="0" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                        
                         <div id="pricing_info" class="hidden p-4 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-xl text-sm">
                            Kursus ini akan dapat diakses secara gratis oleh semua mahasiswa.
                        </div>
                    </div>
                </div>
                
                 {{-- Preview Button (Fake) --}}
                 <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4">
                     <button type="button" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm transition">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                         Pratinjau Sekilas
                     </button>
                 </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="submit" onclick="setStatus('draft')" class="px-6 py-3 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 font-medium rounded-xl hover:bg-blue-50 dark:hover:bg-gray-600 transition shadow-sm">
                    Simpan Sebagai Draft
                </button>
                <button type="submit" onclick="setStatus('aktif')" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition shadow-lg shadow-blue-500/30">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Buat Kursus
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // File Preview
        function previewThumbnail(input) {
            const placeholder = document.getElementById('file-placeholder');
            const fileName = document.getElementById('file-name');
            
            if (input.files && input.files[0]) {
                placeholder.classList.add('hidden');
                fileName.classList.remove('hidden');
                fileName.textContent = input.files[0].name;
            } else {
                placeholder.classList.remove('hidden');
                fileName.classList.add('hidden');
            }
        }

        // Status & Pricing Logic
        const statusDetailToggle = document.getElementById('status_toggle');
        const gratisToggle = document.getElementById('gratis_toggle');
        const statusInput = document.getElementById('status_input');
        const tipeInput = document.getElementById('tipe_input');
        const pricingFields = document.getElementById('pricing_fields');
        const pricingInfo = document.getElementById('pricing_info');

        // Initial State
        // Toggle for Status Details affects nothing visually in the form in my simplified version, 
        // effectively 'Simpan Draft' vs 'Buat Kursus' buttons control the final status.
        // But let's sync the toggle if user manually clicks it. 
        // Actually, the requirements are slightly conflicting: "Toggle Status" inside form vs "Draft/Make" buttons.
        // I will make the buttons override the status input regardless of the toggle, for clarity.

        function setStatus(status) {
            statusInput.value = status;
        }

        // Pricing Toggle
        gratisToggle.addEventListener('change', function() {
            if (this.checked) {
                // Gratis
                tipeInput.value = 'gratis';
                pricingFields.classList.add('hidden');
                pricingInfo.classList.remove('hidden');
                document.getElementById('harga_input').value = 0;
                document.getElementById('diskon_input').value = 0;
            } else {
                // Berbayar
                tipeInput.value = 'berbayar';
                pricingFields.classList.remove('hidden');
                pricingInfo.classList.add('hidden');
            }
        });

    </script>
    @endpush
</x-layouts.dosen>
