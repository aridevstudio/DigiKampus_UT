<x-layouts.dashboard :active="'profile'">
@php
    $user = Auth::guard('mahasiswa')->user();
    $profile = $user?->profile;
    $jurusan = $profile?->jurusan;
    
    // User data
    $userName = $user?->name ?? '';
    $userEmail = $user?->email ?? '';
    
    // Profile data
    $nim = $profile?->nim ?? '';
    $noHp = $profile?->no_hp ?? '';
    $alamat = $profile?->alamat ?? '';
    $tempatLahir = $profile?->tempat_lahir ?? '';
    $tanggalLahir = $profile?->tanggal_lahir ?? '';
    $jenisKelamin = $profile?->jenis_kelamin ?? 'L';
    $fotoProfile = $profile?->foto_profile ? asset('storage/' . $profile->foto_profile) : null;
    $visibilitas = $profile?->visibilitas ?? true;
    $bio = $profile?->bio ?? '';
    
    // Jurusan data (read-only)
    $programStudi = $jurusan?->nama_jurusan ?? 'Belum diisi';
    $fakultas = $jurusan?->fakultas ?? 'Belum diisi';
    
    // Dummy
    $tahunMasuk = '2021';
@endphp

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Profil</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Perbarui data pribadi dan informasi kontak kamu.</p>
</div>

