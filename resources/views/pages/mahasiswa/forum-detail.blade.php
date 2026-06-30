@php
    use Illuminate\Support\Str;
@endphp
<x-layouts.dashboard :active="'forum'">
@php
    $currentUserId = optional(Auth::guard('mahasiswa')->user())->id;
@endphp
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6 py-5 lg:py-8 space-y-4 lg:space-y-6">
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('mahasiswa.forum') }}" class="inline-flex items-center gap-1 hover:text-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Forum Komunitas
        </a>
        @if($topik->category)
            <span class="text-gray-300">/</span>
            <a href="{{ route('mahasiswa.forum') }}?category={{ $topik->category->slug }}" class="hover:text-blue-500 truncate max-w-[160px] sm:max-w-none">{{ $topik->category->nama }}</a>
        @endif
    </nav>

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-300 font-medium">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl flex items-center gap-2 text-sm text-red-700 dark:text-red-300 font-medium">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <article class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <header class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($topik->category)
                    <a href="{{ route('mahasiswa.forum') }}?category={{ $topik->category->slug }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: {{ $topik->category->warna }}1A; color: {{ $topik->category->warna }};">{{ $topik->category->nama }}</a>
                @endif
                @if($topik->is_pinned)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14M9 5v14l4-4 4 4V5"/></svg>Pinned</span>
                @endif
                @if($topik->is_locked)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-xs font-semibold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg>Diskusi Dikunci</span>
                @endif
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white leading-snug">{{ $topik->judul }}</h1>
            <div class="flex items-center gap-3 mt-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    {{ Str::upper(Str::substr($topik->author?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $topik->author?->name ?? 'Anonim' }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
                        <span>Diposting {{ $topik->created_at->diffForHumans() }}</span>
                        <span>•</span>
                        <span>{{ number_format($topik->views) }} dilihat</span>
                    </p>
                </div>
            </div>
        </header>
        <div class="p-4 sm:p-6 prose prose-sm sm:prose-base dark:prose-invert max-w-none text-gray-800 dark:text-gray-100 whitespace-pre-line leading-relaxed">{{ $topik->isi }}</div>
    </article>

    <section class="space-y-3">
        <h2 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            {{ $komentars->count() }} Komentar
        </h2>
        @forelse($komentars as $komentar)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                        {{ Str::upper(Str::substr($komentar->author?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $komentar->author?->name ?? 'Anonim' }}</span>
                            <span class="text-[10px] text-gray-400">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $komentar->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1.5 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $komentar->isi }}</p>

                        @if($komentars && $komentar->publishedReplies->count())
                            <div class="mt-4 space-y-3 pl-3 sm:pl-5 border-l-2 border-blue-100 dark:border-blue-500/30">
                                @foreach($komentar->publishedReplies as $reply)
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                            {{ Str::upper(Str::substr($reply->author?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $reply->author?->name ?? 'Anonim' }}</span>
                                                <span class="text-[10px] text-gray-400">•</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $reply->isi }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!$isLocked && $currentUserId)
                            <details class="mt-3 group">
                                <summary class="cursor-pointer text-xs font-semibold text-blue-500 hover:text-blue-600 inline-flex items-center gap-1.5 list-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4-4M3 10l4 4"/></svg>
                                    Balas Komentar
                                </summary>
                                <form method="POST" action="{{ route('mahasiswa.forum.comment.store', $topik->slug) }}" class="mt-3 space-y-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $komentar->id_forum_comment }}">
                                    <textarea name="isi" rows="3" maxlength="4000" required placeholder="Tulis balasan Anda untuk {{ $komentar->author?->name ?? 'komentator' }}..." class="w-full px-3 py-2 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
            <div class="bg-white dark:bg-gray-800 border border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada komentar. Jadilah yang pertama mengomentari topik ini.
            </div>
        @endforelse
    </section>

    <section class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6">
        @if($isLocked)
            <div class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                <span class="inline-flex w-10 h-10 rounded-xl items-center justify-center bg-red-50 text-red-500 dark:bg-red-500/20"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 118 0v4M5 11h14v10H5V11z"/></svg></span>
                <p><span class="font-semibold">Diskusi telah dikunci oleh administrator.</span><br><span class="text-xs text-gray-500 dark:text-gray-400">Anda tidak dapat menambahkan komentar baru pada topik ini.</span></p>
            </div>
        @elseif($currentUserId)
            <form method="POST" action="{{ route('mahasiswa.forum.comment.store', $topik->slug) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-200 mb-1.5">Tambah Komentar</label>
                    <textarea name="isi" rows="4" maxlength="4000" required placeholder="Tulis komentar Anda..." class="w-full px-3 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Topik ini mendapat notifikasi untuk author.
                    </p>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold shadow-md shadow-blue-500/20 hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Komentar
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-300">Silakan <a href="{{ route('mahasiswa.login') }}" class="text-blue-500 font-semibold">login</a> untuk menambahkan komentar.</p>
        @endif
    </section>

    @if($related->count())
        <section class="space-y-3">
            <h2 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white">Topik Terkait</h2>
            <div class="space-y-2">
                @foreach($related as $item)
                    <a href="{{ route('mahasiswa.forum.show', $item->slug) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/50 p-3 sm:p-4 hover:no-underline hover:border-blue-300 transition">
                        <div class="flex items-center gap-2 mb-1.5">
                            @if($item->category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold" style="background-color: {{ ($item->category->warna ?? '#3B82F6') }}1A; color: {{ $item->category->warna ?? '#3B82F6' }};">{{ $item->category->nama }}</span>
                            @endif
                        </div>
                        <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $item->judul }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ $item->last_activity_at ? $item->last_activity_at->diffForHumans() : $item->created_at->diffForHumans() }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
</x-layouts.dashboard>
