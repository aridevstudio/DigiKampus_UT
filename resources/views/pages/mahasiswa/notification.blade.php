<x-layouts.dashboard :active="'notification'">
@php
    $notifications = [
        [
            'icon' => 'book',
            'iconBg' => 'blue',
            'title' => 'Kursus Baru Tersedia',
            'description' => 'Kursus "Psikologi Organisasi" kini dapat diambil di menu Get Course.',
            'time' => '2 jam yang lalu',
            'unread' => true,
            'category' => 'kursus',
        ],
        [
            'icon' => 'chat',
            'iconBg' => 'green',
            'title' => 'Balasan dari Tutor',
            'description' => 'Tutor Anda membalas diskusi di kursus "Data Science".',
            'time' => '5 jam yang lalu',
            'unread' => true,
            'category' => 'forum',
        ],
        [
            'icon' => 'bell',
            'iconBg' => 'yellow',
            'title' => 'Pengingat Ujian',
            'description' => 'Workshop "Belajar Front-end Pemula" akan dimulai besok pukul 09.00 WIB.',
            'time' => '1 hari yang lalu',
            'unread' => true,
            'category' => 'jadwal',
        ],
        [
            'icon' => 'trophy',
            'iconBg' => 'rose',
            'title' => 'Pencapaian Baru',
            'description' => 'Selamat! Anda telah menyelesaikan 5 modul pembelajaran.',
            'time' => '2 hari yang lalu',
            'unread' => false,
            'category' => 'kursus',
        ],
        [
            'icon' => 'video',
            'iconBg' => 'purple',
            'title' => 'Webinar Selesai',
            'description' => 'Rekaman webinar "Manajemen Keuangan" sudah tersedia.',
            'time' => '3 hari yang lalu',
            'unread' => false,
            'category' => 'kursus',
        ],
    ];
    
    $unreadCount = count(array_filter($notifications, fn($n) => $n['unread']));
@endphp

{{-- Header Section --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up">
    <div class="flex items-center gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Notifikasi Kamu</h1>
        <span class="text-blue-500">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
        </span>
        @if($unreadCount > 0)
        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium">{{ $unreadCount }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition btn-pulse">
            Tandai Semua Telah Dibaca
        </button>
        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>

<p class="text-gray-500 dark:text-gray-400 text-sm mb-6 animate-fade-in-up delay-100">Lihat semua pemberitahuan terbaru seputar kursus, jadwal, dan aktivitas belajar kamu.</p>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Notifications List (Left - 2 columns) --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Search Bar --}}
        <div class="relative animate-fade-in-up delay-200">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input 
                type="text" 
                id="search-notification"
                placeholder="Cari notifikasi..." 
                class="w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-700/50 rounded-xl bg-white dark:bg-[#1f2937] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                oninput="searchNotifications(this.value)"
            >
        </div>

        {{-- Notification Items --}}
        @foreach($notifications as $index => $notif)
        <div class="notification-item bg-white dark:bg-[#1f2937] rounded-xl p-4 border border-gray-100 dark:border-gray-700/50 hover:shadow-md hover-lift transition cursor-pointer animate-fade-in-up" style="animation-delay: {{ ($index + 3) * 50 }}ms" data-category="{{ $notif['category'] }}" data-unread="{{ $notif['unread'] ? 'true' : 'false' }}">
            <div class="flex items-start gap-4">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($notif['iconBg'] === 'blue') bg-blue-100 dark:bg-blue-500/20
                    @elseif($notif['iconBg'] === 'green') bg-green-100 dark:bg-green-500/20
                    @elseif($notif['iconBg'] === 'yellow') bg-yellow-100 dark:bg-yellow-500/20
                    @elseif($notif['iconBg'] === 'rose') bg-rose-100 dark:bg-rose-500/20
                    @elseif($notif['iconBg'] === 'purple') bg-purple-100 dark:bg-purple-500/20
                    @endif">
                    @if($notif['icon'] === 'book')
                    <svg class="w-5 h-5 text-{{ $notif['iconBg'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    @elseif($notif['icon'] === 'chat')
                    <svg class="w-5 h-5 text-{{ $notif['iconBg'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                    @elseif($notif['icon'] === 'bell')
                    <svg class="w-5 h-5 text-{{ $notif['iconBg'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @elseif($notif['icon'] === 'trophy')
                    <svg class="w-5 h-5 text-{{ $notif['iconBg'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                    </svg>
                    @elseif($notif['icon'] === 'video')
                    <svg class="w-5 h-5 text-{{ $notif['iconBg'] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                    </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $notif['title'] }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $notif['description'] }}</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">{{ $notif['time'] }}</p>
                </div>

                {{-- Unread Indicator --}}
                @if($notif['unread'])
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0 animate-pulse"></span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter Panel (Right - 1 column) --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700/50 h-fit animate-fade-in-up delay-300 hover-lift">
        <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Filter Notifikasi</h2>
        
        <div class="space-y-2">
            <button onclick="setActiveFilter(this)" data-filter="all" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium">
                Semua Notifikasi
            </button>
            <button onclick="setActiveFilter(this)" data-filter="unread" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                Belum Dibaca
            </button>
            <button onclick="setActiveFilter(this)" data-filter="kursus" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                Kursus & Pembelajaran
            </button>
            <button onclick="setActiveFilter(this)" data-filter="jadwal" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                Jadwal & Ujian
            </button>
            <button onclick="setActiveFilter(this)" data-filter="forum" class="filter-btn w-full text-left px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 text-sm transition">
                Forum & Diskusi
            </button>
        </div>

        <button onclick="applyFilter()" class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium transition btn-pulse">
            Terapkan Filter
        </button>
    </div>
</div>

@push('scripts')
<script>
    function setActiveFilter(button) {
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white', 'font-medium');
            btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
        });
        
        button.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
        button.classList.add('bg-blue-500', 'text-white', 'font-medium');
    }

    function applyFilter() {
        const activeBtn = document.querySelector('.filter-btn.bg-blue-500');
        const filterType = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        const notifications = document.querySelectorAll('.notification-item');
        
        notifications.forEach(notif => {
            const category = notif.getAttribute('data-category');
            const isUnread = notif.getAttribute('data-unread') === 'true';
            
            if (filterType === 'all') {
                notif.style.display = 'block';
            } else if (filterType === 'unread') {
                notif.style.display = isUnread ? 'block' : 'none';
            } else {
                notif.style.display = category === filterType ? 'block' : 'none';
            }
        });
    }

    function searchNotifications(query) {
        const notifications = document.querySelectorAll('.notification-item');
        const lowerQuery = query.toLowerCase();
        
        notifications.forEach(notif => {
            const title = notif.querySelector('h3').textContent.toLowerCase();
            const description = notif.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(lowerQuery) || description.includes(lowerQuery)) {
                notif.style.display = 'block';
            } else {
                notif.style.display = query === '' ? 'block' : 'none';
            }
        });
    }
</script>
@endpush

</x-layouts.dashboard>
