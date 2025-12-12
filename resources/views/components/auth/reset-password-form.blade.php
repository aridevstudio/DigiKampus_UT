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

                {{-- Success Message --}}
                <div id="success-message" class="hidden w-full mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-xs sm:text-sm text-green-600">Password berhasil diubah!</p>
                </div>

                {{-- Form --}}
                <form id="reset-form" method="POST" action="#" onsubmit="return validatePassword(event);" class="w-full space-y-4 sm:space-y-5">
                    @csrf

                    {{-- New Password Input --}}
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required 
                            oninput="checkPasswordStrength()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="New Password"
                        >
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
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm sm:text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Confirm Password"
                        >
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
                        class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base disabled:bg-gray-300 disabled:cursor-not-allowed"
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

{{-- Interactive Validation Script --}}
<script>
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
        event.preventDefault();
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const successMessage = document.getElementById('success-message');
        
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
            return false;
        }
        
        if (!passwordsMatch) {
            return false;
        }
        
        // If all validations pass, show success
        successMessage.classList.remove('hidden');
        
        return false;
    }
</script>
