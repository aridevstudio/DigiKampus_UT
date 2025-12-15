<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP Dosen - SALUT</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .otp-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s;
            background: white;
        }
        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        @media (max-width: 640px) {
            .otp-input {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
        }
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
                    alt="Admin Verify OTP Illustration" 
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
                        Please check your email
                    </h1>
                    <p class="text-gray-500 text-base">
                        We've send to code your email
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
                <form method="POST" action="{{ route('dosen.verify-otp.post') }}" class="space-y-6">
                    @csrf

                    {{-- OTP Inputs --}}
                    <div class="flex justify-center gap-4">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInput(this, 0)" autofocus>
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInput(this, 1)">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInput(this, 2)">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInput(this, 3)">
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden">

                    {{-- Countdown Timer --}}
                    <div class="text-center text-sm text-gray-500">
                        Code expires in : <span id="countdown-desktop" class="font-semibold text-blue-600">05:00</span>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        id="submit-btn-desktop"
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <svg id="loading-spinner-desktop" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btn-text-desktop">Verifikasi OTP</span>
                    </button>
                </form>

                {{-- Resend OTP --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        Belum menerima kode? 
                        <a href="{{ route('dosen.forgot-password') }}" class="text-blue-500 hover:text-blue-600 hover:underline">
                            Kirim ulang
                        </a>
                    </p>
                </div>

                {{-- Back to Login --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('dosen.login') }}" class="text-sm text-blue-500 hover:text-blue-600 hover:underline">
                        ← Kembali ke Login
                    </a>
                </div>

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
                    alt="Admin Verify OTP Illustration" 
                    class="w-48 sm:w-64 md:w-72 object-contain"
                    style="filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1));"
                >
            </div>

            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                    Please check your email
                </h1>
                <p class="text-gray-500 text-sm px-4">
                    We've send to code your email
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
            <form method="POST" action="{{ route('dosen.verify-otp.post') }}" class="w-full space-y-5">
                @csrf

                {{-- OTP Inputs --}}
                <div class="flex justify-center gap-3">
                    <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInputMobile(this, 0)">
                    <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInputMobile(this, 1)">
                    <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInputMobile(this, 2)">
                    <input type="text" maxlength="1" class="otp-input" inputmode="numeric" pattern="[0-9]" onkeypress="return isNumberKey(event)" oninput="handleOtpInputMobile(this, 3)">
                </div>
                <input type="hidden" name="otp" id="otp-hidden-mobile">

                {{-- Countdown Timer --}}
                <div class="text-center text-xs text-gray-500">
                    Code expires in : <span id="countdown-mobile" class="font-semibold text-blue-600">05:00</span>
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
                    <span id="btn-text-mobile">Verifikasi OTP</span>
                </button>
            </form>

            {{-- Resend OTP --}}
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    Belum menerima kode? 
                    <a href="{{ route('dosen.forgot-password') }}" class="text-blue-500 hover:text-blue-600 hover:underline">
                        Kirim ulang
                    </a>
                </p>
            </div>

            {{-- Back to Login --}}
            <div class="mt-3 text-center">
                <a href="{{ route('dosen.login') }}" class="text-sm text-blue-500 hover:text-blue-600 hover:underline">
                    ← Kembali ke Login
                </a>
            </div>

            {{-- Footer --}}
            <div class="w-full mt-6 text-center text-[10px] text-gray-400">
                <a href="#" class="hover:underline">Privacy Policy</a> • 2025 © Universitas Terbuka
            </div>
        </div>
    </section>

    @vite('resources/js/app.js')
    
    <script>
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        // Desktop OTP handling
        function handleOtpInput(input, index) {
            input.value = input.value.replace(/[^0-9]/g, '');
            const inputs = document.querySelectorAll('.hidden.lg\\:flex .otp-input');
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOtpHidden();
        }

        function updateOtpHidden() {
            const inputs = document.querySelectorAll('.hidden.lg\\:flex .otp-input');
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            document.getElementById('otp-hidden').value = otp;
        }

        // Mobile OTP handling
        function handleOtpInputMobile(input, index) {
            input.value = input.value.replace(/[^0-9]/g, '');
            const inputs = document.querySelectorAll('.lg\\:hidden .otp-input');
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOtpHiddenMobile();
        }

        function updateOtpHiddenMobile() {
            const inputs = document.querySelectorAll('.lg\\:hidden .otp-input');
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            document.getElementById('otp-hidden-mobile').value = otp;
        }

        // Handle backspace
        document.querySelectorAll('.otp-input').forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    const inputs = this.closest('form').querySelectorAll('.otp-input');
                    inputs[index - 1].focus();
                }
            });
        });

        // Countdown Timer
        let timeLeft = 300; // 5 minutes in seconds

        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const formatted = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            document.getElementById('countdown-desktop').textContent = formatted;
            document.getElementById('countdown-mobile').textContent = formatted;

            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            } else {
                document.getElementById('countdown-desktop').textContent = 'Expired';
                document.getElementById('countdown-mobile').textContent = 'Expired';
                document.getElementById('countdown-desktop').classList.remove('text-blue-600');
                document.getElementById('countdown-desktop').classList.add('text-red-500');
                document.getElementById('countdown-mobile').classList.remove('text-blue-600');
                document.getElementById('countdown-mobile').classList.add('text-red-500');
            }
        }

        // Start countdown on page load
        updateCountdown();

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
