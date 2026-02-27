<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Dosen - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen">
    {{-- Navbar --}}
    <x-auth.navbar />

    {{-- Desktop View (lg and above) --}}
    <div class="hidden lg:flex min-h-screen pt-20" style="background: linear-gradient(to right, #E8F0FE, #FFFFFF);">
        {{-- Left Side - Illustration with Blue Circles --}}
        <div class="w-1/2 relative overflow-hidden flex items-center justify-center" style="background: linear-gradient(to bottom, #EFF6FF, #DBEAFE);">
            {{-- Decorative Blue Circles --}}
            <div class="absolute top-[-80px] left-1/2 transform -translate-x-1/2 w-[300px] h-[300px] rounded-full border-[40px] border-[#A8D4F0]/40 circle-animate-1"></div>
            <div class="absolute bottom-[-100px] right-[-50px] w-[250px] h-[250px] rounded-full border-[35px] border-[#A8D4F0]/30 circle-animate-2"></div>
            <div class="absolute bottom-[20%] left-[-80px] w-[200px] h-[200px] rounded-full border-[30px] border-[#A8D4F0]/25 circle-animate-3"></div>
            
            {{-- Illustration --}}
            <div class="relative z-10 px-8 animate-slide-in-left">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Dosen Registration Illustration" 
                    class="w-full max-w-md xl:max-w-lg object-contain illustration-animate"
                    style="filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));"
                >
            </div>
        </div>

        {{-- Right Side - Registration Form --}}
        <div class="flex-1 flex items-center justify-center px-16 py-12 overflow-y-auto">
            <div class="w-full max-w-lg">
                {{-- Header --}}
                <div class="text-center mb-8 animate-hidden animate-fade-in-down mt-16">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Daftar sebagai Dosen UT
                    </h1>
                    <p class="text-gray-500 text-base">
                        Silakan lengkapi formulir di bawah untuk mendaftar sebagai Dosen.
                    </p>
                </div>

                {{-- Alert Messages --}}
                @if (session('alert'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
                        <p class="text-sm text-red-600">{{ session('alert') }}</p>
                    </div>
                @endif
                
                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
                        <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Reg Form --}}
                <form method="POST" action="{{ route('dosen.register.post') }}" class="space-y-4">
                    @csrf
                    
                    <input type="hidden" name="google_id" value="{{ session('google_id') ?? old('google_id') }}">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Name Input --}}
                        <div class="animate-hidden animate-fade-in-up stagger-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}"
                                required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Nama Lengkap dengan Gelar"
                            >
                        </div>
                        
                        {{-- NIP Input --}}
                        <div class="animate-hidden animate-fade-in-up stagger-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Induk Pegawai (NIP)</label>
                            <input 
                                type="text" 
                                name="nip" 
                                value="{{ old('nip') }}"
                                required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Masukkan NIP"
                            >
                        </div>
                    </div>

                    {{-- Email Input --}}
                    <div class="animate-hidden animate-fade-in-up stagger-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Institusi</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="nama.dosen@ut.ac.id"
                        >
                    </div>

                    {{-- Jurusan Dropdown --}}
                    <div class="animate-hidden animate-fade-in-up stagger-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Studi (Jurusan)</label>
                        <select 
                            name="id_jurusan" 
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                            <option value="">Pilih Program Studi</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Password Input --}}
                        <div class="animate-hidden animate-fade-in-up stagger-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi</label>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Min. 8 karakter"
                            >
                        </div>
                        
                        {{-- Confirm Password Input --}}
                        <div class="animate-hidden animate-fade-in-up stagger-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Ulangi kata sandi"
                            >
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="w-full mt-4 bg-[#3B9BD9] hover:bg-[#2d8bc7] active:bg-[#257db5] text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm flex items-center justify-center gap-2 animate-hidden animate-fade-in-up stagger-5"
                    >
                        <span>Daftar Sekarang</span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center my-6 animate-hidden animate-fade-in-up stagger-6">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-4 text-sm text-gray-500">atau daftar dengan</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                {{-- Google Login Button --}}
                <a 
                    href="{{ route('dosen.google.redirect') }}"
                    class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 google-btn-animate animate-hidden animate-fade-in-up stagger-6"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Daftar Dengan Google</span>
                </a>

                {{-- Login Link --}}
                <div class="mt-6 text-center animate-hidden animate-fade-in stagger-6 mb-16">
                    <p class="text-sm text-gray-500">
                        Sudah punya akun? 
                        <a href="{{ route('dosen.login') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile/Tablet View (below lg) --}}
    <section class="lg:hidden min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 pt-24 pb-6 sm:pb-8">
        <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center">
            
            {{-- Mobile Illustration --}}
            <div class="w-full flex justify-center mb-6 animate-fade-in-down">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Dosen Registration Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6 animate-fade-in-up">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Daftar sebagai Dosen UT
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    Lengkapi form di bawah untuk registrasi Dosen baru.
                </p>
            </div>

            {{-- Alert Messages --}}
            @if (session('alert'))
                <div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-xs text-red-600">{{ session('alert') }}</p>
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li class="text-xs text-red-600">{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('dosen.register.post') }}" class="w-full space-y-4">
                @csrf

                <input type="hidden" name="google_id" value="{{ session('google_id') ?? old('google_id') }}">

                {{-- Name Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ketik Nama Lengkap">
                </div>
                
                {{-- NIP Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="NIP Pegawai">
                </div>

                {{-- Email Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email Institusi</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="nama.dosen@ut.ac.id">
                </div>

                {{-- Jurusan Dropdown --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Program Studi</label>
                    <select name="id_jurusan" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Program Studi</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Password Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Sandi min 8 karakter">
                </div>
                
                {{-- Confirm Password --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ulangi Kata Sandi">
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-4 mt-2 rounded-lg transition duration-200">
                    Daftar Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="w-full flex items-center my-5">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-3 text-xs text-gray-500">atau daftar dengan</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            {{-- Google Login Button --}}
            <a 
                href="{{ route('dosen.google.redirect') }}"
                class="w-full flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 text-sm google-btn-animate"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Daftar Dengan Google</span>
            </a>

            {{-- Login Link --}}
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    Sudah punya akun? <a href="{{ route('dosen.login') }}" class="text-blue-500 hover:text-blue-600 font-medium">Masuk di sini</a>
                </p>
            </div>
            
        </div>
    </section>

    @vite('resources/js/app.js')
</body>
</html>
