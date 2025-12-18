<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Dosen - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen">
    {{-- Desktop View (lg and above) --}}
    <div class="hidden lg:flex min-h-screen" style="background: linear-gradient(to right, #E8F0FE, #FFFFFF);">
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
                    alt="Dosen Login Illustration" 
                    class="w-full max-w-md xl:max-w-lg object-contain illustration-animate"
                    style="filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));"
                >
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="flex-1 flex items-center justify-center px-16 py-12">
            <div class="w-full max-w-md">
                {{-- Header --}}
                <div class="text-center mb-8 animate-hidden animate-fade-in-down">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Masuk sebagai Dosen UT
                    </h1>
                    <p class="text-gray-500 text-base">
                        Silakan masuk untuk mengelola kelas, memantau mahasiswa, dan berbagi materi pembelajaran.
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in">
                        <p class="text-sm text-green-600">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Alert Messages --}}
                @if (session('alert'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
                        <p class="text-sm text-red-600">{{ session('alert') }}</p>
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('dosen.login.post') }}" class="space-y-4">
                    @csrf

                    {{-- Email Input --}}
                    <div class="animate-hidden animate-fade-in-up stagger-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Institusi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="w-full border border-gray-300 rounded-lg pl-12 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                                placeholder="nama.dosen@ut.ac.id"
                            >
                        </div>
                    </div>

                    {{-- Password Input --}}
                    <div class="animate-hidden animate-fade-in-up stagger-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                id="password-desktop"
                                required 
                                class="w-full border border-gray-300 rounded-lg pl-12 pr-12 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                                placeholder="Masukkan kata sandi"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordDesktop()"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 password-toggle"
                            >
                                <svg id="eye-open-desktop" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eye-closed-desktop" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between animate-hidden animate-fade-in-up stagger-3">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-blue-500 focus:ring-blue-500 mr-2 checkbox-animate">
                            Ingat saya
                        </label>
                        <a href="{{ route('dosen.forgot-password') }}" class="text-sm text-blue-500 hover:text-blue-600 link-animate">
                            Lupa Kata Sandi?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn-desktop"
                        class="w-full bg-[#3B9BD9] hover:bg-[#2d8bc7] active:bg-[#257db5] text-white font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed btn-animate animate-hidden animate-fade-in-up stagger-4"
                    >
                        <svg id="loading-spinner-desktop" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text-desktop">Masuk Sekarang</span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center my-6 animate-hidden animate-fade-in-up stagger-5">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-4 text-sm text-gray-500">or with e-mail</span>
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
                    <span>Masuk Dengan Google</span>
                </a>

                {{-- Register Link --}}
                <div class="mt-6 text-center animate-hidden animate-fade-in stagger-7">
                    <p class="text-sm text-gray-500">
                        Belum punya akun? 
                        <a href="#" class="text-blue-500 hover:text-blue-600 link-animate font-medium">
                            Daftar sebagai Dosen Baru
                        </a>
                    </p>
                </div>

                {{-- Footer --}}
                <div class="mt-8 flex items-center justify-between text-xs text-gray-400 footer-animate">
                    <a href="#" class="hover:underline">Privacy Policy</a>
                    <span>2025 © Universitas Terbuka</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile/Tablet View (below lg) --}}
    <section class="lg:hidden min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 py-6 sm:py-8">
        <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center">
            
            {{-- Mobile Illustration --}}
            <div class="w-full flex justify-center mb-6 animate-fade-in-down">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Dosen Login Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain illustration-animate"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6 animate-fade-in-up">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Masuk sebagai Dosen UT
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    Silakan masuk untuk mengelola kelas, memantau mahasiswa, dan berbagi materi pembelajaran.
                </p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="w-full mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-xs text-green-600">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Alert Messages --}}
            @if (session('alert'))
                <div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-xs text-red-600">{{ session('alert') }}</p>
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('dosen.login.post') }}" class="w-full space-y-4">
                @csrf

                {{-- Email Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email Institusi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                            placeholder="nama.dosen@ut.ac.id"
                        >
                    </div>
                </div>

                {{-- Password Input --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="password-mobile"
                            required 
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-10 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                            placeholder="Masukkan kata sandi"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordMobile()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 password-toggle"
                        >
                            <svg id="eye-open-mobile" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-closed-mobile" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-blue-500 focus:ring-blue-500 mr-2">
                        Ingat saya
                    </label>
                    <a href="{{ route('dosen.forgot-password') }}" class="text-xs text-blue-500 hover:text-blue-600 link-animate">
                        Lupa Kata Sandi?
                    </a>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    id="submit-btn-mobile"
                    class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed btn-animate"
                >
                    <svg id="loading-spinner-mobile" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text-mobile">Masuk Sekarang</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="w-full flex items-center my-5">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-3 text-xs text-gray-500">or with e-mail</span>
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
                <span>Masuk Dengan Google</span>
            </a>

            {{-- Register Link --}}
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    Belum punya akun? 
                    <a href="#" class="text-blue-500 hover:text-blue-600 link-animate font-medium">
                        Daftar sebagai Dosen Baru
                    </a>
                </p>
            </div>

            {{-- Footer --}}
            <div class="w-full mt-6 flex items-center justify-between text-[10px] text-gray-400 footer-animate">
                <a href="#" class="hover:underline">Privacy Policy</a>
                <span>2025 © Universitas Terbuka</span>
            </div>
        </div>
    </section>

    @vite('resources/js/app.js')
    
    <script>
        function togglePasswordDesktop() {
            const passwordInput = document.getElementById('password-desktop');
            const eyeOpen = document.getElementById('eye-open-desktop');
            const eyeClosed = document.getElementById('eye-closed-desktop');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }

        function togglePasswordMobile() {
            const passwordInput = document.getElementById('password-mobile');
            const eyeOpen = document.getElementById('eye-open-mobile');
            const eyeClosed = document.getElementById('eye-closed-mobile');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }

        // Form submit loading state
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                const spinner = this.querySelector('svg[id*="loading-spinner"]');
                const btnText = this.querySelector('span[id*="btn-text"]');
                
                if (btn && spinner && btnText) {
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
                    btnText.textContent = 'Loading...';
                }
            });
        });
    </script>
</body>
</html>
