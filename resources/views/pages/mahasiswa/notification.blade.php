<x-layouts.dashboard :active="'notification'">

@php
    // Transform notifications for Alpine.js
    $notifData = $notifications->map(function ($notif) {
        return [
            'id' => $notif->id_notification,
            'judul' => $notif->judul,
            'konten' => $notif->konten,
            'tipe' => $notif->tipe,
            'icon' => $notif->icon,
            'icon_color' => $notif->icon_color,
            'is_read' => $notif->is_read,
            'waktu_relatif' => $notif->created_at->diffForHumans(),
        ];
    })->values()->toArray();
@endphp

<div x-data="{
    allNotifications: {{ json_encode($notifData) }},
    activeFilter: 'all',
    searchQuery: '',
    unreadCount: {{ $unreadCount }},
    csrfToken: '{{ csrf_token() }}',

    async markAsRead(notif) {
        if (notif.is_read) return;

        try {
            await fetch(`/mahasiswa/notification/${notif.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            notif.is_read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        } catch (error) {
            const fallbackForm = document.getElementById(`notif-read-form-${notif.id}`);
            if (fallbackForm) {
                fallbackForm.submit();
            }
        }
    },

    get filteredNotifications() {
        return this.allNotifications.filter(n => {
            // Filter
            if (this.activeFilter === 'unread' && n.is_read) return false;
            if (!['all', 'unread'].includes(this.activeFilter) && n.tipe !== this.activeFilter) return false;
            // Search
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                return n.judul.toLowerCase().includes(q) || n.konten.toLowerCase().includes(q);
            }
            return true;
        });
    },

    getIconBg(color) {
        const map = { blue: 'bg-blue-100 dark:bg-blue-500/20', green: 'bg-green-100 dark:bg-green-500/20', yellow: 'bg-yellow-100 dark:bg-yellow-500/20', rose: 'bg-rose-100 dark:bg-rose-500/20', purple: 'bg-purple-100 dark:bg-purple-500/20', red: 'bg-red-100 dark:bg-red-500/20', orange: 'bg-orange-100 dark:bg-orange-500/20' };
        return map[color] || 'bg-gray-100 dark:bg-gray-500/20';
    },
    getIconText(color) {
        const map = { blue: 'text-blue-500', green: 'text-green-500', yellow: 'text-yellow-500', rose: 'text-rose-500', purple: 'text-purple-500', red: 'text-red-500', orange: 'text-orange-500' };
        return map[color] || 'text-gray-500';
    }
}" class="space-y-6">

    {{-- Flash Messages --}}
    

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-up">
        <div class="flex items-center gap-2">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Notifikasi Kamu</h1>
            <span class="text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </span>
            <template x-if="unreadCount > 0">
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium" x-text="unreadCount"></span>
            </template>
        </div>
        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
            <form action="{{ route('mahasiswa.notification.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                    Tandai Semua Telah Dibaca
                </button>
            </form>
            @else
            <button disabled class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-white rounded-lg text-sm font-medium cursor-not-allowed">
                Tandai Semua Telah Dibaca
            </button>
            @endif
            <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1f2937] border border-gray-200 dark:border-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <p class="text-gray-500 dark:text-gray-400 text-sm animate-fade-in-up delay-100">Lihat semua pemberitahuan terbaru seputar kursus, jadwal, dan aktivitas belajar kamu.</p>

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
                    x-model="searchQuery"
                    placeholder="Cari notifikasi..." 
                    class="w-full pl-12 pr-4 py-3 border border-gray-200 dark:border-gray-700/50 rounded-xl bg-white dark:bg-[#1f2937] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            {{-- Notification Items --}}
            <template x-if="filteredNotifications.length > 0">
                <div class="space-y-3">
                    <template x-for="(notif, index) in filteredNotifications" :key="notif.id">
                        <div class="bg-white dark:bg-[#1f2937] rounded-xl p-4 border border-gray-100 dark:border-gray-700/50 hover:shadow-md transition animate-fade-in-up group"
                             @click="markAsRead(notif)"
                             :class="{ 'border-l-4 border-l-blue-500 cursor-pointer': !notif.is_read }"
                             :style="'animation-delay: ' + (index * 50) + 'ms'">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     :class="getIconBg(notif.icon_color)">
                                    {{-- Book icon --}}
                                    <template x-if="notif.tipe === 'kursus_pembelajaran'">
                                        <svg class="w-5 h-5" :class="getIconText(notif.icon_color)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </template>
                                    {{-- Bell icon --}}
                                    <template x-if="notif.tipe === 'jadwal_ujian'">
                                        <svg class="w-5 h-5" :class="getIconText(notif.icon_color)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                    </template>
                                    {{-- Trophy icon --}}
                                    <template x-if="notif.tipe === 'pencapaian'">
                                        <svg class="w-5 h-5" :class="getIconText(notif.icon_color)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                                        </svg>
                                    </template>
                                    {{-- Default info icon --}}
                                    <template x-if="!['kursus_pembelajaran','jadwal_ujian','pencapaian'].includes(notif.tipe)">
                                        <svg class="w-5 h-5" :class="getIconText(notif.icon_color)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                        </svg>
                                    </template>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm text-gray-800 dark:text-gray-100" 
                                        :class="notif.is_read ? 'font-medium' : 'font-bold'"
                                        x-text="notif.judul"></h3>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1" x-text="notif.konten"></p>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-2" x-text="notif.waktu_relatif"></p>
                                </div>

                                {{-- Right side: Unread dot + mark read button --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span x-show="!notif.is_read" class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                    <template x-if="!notif.is_read">
                                        <button type="button" @click.stop="markAsRead(notif)" class="opacity-0 group-hover:opacity-100 transition text-xs text-blue-500 hover:text-blue-600 whitespace-nowrap" title="Tandai dibaca">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </button>
                                    </template>

                                    <form :id="'notif-read-form-' + notif.id" :action="'/mahasiswa/notification/' + notif.id + '/read'" method="POST" class="hidden">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredNotifications.length === 0">
                <div class="text-center py-16 animate-fade-in-up">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-500 dark:text-gray-400" x-text="searchQuery ? 'Tidak ditemukan notifikasi yang cocok dengan pencarian' : 'Belum ada pemberitahuan untuk kamu saat ini'"></p>
                </div>
            </template>
        </div>

        {{-- Filter Panel (Right - 1 column) --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-xl p-4 sm:p-6 border border-gray-100 dark:border-gray-700/50 h-fit animate-fade-in-up delay-300">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-4">Filter Notifikasi</h2>
            
            <div class="space-y-2">
                <button @click="activeFilter = 'all'" 
                        :class="activeFilter === 'all' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Semua Notifikasi
                    <span class="float-right text-xs opacity-70">({{ $notifications->count() }})</span>
                </button>
                <button @click="activeFilter = 'unread'" 
                        :class="activeFilter === 'unread' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Belum Dibaca
                    <span class="float-right text-xs opacity-70">(<span x-text="unreadCount"></span>)</span>
                </button>
                <button @click="activeFilter = 'kursus_pembelajaran'" 
                        :class="activeFilter === 'kursus_pembelajaran' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Kursus & Pembelajaran
                    <span class="float-right text-xs opacity-70">({{ $notifications->where('tipe', 'kursus_pembelajaran')->count() }})</span>
                </button>
                <button @click="activeFilter = 'jadwal_ujian'" 
                        :class="activeFilter === 'jadwal_ujian' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Jadwal & Ujian
                    <span class="float-right text-xs opacity-70">({{ $notifications->where('tipe', 'jadwal_ujian')->count() }})</span>
                </button>
                <button @click="activeFilter = 'pencapaian'" 
                        :class="activeFilter === 'pencapaian' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Pencapaian
                    <span class="float-right text-xs opacity-70">({{ $notifications->where('tipe', 'pencapaian')->count() }})</span>
                </button>
                <button @click="activeFilter = 'umum'" 
                        :class="activeFilter === 'umum' ? 'bg-blue-500 text-white font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50'"
                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition">
                    Umum
                    <span class="float-right text-xs opacity-70">({{ $notifications->where('tipe', 'umum')->count() }})</span>
                </button>
            </div>
        </div>
    </div>
</div>

</x-layouts.dashboard>
