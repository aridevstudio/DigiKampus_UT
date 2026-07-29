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

<div class="w-full space-y-6">

    {{-- Forum Hero Header (Full Content Width) --}}
    <header class="w-full forum-hero p-5 sm:p-7 lg:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between shadow-xs">
        <div class="space-y-2 min-w-0 flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Forum Komunitas</h1>
            </div>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Diskusi, berbagi pengalaman, dan saling membantu antar sesama mahasiswa & pengajar Universitas Terbuka.</p>
            <div class="flex flex-wrap gap-2 pt-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 dark:bg-gray-800/80 border border-blue-200/80 dark:border-blue-500/30 text-xs font-bold text-blue-700 dark:text-blue-300 shadow-2xs">
                    💬 {{ number_format($stats['topics']) }} Topik Diskusi
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 dark:bg-gray-800/80 border border-violet-200/80 dark:border-violet-500/30 text-xs font-bold text-violet-700 dark:text-violet-300 shadow-2xs">
                    🗣️ {{ number_format($stats['comments']) }} Tanggapan
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 dark:bg-gray-800/80 border border-emerald-200/80 dark:border-emerald-500/30 text-xs font-bold text-emerald-700 dark:text-emerald-300 shadow-2xs">
                    📌 {{ number_format($stats['categories']) }} Kategori Diskusi
                </span>
            </div>
        </div>

        {{-- Action CTA Button at Far Right --}}
        <div class="lg:flex-shrink-0 self-start lg:self-center">
            <a href="{{ route('mahasiswa.forum.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-md shadow-blue-500/20 hover:shadow-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Topik Baru
            </a>
        </div>
    </header>

    {{-- Category Filter Pills (Full Width) --}}
    <nav aria-label="Kategori forum" class="w-full">
        <div class="forum-scrollbar-x flex items-center gap-2 pb-1.5 w-full">
            <a href="{{ route('mahasiswa.forum') }}" class="forum-cat-chip inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold border transition {{ empty($categorySlug) ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-blue-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"/></svg>
                Semua Kategori
            </a>
            @foreach($kategoriList as $cat)
                <a href="{{ route('mahasiswa.forum') }}?category={{ $cat->slug }}" class="forum-cat-chip inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition" style="{{ $categorySlug === $cat->slug ? 'background-color: ' . $cat->warna . '; color: white; border-color: ' . $cat->warna . ';' : 'background-color: white; border-color: rgb(229 231 235); color: rgb(55 65 81);' }}">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $cat->warna }};"></span>
                    {{ $cat->nama }}
                    @if($cat->topics_count > 0)
                        <span class="text-[11px] font-extrabold opacity-80 px-1.5 py-0.5 rounded-full bg-black/10 dark:bg-white/20">{{ $cat->topics_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Search & Filter Controls Bar (Full Width) --}}
    <form method="GET" action="{{ route('mahasiswa.forum') }}" class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 space-y-3 sm:space-y-0 sm:flex sm:items-center sm:gap-3 shadow-xs">
        @if(!empty($categorySlug))<input type="hidden" name="category" value="{{ $categorySlug }}">@endif
        
        {{-- Search Input (Takes Maximum Horizontal Space) --}}
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari topik diskusi, kata kunci, atau kategori..." class="w-full pl-10 pr-4 py-2.5 text-sm sm:text-base rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
        </div>

        {{-- Sort Filter Dropdown --}}
        <div class="relative sm:w-48">
            <select name="sort" onchange="this.form.submit()" class="w-full appearance-none pl-3.5 pr-8 py-2.5 text-sm font-medium rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer">
                <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>🕒 Terbaru</option>
                <option value="terpopuler" {{ $sort === 'terpopuler' ? 'selected' : '' }}>🔥 Terpopuler</option>
                <option value="belum_dijawab" {{ $sort === 'belum_dijawab' ? 'selected' : '' }}>❓ Belum Dijawab</option>
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        {{-- Pinned Only Checkbox --}}
        <label class="inline-flex items-center gap-2 px-3.5 py-2.5 text-sm font-semibold rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 sm:w-auto cursor-pointer select-none">
            <input type="checkbox" name="pinned" value="1" {{ $onlyPinned ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
            <span class="text-gray-800 dark:text-gray-200 text-sm">Pinned Saja</span>
        </label>

        {{-- Submit Filter Button --}}
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-xs">
            Cari
        </button>
    </form>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl flex items-center gap-2.5 text-sm text-emerald-700 dark:text-emerald-300 font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Topic List (Full Width Cards) --}}
    <section class="w-full space-y-3.5">
        @forelse($topikPaginated as $topik)
            <a href="{{ route('mahasiswa.forum.show', $topik->slug) }}" class="forum-topic-card block w-full bg-white dark:bg-gray-800 p-5 sm:p-6 hover:no-underline shadow-xs">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-base flex-shrink-0 shadow-xs">
                        {{ \Str::upper(\Str::substr($topik->author?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $topik->author?->name ?? 'Anonim' }}</span>
                            <span class="text-[10px] text-gray-400">•</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold" style="background-color: {{ ($topik->category->warna ?? '#3B82F6') }}1A; color: {{ $topik->category->warna ?? '#3B82F6' }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $topik->category->warna ?? '#3B82F6' }};"></span>
                                {{ $topik->category->nama ?? 'Tanpa Kategori' }}
                            </span>
                            @if($topik->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[11px] font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14M9 5v14l4-4 4 4V5"/></svg>
                                    Pinned
                                </span>
                            @endif
                            @if($topik->is_locked)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-[11px] font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg>
                                    Locked
                                </span>
                            @endif
                        </div>

                        <h3 class="font-bold text-base sm:text-lg lg:text-xl text-gray-900 dark:text-white mt-1.5 hover:text-blue-600 dark:hover:text-blue-400 transition leading-snug">
                            {{ $topik->judul }}
                        </h3>

                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ \Str::limit(strip_tags((string) $topik->isi), 200) }}
                        </p>

                        <div class="flex items-center flex-wrap gap-4 mt-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-700/40">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ number_format($topik->views) }} Dilihat
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                {{ number_format($topik->published_comments_count) }} Komentar
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $topik->last_activity_at ? $topik->last_activity_at->diffForHumans() : $topik->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v3l-4-3H9a2 2 0 01-2-2v-1m10-9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v3l4-3h2a2 2 0 002-2V7z"/></svg>
                </div>
                <p class="text-base font-bold text-gray-800 dark:text-gray-100">Belum ada topik diskusi saat ini</p>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Jadilah mahasiswa pertama yang memulai pembicaraan dengan membuat topik baru.</p>
                <a href="{{ route('mahasiswa.forum.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition shadow-sm">
                    + Buat Topik Pertama
                </a>
            </div>
        @endforelse
    </section>

    {{-- Pagination Controls --}}
    @if($topikPaginated->hasPages())
        <div class="flex justify-center pt-2">
            {{ $topikPaginated->links('vendor.pagination.tailwind') }}
        </div>
    @endif

    {{-- Mobile Sticky CTA Button --}}
    <div class="lg:hidden forum-sticky-cta">
        <a href="{{ route('mahasiswa.forum.create') }}" class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-blue-500/30 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Topik Baru
        </a>
    </div>
</div>
</x-layouts.dashboard>
