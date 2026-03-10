<x-layouts.admin title="Kelola Kategori" active="kategori">
    @php
        $sampleKategori = [
            ['id' => 1, 'kode' => 'KAT-001', 'nama' => 'Webinar Premium', 'tipe' => 'kursus', 'status' => 'aktif'],
            ['id' => 2, 'kode' => 'KAT-002', 'nama' => 'Kursus Dasar', 'tipe' => 'kursus', 'status' => 'aktif'],
            ['id' => 3, 'kode' => 'KAT-003', 'nama' => 'Informasi Akademik', 'tipe' => 'pengumuman', 'status' => 'aktif'],
            ['id' => 4, 'kode' => 'KAT-004', 'nama' => 'Event Kampus', 'tipe' => 'pengumuman', 'status' => 'nonaktif'],
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kategori</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">UI frontend untuk tambah, edit, filter, dan hapus kategori.</p>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-3 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($sampleKategori) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Kategori (UI)</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">2</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Tipe Kursus</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">2</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Tipe Pengumuman</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="space-y-2 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:gap-3">
            {{-- Buttons --}}
            <div class="flex flex-wrap gap-1.5 sm:contents">
                <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-medium rounded-xl transition">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kategori
                </button>
            </div>

            {{-- Filters --}}
            <div class="grid grid-cols-2 gap-1.5 sm:contents">
                <div class="relative">
                    <select class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                        <option>Semua Tipe</option>
                        <option>Kursus</option>
                        <option>Pengumuman</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
                <div class="relative">
                    <select class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                        <option>Semua Status</option>
                        <option>Aktif</option>
                        <option>Nonaktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>

            {{-- Search --}}
            <div class="relative sm:flex-1 sm:min-w-[200px] sm:max-w-xs sm:ml-auto">
                <input type="text" placeholder="Cari kategori..." class="w-full px-3 py-1.5 pl-9 sm:px-4 sm:py-2 sm:pl-9 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400">
                <svg class="w-4 h-4 absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Kategori</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($sampleKategori as $item)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-3.5"><span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-xs font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $item['kode'] }}</span></td>
                        <td class="px-6 py-3.5"><span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['nama'] }}</span></td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item['tipe'] === 'kursus' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ ucfirst($item['tipe']) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item['status'] === 'aktif' ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($item['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-0.5">
                                <button onclick="openEditModal('{{ $item['kode'] }}', '{{ addslashes($item['nama']) }}', '{{ $item['tipe'] }}', '{{ $item['status'] }}')" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button onclick="confirmDelete('{{ addslashes($item['nama']) }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="addKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <form class="p-6" onsubmit="event.preventDefault(); closeAddModal(); Swal.fire('Frontend Only', 'Simulasi simpan kategori berhasil.', 'success');">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Kategori</h3>
                        <p class="text-sm text-blue-500">Modal UI frontend-only.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="text" required placeholder="Kode Kategori" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            <select required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="">Pilih Tipe</option>
                                <option value="kursus">Kursus</option>
                                <option value="pengumuman">Pengumuman</option>
                            </select>
                        </div>
                        <input type="text" required placeholder="Nama Kategori" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-10 h-6 bg-gray-200 rounded-full peer dark:bg-gray-600 peer-checked:bg-blue-500 relative transition">
                                <span class="absolute top-[2px] left-[2px] bg-white rounded-full h-5 w-5 transition-all peer-checked:translate-x-4"></span>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Status aktif</span>
                        </label>
                    </div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <form class="p-6" onsubmit="event.preventDefault(); closeEditModal(); Swal.fire('Frontend Only', 'Simulasi update kategori berhasil.', 'success');">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Kategori</h3>
                        <p class="text-sm text-blue-500">Modal UI frontend-only.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input id="editKodeKategori" type="text" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            <select id="editTipeKategori" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="kursus">Kursus</option>
                                <option value="pengumuman">Pengumuman</option>
                            </select>
                        </div>
                        <input id="editNamaKategori" type="text" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="editStatusKategori" type="checkbox" class="sr-only peer">
                            <div class="w-10 h-6 bg-gray-200 rounded-full peer dark:bg-gray-600 peer-checked:bg-blue-500 relative transition">
                                <span class="absolute top-[2px] left-[2px] bg-white rounded-full h-5 w-5 transition-all peer-checked:translate-x-4"></span>
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Status aktif</span>
                        </label>
                    </div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openAddModal() {
            document.getElementById('addKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddModal() {
            document.getElementById('addKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(kode, nama, tipe, status) {
            document.getElementById('editKodeKategori').value = kode || '';
            document.getElementById('editNamaKategori').value = nama || '';
            document.getElementById('editTipeKategori').value = tipe || 'kursus';
            document.getElementById('editStatusKategori').checked = status === 'aktif';

            document.getElementById('editKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDelete(nama) {
            Swal.fire({
                title: 'Hapus Kategori?',
                html: '<p class="text-gray-500">Kategori <strong>' + nama + '</strong> akan dihapus.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Frontend Only', 'Simulasi hapus berhasil.', 'success');
                }
            });
        }
    </script>
    @endpush
</x-layouts.admin>
