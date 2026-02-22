<x-layouts.admin :active="'dashboard'">

{{-- Header --}}
<div class="mb-8 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Admin</h1>
    <p class="text-gray-500 dark:text-gray-400">Ringkasan data dan aktivitas terbaru dalam sistem</p>
</div>

{{-- Stats Cards --}}
<div class="flex flex-col sm:flex-row gap-4 mb-8">
    {{-- Total Dosen --}}
    <div class="flex-1 min-w-0 bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-100 hover-lift">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ number_format($totalDosen) }}</p>
        <p class="text-gray-400 text-xs">Dosen Aktif</p>
    </div>
    
    {{-- Total Mahasiswa --}}
    <div class="flex-1 min-w-0 bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-200 hover-lift">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ number_format($totalMahasiswa) }}</p>
        <p class="text-gray-400 text-xs">Mahasiswa Aktif</p>
    </div>
    
    {{-- Kursus Aktif --}}
    <div class="flex-1 min-w-0 bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-300 hover-lift">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 bg-cyan-50 dark:bg-cyan-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ number_format($kursusAktif) }}</p>
        <p class="text-gray-400 text-xs">Kursus Aktif</p>
    </div>
    
    {{-- Pendaftaran Bulan Ini --}}
    <div class="flex-1 min-w-0 bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm animate-fade-in-up delay-400 hover-lift">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ number_format($pendaftaranBulanIni) }}</p>
        <p class="text-gray-400 text-xs">Pendaftaran Bulan Ini</p>
    </div>
</div>

{{-- Chart & Activities --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Grafik Pendaftaran Kursus</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pendaftaran Kursus Mingguan</p>
        <div style="height: 250px; position: relative;">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>
    
    {{-- Activities --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</h2>
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">DS</div>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">Dosen baru bergabung</p>
                    <p class="text-xs text-gray-500">Dr. Ahmad Susanto - Fakultas FMIPA</p>
                    <p class="text-xs text-gray-400 mt-1">2 jam lalu</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">MK</div>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">Mahasiswa mendaftar kursus</p>
                    <p class="text-xs text-gray-500">45 mahasiswa baru - Statistika Dasar</p>
                    <p class="text-xs text-gray-400 mt-1">3 jam lalu</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">KB</div>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">Kursus baru dibuat</p>
                    <p class="text-xs text-gray-500">Pemrograman Web Lanjutan</p>
                    <p class="text-xs text-gray-400 mt-1">1 hari lalu</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">UD</div>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">Update data sistem</p>
                    <p class="text-xs text-gray-500">Database mahasiswa diperbarui</p>
                    <p class="text-xs text-gray-400 mt-1">2 hari lalu</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Announcements & Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Announcements --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Pengumuman Admin</h2>
            <a href="{{ route('admin.notifications') }}" class="text-blue-600 hover:text-blue-700 text-sm">Lihat Semua</a>
        </div>
        <div class="space-y-4">
            <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-gray-800 dark:text-white">Update Sistem Akademik v2.1</h3>
                    <span class="text-xs text-gray-400">15 Nov 2024</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Sistem akademik telah diperbarui dengan fitur baru untuk manajemen nilai dan absensi mahasiswa.</p>
                <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs rounded">Sistem</span>
            </div>
            <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-gray-800 dark:text-white">Jadwal Maintenance Server</h3>
                    <span class="text-xs text-gray-400">12 Nov 2024</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Maintenance server terjadwal pada tanggal 20 November 2024, pukul 02:00-05:00 WIB.</p>
                <span class="inline-block px-2 py-1 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 text-xs rounded">Penting</span>
            </div>
            <div class="p-4 border border-gray-100 dark:border-gray-700 rounded-lg">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-gray-800 dark:text-white">Pelatihan Sistem Baru untuk Dosen</h3>
                    <span class="text-xs text-gray-400">10 Nov 2024</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Diadakan pelatihan penggunaan sistem baru untuk seluruh dosen pada tanggal 25 November 2024.</p>
                <span class="inline-block px-2 py-1 bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs rounded">Pelatihan</span>
            </div>
        </div>
    </div>
    
    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('admin.dosen') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Dosen
            </a>
            <a href="{{ route('admin.mahasiswa') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Tambah Mahasiswa
            </a>
            <a href="{{ route('admin.kursus') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Pendaftaran',
                data: [120, 150, 180, 200, 170, 90, 60],
                backgroundColor: '#3b82f6',
                borderRadius: 6,
                barThickness: 40
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
                    ticks: { color: '#6b7280' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7280' }
                }
            }
        }
    });
});
</script>
@endpush

</x-layouts.admin>
