<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password Dosen - SALUT</title>
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
                    alt="Admin Reset Password Illustration" 
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
                        Reset Password Account
                    </h1>
                    <p class="text-gray-500 text-base">
                        Send, spend and save smarter
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-600">{{ session('status') }}</p>
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
                <form id="reset-form-desktop" method="POST" action="{{ route('dosen.reset-password.post') }}" onsubmit="return validatePasswordDesktop(event);" class="space-y-4">
                    @csrf

                    {{-- New Password Input --}}
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password-desktop"
                            required 
                            oninput="checkPasswordStrengthDesktop()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="New Password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password-desktop', 'eye-icon-password-desktop', 'eye-off-icon-password-desktop')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg id="eye-icon-password-desktop" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-off-icon-password-desktop" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    {{-- Password Requirements Checklist --}}
                    <div class="space-y-1.5 text-sm">
                        <div id="req-length-desktop" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-length-desktop" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div id="req-upper-desktop" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-upper-desktop" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 1 huruf besar (A-Z)</span>
                        </div>
                        <div id="req-lower-desktop" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-lower-desktop" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 1 huruf kecil (a-z)</span>
                        </div>
                    </div>

                    {{-- Confirm Password Input --}}
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password-confirm-desktop"
                            required 
                            oninput="checkPasswordMatchDesktop()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Confirm Password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password-confirm-desktop', 'eye-icon-confirm-desktop', 'eye-off-icon-confirm-desktop')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg id="eye-icon-confirm-desktop" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-off-icon-confirm-desktop" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    {{-- Password Match Indicator --}}
                    <div id="match-indicator-desktop" class="hidden items-center gap-2 text-sm">
                        <span id="match-icon-desktop" class="w-4 h-4 flex items-center justify-center">○</span>
                        <span id="match-text-desktop">Password cocok</span>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn-desktop"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg id="loading-spinner-desktop" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text-desktop">Reset Password</span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-8 text-center text-xs text-gray-400">
                    <a href="#" class="hover:underline">Privacy Policy</a> • 2025 © Universitas Terbuka
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile/Tablet View (below lg) --}}
    <section class="lg:hidden min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 py-6 sm:py-8">
        <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center">
            
            {{-- Mobile Illustration --}}
            <div class="w-full flex justify-center mb-6">
                <img 
                    src="{{ asset('assets/image/auth/Ilustrasi Login Admin.png') }}" 
                    alt="Admin Reset Password Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Reset Password Account
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    Send, spend and save smarter
                </p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="w-full mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-xs text-green-600">{{ session('status') }}</p>
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
            <form id="reset-form-mobile" method="POST" action="{{ route('dosen.reset-password.post') }}" onsubmit="return validatePasswordMobile(event);" class="w-full space-y-3">
                @csrf

                {{-- New Password Input --}}
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password-mobile"
                        required 
                        oninput="checkPasswordStrengthMobile()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="New Password"
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword('password-mobile', 'eye-icon-password-mobile', 'eye-off-icon-password-mobile')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <svg id="eye-icon-password-mobile" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-off-icon-password-mobile" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                {{-- Password Requirements Checklist --}}
                <div class="space-y-1 text-xs">
                    <div id="req-length-mobile" class="flex items-center gap-2 text-gray-400">
                        <span id="icon-length-mobile" class="w-4 h-4 flex items-center justify-center">○</span>
                        <span>Minimal 8 karakter</span>
                    </div>
                    <div id="req-upper-mobile" class="flex items-center gap-2 text-gray-400">
                        <span id="icon-upper-mobile" class="w-4 h-4 flex items-center justify-center">○</span>
                        <span>Minimal 1 huruf besar (A-Z)</span>
                    </div>
                    <div id="req-lower-mobile" class="flex items-center gap-2 text-gray-400">
                        <span id="icon-lower-mobile" class="w-4 h-4 flex items-center justify-center">○</span>
                        <span>Minimal 1 huruf kecil (a-z)</span>
                    </div>
                </div>

                {{-- Confirm Password Input --}}
                <div class="relative">
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password-confirm-mobile"
                        required 
                        oninput="checkPasswordMatchMobile()"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Confirm Password"
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword('password-confirm-mobile', 'eye-icon-confirm-mobile', 'eye-off-icon-confirm-mobile')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <svg id="eye-icon-confirm-mobile" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-off-icon-confirm-mobile" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                {{-- Password Match Indicator --}}
                <div id="match-indicator-mobile" class="hidden items-center gap-2 text-xs">
                    <span id="match-icon-mobile" class="w-4 h-4 flex items-center justify-center">○</span>
                    <span id="match-text-mobile">Password cocok</span>
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit"
                    id="submit-btn-mobile"
                    class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg id="loading-spinner-mobile" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btn-text-mobile">Reset Password</span>
                </button>
            </form>

            {{-- Footer --}}
            <div class="w-full mt-6 text-center text-[10px] text-gray-400">
                <a href="#" class="hover:underline">Privacy Policy</a> • 2025 © Universitas Terbuka
            </div>
        </div>
    </section>

    @vite('resources/js/app.js')
    
    {{-- Interactive Validation Script --}}
    <script>
        function togglePassword(inputId, eyeIconId, eyeOffIconId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);
            const eyeOffIcon = document.getElementById(eyeOffIconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // Desktop functions
        function checkPasswordStrengthDesktop() {
            const password = document.getElementById('password-desktop').value;
            
            // Check length
            const reqLength = document.getElementById('req-length-desktop');
            const iconLength = document.getElementById('icon-length-desktop');
            if (password.length >= 8) {
                reqLength.classList.remove('text-gray-400', 'text-red-500');
                reqLength.classList.add('text-green-500');
                iconLength.textContent = '✓';
            } else {
                reqLength.classList.remove('text-green-500');
                reqLength.classList.add('text-gray-400');
                iconLength.textContent = '○';
            }
            
            // Check uppercase
            const reqUpper = document.getElementById('req-upper-desktop');
            const iconUpper = document.getElementById('icon-upper-desktop');
            if (/[A-Z]/.test(password)) {
                reqUpper.classList.remove('text-gray-400', 'text-red-500');
                reqUpper.classList.add('text-green-500');
                iconUpper.textContent = '✓';
            } else {
                reqUpper.classList.remove('text-green-500');
                reqUpper.classList.add('text-gray-400');
                iconUpper.textContent = '○';
            }
            
            // Check lowercase
            const reqLower = document.getElementById('req-lower-desktop');
            const iconLower = document.getElementById('icon-lower-desktop');
            if (/[a-z]/.test(password)) {
                reqLower.classList.remove('text-gray-400', 'text-red-500');
                reqLower.classList.add('text-green-500');
                iconLower.textContent = '✓';
            } else {
                reqLower.classList.remove('text-green-500');
                reqLower.classList.add('text-gray-400');
                iconLower.textContent = '○';
            }
            
            checkPasswordMatchDesktop();
        }
        
        function checkPasswordMatchDesktop() {
            const password = document.getElementById('password-desktop').value;
            const confirmPassword = document.getElementById('password-confirm-desktop').value;
            const matchIndicator = document.getElementById('match-indicator-desktop');
            const matchIcon = document.getElementById('match-icon-desktop');
            const matchText = document.getElementById('match-text-desktop');
            
            if (confirmPassword.length > 0) {
                matchIndicator.classList.remove('hidden');
                matchIndicator.classList.add('flex');
                
                if (password === confirmPassword) {
                    matchIndicator.classList.remove('text-red-500');
                    matchIndicator.classList.add('text-green-500');
                    matchIcon.textContent = '✓';
                    matchText.textContent = 'Password cocok';
                } else {
                    matchIndicator.classList.remove('text-green-500');
                    matchIndicator.classList.add('text-red-500');
                    matchIcon.textContent = '✗';
                    matchText.textContent = 'Password tidak cocok';
                }
            } else {
                matchIndicator.classList.add('hidden');
                matchIndicator.classList.remove('flex');
            }
        }
        
        function validatePasswordDesktop(event) {
            const password = document.getElementById('password-desktop').value;
            const confirmPassword = document.getElementById('password-confirm-desktop').value;
            
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const passwordsMatch = password === confirmPassword;
            
            if (!hasLength || !hasUpper || !hasLower) {
                if (!hasLength) document.getElementById('req-length-desktop').classList.add('text-red-500');
                if (!hasUpper) document.getElementById('req-upper-desktop').classList.add('text-red-500');
                if (!hasLower) document.getElementById('req-lower-desktop').classList.add('text-red-500');
                event.preventDefault();
                return false;
            }
            
            if (!passwordsMatch) {
                event.preventDefault();
                return false;
            }
            
            const btn = document.getElementById('submit-btn-desktop');
            const spinner = document.getElementById('loading-spinner-desktop');
            const btnText = document.getElementById('btn-text-desktop');
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Loading...';
            
            return true;
        }

        // Mobile functions
        function checkPasswordStrengthMobile() {
            const password = document.getElementById('password-mobile').value;
            
            // Check length
            const reqLength = document.getElementById('req-length-mobile');
            const iconLength = document.getElementById('icon-length-mobile');
            if (password.length >= 8) {
                reqLength.classList.remove('text-gray-400', 'text-red-500');
                reqLength.classList.add('text-green-500');
                iconLength.textContent = '✓';
            } else {
                reqLength.classList.remove('text-green-500');
                reqLength.classList.add('text-gray-400');
                iconLength.textContent = '○';
            }
            
            // Check uppercase
            const reqUpper = document.getElementById('req-upper-mobile');
            const iconUpper = document.getElementById('icon-upper-mobile');
            if (/[A-Z]/.test(password)) {
                reqUpper.classList.remove('text-gray-400', 'text-red-500');
                reqUpper.classList.add('text-green-500');
                iconUpper.textContent = '✓';
            } else {
                reqUpper.classList.remove('text-green-500');
                reqUpper.classList.add('text-gray-400');
                iconUpper.textContent = '○';
            }
            
            // Check lowercase
            const reqLower = document.getElementById('req-lower-mobile');
            const iconLower = document.getElementById('icon-lower-mobile');
            if (/[a-z]/.test(password)) {
                reqLower.classList.remove('text-gray-400', 'text-red-500');
                reqLower.classList.add('text-green-500');
                iconLower.textContent = '✓';
            } else {
                reqLower.classList.remove('text-green-500');
                reqLower.classList.add('text-gray-400');
                iconLower.textContent = '○';
            }
            
            checkPasswordMatchMobile();
        }
        
        function checkPasswordMatchMobile() {
            const password = document.getElementById('password-mobile').value;
            const confirmPassword = document.getElementById('password-confirm-mobile').value;
            const matchIndicator = document.getElementById('match-indicator-mobile');
            const matchIcon = document.getElementById('match-icon-mobile');
            const matchText = document.getElementById('match-text-mobile');
            
            if (confirmPassword.length > 0) {
                matchIndicator.classList.remove('hidden');
                matchIndicator.classList.add('flex');
                
                if (password === confirmPassword) {
                    matchIndicator.classList.remove('text-red-500');
                    matchIndicator.classList.add('text-green-500');
                    matchIcon.textContent = '✓';
                    matchText.textContent = 'Password cocok';
                } else {
                    matchIndicator.classList.remove('text-green-500');
                    matchIndicator.classList.add('text-red-500');
                    matchIcon.textContent = '✗';
                    matchText.textContent = 'Password tidak cocok';
                }
            } else {
                matchIndicator.classList.add('hidden');
                matchIndicator.classList.remove('flex');
            }
        }
        
        function validatePasswordMobile(event) {
            const password = document.getElementById('password-mobile').value;
            const confirmPassword = document.getElementById('password-confirm-mobile').value;
            
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const passwordsMatch = password === confirmPassword;
            
            if (!hasLength || !hasUpper || !hasLower) {
                if (!hasLength) document.getElementById('req-length-mobile').classList.add('text-red-500');
                if (!hasUpper) document.getElementById('req-upper-mobile').classList.add('text-red-500');
                if (!hasLower) document.getElementById('req-lower-mobile').classList.add('text-red-500');
                event.preventDefault();
                return false;
            }
            
            if (!passwordsMatch) {
                event.preventDefault();
                return false;
            }
            
            const btn = document.getElementById('submit-btn-mobile');
            const spinner = document.getElementById('loading-spinner-mobile');
            const btnText = document.getElementById('btn-text-mobile');
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Loading...';
            
            return true;
        }
    </script>
</body>
</html>
