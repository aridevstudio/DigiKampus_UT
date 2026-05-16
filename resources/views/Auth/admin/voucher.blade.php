<x-layouts.admin title="Voucher Pembelian" active="voucher">
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $hasPaginator = is_object($vouchers) && method_exists($vouchers, 'total');
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Voucher Pembelian</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Kelola voucher diskon yang dipakai mahasiswa saat checkout course atau webinar.</p>
        </div>
        @unless($tableMissing)
        <button type="button" onclick="openAddVoucherModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Voucher
        </button>
        @endunless
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl">
        <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Data belum valid:</p>
        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-300 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($tableMissing)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-800">
        <p class="font-semibold">Tabel voucher belum tersedia.</p>
        <p class="text-sm mt-1">Jalankan migration terlebih dahulu agar admin bisa membuat voucher.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Total Voucher</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-green-100 dark:border-green-800/40 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-500">Aktif</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-blue-100 dark:border-blue-800/40 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-500">Siap Dipakai</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['available'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-amber-100 dark:border-amber-800/40 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-amber-500">Terpakai</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['used'] }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.voucher') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-[1fr_180px_180px_auto] gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode voucher atau pengguna..." class="w-full px-4 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="status" class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>Siap Dipakai</option>
                <option value="used" {{ $statusFilter === 'used' ? 'selected' : '' }}>Terpakai</option>
                <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <select name="type" class="px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200">
                <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                <option value="percent" {{ $typeFilter === 'percent' ? 'selected' : '' }}>Persen</option>
                <option value="fixed" {{ $typeFilter === 'fixed' ? 'selected' : '' }}>Nominal</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition">Terapkan</button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px]">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Diskon</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Minimal Belanja</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pemakaian</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-sm font-mono font-bold text-blue-700 dark:text-blue-300">{{ $voucher->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $voucher->type === 'percent' ? rtrim(rtrim(number_format((float) $voucher->value, 2, ',', '.'), '0'), ',') . '%' : $formatCurrency($voucher->value) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $voucher->type === 'percent' ? 'Potongan persen' : 'Potongan nominal' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $formatCurrency($voucher->min_subtotal) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $voucher->is_active ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">{{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $voucher->used_at ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' }}">{{ $voucher->used_at ? 'Terpakai' : 'Belum dipakai' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->used_at)
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $voucher->usedBy->name ?? 'User tidak ditemukan' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $voucher->paymentTransaction->order_id ?? '-' }} - {{ $voucher->used_at->format('d M Y H:i') }}</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum digunakan</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="openEditVoucherModal({{ $voucher->id_voucher }})" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                @if($voucher->used_at)
                                <form method="POST" action="{{ route('admin.voucher.reset-usage', $voucher->id_voucher) }}" onsubmit="return confirm('Reset pemakaian voucher ini agar bisa dipakai lagi?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition" title="Reset Pemakaian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.voucher.delete', $voucher->id_voucher) }}" onsubmit="return confirm('Hapus voucher {{ addslashes($voucher->code) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 010 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 010-4V7a2 2 0 00-2-2H5z" /></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada voucher pada filter ini</p>
                            <p class="text-xs text-gray-400 mt-1">Buat voucher baru agar muncul di checkout mahasiswa.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hasPaginator && $vouchers->total() > 0)
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan {{ $vouchers->firstItem() }}-{{ $vouchers->lastItem() }} dari {{ $vouchers->total() }} voucher</p>
            <div>{{ $vouchers->links() }}</div>
        </div>
        @endif
    </div>

    <div id="voucherModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVoucherModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button type="button" onclick="closeVoucherModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <form id="voucherForm" action="{{ route('admin.voucher.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" id="voucherMethod" name="_method" value="POST" disabled>
                    <input type="hidden" name="filter_search" value="{{ $search }}">
                    <input type="hidden" name="filter_status" value="{{ $statusFilter }}">
                    <input type="hidden" name="filter_type" value="{{ $typeFilter }}">
                    <input type="hidden" name="filter_page" value="{{ request('page') }}">

                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">Voucher</p>
                        <h3 id="voucherModalTitle" class="text-xl font-bold text-gray-900 dark:text-white mt-1">Tambah Voucher</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode voucher akan tampil di halaman checkout jika aktif dan belum terpakai.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kode Voucher <span class="text-red-500">*</span></label>
                            <input id="voucherCode" type="text" name="code" required placeholder="HEMAT10" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-800 dark:text-gray-100 uppercase">
                            <p class="text-xs text-gray-400 mt-1">Gunakan huruf, angka, strip, atau underscore. Contoh: WELCOME50.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis Diskon <span class="text-red-500">*</span></label>
                            <select id="voucherType" name="type" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-800 dark:text-gray-100">
                                <option value="percent">Persen</option>
                                <option value="fixed">Nominal Rupiah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nilai Diskon <span class="text-red-500">*</span></label>
                            <input id="voucherValue" type="number" name="value" required min="0.01" step="0.01" placeholder="10" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-800 dark:text-gray-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Minimal Belanja</label>
                            <input id="voucherMinSubtotal" type="number" name="min_subtotal" min="0" step="1" value="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-800 dark:text-gray-100">
                        </div>
                        <label class="sm:col-span-2 flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40 cursor-pointer">
                            <input id="voucherIsActive" type="checkbox" name="is_active" value="1" checked class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Aktifkan voucher</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Jika nonaktif, voucher tetap tersimpan tapi tidak bisa dipakai di checkout.</span>
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeVoucherModal()" class="px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-semibold">Batal</button>
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold">Simpan Voucher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const voucherModal = document.getElementById('voucherModal');
        const voucherForm = document.getElementById('voucherForm');
        const voucherMethod = document.getElementById('voucherMethod');
        const voucherModalTitle = document.getElementById('voucherModalTitle');
        const voucherCode = document.getElementById('voucherCode');
        const voucherType = document.getElementById('voucherType');
        const voucherValue = document.getElementById('voucherValue');
        const voucherMinSubtotal = document.getElementById('voucherMinSubtotal');
        const voucherIsActive = document.getElementById('voucherIsActive');
        const storeVoucherUrl = @json(route('admin.voucher.store'));
        const getVoucherUrl = @json(route('admin.voucher.get', ['id' => '__ID__']));
        const updateVoucherUrl = @json(route('admin.voucher.update', ['id' => '__ID__']));

        function openAddVoucherModal() {
            voucherModalTitle.textContent = 'Tambah Voucher';
            voucherForm.action = storeVoucherUrl;
            voucherMethod.disabled = true;
            voucherMethod.value = 'POST';
            voucherForm.reset();
            voucherType.value = 'percent';
            voucherMinSubtotal.value = '0';
            voucherIsActive.checked = true;
            voucherModal.classList.remove('hidden');
        }

        function closeVoucherModal() {
            voucherModal.classList.add('hidden');
        }

        async function openEditVoucherModal(id) {
            voucherModalTitle.textContent = 'Edit Voucher';
            voucherForm.action = updateVoucherUrl.replace('__ID__', id);
            voucherMethod.disabled = false;
            voucherMethod.value = 'PUT';
            voucherModal.classList.remove('hidden');

            const response = await fetch(getVoucherUrl.replace('__ID__', id), {
                headers: { 'Accept': 'application/json' }
            });
            const voucher = await response.json();

            voucherCode.value = voucher.code || '';
            voucherType.value = voucher.type || 'percent';
            voucherValue.value = voucher.value || 0;
            voucherMinSubtotal.value = voucher.min_subtotal || 0;
            voucherIsActive.checked = Boolean(voucher.is_active);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeVoucherModal();
            }
        });

        @if($errors->any())
            openAddVoucherModal();
        @endif
    </script>
    @endif
</x-layouts.admin>
