<section class="min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-inter">
    {{-- Main Container --}}
    <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-16 mx-auto">
        
        {{-- Left Side - Illustration with Logo (Hidden on mobile, shown on lg screens) --}}
        <div class="hidden lg:flex flex-1 justify-center items-center">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Reset Password Illustration" 
                class="w-full max-w-sm xl:max-w-md 2xl:max-w-lg object-contain"
            >
        </div>

        {{-- Mobile Logo (Shown only on mobile/tablet) --}}
        <div class="lg:hidden w-full flex justify-center mb-4">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Reset Password Illustration" 
                class="w-48 sm:w-64 md:w-80 object-contain"
            >
        </div>

        {{-- Right Side - Form --}}
        <div class="flex-1 w-full max-w-sm sm:max-w-md px-2 sm:px-0">
            <div class="flex flex-col items-center">
                {{-- Header --}}
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">
                        Reset Password Account
                    </h1>
                    <p class="text-gray-500 text-sm sm:text-base">
                        Send, spend and save smarter
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="w-full mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-xs sm:text-sm text-green-600">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="w-full mb-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-xs sm:text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Form --}}
                <form id="reset-form" method="POST" action="{{ route('mahasiswa.reset-password.post') }}" onsubmit="return validatePassword(event);" class="w-full space-y-4 sm:space-y-5">
                    @csrf

                    {{-- New Password Input --}}
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            oninput="checkPasswordStrength()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 pr-12 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="New Password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password', 'eye-icon-password', 'eye-off-icon-password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-off-icon-password" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    {{-- Password Requirements Checklist --}}
                    <div class="space-y-1.5 text-xs sm:text-sm">
                        <div id="req-length" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-length" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div id="req-upper" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-upper" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 1 huruf besar (A-Z)</span>
                        </div>
                        <div id="req-lower" class="flex items-center gap-2 text-gray-400">
                            <span id="icon-lower" class="w-4 h-4 flex items-center justify-center">○</span>
                            <span>Minimal 1 huruf kecil (a-z)</span>
                        </div>
                    </div>

                    {{-- Confirm Password Input --}}
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            required 
                            oninput="checkPasswordMatch()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 pr-12 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Confirm Password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation', 'eye-icon-confirm', 'eye-off-icon-confirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                        >
                            <svg id="eye-icon-confirm" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-off-icon-confirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    {{-- Password Match Indicator --}}
                    <div id="match-indicator" class="hidden flex items-center gap-2 text-xs sm:text-sm">
                        <span id="match-icon" class="w-4 h-4 flex items-center justify-center">○</span>
                        <span id="match-text">Password cocok</span>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg id="loading-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text">Reset Password</span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-6 sm:mt-8 text-center text-[10px] sm:text-xs text-gray-400">
                    <a href="#" class="hover:underline">Privacy Policy</a> • 2025 © Universitas Terbuka
                </div>
            </div>
        </div>
    </div>
</section>

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

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        
        // Check length
        const reqLength = document.getElementById('req-length');
        const iconLength = document.getElementById('icon-length');
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
        const reqUpper = document.getElementById('req-upper');
        const iconUpper = document.getElementById('icon-upper');
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
        const reqLower = document.getElementById('req-lower');
        const iconLower = document.getElementById('icon-lower');
        if (/[a-z]/.test(password)) {
            reqLower.classList.remove('text-gray-400', 'text-red-500');
            reqLower.classList.add('text-green-500');
            iconLower.textContent = '✓';
        } else {
            reqLower.classList.remove('text-green-500');
            reqLower.classList.add('text-gray-400');
            iconLower.textContent = '○';
        }
        
        // Also check match if confirm field has value
        checkPasswordMatch();
    }
    
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchIndicator = document.getElementById('match-indicator');
        const matchIcon = document.getElementById('match-icon');
        const matchText = document.getElementById('match-text');
        
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
    
    function validatePassword(event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        // Check all requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const passwordsMatch = password === confirmPassword;
        
        if (!hasLength || !hasUpper || !hasLower) {
            // Highlight failed requirements in red
            if (!hasLength) {
                document.getElementById('req-length').classList.add('text-red-500');
            }
            if (!hasUpper) {
                document.getElementById('req-upper').classList.add('text-red-500');
            }
            if (!hasLower) {
                document.getElementById('req-lower').classList.add('text-red-500');
            }
            event.preventDefault();
            return false;
        }
        
        if (!passwordsMatch) {
            event.preventDefault();
            return false;
        }
        
        // All validations passed, show loading and submit
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Loading...';
        
        return true;
    }
</script>
