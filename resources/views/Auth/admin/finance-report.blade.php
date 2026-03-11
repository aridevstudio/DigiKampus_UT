<x-layouts.admin title="Finance Report" active="finance-report">
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Finance Report</h1>
        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Ringkasan pendapatan platform dari kursus, webinar, dan tiket (frontend demo).</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-4 sm:mb-6 lg:mb-8">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">Finance Report</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Data pendapatan gabungan ditampilkan sebagai simulasi frontend.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button id="financeExportBtn" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                        </svg>
                        Export Excel
                    </button>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">Bulan Ini</span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Q1 2026</span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Data Demo</span>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</p>
                    <p id="finance-total-revenue" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp 0</p>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">+12.5% vs bulan lalu</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kursus</p>
                    <p id="finance-rev-course" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp 0</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Kontribusi utama</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Webinar</p>
                    <p id="finance-rev-webinar" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp 0</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Live event income</p>
                </div>
                <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 p-3 sm:p-4">
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiket Event</p>
                    <p id="finance-rev-ticket" class="mt-1 text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">Rp 0</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Seminar/workshop</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
                <div class="xl:col-span-2 rounded-xl border border-gray-100 dark:border-gray-700/60 p-4">
                    <h3 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-white">Trend Revenue 6 Bulan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total pendapatan gabungan per bulan.</p>
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

        const financeData = {
            monthlyRevenue: [72500000, 81200000, 79800000, 90500000, 101500000, 112300000],
            monthlyLabels: ['Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar'],
            channels: { kursus: 67400000, webinar: 27800000, tiket: 17100000 },
            topProducts: [
                { name: 'Kursus Data Analyst Pro', type: 'Kursus', tx: 142, revenue: 35500000 },
                { name: 'Webinar AI for Campus', type: 'Webinar', tx: 224, revenue: 21400000 },
                { name: 'Tiket Seminar EduTech 2026', type: 'Tiket', tx: 87, revenue: 17100000 },
                { name: 'Kursus UI/UX Dasar', type: 'Kursus', tx: 96, revenue: 18200000 },
                { name: 'Webinar Product Management', type: 'Webinar', tx: 73, revenue: 6400000 },
            ]
        };

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

        function exportFinanceToExcel() {
            const total = financeData.channels.kursus + financeData.channels.webinar + financeData.channels.tiket;
            const rows = financeData.topProducts.map((item) => `<tr><td>${item.name}</td><td>${item.type}</td><td>${item.tx}</td><td>${item.revenue}</td></tr>`).join('');
            const html = `
                <html><head><meta charset="UTF-8"></head><body>
                <table border="1">
                    <tr><th colspan="2">Finance Summary</th></tr>
                    <tr><td>Total Revenue</td><td>${total}</td></tr>
                    <tr><td>Revenue Kursus</td><td>${financeData.channels.kursus}</td></tr>
                    <tr><td>Revenue Webinar</td><td>${financeData.channels.webinar}</td></tr>
                    <tr><td>Revenue Tiket</td><td>${financeData.channels.tiket}</td></tr>
                </table><br/>
                <table border="1">
                    <tr><th>Produk</th><th>Kategori</th><th>Transaksi</th><th>Revenue</th></tr>
                    ${rows}
                </table>
                </body></html>
            `;
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `finance-report-${new Date().toISOString().slice(0, 10)}.xls`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
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
        document.getElementById('financeExportBtn')?.addEventListener('click', exportFinanceToExcel);

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(renderCharts, 250);
        });
    });
    </script>
    @endpush
</x-layouts.admin>
