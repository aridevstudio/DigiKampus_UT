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

                {{-- Form --}}
                <form method="POST" action="#" onsubmit="event.preventDefault(); window.location.href='{{ route('mahasiswa.reset-password') }}';" class="w-full space-y-4 sm:space-y-6">
                    @csrf

                    {{-- OTP Input Boxes --}}
                    <div class="flex justify-center gap-2 sm:gap-3 md:gap-4">
                        <input 
                            type="text" 
                            maxlength="1" 
                            id="otp1"
                            oninput="handleOtpInput(this, 1)"
                            onkeydown="handleOtpKeydown(event, 1)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            maxlength="1" 
                            id="otp2"
                            oninput="handleOtpInput(this, 2)"
                            onkeydown="handleOtpKeydown(event, 2)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            maxlength="1" 
                            id="otp3"
                            oninput="handleOtpInput(this, 3)"
                            onkeydown="handleOtpKeydown(event, 3)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                        <input 
                            type="text" 
                            maxlength="1" 
                            id="otp4"
                            oninput="handleOtpInput(this, 4)"
                            onkeydown="handleOtpKeydown(event, 4)"
                            class="w-14 h-14 sm:w-16 sm:h-16 md:w-[72px] md:h-[72px] border-2 border-blue-300 rounded-xl text-center text-xl sm:text-2xl font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        >
                    </div>

                    {{-- Timer --}}
                    <div class="text-center">
                        <p class="text-gray-600 text-sm sm:text-base">
                            Code expires in : <span id="countdown" class="font-bold text-gray-900">03.00</span>
                        </p>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base"
                    >
                        Sign Up
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
    // OTP Input Handler
    function handleOtpInput(input, index) {
        if (input.value.length === 1 && index < 4) {
            document.getElementById('otp' + (index + 1)).focus();
        }
    }

    function handleOtpKeydown(event, index) {
        if (event.key === 'Backspace' && event.target.value === '' && index > 1) {
            document.getElementById('otp' + (index - 1)).focus();
        }
    }

    // Countdown Timer
    (function() {
        let minutes = 3;
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