<form action="{{ route('mahasiswa.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    {{-- Main Content - Two Columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Left Column - Data Diri --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-100 hover-lift">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Data Diri</h2>
            
            {{-- Profile Photo --}}
            <div class="flex flex-col items-center mb-6">
                <div class="relative mb-2">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 border-4 border-white dark:border-gray-700 shadow-lg">
                        <img id="preview-foto" src="{{ $fotoProfile ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&size=128&background=3b82f6&color=fff' }}" alt="Profile Photo" class="w-full h-full object-cover">
                    </div>
                    <label for="foto_profile" class="absolute bottom-0 right-0 w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center text-white shadow-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </label>
                    <input type="file" id="foto_profile" name="foto_profile" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
                <label for="foto_profile" class="text-blue-500 hover:text-blue-600 text-sm font-medium cursor-pointer">Ganti Foto</label>
            </div>
            
            {{-- Nama Lengkap --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Nama Lengkap
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $userName) }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Masukkan nama lengkap"
                >
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- NIM --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    NIM
                </label>
                <input 
                    type="text" 
                    value="{{ $nim }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    readonly
                    disabled
                >
                <p class="text-gray-400 text-xs mt-1">NIM tidak dapat diubah</p>
            </div>
            
            {{-- Tempat Lahir --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Tempat Lahir
                </label>
                <input 
                    type="text" 
                    name="tempat_lahir" 
                    value="{{ old('tempat_lahir', $tempatLahir) }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Contoh: Jakarta"
                >
                @error('tempat_lahir')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Tanggal Lahir
                </label>
                <input 
                    type="date" 
                    name="tanggal_lahir" 
                    value="{{ old('tanggal_lahir', $tanggalLahir) }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                @error('tanggal_lahir')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Jenis Kelamin --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Jenis Kelamin
                </label>
                <select 
                    name="jenis_kelamin"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none cursor-pointer"
                >
                    <option value="L" {{ old('jenis_kelamin', $jenisKelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $jenisKelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Alamat --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Alamat
                </label>
                <textarea 
                    name="alamat" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                    placeholder="Masukkan alamat lengkap"
                >{{ old('alamat', $alamat) }}</textarea>
                @error('alamat')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Nomor Telepon --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Nomor Telepon
                </label>
                <input 
                    type="tel" 
                    name="no_hp" 
                    value="{{ old('no_hp', $noHp) }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Contoh: 081234567890"
                >
                @error('no_hp')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bio --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Bio
                </label>
                <textarea 
                    name="bio" 
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                    placeholder="Tuliskan bio singkat tentang dirimu"
                >{{ old('bio', $bio) }}</textarea>
                @error('bio')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        {{-- Right Column - Data Akun & Akademik --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200 hover-lift">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Data Akun & Akademik</h2>
            
            {{-- Email --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Email
                </label>
                <input 
                    type="email" 
                    value="{{ $userEmail }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    readonly
                    disabled
                >
            </div>
            
            {{-- Kata Sandi --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Kata Sandi
                </label>
                
                {{-- Current Password Display --}}
                <div id="password-display" class="flex gap-3">
                    <input 
                        type="password" 
                        value="********"
                        class="flex-1 px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                        readonly
                        disabled
                    >
                    <button type="button" onclick="togglePasswordForm()" class="px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium whitespace-nowrap">
                        Ubah Kata Sandi
                    </button>
                </div>
                
                {{-- Password Change Form (Hidden by default) --}}
                <div id="password-form" class="hidden space-y-3 mt-3 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Kata Sandi Baru</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password-input"
                                name="password"
                                oninput="validatePassword(this.value)"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                                placeholder="Masukkan kata sandi baru"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password-input', 'eye-icon-1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg id="eye-icon-1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        {{-- Password Requirements --}}
                        <div class="mt-3 space-y-1">
                            <div id="req-length" class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4 req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                </svg>
                                <span>Minimal 8 karakter</span>
                            </div>
                            <div id="req-uppercase" class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4 req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                </svg>
                                <span>Minimal 1 huruf besar (A-Z)</span>
                            </div>
                            <div id="req-lowercase" class="flex items-center gap-2 text-sm text-gray-400">
                                <svg class="w-4 h-4 req-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                </svg>
                                <span>Minimal 1 huruf kecil (a-z)</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password-confirm-input"
                                name="password_confirmation"
                                oninput="validatePasswordMatch()"
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                                placeholder="Konfirmasi kata sandi baru"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password-confirm-input', 'eye-icon-2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg id="eye-icon-2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p id="match-status" class="text-xs mt-1 hidden"></p>
                    </div>
                    <button type="button" onclick="togglePasswordForm()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Batal ubah kata sandi
                    </button>
                </div>
            </div>
            
            {{-- Program Studi --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Program Studi
                </label>
                <input 
                    type="text" 
                    value="{{ $programStudi }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    readonly
                    disabled
                >
            </div>
            
            {{-- Fakultas --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Fakultas
                </label>
                <input 
                    type="text" 
                    value="{{ $fakultas }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    readonly
                    disabled
                >
            </div>
            
            {{-- Tahun Masuk --}}
            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Tahun Masuk
                </label>
                <input 
                    type="text" 
                    value="{{ $tahunMasuk }}"
                    class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                    readonly
                    disabled
                >
            </div>
            
            {{-- Visibilitas Profil --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-gray-800 dark:text-gray-100">Visibilitas Profil</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tampilkan profil saya ke dosen dan teman kuliah</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="visibilitas" value="1" class="sr-only peer" {{ $visibilitas ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-500"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Footer Buttons --}}
    <div class="flex justify-center gap-4 animate-fade-in-up delay-300">
        <a href="{{ route('mahasiswa.profile') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition font-medium">
            Batalkan
        </a>
        <button type="submit" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition btn-pulse">
            Simpan Perubahan
        </button>
    </div>
</form>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-foto').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePasswordForm() {
        const display = document.getElementById('password-display');
        const form = document.getElementById('password-form');
        
        if (form.classList.contains('hidden')) {
            display.classList.add('hidden');
            form.classList.remove('hidden');
        } else {
            display.classList.remove('hidden');
            form.classList.add('hidden');
            // Clear password fields and reset validation when canceling
            form.querySelectorAll('input[type="password"]').forEach(input => input.value = '');
            resetPasswordValidation();
        }
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            `;
        } else {
            input.type = 'password';
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
        }
    }

    function validatePassword(password) {
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);

        updateRequirement('req-length', hasLength);
        updateRequirement('req-uppercase', hasUppercase);
        updateRequirement('req-lowercase', hasLowercase);
        
        // Also validate match if confirm field has value
        validatePasswordMatch();
    }

    function updateRequirement(elementId, isValid) {
        const element = document.getElementById(elementId);
        const icon = element.querySelector('.req-icon');
        
        if (isValid) {
            element.classList.remove('text-gray-400');
            element.classList.add('text-green-500');
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            `;
        } else {
            element.classList.remove('text-green-500');
            element.classList.add('text-gray-400');
            icon.innerHTML = `<circle cx="12" cy="12" r="10" stroke-width="2"/>`;
        }
    }

    function validatePasswordMatch() {
        const password = document.getElementById('password-input').value;
        const confirm = document.getElementById('password-confirm-input').value;
        const status = document.getElementById('match-status');
        
        if (confirm.length === 0) {
            status.classList.add('hidden');
            return;
        }
        
        status.classList.remove('hidden');
        
        if (password === confirm) {
            status.textContent = '✓ Kata sandi cocok';
            status.classList.remove('text-red-500');
            status.classList.add('text-green-500');
        } else {
            status.textContent = '✗ Kata sandi tidak cocok';
            status.classList.remove('text-green-500');
            status.classList.add('text-red-500');
        }
    }

    function resetPasswordValidation() {
        ['req-length', 'req-uppercase', 'req-lowercase'].forEach(id => {
            const element = document.getElementById(id);
            element.classList.remove('text-green-500');
            element.classList.add('text-gray-400');
            element.querySelector('.req-icon').innerHTML = `<circle cx="12" cy="12" r="10" stroke-width="2"/>`;
        });
        document.getElementById('match-status').classList.add('hidden');
    }
</script>
@endpush

</x-layouts.dashboard>
