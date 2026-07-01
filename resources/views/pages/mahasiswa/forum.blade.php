<x-layouts.dashboard :active="'forum'">
@push('head')
<style>
    .forum-hero {
        background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(99,102,241,0.12));
        border-radius: 1.75rem;
        border: 1px solid rgba(59,130,246,0.18);
    }
    .dark .forum-hero {
        background: linear-gradient(135deg, rgba(59,130,246,0.16), rgba(99,102,241,0.10));
        border-color: rgba(59,130,246,0.22);
    }
    .forum-topic-card {
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(226,232,240,0.85);
        border-radius: 1.25rem;
    }
    .forum-topic-card:hover {
        transform: translateY(-2px);
        border-color: rgba(59,130,246,0.45);
        box-shadow: 0 12px 28px -20px rgba(15,23,42,0.4);
    }
    .forum-cat-chip {
        transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        white-space: nowrap;
    }
    .forum-cat-chip:active {
        transform: scale(0.97);
    }
    .forum-scrollbar-x {
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(148,163,184,0.55) transparent;
        -webkit-overflow-scrolling: touch;
    }
    .forum-scrollbar-x::-webkit-scrollbar { height: 6px; }
    .forum-scrollbar-x::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.55); border-radius: 9999px; }
    .forum-sticky-cta {
        position: sticky;
        bottom: 1rem;
        z-index: 20;
    }
    @media (min-width: 1024px) {
        .forum-sticky-cta {
            position: static;
            bottom: auto;
        }
    }
</style>
@endpush

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-5 lg:py-8 space-y-5 lg:space-y-7">
    <header class="forum-hero p-5 sm:p-7 lg:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="space-y-2 min-w-0">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Forum Komunitas</h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 max-w-xl">Diskusi, berbagi pengalaman, dan saling membantu antar mahasiswa Universitas Terbuka.</p>
            <div class="flex flex-wrap gap-2 pt-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/70 dark:bg-gray-800/60 border border-blue-200/70 dark:border-blue-500/30 text-xs font-semibold text-blue-700 dark:text-blue-300">{{ number_format($stats['topics']) }} Topik</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/70 dark:bg-gray-800/60 border border-violet-200/70 dark:border-violet-500/30 text-xs font-semibold text-violet-700 dark:text-violet-300">{{ number_format($stats['comments']) }} Komentar</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/70 dark:bg-gray-800/60 border border-emerald-200/70 dark:border-emerald-500/30 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($stats['categories']) }} Kategori</span>
            </div>
        </div>
        <div class="lg:flex-shrink-0">
            <a href="{{ route('mahasiswa.forum.create') }}" class="inline-flex items-center justify-center gap-2 w-full lg:w-auto px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Topik
            </a>
        </div>
    </header>

    <nav aria-label="Kategori forum" class="-mx-3 sm:mx-0 px-3 sm:px-0">
        <div class="forum-scrollbar-x flex items-center gap-2 pb-1.5">
            <a href="{{ route('mahasiswa.forum') }}" class="forum-cat-chip inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium border {{ empty($categorySlug) ? 'bg-blue-500 text-white border-blue-500 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-blue-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"/></svg>
                Semua
            </a>
            @foreach($kategoriList as $cat)
                <a href="{{ route('mahasiswa.forum') }}?category={{ $cat->slug }}" class="forum-cat-chip inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium border transition" style="{{ $categorySlug === $cat->slug ? 'background-color: ' . $cat->warna . '; color: white; border-color: ' . $cat->warna . ';' : 'background-color: white; border-color: rgb(229 231 235); color: rgb(55 65 81);' }}">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat->warna }};"></span>
                    {{ $cat->nama }}
                    @if($cat->topics_count > 0)<span class="text-[10px] font-bold opacity-70">({{ $cat->topics_count }})</span>@endif
                </a>
            @endforeach
        </div>
    </nav>

    <form method="GET" action="{{ route('mahasiswa.forum') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-3 sm:p-4 space-y-3 sm:space-y-0 sm:flex sm:items-center sm:gap-2">
        @if(!empty($categorySlug))<input type="hidden" name="category" value="{{ $categorySlug }}">@endif
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari topik, kategori, atau konten..." class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="relative sm:w-44">
            <select name="sort" onchange="this.form.submit()" class="w-full appearance-none pl-3 pr-8 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200">
                <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terpopuler" {{ $sort === 'terpopuler' ? 'selected' : '' }}>Terpopuler</option>
                <option value="belum_dijawab" {{ $sort === 'belum_dijawab' ? 'selected' : '' }}>Belum Dijawab</option>
            </select>
            <svg class="w-4 h-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <label class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 sm:w-auto cursor-pointer select-none">
            <input type="checkbox" name="pinned" value="1" {{ $onlyPinned ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
            <span class="text-gray-700 dark:text-gray-200 text-sm font-medium">Pinned saja</span>
        </label>
        <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold transition">Terapkan</button>
    </form>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-300 font-medium">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <section class="space-y-3">
        @forelse($topikPaginated as $topik)
            <a href="{{ route('mahasiswa.forum.show', $topik->slug) }}" class="forum-topic-card block bg-white dark:bg-gray-800 p-4 sm:p-5 hover:no-underline">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ \Str::upper(\Str::substr($topik->author?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $topik->author?->name ?? 'Anonim' }}</span>
                            <span class="text-[10px] text-gray-400">•</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold" style="background-color: {{ ($topik->category->warna ?? '#3B82F6') }}1A; color: {{ $topik->category->warna ?? '#3B82F6' }};">{{ $topik->category->nama ?? 'Tanpa kategori' }}</span>
                            @if($topik->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[11px] font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14M9 5v14l4-4 4 4V5"/></svg>Pinned</span>
                            @endif
                            @if($topik->is_locked)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-[11px] font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg>Locked</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white mt-1.5 truncate">{{ $topik->judul }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">{{ \Str::limit(strip_tags((string) $topik->isi), 160) }}</p>
                        <div class="flex items-center flex-wrap gap-3 mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>{{ number_format($topik->views) }} dilihat</span>
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>{{ number_format($topik->published_comments_count) }} komentar</span>
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $topik->last_activity_at ? $topik->last_activity_at->diffForHumans() : $topik->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-10 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v3l-4-3H9a2 2 0 01-2-2v-1m10-9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v3l4-3h2a2 2 0 002-2V7z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada topik saat ini</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Jadilah yang pertama memulai diskusi dengan membuat topik baru.</p>
                <a href="{{ route('mahasiswa.forum.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl">+ Buat Topik Pertama</a>
            </div>
        @endforelse
    </section>

    @if($topikPaginated->hasPages())
        <div class="flex justify-center">
            {{ $topikPaginated->links('vendor.pagination.tailwind') }}
        </div>
    @endif

    <div class="lg:hidden forum-sticky-cta">
        <a href="{{ route('mahasiswa.forum.create') }}" class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-2xl shadow-xl shadow-blue-500/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Topik Baru
        </a>
    </div>
</div>
</x-layouts.dashboard>
