<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password Dosen - SALUT</title>
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
            <div class="absolute top-[-80px] left-1/2 transform -translate-x-1/2 w-[300px] h-[300px] rounded-full border-[40px] border-[#A8D4F0]/40"></div>
            <div class="absolute bottom-[-100px] right-[-50px] w-[250px] h-[250px] rounded-full border-[35px] border-[#A8D4F0]/30"></div>
            <div class="absolute bottom-[20%] left-[-80px] w-[200px] h-[200px] rounded-full border-[30px] border-[#A8D4F0]/25"></div>
            
            {{-- Illustration --}}
            <div class="relative z-10 px-8">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Admin Forgot Password Illustration" 
                    class="w-full max-w-md xl:max-w-lg object-contain"
                    style="filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));"
                >
            </div>
        </div>

        {{-- Right Side - Form --}}
        <div class="flex-1 flex items-center justify-center px-16 py-12">
            <div class="w-full max-w-md">
                {{-- Header --}}
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Lupa Password Dosen
                    </h1>
                    <p class="text-gray-500 text-base">
                        Masukkan email Anda untuk menerima kode OTP reset password.
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

                {{-- Form --}}
                <form method="POST" action="{{ route('dosen.forgot-password.post') }}" class="space-y-6">
                    @csrf

                    {{-- Email Input --}}
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
                            class="w-full border border-gray-300 rounded-lg pl-12 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="nama.dosen@ut.ac.id"
                        >
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn-desktop"
                        class="w-full bg-[#3B9BD9] hover:bg-[#2d8bc7] active:bg-[#257db5] text-white font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <svg id="loading-spinner-desktop" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text-desktop">Kirim OTP</span>
                    </button>
                </form>

                {{-- Back to Login --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('dosen.login') }}" class="text-sm text-blue-500 hover:text-blue-600 hover:underline">
                        ← Kembali ke Login
                    </a>
                </div>

                {{-- Footer --}}
                <div class="mt-8 flex items-center justify-between text-xs text-gray-400">
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
                    alt="Admin Forgot Password Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Lupa Password Dosen
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    Masukkan email Anda untuk menerima kode OTP reset password.
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
            <form method="POST" action="{{ route('dosen.forgot-password.post') }}" class="w-full space-y-5">
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

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    id="submit-btn-mobile"
                    class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <svg id="loading-spinner-mobile" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text-mobile">Kirim OTP</span>
                </button>
            </form>

            {{-- Back to Login --}}
            <div class="mt-4 text-center">
                <a href="{{ route('dosen.login') }}" class="text-sm text-blue-500 hover:text-blue-600 hover:underline">
                    ← Kembali ke Login
                </a>
            </div>

            {{-- Footer --}}
            <div class="w-full mt-6 flex items-center justify-between text-[10px] text-gray-400">
                <a href="#" class="hover:underline">Privacy Policy</a>
                <span>2025 © Universitas Terbuka</span>
            </div>
        </div>
    </section>

    @vite('resources/js/app.js')
    
    <script>
        // Desktop form loading
        document.querySelector('.hidden.lg\\:flex form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn-desktop');
            const spinner = document.getElementById('loading-spinner-desktop');
            const btnText = document.getElementById('btn-text-desktop');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Loading...';
        });

        // Mobile form loading
        document.querySelector('.lg\\:hidden form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn-mobile');
            const spinner = document.getElementById('loading-spinner-mobile');
            const btnText = document.getElementById('btn-text-mobile');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Loading...';
        });
    </script>
</body>
</html>
