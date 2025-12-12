<section class="min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-inter">
    {{-- Main Container --}}
    <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-16 mx-auto">
        
        {{-- Left Side - Illustration with Logo (Hidden on mobile, shown on lg screens) --}}
        <div class="hidden lg:flex flex-1 justify-center items-center">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Forgot Password Illustration" 
                class="w-full max-w-sm xl:max-w-md 2xl:max-w-lg object-contain"
            >
        </div>

        {{-- Mobile Logo (Shown only on mobile/tablet) --}}
        <div class="lg:hidden w-full flex justify-center mb-4">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Forgot Password Illustration" 
                class="w-48 sm:w-64 md:w-80 object-contain"
            >
        </div>

        {{-- Right Side - Form --}}
        <div class="flex-1 w-full max-w-sm sm:max-w-md px-2 sm:px-0">
            <div class="flex flex-col items-center">
                {{-- Header --}}
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">
                        Forgot Password Account
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
                <form method="POST" action="{{ route('mahasiswa.forgot-password.post') }}" class="w-full space-y-4 sm:space-y-6">
                    @csrf

                    {{-- Email Input --}}
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-4 text-gray-400">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
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
                            class="w-full border border-gray-300 rounded-lg pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="olivia@untitledui.com"
                        >
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <svg id="loading-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text">Sign Up</span>
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

{{-- Loading Script --}}
<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');
        
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Loading...';
    });
</script>
