<x-layouts.admin :active="'dashboard'">

{{-- Header --}}
<div class="mb-4 sm:mb-6 lg:mb-8 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Dashboard Admin</h1>
    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Ringkasan data dan aktivitas terbaru dalam sistem</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6 lg:mb-8">
    {{-- Total Dosen --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-100 hover-lift">
        <div class="flex items-start justify-between mb-2 sm:mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 dark:bg-blue-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <p class="text-lg sm:text-2xl font-bold text-gray-800 dark:text-white mb-0.5 sm:mb-1">{{ number_format($totalDosen) }}</p>
        <p class="text-gray-400 text-[10px] sm:text-xs">Dosen Aktif</p>
    </div>
    
    {{-- Total Mahasiswa --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-200 hover-lift">
        <div class="flex items-start justify-between mb-2 sm:mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-indigo-50 dark:bg-indigo-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <p class="text-lg sm:text-2xl font-bold text-gray-800 dark:text-white mb-0.5 sm:mb-1">{{ number_format($totalMahasiswa) }}</p>
        <p class="text-gray-400 text-[10px] sm:text-xs">Mahasiswa Aktif</p>
    </div>
    
    {{-- Kursus Aktif --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-300 hover-lift">
        <div class="flex items-start justify-between mb-2 sm:mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-cyan-50 dark:bg-cyan-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>
        <p class="text-lg sm:text-2xl font-bold text-gray-800 dark:text-white mb-0.5 sm:mb-1">{{ number_format($kursusAktif) }}</p>
        <p class="text-gray-400 text-[10px] sm:text-xs">Kursus Aktif</p>
    </div>
    
    {{-- Pendaftaran Bulan Ini --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-400 hover-lift">
        <div class="flex items-start justify-between mb-2 sm:mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-emerald-50 dark:bg-emerald-500/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
        </div>
        <p class="text-lg sm:text-2xl font-bold text-gray-800 dark:text-white mb-0.5 sm:mb-1">{{ number_format($pendaftaranBulanIni) }}</p>
        <p class="text-gray-400 text-[10px] sm:text-xs truncate">Pendaftaran Bulan Ini</p>
    </div>
</div>

{{-- Chart & Activities --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6 lg:mb-8">
    {{-- Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white mb-2 sm:mb-4">Grafik Pendaftaran Kursus</h2>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-3 sm:mb-4">Pendaftaran Kursus Minggu Ini ({{ now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d M') }} - {{ now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d M Y') }})</p>
        <div class="relative mhs-chart-container" style="height: clamp(180px, 28vw, 260px);" id="chartContainer">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>
    
    {{-- Activities --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white mb-3 sm:mb-4">Aktivitas Terbaru</h2>
        <div class="space-y-3 sm:space-y-4 max-h-[280px] sm:max-h-[320px] lg:max-h-none overflow-y-auto">
            @forelse($recentActivities as $activity)
            <div class="flex items-start gap-2 sm:gap-3">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full {{ $activity['icon_color'] }} flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0">{{ $activity['initials'] }}</div>
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-800 dark:text-white">{{ $activity['title'] }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-500 truncate">{{ $activity['description'] }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 sm:mt-1">{{ $activity['time']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-400">Belum ada aktivitas</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Announcements & Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    {{-- Broadcast Announcements --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">Broadcast Pengumuman</h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Ringkasan broadcast terbaru untuk mahasiswa.</p>
                </div>
                <a href="{{ route('admin.pengumuman') }}"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    Kelola Broadcast
                </a>
            </div>
            <div class="mt-4 rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-700/20 px-3 sm:px-4 py-2 flex items-center justify-between gap-3">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Menampilkan broadcast terbaru yang sudah aktif</p>
                <span class="text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400">Total: {{ $recentNews->count() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100/60 dark:from-gray-700/60 dark:to-gray-700/20">
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Publish</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-center text-[11px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($recentNews as $index => $news)
                    <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-colors">
                        <td class="px-4 sm:px-6 py-4">
                            <span class="font-medium text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 dark:text-white truncate max-w-[130px] sm:max-w-[220px] lg:max-w-[320px]">{{ $news->judul }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[130px] sm:max-w-[220px] lg:max-w-[320px]">{{ Str::limit(strip_tags($news->konten), 65) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            @php
                                $kategoriColors = [
                                    'pengumuman' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                    'berita' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                    'event' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $kategoriColors[$news->kategori] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($news->kategori ?? 'Umum') }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $news->tanggal_publish->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            @if($news->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center">
                            <a href="{{ route('admin.pengumuman') }}"
                                class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/10 rounded-lg transition"
                                title="Kelola Pengumuman">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="text-gray-400 dark:text-gray-500 font-medium">Belum ada broadcast pengumuman</p>
                            <a href="{{ route('admin.pengumuman') }}" class="inline-flex items-center mt-3 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                Buka halaman kelola pengumuman
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white mb-3 sm:mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-2 sm:gap-3">
            <a href="{{ route('admin.dosen') }}" class="flex items-center justify-center gap-2 w-full py-2.5 sm:py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium text-sm transition">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Dosen
            </a>
            <a href="{{ route('admin.mahasiswa') }}" class="flex items-center justify-center gap-2 w-full py-2.5 sm:py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium text-sm transition">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Tambah Mahasiswa
            </a>
            <a href="{{ route('admin.kursus') }}" class="flex items-center justify-center gap-2 w-full py-2.5 sm:py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium text-sm transition">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Tambah Kursus Baru
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('enrollmentChart').getContext('2d');
    const chartData = @json($chartData);
    let enrollmentChart = null;

    function getChartConfig() {
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // Responsive chart container height
        const chartContainer = document.getElementById('chartContainer');
        if (chartContainer) {
            chartContainer.style.height = isMobile ? '180px' : '220px';
        }

        return {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pendaftaran',
                    data: chartData,
                    backgroundColor: '#3b82f6',
                    borderRadius: isMobile ? 4 : 6,
                    barThickness: isMobile ? 16 : (isTablet ? 28 : 40)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: { 
                            color: '#6b7280',
                            font: { size: isMobile ? 10 : 12 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            color: '#6b7280',
                            font: { size: isMobile ? 10 : 12 }
                        }
                    }
                }
            }
        };
    }

    enrollmentChart = new Chart(ctx, getChartConfig());

    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (enrollmentChart) {
                enrollmentChart.destroy();
            }
            enrollmentChart = new Chart(ctx, getChartConfig());
        }, 250);
    });
});
</script>
@endpush

</x-layouts.admin>
