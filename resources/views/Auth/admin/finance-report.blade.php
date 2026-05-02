<x-layouts.admin title="Finance Report" active="finance-report">
    @php
        $momPercent = (float) ($financeSummary['momPercent'] ?? 0);
        $momClass = $momPercent >= 0
            ? 'text-emerald-600 dark:text-emerald-400'
            : 'text-rose-600 dark:text-rose-400';
        $momPrefix = $momPercent > 0 ? '+' : '';
        $compareLabel = $financeSummary['compareLabel'] ?? 'vs bulan lalu';
        $financePayload = $financeChartPayload ?? [
            'monthlyRevenue' => [],
            'monthlyLabels' => [],
            'chartTitle' => 'Trend Revenue 6 Bulan',
            'chartSubtitle' => 'Total pendapatan gabungan per bulan.',
            'channels' => [
                'kursus' => 0,
                'webinar' => 0,
                'tiket' => 0,
            ],
            'topProducts' => [],
        ];
        $financeFilter = $financeFilter ?? [
            'year' => null,
            'month' => null,
            'availableYears' => [],
            'availableMonths' => [],
            'label' => '6 Bulan Terakhir',
        ];
        $financeServiceFee = $financeServiceFee ?? [
            'current' => 5000,
            'collected' => 0,
        ];
    @endphp

    <div class="mb-4 sm:mb-6 lg:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Finance Report</h1>
        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Ringkasan pendapatan platform dari kursus, webinar, dan tiket berbasis transaksi pembayaran berhasil.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-4 sm:mb-6 lg:mb-8">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">Finance Report</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Data live dari transaksi dengan status settlement/capture.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:items-end">
                    <form method="GET" action="{{ route('admin.finance-report') }}" class="grid grid-cols-1 sm:grid-cols-[minmax(0,150px)_minmax(0,150px)_auto_auto] gap-2 w-full lg:w-auto">
                        <select name="year" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs sm:text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="">Semua Tahun</option>
                            @foreach(($financeFilter['availableYears'] ?? []) as $year)
                                <option value="{{ $year }}" {{ (int) ($financeFilter['year'] ?? 0) === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <select name="month" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs sm:text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="">Semua Bulan</option>
                            @foreach(($financeFilter['availableMonths'] ?? []) as $monthValue => $monthLabel)
                                <option value="{{ $monthValue }}" {{ (int) ($financeFilter['month'] ?? 0) === (int) $monthValue ? 'selected' : '' }}>{{ $monthLabel }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-xs sm:text-sm font-medium text-white transition hover:bg-blue-700">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.finance-report') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                            Reset
                        </a>
                    </form>
                    <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.finance-report.export', array_filter(['year' => $financeFilter['year'] ?? null, 'month' => $financeFilter['month'] ?? null], fn ($value) => filled($value))) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                        Export Excel
                    </a>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">{{ $financeFilter['label'] ?? '6 Bulan Terakhir' }}</span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $financeSummary['periodLabel'] ?? '-' }}</span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Live Data</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)] gap-4 sm:gap-6">
                <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 via-white to-cyan-50 p-4 sm:p-5 dark:border-blue-500/20 dark:from-blue-500/10 dark:via-gray-800 dark:to-cyan-500/10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white">Biaya Layanan Global</span>
                            <h3 class="mt-3 text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Setting checkout untuk semua pembelian kursus</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Nominal ini dipakai otomatis saat mahasiswa checkout. Perubahan hanya berlaku untuk transaksi baru setelah setting disimpan.</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200/70 bg-white/90 px-4 py-3 shadow-sm backdrop-blur dark:border-blue-400/20 dark:bg-gray-900/70">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-blue-600 dark:text-blue-300">Nominal Aktif</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format((int) ($financeServiceFee['current'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-white/80 bg-white/80 px-4 py-3 dark:border-gray-700/60 dark:bg-gray-900/50">
                            <p class="text-[11px] uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Fee Terkumpul Pada Periode</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format((int) ($financeServiceFee['collected'] ?? 0), 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Diambil dari transaksi settlement/capture pada filter aktif.</p>
                        </div>
                        <div class="rounded-xl border border-white/80 bg-white/80 px-4 py-3 dark:border-gray-700/60 dark:bg-gray-900/50">
                            <p class="text-[11px] uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Scope</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Semua Checkout Kursus</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Saat ini masih flat per transaksi, bukan per item kursus.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.finance-report.service-fee.update') }}" class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    @csrf
                    <input type="hidden" name="year" value="{{ $financeFilter['year'] ?? '' }}">
                    <input type="hidden" name="month" value="{{ $financeFilter['month'] ?? '' }}">

                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Update Biaya Layanan</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Masukkan nominal rupiah tanpa titik atau simbol.</p>
                        </div>
                        <div class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Live</div>
                    </div>

                    <label for="course_service_fee" class="mt-4 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Nominal Biaya Layanan</label>
                    <div class="mt-2 flex items-center rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900">
                        <span class="mr-3 text-sm font-semibold text-gray-500 dark:text-gray-400">Rp</span>
                        <input
                            id="course_service_fee"
                            name="course_service_fee"
                            type="number"
                            min="0"
                            step="1"
                            value="{{ old('course_service_fee', (int) ($financeServiceFee['current'] ?? 0)) }}"
                            class="w-full border-0 bg-transparent p-0 text-base font-semibold text-gray-900 outline-none focus:ring-0 dark:text-white"
                            placeholder="5000"
                        >
                    </div>

                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                        Perubahan nominal tidak mengubah transaksi lama. Hanya checkout baru yang akan memakai biaya layanan ini.
                    </div>

                    <button type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Simpan Biaya Layanan
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</p>
                    <p id="finance-total-revenue" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financeSummary['totalRevenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-[11px] {{ $momClass }} mt-1">{{ $momPrefix }}{{ number_format($momPercent, 1, ',', '.') }}% {{ $compareLabel }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kursus</p>
                    <p id="finance-rev-course" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financeSummary['channels']['kursus'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Kontribusi utama</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Webinar</p>
                    <p id="finance-rev-webinar" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financeSummary['channels']['webinar'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Live event income</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiket Event</p>
                    <p id="finance-rev-ticket" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financeSummary['channels']['tiket'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Seminar/workshop</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <div class="xl:col-span-2 rounded-xl border border-gray-100 dark:border-gray-700/60 p-4">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">{{ $financePayload['chartTitle'] ?? 'Trend Revenue 6 Bulan' }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $financePayload['chartSubtitle'] ?? 'Total pendapatan gabungan per bulan.' }}</p>
                    <div class="relative mt-3" style="height: clamp(210px, 28vw, 280px);">
                        <canvas id="financeRevenueChart"></canvas>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 p-4">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">Komposisi Pendapatan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Distribusi channel produk saat ini.</p>
                    <div class="relative mt-3 mx-auto" style="height: 220px; max-width: 280px;">
                        <canvas id="financeCompositionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50/70 dark:bg-gray-700/20 border-b border-gray-100 dark:border-gray-700/60">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Top Produk (By Revenue)</h3>
                </div>
                <div class="overflow-x-auto responsive-table">
                    <table class="w-full responsive-data-table admin-desktop-table admin-mobile-list text-sm">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaksi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="finance-top-products-body" class="divide-y divide-gray-100 dark:divide-gray-700/60"></tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 p-4">
                <h3 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">Performa Channel Revenue</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Perbandingan nominal pendapatan antar channel.</p>
                <div class="relative mt-3" style="height: clamp(200px, 24vw, 260px);">
                    <canvas id="financeChannelBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let financeRevenueChart = null;
        let financeCompositionChart = null;
        let financeChannelBarChart = null;

        const financeData = @json($financePayload);

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        }

        function initFinanceSummary() {
            const total = financeData.channels.kursus + financeData.channels.webinar + financeData.channels.tiket;
            document.getElementById('finance-total-revenue').textContent = formatRupiah(total);
            document.getElementById('finance-rev-course').textContent = formatRupiah(financeData.channels.kursus);
            document.getElementById('finance-rev-webinar').textContent = formatRupiah(financeData.channels.webinar);
            document.getElementById('finance-rev-ticket').textContent = formatRupiah(financeData.channels.tiket);

            const body = document.getElementById('finance-top-products-body');
            if (!financeData.topProducts || financeData.topProducts.length === 0) {
                body.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada transaksi sukses pada periode ini.
                        </td>
                    </tr>
                `;
                return;
            }

            body.innerHTML = financeData.topProducts.map((item) => `
                <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-colors">
                    <td class="px-4 py-3"><p class="font-medium text-gray-800 dark:text-white">${item.name}</p></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold ${item.type === 'Kursus' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' : (item.type === 'Webinar' ? 'bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300')}">
                            ${item.type}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">${item.tx}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">${formatRupiah(item.revenue)}</td>
                </tr>
            `).join('');
        }

        function getFinanceRevenueConfig() {
            const isMobile = window.innerWidth < 640;
            return {
                type: 'line',
                data: { labels: financeData.monthlyLabels, datasets: [{ label: 'Revenue', data: financeData.monthlyRevenue, borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.12)', pointBackgroundColor: '#1d4ed8', pointRadius: isMobile ? 2.5 : 3.5, borderWidth: 2, fill: true, tension: 0.35 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => ` ${formatRupiah(ctx.parsed.y)}` } } }, scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' }, ticks: { color: '#6b7280', font: { size: isMobile ? 10 : 12 }, callback: (v) => v >= 1000000 ? `Rp ${Math.round(v / 1000000)}jt` : `Rp ${v}` } }, x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: isMobile ? 10 : 12 } } } } }
            };
        }

        function getFinanceCompositionConfig() {
            return {
                type: 'doughnut',
                data: { labels: ['Kursus', 'Webinar', 'Tiket'], datasets: [{ data: [financeData.channels.kursus, financeData.channels.webinar, financeData.channels.tiket], backgroundColor: ['#2563eb', '#8b5cf6', '#10b981'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, color: '#6b7280', usePointStyle: true } }, tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${formatRupiah(ctx.parsed)}` } } }, cutout: '68%' }
            };
        }

        function getFinanceChannelBarConfig() {
            return {
                type: 'bar',
                data: { labels: ['Kursus', 'Webinar', 'Tiket'], datasets: [{ label: 'Revenue', data: [financeData.channels.kursus, financeData.channels.webinar, financeData.channels.tiket], backgroundColor: ['#2563eb', '#8b5cf6', '#10b981'], borderRadius: 8 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => ` ${formatRupiah(ctx.parsed.y)}` } } }, scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' }, ticks: { color: '#6b7280', callback: (v) => v >= 1000000 ? `Rp ${Math.round(v / 1000000)}jt` : `Rp ${v}` } }, x: { grid: { display: false }, ticks: { color: '#6b7280' } } } }
            };
        }

        function renderCharts() {
            if (financeRevenueChart) financeRevenueChart.destroy();
            if (financeCompositionChart) financeCompositionChart.destroy();
            if (financeChannelBarChart) financeChannelBarChart.destroy();

            const revenueCanvas = document.getElementById('financeRevenueChart');
            const compositionCanvas = document.getElementById('financeCompositionChart');
            const channelBarCanvas = document.getElementById('financeChannelBarChart');
            if (revenueCanvas) financeRevenueChart = new Chart(revenueCanvas.getContext('2d'), getFinanceRevenueConfig());
            if (compositionCanvas) financeCompositionChart = new Chart(compositionCanvas.getContext('2d'), getFinanceCompositionConfig());
            if (channelBarCanvas) financeChannelBarChart = new Chart(channelBarCanvas.getContext('2d'), getFinanceChannelBarConfig());
        }

        initFinanceSummary();
        renderCharts();

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(renderCharts, 250);
        });
    });
    </script>
    @endpush
</x-layouts.admin>
