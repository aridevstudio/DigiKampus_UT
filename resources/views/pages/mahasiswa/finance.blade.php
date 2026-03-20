<x-layouts.dashboard :active="'finance'">

<div class="mb-6 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Finance</h1>
    <p class="text-gray-500 dark:text-gray-400">Riwayat pembayaran Midtrans dan status transaksi kursus Anda</p>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 animate-fade-in-up">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</p>
        <p class="mt-2 text-xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran Berhasil</p>
        <p class="mt-2 text-xl font-bold text-green-600 dark:text-green-400">{{ $successCount }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu</p>
        <p class="mt-2 text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <p class="text-sm text-gray-500 dark:text-gray-400">Gagal / Expire</p>
        <p class="mt-2 text-xl font-bold text-red-600 dark:text-red-400">{{ $failedCount }}</p>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <form action="{{ route('mahasiswa.finance') }}" method="GET" class="flex flex-col gap-4 md:flex-row">
        <div class="flex-1">
            <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari order ID atau nama kursus..." class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-[#111827] dark:text-gray-200">
        </div>
        <select name="date" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-[#111827] dark:text-gray-200">
            <option value="">Semua Tanggal</option>
            <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
        </select>
        <select name="status" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-[#111827] dark:text-gray-200">
            <option value="semua" {{ $selectedStatus === 'semua' ? 'selected' : '' }}>Semua Status</option>
            <option value="aktif" {{ $selectedStatus === 'aktif' ? 'selected' : '' }}>Berhasil</option>
            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="gagal" {{ $selectedStatus === 'gagal' ? 'selected' : '' }}>Gagal</option>
        </select>
        <button type="submit" class="rounded-xl bg-blue-500 px-4 py-2.5 font-medium text-white transition hover:bg-blue-600">Filter</button>
        <a href="{{ route('mahasiswa.finance') }}" class="rounded-xl border border-gray-200 px-4 py-2.5 text-center font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700/50">Reset</a>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-700/50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Order ID</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Kursus</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Metode</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Jumlah</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($transactions as $transaction)
                @php
                    $status = $transaction->effective_transaction_status;
                    $courseNames = $transaction->items->pluck('course_name')->filter()->values();
                @endphp
                <tr onclick="window.location='{{ route('mahasiswa.transaction-detail', ['id' => $transaction->id_payment_transaction]) }}'" class="cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $transaction->order_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        @if($courseNames->count() > 1)
                            {{ $courseNames->count() }} kursus
                        @else
                            {{ $courseNames->first() ?? '-' }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ strtoupper($transaction->payment_method ?: 'midtrans') }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if(in_array($status, ['settlement', 'capture'], true))
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-500/20 dark:text-green-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            Berhasil
                        </span>
                        @elseif($status === 'pending')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                            Menunggu
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-500/20 dark:text-red-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            Gagal
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 dark:border-gray-700/50">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
        </p>
        <div>{{ $transactions->withQueryString()->links() }}</div>
    </div>
    @endif
</div>

</x-layouts.dashboard>
