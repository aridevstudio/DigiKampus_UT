<x-layouts.admin title="Voucher Pembelian" active="voucher">
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $hasPaginator = is_object($vouchers) && method_exists($vouchers, 'total');
    @endphp

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Voucher Pembelian</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola kode diskon checkout. Voucher bisa tanpa batas, dibatasi kuota, dan otomatis expired sesuai tanggal.</p>
        </div>
        @unless($tableMissing)
        <button type="button" onclick="openAddVoucherModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-blue-500/20 transition hover:bg-blue-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Voucher
        </button>
        @endunless
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800/50 dark:bg-green-900/20">
        <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800/50 dark:bg-red-900/20">
        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800/50 dark:bg-red-900/20">
        <p class="mb-2 text-sm font-semibold text-red-700 dark:text-red-300">Data belum valid:</p>
        <ul class="list-inside list-disc space-y-1 text-sm text-red-600 dark:text-red-300">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($tableMissing)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-800">
        <p class="font-semibold">Tabel voucher belum tersedia.</p>
        <p class="mt-1 text-sm">Jalankan migration terlebih dahulu agar admin bisa membuat voucher.</p>
    </div>
    @else
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700/50 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Total Voucher</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-green-100 bg-white p-5 dark:border-green-800/40 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-widest text-green-500">Aktif</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-white p-5 dark:border-blue-800/40 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-500">Siap Dipakai</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['available'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-white p-5 dark:border-amber-800/40 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-widest text-amber-500">Pernah Dipakai</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['used'] }}</p>
        </div>
        <div class="rounded-2xl border border-red-100 bg-white p-5 dark:border-red-800/40 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Expired</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['expired'] }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.voucher') }}" class="mb-6 rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-700/50 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_180px_180px_auto]">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode voucher atau pengguna..." class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 pl-10 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-200">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="status" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-200">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>Siap Dipakai</option>
                <option value="used" {{ $statusFilter === 'used' ? 'selected' : '' }}>Pernah Dipakai</option>
                <option value="expired" {{ $statusFilter === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <select name="type" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-200">
                <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                <option value="percent" {{ $typeFilter === 'percent' ? 'selected' : '' }}>Persen</option>
                <option value="fixed" {{ $typeFilter === 'fixed' ? 'selected' : '' }}>Nominal</option>
            </select>
            <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800">Terapkan</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px]">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kode</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Diskon</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Minimal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kuota</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Masa Berlaku</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Terakhir Dipakai</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($vouchers as $voucher)
                    @php
                        $isExpired = $voucher->isExpired();
                        $remainingUses = $voucher->remainingUses();
                        $quotaIsFull = $remainingUses !== null && $remainingUses <= 0;
                        $statusLabel = !$voucher->is_active ? 'Nonaktif' : ($isExpired ? 'Expired' : ($quotaIsFull ? 'Kuota habis' : 'Siap dipakai'));
                        $statusClass = match ($statusLabel) {
                            'Siap dipakai' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'Kuota habis' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            'Expired' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <tr class="transition hover:bg-blue-50/40 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-xl bg-blue-50 px-3 py-1.5 font-mono text-sm font-bold text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">{{ $voucher->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $voucher->type === 'percent' ? rtrim(rtrim(number_format((float) $voucher->value, 2, ',', '.'), '0'), ',') . '%' : $formatCurrency($voucher->value) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $voucher->type === 'percent' ? 'Potongan persen' : 'Potongan nominal' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $formatCurrency($voucher->min_subtotal) }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $voucher->usageLabel() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $remainingUses === null ? 'Bisa dipakai tanpa batas kuota' : 'Sisa ' . $remainingUses . ' pemakaian' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->expires_at)
                                <p class="text-sm font-semibold {{ $isExpired ? 'text-red-600 dark:text-red-300' : 'text-gray-900 dark:text-white' }}">{{ $voucher->expires_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $isExpired ? 'Sudah expired' : 'Otomatis expired' }}</p>
                            @else
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Selamanya</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada tanggal expired</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
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
                                <button type="button" onclick="openEditVoucherModal({{ $voucher->id_voucher }})" class="rounded-lg p-2 text-gray-500 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20" title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                @if((int) $voucher->used_count > 0)
                                <form method="POST" action="{{ route('admin.voucher.reset-usage', $voucher->id_voucher) }}" onsubmit="return confirm('Reset histori pemakaian voucher ini? Tindakan ini hanya untuk koreksi admin.')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="rounded-lg p-2 text-gray-500 transition hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-900/20" title="Reset Pemakaian">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.voucher.delete', $voucher->id_voucher) }}" onsubmit="return confirm('Hapus voucher {{ addslashes($voucher->code) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700/50">
                                <svg class="h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 010 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 010-4V7a2 2 0 00-2-2H5z" /></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada voucher pada filter ini</p>
                            <p class="mt-1 text-xs text-gray-400">Buat voucher baru agar muncul di checkout mahasiswa.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hasPaginator && $vouchers->total() > 0)
        <div class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-700/50 dark:bg-gray-700/20 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan {{ $vouchers->firstItem() }}-{{ $vouchers->lastItem() }} dari {{ $vouchers->total() }} voucher</p>
            <div>{{ $vouchers->links() }}</div>
        </div>
        @endif
    </div>

    <div id="voucherModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVoucherModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                <button type="button" onclick="closeVoucherModal()" class="absolute right-5 top-5 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                        <h3 id="voucherModalTitle" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">Tambah Voucher</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kosongkan kuota untuk tanpa batas. Kosongkan expired untuk berlaku selamanya.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Voucher <span class="text-red-500">*</span></label>
                            <input id="voucherCode" type="text" name="code" required placeholder="HEMAT10" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm uppercase text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                            <p class="mt-1 text-xs text-gray-400">Gunakan huruf, angka, strip, atau underscore. Contoh: WELCOME50.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Diskon <span class="text-red-500">*</span></label>
                            <select id="voucherType" name="type" required class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                                <option value="percent">Persen</option>
                                <option value="fixed">Nominal Rupiah</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nilai Diskon <span class="text-red-500">*</span></label>
                            <input id="voucherValue" type="number" name="value" required min="0.01" step="0.01" placeholder="10" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimal Belanja</label>
                            <input id="voucherMinSubtotal" type="number" name="min_subtotal" min="0" step="1" value="0" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Batas Pemakaian</label>
                            <input id="voucherUsageLimit" type="number" name="usage_limit" min="1" step="1" placeholder="Kosongkan = tanpa batas" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                            <p class="mt-1 text-xs text-gray-400">Contoh: 100 berarti maksimal 100 kali dipakai.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Expired</label>
                            <input id="voucherExpiresAt" type="datetime-local" name="expires_at" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100">
                            <p class="mt-1 text-xs text-gray-400">Kosongkan jika voucher berlaku selamanya sampai dinonaktifkan atau kuotanya habis.</p>
                        </div>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-800/40 dark:bg-blue-900/20 sm:col-span-2">
                            <input id="voucherIsActive" type="checkbox" name="is_active" value="1" checked class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Aktifkan voucher</span>
                                <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">Jika nonaktif, voucher tetap tersimpan tapi tidak bisa dipakai di checkout.</span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" onclick="closeVoucherModal()" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-600 dark:text-gray-200">Batal</button>
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Simpan Voucher</button>
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
        const voucherUsageLimit = document.getElementById('voucherUsageLimit');
        const voucherExpiresAt = document.getElementById('voucherExpiresAt');
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
            voucherUsageLimit.value = '';
            voucherExpiresAt.value = '';
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
            voucherUsageLimit.value = voucher.usage_limit || '';
            voucherExpiresAt.value = voucher.expires_at || '';
            voucherIsActive.checked = Boolean(voucher.is_active);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeVoucherModal();
        });

        @if($errors->any())
            openAddVoucherModal();
        @endif
    </script>
    @endif
</x-layouts.admin>
