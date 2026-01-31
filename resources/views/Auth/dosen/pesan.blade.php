<x-layouts.dosen title="Pesan" active="pesan">
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Komunikasi dengan mahasiswa dalam satu tempat.</p>
    </div>

    {{-- Chat Container - 3 Column Layout --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden" style="height: calc(100vh - 200px); min-height: 500px;">
        <div class="flex h-full">
            
            {{-- Left Sidebar - Conversation List --}}
            <div class="w-72 border-r border-gray-100 dark:border-gray-700 flex flex-col">
                {{-- Search --}}
                <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="relative">
                        <input type="text" placeholder="Cari pesan..." class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                
                {{-- Conversation List --}}
                <div class="flex-1 overflow-y-auto">
                    {{-- Active Conversation --}}
                    <div class="px-3 py-3 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff" class="w-10 h-10 rounded-full">
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">Siti Nurhaliza</p>
                                    <span class="text-xs text-gray-400">Online</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Terima kasih Bu, atas penjelasannya...</p>
                            </div>
                        </div>
                    </div>

                    {{-- Other Conversations --}}
                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border-l-4 border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=10b981&color=fff" class="w-10 h-10 rounded-full">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">Ahmad Fauzi</p>
                                    <span class="text-xs text-gray-400">2 jam</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Baik Bu, saya akan perbaiki...</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border-l-4 border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Dewi+Lestari&background=f59e0b&color=fff" class="w-10 h-10 rounded-full">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">Dewi Lestari</p>
                                    <span class="text-xs text-gray-400">1 hari</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Selamat siang Bu, saya mau...</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border-l-4 border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=ef4444&color=fff" class="w-10 h-10 rounded-full">
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 text-white text-xs font-medium rounded-full flex items-center justify-center">3</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">Budi Santoso</p>
                                    <span class="text-xs text-gray-400">2 hari</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Kapan batas pengumpulan tugas...</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border-l-4 border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Rina+Wijaya&background=8b5cf6&color=fff" class="w-10 h-10 rounded-full">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">Rina Wijaya</p>
                                    <span class="text-xs text-gray-400">3 hari</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Mohon maaf Bu, saya ingin bertanya...</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition border-l-4 border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Gunawan&background=06b6d4&color=fff" class="w-10 h-10 rounded-full">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">Gunawan</p>
                                    <span class="text-xs text-gray-400">5 hari</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Pengumpulan Web sudah selesai...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Center - Chat Area --}}
            <div class="flex-1 flex flex-col">
                {{-- Chat Header --}}
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Siti Nurhaliza</p>
                            <p class="text-xs text-green-500">Online</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-gray-50/50 dark:bg-gray-900/30">
                    {{-- Date Separator --}}
                    <div class="flex items-center justify-center">
                        <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded-full">Hari ini</span>
                    </div>

                    {{-- Received Message --}}
                    <div class="flex items-end gap-2 max-w-[75%]">
                        <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff" class="w-8 h-8 rounded-full">
                        <div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Selamat pagi Bu, saya ingin bertanya tentang materi minggu ini mengenai struktur kontrol (branching). Apakah bisa dijelaskan lebih detail?</p>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block">09:15</span>
                        </div>
                    </div>

                    {{-- Sent Message --}}
                    <div class="flex items-end gap-2 max-w-[75%] ml-auto flex-row-reverse">
                        <div>
                            <div class="bg-blue-500 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm">Selamat pagi Bu, tentu. Struktur kontrol branching adalah konsep untuk mengambil keputusan dalam pemrograman, mencakup if-else, Nested-condition, dan Switch case. Biasanya ada penjelasan lebih lengkap di slide ya.</p>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block text-right">09:18</span>
                        </div>
                    </div>

                    {{-- Received Message --}}
                    <div class="flex items-end gap-2 max-w-[75%]">
                        <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff" class="w-8 h-8 rounded-full">
                        <div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Saya masih bingung Bu dengan penggunaan cara mengimplementasikan penyelesaian case tersebut, dan bagaimana sistem penilaian portofolio?</p>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block">09:20</span>
                        </div>
                    </div>

                    {{-- Sent Message --}}
                    <div class="flex items-end gap-2 max-w-[75%] ml-auto flex-row-reverse">
                        <div>
                            <div class="bg-blue-500 text-white rounded-2xl rounded-br-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm">Baik, saya jelaskan ya. Biasanya ada pembuatan mini project yang terdiri, dan langkah-langkah penyelesaiannya yang, pencarian sumber yang mengutamakan, dan hasilnya tingkatkan lagi!!</p>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block text-right">09:25</span>
                        </div>
                    </div>

                    {{-- Received Message --}}
                    <div class="flex items-end gap-2 max-w-[75%]">
                        <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff" class="w-8 h-8 rounded-full">
                        <div>
                            <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Terima kasih Bu atas penjelasannya!! Semakin saya lebih memahami dengan materi ini. Apakah ada referensi tambahan yang lebih akurat? 🙏</p>
                            </div>
                            <span class="text-xs text-gray-400 mt-1 block">09:28</span>
                        </div>
                    </div>
                </div>

                {{-- Message Input --}}
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                        <input type="text" placeholder="Ketik pesan untuk mahasiswa..." class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-xl text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                        <button class="p-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar - Profile Details --}}
            <div class="w-72 border-l border-gray-100 dark:border-gray-700 overflow-y-auto">
                {{-- Profile Header --}}
                <div class="p-5 text-center border-b border-gray-100 dark:border-gray-700">
                    <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=6366f1&color=fff&size=80" class="w-20 h-20 rounded-full mx-auto mb-3">
                    <h3 class="font-bold text-gray-900 dark:text-white">Siti Nurhaliza</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">NIM: 1.202100.045</p>
                </div>

                {{-- Profile Info --}}
                <div class="p-5 space-y-4">
                    {{-- Status --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-1">Status</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Mata kuliah sedang diikuti</span>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-2">Progress Belajar</p>
                        <div class="flex items-center gap-3">
                            <div class="relative w-14 h-14">
                                <svg class="w-14 h-14 transform -rotate-90">
                                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="none" class="text-gray-200 dark:text-gray-700"></circle>
                                    <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="none" stroke-dasharray="150.8" stroke-dashoffset="22.62" stroke-linecap="round" class="text-blue-500"></circle>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-gray-900 dark:text-white">85%</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Progress baik</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Sudah menyelesaikan 17/20 materi</p>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-1">Email</p>
                        <p class="text-sm text-blue-500">sitinurhaliza@univ.ac.id</p>
                    </div>

                    {{-- Bergabung Sejak --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-1">Bergabung Sejak</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Agustus 2023</p>
                    </div>

                    {{-- Kursus yang Diikuti --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium mb-2">Kursus yang Diikuti</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white text-xs font-bold">P</div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pemrograman Web</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center text-white text-xs font-bold">B</div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Basis Data</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dosen>
