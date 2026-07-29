<x-layouts.dashboard :active="'forum'">
@php
    $currentUserId = optional(Auth::guard('mahasiswa')->user())->id;
@endphp
<div class="w-full space-y-6">
    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center gap-1 hover:text-blue-500 transition font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Forum Komunitas
        </a>
        @if($topik->category)
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <a href="{{ route('mahasiswa.forum') }}?category={{ $topik->category->slug }}" class="hover:text-blue-500 transition font-medium truncate max-w-[200px] sm:max-w-none">{{ $topik->category->nama }}</a>
        @endif
        <span class="text-gray-300 dark:text-gray-600">/</span>
        <span class="font-medium text-gray-700 dark:text-gray-300 truncate max-w-[200px] sm:max-w-xs">{{ $topik->judul }}</span>
    </nav>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl flex items-center gap-2.5 text-sm text-emerald-700 dark:text-emerald-300 font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl flex items-center gap-2.5 text-sm text-red-700 dark:text-red-300 font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Topic Detail Article Card (Full Width) --}}
    <article class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm overflow-hidden">
        <header class="p-5 sm:p-7 lg:p-8 border-b border-gray-100 dark:border-gray-700/50 space-y-4">
            <div class="flex items-center gap-2 flex-wrap">
                @if($topik->category)
                    <a href="{{ route('mahasiswa.forum') }}?category={{ $topik->category->slug }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition hover:opacity-80" style="background-color: {{ $topik->category->warna }}1A; color: {{ $topik->category->warna }};">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $topik->category->warna }};"></span>
                        {{ $topik->category->nama }}
                    </a>
                @endif
                @if($topik->is_pinned)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-xs font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14M9 5v14l4-4 4 4V5"/></svg>
                        Pinned
                    </span>
                @endif
                @if($topik->is_locked)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-xs font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg>
                        Diskusi Dikunci
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight">
                {{ $topik->judul }}
            </h1>

            <div class="flex items-center justify-between gap-4 pt-2 border-t border-gray-100 dark:border-gray-700/40 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-base shadow-sm flex-shrink-0">
                        {{ \Str::upper(\Str::substr($topik->author?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $topik->author?->name ?? 'Anonim' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2 flex-wrap">
                            <span>📅 {{ $topik->created_at->translatedFormat('d F Y • H:i') }} WIB</span>
                            <span>•</span>
                            <span>{{ $topik->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-3.5 py-2 rounded-xl border border-gray-100 dark:border-gray-600/50">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ number_format($topik->views) }} Dilihat
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        {{ $komentars->count() }} Komentar
                    </span>
                </div>
            </div>
        </header>

        {{-- Discussion Main Text Content --}}
        <div class="p-5 sm:p-7 lg:p-8 prose prose-base dark:prose-invert max-w-none text-gray-800 dark:text-gray-100 whitespace-pre-line leading-relaxed font-sans min-h-[160px]">
            {{ $topik->isi }}
        </div>
    </article>

    {{-- Comments List Section (Full Width) --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-lg sm:text-xl text-gray-900 dark:text-white flex items-center gap-2.5">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Diskusi & Komentar ({{ $komentars->count() }})
            </h2>
        </div>

        @forelse($komentars as $komentar)
            <div class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-xs">
                        {{ \Str::upper(\Str::substr($komentar->author?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $komentar->author?->name ?? 'Anonim' }}</span>
                                @if($komentar->author?->id === $topik->author?->id)
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 text-[10px] font-extrabold uppercase">Penulis Topik</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-400 font-medium">{{ $komentar->created_at->diffForHumans() }}</span>
                        </div>

                        <p class="mt-2 text-sm sm:text-base text-gray-700 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $komentar->isi }}</p>

                        {{-- Replies List --}}
                        @if($komentars && $komentar->publishedReplies->count())
                            <div class="mt-4 space-y-3.5 pl-4 sm:pl-6 border-l-2 border-blue-200 dark:border-blue-500/30">
                                @foreach($komentar->publishedReplies as $reply)
                                    <div class="flex items-start gap-3 pt-1">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-xs">
                                            {{ \Str::upper(\Str::substr($reply->author?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate">{{ $reply->author?->name ?? 'Anonim' }}</span>
                                                <span class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mt-1 text-xs sm:text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $reply->isi }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Reply Toggle Form --}}
                        @if(!$isLocked && $currentUserId)
                            <details class="mt-3 group">
                                <summary class="cursor-pointer text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 inline-flex items-center gap-1.5 list-none py-1 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                                    Balas Komentar Ini
                                </summary>
                                <form method="POST" action="{{ route('mahasiswa.forum.comment.store', $topik->slug) }}" class="mt-3 space-y-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $komentar->id_forum_comment }}">
                                    <textarea name="isi" rows="3" maxlength="4000" required placeholder="Tulis balasan Anda untuk {{ $komentar->author?->name ?? 'komentator' }}..." class="w-full px-4 py-3 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y min-h-[90px] transition"></textarea>
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            </details>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="w-full bg-white dark:bg-gray-800 border border-dashed border-gray-200 dark:border-gray-700/70 rounded-2xl p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada komentar pada diskusi ini. Jadilah mahasiswa pertama yang memberikan tanggapan!
            </div>
        @endforelse
    </section>

    {{-- Add Comment Form Section (Full Width) --}}
    <section class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 sm:p-7 lg:p-8 shadow-sm">
        @if($isLocked)
            <div class="flex items-center gap-3.5 text-sm text-gray-700 dark:text-gray-300">
                <span class="inline-flex w-11 h-11 rounded-2xl items-center justify-center bg-red-50 text-red-500 dark:bg-red-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">Diskusi telah dikunci oleh administrator</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Topik ini sudah ditutup untuk tanggapan baru.</p>
                </div>
            </div>
        @elseif($currentUserId)
            <form method="POST" action="{{ route('mahasiswa.forum.comment.store', $topik->slug) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="komentar_isi" class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                        Tambah Komentar / Tanggapan Anda
                    </label>
                    <textarea id="komentar_isi" name="isi" rows="5" maxlength="4000" required placeholder="Tuliskan komentar atau solusi pendapat Anda untuk topik ini secara santun dan akademik..." class="w-full px-4 py-3.5 text-sm sm:text-base rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y min-h-[140px] transition font-sans leading-relaxed"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Penulis topik dan civitas akademika akan mendapatkan notifikasi balasan Anda.
                    </p>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-md shadow-blue-500/20 hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Komentar
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Silakan <a href="{{ route('mahasiswa.login') }}" class="text-blue-600 dark:text-blue-400 font-bold underline hover:text-blue-700">login</a> terlebih dahulu untuk ikut berdiskusi dan menambahkan komentar.
                </p>
            </div>
        @endif
    </section>

    {{-- Related Topics Section (Responsive Grid 3 Columns on Desktop) --}}
    @if($related->count())
        <section class="space-y-3 pt-2">
            <h2 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Topik Terkait Lainnya
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($related as $item)
                    <a href="{{ route('mahasiswa.forum.show', $item->slug) }}" class="block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 hover:border-blue-400 dark:hover:border-blue-500/50 hover:shadow-md transition group">
                        <div class="flex items-center gap-2 mb-2">
                            @if($item->category)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold" style="background-color: {{ ($item->category->warna ?? '#3B82F6') }}1A; color: {{ $item->category->warna ?? '#3B82F6' }};">
                                    {{ $item->category->nama }}
                                </span>
                            @endif
                        </div>
                        <p class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-2 leading-snug">
                            {{ $item->judul }}
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-400 mt-2">
                            {{ $item->last_activity_at ? $item->last_activity_at->diffForHumans() : $item->created_at->diffForHumans() }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
</x-layouts.dashboard>
