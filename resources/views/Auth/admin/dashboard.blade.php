<x-layouts.admin :active="'dashboard'">

{{-- Header --}}
<div class="mb-4 sm:mb-6 lg:mb-8 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Dashboard Admin</h1>
    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">Ringkasan data dan aktivitas terbaru dalam sistem</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6 lg:mb-8">
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
        <div class="relative" style="height: 200px;" id="chartContainer">
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
    {{-- Announcements --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">Pengumuman Admin</h2>
            <a href="{{ route('admin.pengumuman') }}" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm">Lihat Semua</a>
        </div>
        <div class="space-y-3 sm:space-y-4">
            @forelse($recentNews as $news)
            <div class="p-3 sm:p-4 border border-gray-100 dark:border-gray-700 rounded-lg">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2 gap-1">
                    <h3 class="font-medium text-sm sm:text-base text-gray-800 dark:text-white line-clamp-1">{{ $news->judul }}</h3>
                    <span class="text-[10px] sm:text-xs text-gray-400 whitespace-nowrap">{{ $news->tanggal_publish->format('d M Y') }}</span>
                </div>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ Str::limit(strip_tags($news->konten), 120) }}</p>
                <span class="inline-block px-2 py-0.5 sm:py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] sm:text-xs rounded">{{ $news->kategori ?? 'Umum' }}</span>
            </div>
            @empty
            <div class="text-center py-6 sm:py-8">
                <p class="text-sm text-gray-400">Belum ada pengumuman</p>
            </div>
            @endforelse
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
