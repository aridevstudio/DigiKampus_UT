<x-layouts.dashboard :active="'profile'">
@php
    $user = Auth::guard('mahasiswa')->user();
    $profile = $user?->profile;
    $jurusan = $profile?->jurusan;
    
    // User data
    $userName = $user?->name ?? 'Mahasiswa';
    $userEmail = $user?->email ?? 'email@ecampus.ut.ac.id';
    
    // Profile data (use real data if available, fallback to dummy)
    $nim = $profile?->nim ?? '0000000000';
    $noHp = $profile?->no_hp ?? '+62 812-3456-7890';
    $alamat = $profile?->alamat ?? 'Alamat belum diisi';
    $tanggalLahir = $profile?->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->translatedFormat('d F Y') : '1 Januari 2000';
    $jenisKelaminRaw = $profile?->jenis_kelamin ?? 'L';
    $jenisKelamin = $jenisKelaminRaw === 'L' ? 'Laki-laki' : ($jenisKelaminRaw === 'P' ? 'Perempuan' : $jenisKelaminRaw);
    $ipk = $profile?->ipk ?? '0.00';
    $totalSks = $profile?->total_sks ?? 0;
    $maxSks = 144;
    $statusAkademik = $profile?->status_akademik ?? 'Aktif';
    $fotoProfile = $profile?->foto_profile ? asset('storage/' . $profile->foto_profile) : asset('assets/image/default-avatar.png');
    
    // Jurusan data
    $programStudi = $jurusan?->nama_jurusan ?? 'Program Studi';
    $fakultas = $jurusan?->fakultas ?? 'Fakultas';
    $jenjang = $jurusan?->jenjang ?? 'S1';
    
    // Dummy data for features not in backend
    $tahunMasuk = '2021';
    $semesterProgress = 70;
    $kursusAktif = 4;
    $tugasDiselesaikan = 12;
    
    $kegiatanTerakhir = [
        ['icon' => 'check', 'text' => 'Menyelesaikan Modul 3 – Etika Profesi', 'color' => 'green'],
        ['icon' => 'chat', 'text' => 'Membalas diskusi di Forum Data Science', 'color' => 'blue'],
        ['icon' => 'calendar', 'text' => 'Mengikuti Webinar Manajemen, 10 Juni 2025', 'color' => 'rose'],
    ];
@endphp

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Profil Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Lihat informasi pribadi dan status akademik kamu di Universitas Terbuka.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('mahasiswa.profile.edit') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium text-sm transition flex items-center gap-2 btn-pulse">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            Edit Profil
        </a>
        <a href="{{ route('mahasiswa.dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-500 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    {{-- Left Column - Profile Card --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-100 hover-lift">
        {{-- Profile Photo & Basic Info --}}
        <div class="flex flex-col items-center text-center mb-6">
            <div class="relative mb-4">
                <div class="w-28 h-28 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 border-4 border-white dark:border-gray-700 shadow-lg">
                    <img src="{{ $fotoProfile }}" alt="Profile Photo" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName) }}&size=128&background=3b82f6&color=fff'">
                </div>
                <button class="absolute bottom-0 right-0 w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center text-white shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $userName }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">NIM: {{ $nim }}</p>
            <a href="#" class="text-blue-500 hover:text-blue-600 text-sm font-medium mt-1">{{ $programStudi }} ({{ $jenjang }})</a>
            
            <div class="flex items-center gap-2 mt-4">
                <span class="bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs font-medium">
                    Mahasiswa {{ $statusAkademik }}
                </span>
                <button class="text-gray-500 dark:text-gray-400 hover:text-blue-500 text-xs flex items-center gap-1 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-full transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                    Ubah Foto
                </button>
            </div>
        </div>
        
        {{-- Personal Information --}}
        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Informasi Pribadi</h3>
            
            <div class="space-y-4">
                {{-- Email --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $userEmail }}</p>
                    </div>
                </div>
                
                {{-- Phone --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">No. Telepon</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $noHp }}</p>
                    </div>
                </div>
                
                {{-- Address --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Alamat</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $alamat }}</p>
                    </div>
                </div>
                
                {{-- Birth Date --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tanggalLahir }}</p>
                    </div>
                </div>
                
                {{-- Gender --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $jenisKelamin }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Right Column - Academic Info --}}
    <div class="space-y-6">
        {{-- Academic Information Card --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200 hover-lift">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Informasi Akademik</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Program Studi</p>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $programStudi }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Fakultas</p>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $fakultas }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tahun Masuk</p>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $tahunMasuk }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Status Akademik</p>
                    <span class="inline-block bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 px-2 py-0.5 rounded text-xs font-medium">
                        {{ $statusAkademik }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">IPK Terakhir</p>
                    <p class="text-lg font-bold text-blue-500">{{ number_format((float)$ipk, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total SKS</p>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $totalSks }} dari {{ $maxSks }}</p>
                </div>
            </div>
        </div>
        
        {{-- Recent Activities Card --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-300 hover-lift">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Kegiatan Terakhir</h3>
            
            <div class="space-y-3">
                @foreach($kegiatanTerakhir as $kegiatan)
                <div class="flex items-center gap-3 p-3 rounded-xl 
                    @if($kegiatan['color'] === 'green') bg-green-50 dark:bg-green-500/10
                    @elseif($kegiatan['color'] === 'blue') bg-blue-50 dark:bg-blue-500/10
                    @else bg-rose-50 dark:bg-rose-500/10
                    @endif">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                        @if($kegiatan['color'] === 'green') bg-green-500/20 text-green-600 dark:text-green-400
                        @elseif($kegiatan['color'] === 'blue') bg-blue-500/20 text-blue-600 dark:text-blue-400
                        @else bg-rose-500/20 text-rose-600 dark:text-rose-400
                        @endif">
                        @if($kegiatan['icon'] === 'check')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        @elseif($kegiatan['icon'] === 'chat')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        @endif
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $kegiatan['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Bottom Section - Progress --}}
<div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-400 hover-lift">
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Progress Bar Section --}}
        <div class="flex-1">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Kemajuan Belajar Semester Ini</h3>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Progress</span>
                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $semesterProgress }}%</span>
                </div>
                <div class="w-full h-3 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full animate-progress" style="width: {{ $semesterProgress }}%"></div>
                </div>
            </div>
        </div>
        
        {{-- Stats Cards --}}
        <div class="flex flex-row lg:flex-col gap-4">
            <div class="flex-1 bg-blue-50 dark:bg-blue-500/10 rounded-xl p-4 text-center min-w-[120px] hover-scale">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $kursusAktif }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kursus Aktif</p>
            </div>
            <div class="flex-1 bg-green-50 dark:bg-green-500/10 rounded-xl p-4 text-center min-w-[120px] hover-scale">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $tugasDiselesaikan }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tugas Diselesaikan</p>
            </div>
        </div>
    </div>
</div>

</x-layouts.dashboard>
