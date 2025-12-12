<section class="container font-inter card-login ">
    <div class="card flex flex-col justify-center items-center p-8 gap-5">
        <div class="card-head">
            <h1 class="card-title text-center mb-1 font-bold text-[36px]">Sign In Mahasiswa</h1>
            <h3 class="text-center font-normal text-[18px]">Send, spend and save smarter</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" x-data="{ show: false }">
                @csrf

                {{-- NIM Input --}}
                <div class="mb-4 relative">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0v1a4 4 0 01-8 0v-1m8 0H8"></path>
                            </svg>
                        </span>
                        <input type="text" name="nim" required
                            class="w-full border border-gray-300 rounded-[8px] px-10 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="NIM Mahasiswa">
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="mb-4 relative">
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="w-full border border-gray-300 rounded-[8px] px-10 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Password Mahasiswa">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.5c-4.5 0-8.5 3.5-8.5 7.5s4 7.5 8.5 7.5 8.5-3.5 8.5-7.5-4-7.5-8.5-7.5z">
                                </path>
                            </svg>
                        </span>
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 focus:outline-none">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.517-4.263M6.634 6.634A9.965 9.965 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.965 9.965 0 01-1.257 2.592M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm">
                        <input type="checkbox" name="remember" class="mr-2"> Remember me
                    </label>
                    <a href="" class="text-sm text-blue-600 hover:underline">Forgot
                        Password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-[8px] hover:bg-blue-700 transition">
                    Sign In
                </button>

                {{-- Footer --}}
                <div class="mt-6 text-center text-xs text-gray-400">
                    <a href="#" class="hover:underline">Privacy Policy</a> • 2025 © Universitas Terbuka
                </div>
            </form>
        </div>
    </div>
</section>
