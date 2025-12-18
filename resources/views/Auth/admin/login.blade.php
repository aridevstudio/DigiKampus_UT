<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen">
    {{-- Desktop View (lg and above) - Original Design --}}
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
                    alt="Admin Login Illustration" 
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
                        Masuk sebagai Admin
                    </h1>
                    <p class="text-gray-500 text-base">
                        Silakan masuk untuk mengelola data dosen, mahasiswa, kursus dan memantau progres kursus.
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-600">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Alert Messages --}}
                @if (session('alert'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ session('alert') }}</p>
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                    @csrf

                    {{-- Email Input --}}
                    <div class="relative animate-hidden animate-fade-in-up stagger-1">
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
                            placeholder="nama.admin@ut.ac.id"
                        >
                    </div>

                    {{-- Password Input --}}
                    <div class="relative animate-hidden animate-fade-in-up stagger-2">
                        <input 
                            type="password" 
                            name="password" 
                            id="password-desktop"
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                            placeholder="Password"
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

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between animate-hidden animate-fade-in-up stagger-3">
                        <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-blue-500 focus:ring-blue-500 mr-2 checkbox-animate">
                            Ingat saya
                        </label>
                        <a href="{{ route('admin.forgot-password') }}" class="text-sm text-blue-500 hover:text-blue-600 link-animate">
                            Forgot Password?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="w-full bg-[#3B9BD9] hover:bg-[#2d8bc7] active:bg-[#257db5] text-white font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed btn-animate animate-hidden animate-fade-in-up stagger-4"
                    >
                        <span>Masuk Sekarang</span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-8 flex items-center justify-between text-xs text-gray-400 footer-animate">
                    <a href="#" class="hover:underline">Privacy Policy</a>
                    <span>2025 © Universitas Terbuka</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile/Tablet View (below lg) - Clean Design like Mahasiswa --}}
    <section class="lg:hidden min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 py-6 sm:py-8">
        <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center">
            
            {{-- Mobile Illustration --}}
            <div class="w-full flex justify-center mb-6">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Admin Login Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Masuk sebagai Admin
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    Silakan masuk untuk mengelola data dosen, mahasiswa, kursus dan memantau progres kursus.
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
            <form method="POST" action="{{ route('admin.login.post') }}" class="w-full space-y-4">
                @csrf

                {{-- Email Input --}}
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
                        class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="nama.admin@ut.ac.id"
                    >
                </div>

                {{-- Password Input --}}
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password-mobile"
                        required 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Password"
                    >
                    <button 
                        type="button" 
                        onclick="togglePasswordMobile()"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
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

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-xs text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-blue-500 focus:ring-blue-500 mr-2">
                        Ingat saya
                    </label>
                    <a href="{{ route('admin.forgot-password') }}" class="text-xs text-blue-500 hover:text-blue-600 hover:underline">
                        Forgot Password?
                    </a>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <span>Masuk Sekarang</span>
                </button>
            </form>

            {{-- Footer --}}
            <div class="w-full mt-6 flex items-center justify-between text-[10px] text-gray-400">
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
    </script>
</body>
</html>
