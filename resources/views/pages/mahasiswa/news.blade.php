<x-layouts.dashboard :active="$active ?? 'news'">
    <div x-data="newsPage()" x-init="init()" class="pb-10">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Berita & Pengumuman</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Informasi terbaru seputar kampus dan pembelajaran.</p>
            </div>
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterNews()" placeholder="Cari berita..." class="pl-10 pr-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-full sm:w-64">
            </div>
        </div>

        {{-- Category Filter Chips --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <button @click="activeCategory = 'all'; filterNews()"
                :class="activeCategory === 'all' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                Semua
            </button>
            <template x-for="cat in categories" :key="cat">
                <button @click="activeCategory = cat; filterNews()"
                    :class="activeCategory === cat ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 capitalize" x-text="cat">
                </button>
            </template>
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading" class="flex items-center justify-center py-20">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-8 h-8 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Memuat berita...</p>
            </div>
        </div>

        {{-- Empty State --}}
        <div x-show="!isLoading && filteredNews.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Belum ada berita</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="searchQuery ? 'Tidak ditemukan berita dengan kata kunci tersebut.' : 'Berita dan pengumuman akan muncul di sini.'"></p>
        </div>

        {{-- News Grid --}}
        <div x-show="!isLoading && filteredNews.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <template x-for="item in filteredNews" :key="item.id_news">
                <article @click="openDetail(item)" class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    {{-- Thumbnail --}}
                    <div class="aspect-[16/9] bg-gradient-to-br from-blue-500/10 to-purple-500/10 dark:from-blue-900/30 dark:to-purple-900/30 overflow-hidden relative">
                        <template x-if="item.thumbnail_url">
                            <img :src="item.thumbnail_url" :alt="item.judul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </template>
                        <template x-if="!item.thumbnail_url">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-blue-300 dark:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/></svg>
                            </div>
                        </template>
                        {{-- Category Badge --}}
                        <template x-if="item.kategori">
                            <span class="absolute top-3 left-3 px-3 py-1 bg-blue-500/90 backdrop-blur-sm text-white text-xs font-medium rounded-full capitalize" x-text="item.kategori"></span>
                        </template>
                    </div>
                    {{-- Content --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="item.waktu_relatif"></span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-500 transition-colors" x-text="item.judul"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3" x-text="stripHtml(item.konten)"></p>
                    </div>
                </article>
            </template>
        </div>

        {{-- Detail Modal --}}
        <div x-show="showDetail" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showDetail = false" @keydown.escape.window="showDetail = false" style="display: none;">
            <div x-show="showDetail" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <template x-if="selectedNews?.kategori">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full capitalize" x-text="selectedNews.kategori"></span>
                        </template>
                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="selectedNews?.waktu_relatif"></span>
                    </div>
                    <button @click="showDetail = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                {{-- Modal Body --}}
                <div class="overflow-y-auto p-5 flex-1">
                    <template x-if="selectedNews?.thumbnail_url">
                        <img :src="selectedNews.thumbnail_url" :alt="selectedNews.judul" class="w-full aspect-video object-cover rounded-xl mb-4">
                    </template>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4" x-text="selectedNews?.judul"></h2>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300" x-html="selectedNews?.konten"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('newsPage', () => ({
                allNews: [],
                filteredNews: [],
                categories: [],
                isLoading: false,
                searchQuery: '',
                activeCategory: 'all',
                showDetail: false,
                selectedNews: null,

                init() {
                    this.fetchNews();
                },

                async fetchNews() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/mahasiswa/dashboard/news?limit=50', {
                            headers: {
                                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.allNews = data.data;
                            // Extract unique categories
                            const cats = [...new Set(this.allNews.map(n => n.kategori).filter(Boolean))];
                            this.categories = cats;
                            this.filterNews();
                        }
                    } catch (error) {
                        console.error('Error fetching news:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                filterNews() {
                    let result = [...this.allNews];

                    // Filter by category
                    if (this.activeCategory !== 'all') {
                        result = result.filter(n => n.kategori === this.activeCategory);
                    }

                    // Filter by search
                    if (this.searchQuery.trim()) {
                        const q = this.searchQuery.toLowerCase();
                        result = result.filter(n =>
                            n.judul.toLowerCase().includes(q) ||
                            (n.konten && n.konten.toLowerCase().includes(q))
                        );
                    }

                    this.filteredNews = result;
                },

                stripHtml(html) {
                    if (!html) return '';
                    const tmp = document.createElement('div');
                    tmp.innerHTML = html;
                    return tmp.textContent || tmp.innerText || '';
                },

                openDetail(item) {
                    this.selectedNews = item;
                    this.showDetail = true;
                }
            }));
        });
    </script>
    @endpush
</x-layouts.dashboard>
