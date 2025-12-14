<section class="min-h-screen flex flex-col items-center justify-center bg-white px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-inter">
    {{-- Main Container --}}
    <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-16 mx-auto">
        
        {{-- Left Side - Illustration with Logo (Hidden on mobile, shown on lg screens) --}}
        <div class="hidden lg:flex flex-1 justify-center items-center">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Verify OTP Illustration" 
                class="w-full max-w-sm xl:max-w-md 2xl:max-w-lg object-contain"
            >
        </div>

        {{-- Mobile Logo (Shown only on mobile/tablet) --}}
        <div class="lg:hidden w-full flex justify-center mb-4">
            <img 
                src="{{ asset('assets/image/auth/Ilustrasi Survey Forget Password.png') }}" 
                alt="Verify OTP Illustration" 
                class="w-48 sm:w-64 md:w-80 object-contain"
            >
        </div>

        {{-- Right Side - Form --}}
        <div class="flex-1 w-full max-w-sm sm:max-w-md px-2 sm:px-0">
            <div class="flex flex-col items-center">
                {{-- Header --}}
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">
                        Please check your email
                    </h1>
                    <p class="text-gray-500 text-sm sm:text-base">
                        We've send to code your email
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
                <form id="otp-form" method="POST" action="{{ route('mahasiswa.verify-otp.post') }}" class="w-full space-y-4 sm:space-y-6">
                    @csrf
                    
                    {{-- Hidden OTP field that will be populated by JS --}}
                    <input type="hidden" name="otp" id="otp-combined">

                    {{-- OTP Input Boxes --}}
                    <div class="flex justify-center gap-2 sm:gap-3 md:gap-4">
                        <input 
                            type="text" 
                            inputmode="numeric"
                            pattern="[0-9]"
                            maxlength="1" 
                            id="otp1"
                            oninput="handleOtpInput(this, 1)"
                            onkeydown="handleOtpKeydown(event, 1)"
                            onkeypress="return isNumberKey(event)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            inputmode="numeric"
                            pattern="[0-9]"
                            maxlength="1" 
                            id="otp2"
                            oninput="handleOtpInput(this, 2)"
                            onkeydown="handleOtpKeydown(event, 2)"
                            onkeypress="return isNumberKey(event)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            inputmode="numeric"
                            pattern="[0-9]"
                            maxlength="1" 
                            id="otp3"
                            oninput="handleOtpInput(this, 3)"
                            onkeydown="handleOtpKeydown(event, 3)"
                            onkeypress="return isNumberKey(event)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            inputmode="numeric"
                            pattern="[0-9]"
                            maxlength="1" 
                            id="otp4"
                            oninput="handleOtpInput(this, 4)"
                            onkeydown="handleOtpKeydown(event, 4)"
                            onkeypress="return isNumberKey(event)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                    </div>

                    {{-- Timer --}}
                    <div class="text-center">
                        <p class="text-gray-600 text-sm sm:text-base">
                            Code expires in : <span id="countdown" class="font-bold text-gray-900">05.00</span>
                        </p>
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

{{-- Vanilla JavaScript --}}
<script>
    // Only allow number keys
    function isNumberKey(evt) {
        const charCode = (evt.which) ? evt.which : evt.keyCode;
        // Allow digits 0-9 (keycodes 48-57)
        if (charCode < 48 || charCode > 57) {
            return false;
        }
        return true;
    }

    // OTP Input Handler
    function handleOtpInput(input, index) {
        // Remove any non-numeric characters
        input.value = input.value.replace(/[^0-9]/g, '');
        
        if (input.value.length === 1 && index < 4) {
            document.getElementById('otp' + (index + 1)).focus();
        }
        updateCombinedOtp();
    }

    function handleOtpKeydown(event, index) {
        if (event.key === 'Backspace' && event.target.value === '' && index > 1) {
            document.getElementById('otp' + (index - 1)).focus();
        }
    }

    // Combine OTP values into hidden field before submit
    function updateCombinedOtp() {
        const otp1 = document.getElementById('otp1').value;
        const otp2 = document.getElementById('otp2').value;
        const otp3 = document.getElementById('otp3').value;
        const otp4 = document.getElementById('otp4').value;
        document.getElementById('otp-combined').value = otp1 + otp2 + otp3 + otp4;
    }

    // Update combined OTP before form submit
    document.getElementById('otp-form').addEventListener('submit', function(e) {
        updateCombinedOtp();
        
        // Show loading state
        const btn = document.getElementById('submit-btn');
        const spinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Loading...';
    });

    // Countdown Timer (5 minutes to match backend expiry)
    (function() {
        let minutes = 5;
        let seconds = 0;
        const countdownEl = document.getElementById('countdown');

        function updateTimer() {
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            countdownEl.textContent = m + '.' + s;

            if (seconds > 0) {
                seconds--;
            } else if (minutes > 0) {
                minutes--;
                seconds = 59;
            }
        }

        setInterval(updateTimer, 1000);
    })();
</script>
