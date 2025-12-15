<section class="min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-inter">
    {{-- Main Container --}}
    <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-16 mx-auto">
        
        {{-- Left Side - Form --}}
        <div class="flex-1 w-full max-w-sm sm:max-w-md px-2 sm:px-0 order-2 lg:order-1">
            <div class="flex flex-col items-center">
                {{-- Header --}}
                <div class="text-center mb-6 sm:mb-8 animate-hidden animate-fade-in-down">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">
                        Sign In Mahasiswa
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

                {{-- Alert Messages --}}
                @if (session('alert'))
                    <div class="w-full mb-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs sm:text-sm text-red-600">{{ session('alert') }}</p>
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
                <form method="POST" action="{{ route('mahasiswa.post') }}" class="w-full space-y-4 sm:space-y-5">
                    @csrf

                    {{-- NIM Input --}}
                    <div class="relative animate-hidden animate-fade-in-up stagger-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="nim" 
                            value="{{ old('nim') }}"
                            required 
                            autofocus
                            class="w-full border border-gray-300 rounded-lg pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                            placeholder="Nim Mahasiswa"
                        >
                    </div>

                    {{-- Password Input --}}
                    <div class="relative animate-hidden animate-fade-in-up stagger-2">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 pr-12 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition input-animate"
                            placeholder="Password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-4 text-gray-400 hover:text-gray-600 password-toggle"
                        >
                            <svg id="eye-open" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eye-closed" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between animate-hidden animate-fade-in-up stagger-3">
                        <label class="flex items-center text-xs sm:text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-blue-500 focus:ring-blue-500 mr-2 checkbox-animate">
                            Remember me
                        </label>
                        <a href="{{ route('mahasiswa.forgot-password') }}" class="text-xs sm:text-sm text-blue-500 hover:text-blue-600 link-animate">
                            Forgot Password?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed btn-animate animate-hidden animate-fade-in-up stagger-4"
                    >
                        <svg id="loading-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text">Sign In</span>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="w-full mt-6 sm:mt-8 flex items-center justify-between text-[10px] sm:text-xs text-gray-400 footer-animate">
                    <a href="#" class="hover:underline">Privacy Policy</a>
                    <span>2025 © Universitas Terbuka</span>
                </div>
            </div>
        </div>

        {{-- Right Side - Illustration with Logo (Hidden on mobile, shown on lg screens) --}}
        <div class="hidden lg:flex flex-1 justify-center items-center order-1 lg:order-2 animate-slide-in-right">
            <img 
                src="{{ asset('assets/image/auth/Illustration 1 Login Mahasiswa.png') }}" 
                alt="Login Illustration" 
                class="w-full max-w-sm xl:max-w-md 2xl:max-w-lg object-contain illustration-animate"
            >
        </div>

        {{-- Mobile Logo (Shown only on mobile/tablet) --}}
        <div class="lg:hidden w-full flex justify-center mb-4 order-1 animate-fade-in-down">
            <img 
                src="{{ asset('assets/image/auth/Illustration 1 Login Mahasiswa.png') }}" 
                alt="Login Illustration" 
                class="w-48 sm:w-64 md:w-80 object-contain illustration-animate"
            >
        </div>
    </div>
</section>

{{-- Scripts --}}
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
        
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

    // Loading state on form submit
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');
        
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Loading...';
    });
</script>
