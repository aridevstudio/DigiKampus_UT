<x-layouts.admin title="Moderasi Topik Forum" active="forum-topik">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Moderasi Topik Forum</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Pantau semua topik diskusi, pin/unpin thread, kunci diskusi, hide atau hapus topik.</p>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <div class="responsive-grid-stats mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4"><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAll }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Total Topik</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4"><p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalPublished }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Dipublikasikan</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4"><p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $totalHidden + $totalDeleted }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Disembunyikan / Dihapus</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4"><p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalPinned }}</p><p class="text-xs text-gray-500 dark:text-gray-400">Pinned / Locked ({{ $totalLocked }})</p></div>
    </div>

    <form method="GET" action="{{ route('admin.forum-topik') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3">
            <!-- Status Filter -->
            <div class="w-full sm:w-auto">
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3.5 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="published" {{ $statusFilter === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                        <option value="hidden" {{ $statusFilter === 'hidden' ? 'selected' : '' }}>Disembunyikan</option>
                        <option value="deleted" {{ $statusFilter === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <!-- Kategori Filter -->
            <div class="w-full sm:w-auto">
                <div class="relative">
                    <select name="category" onchange="this.form.submit()" class="w-full appearance-none px-3.5 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id_forum_category }}" {{ (string)$categoryId === (string)$cat->id_forum_category ? 'selected' : '' }}>{{ $cat->nama }}{{ !$cat->is_active ? ' (nonaktif)' : '' }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>

            <!-- Search Filter -->
            <div class="w-full sm:flex-1">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul atau isi topik..." class="w-full px-3.5 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Cari Button -->
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full justify-center inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <!-- Desktop Table view -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Topik</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Author</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statistik</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($topikPaginated as $topik)
                    @php
                        $statusBadge = match($topik->status) {
                            'published' => ['bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400', 'Dipublikasikan'],
                            'hidden' => ['bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400', 'Disembunyikan'],
                            'deleted' => ['bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400', 'Dihapus'],
                            default => ['bg-gray-100 text-gray-700', ucfirst($topik->status)],
                        };
                    @endphp
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-2">
                                @if($topik->is_pinned)<span class="inline-flex w-6 h-6 rounded-md bg-amber-100 dark:bg-amber-500/20 items-center justify-center text-amber-600 dark:text-amber-400" title="Pinned"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg></span>@endif
                                @if($topik->is_locked)<span class="inline-flex w-6 h-6 rounded-md bg-red-100 dark:bg-red-500/20 items-center justify-center text-red-600 dark:text-red-400" title="Locked"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg></span>@endif
                                <div class="min-w-0 flex-1">
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.forum-komentar.topic') ? route('admin.forum-komentar.topic', $topik->id_forum_topic) : '#' }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate block max-w-[420px]">{{ $topik->judul }}</a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[420px]">{{ \Illuminate\Support\Str::limit(strip_tags((string) $topik->isi), 80) }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Update terakhir: {{ optional($topik->last_activity_at)->diffForHumans() ?? $topik->updated_at?->diffForHumans() ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($topik->category)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: {{ $topik->category->warna ?: '#3B82F6' }}1A; color: {{ $topik->category->warna ?: '#3B82F6' }};">{{ $topik->category->nama }}</span>
                            @else
                                <span class="text-xs text-gray-400">Tanpa kategori</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topik->author?->name ?? 'Anonim' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>{{ $topik->views }}</span>
                                <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>{{ $topik->comments_count }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-wrap items-center gap-1 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-0.5">
                                <form method="POST" action="{{ route('admin.forum-topik.pin', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<button class="p-1.5 {{ $topik->is_pinned ? 'text-amber-600' : 'text-gray-400 hover:text-amber-600' }} hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="{{ $topik->is_pinned ? 'Lepas Pin' : 'Pin Topik' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg></button></form>
                                <form method="POST" action="{{ route('admin.forum-topik.lock', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<button class="p-1.5 {{ $topik->is_locked ? 'text-red-600' : 'text-gray-400 hover:text-red-600' }} hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="{{ $topik->is_locked ? 'Buka Kunci' : 'Kunci Topik' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">@if($topik->is_locked)<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>@else<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>@endif</svg></button></form>
                                @if($topik->status !== 'published')
                                    <form method="POST" action="{{ route('admin.forum-topik.status', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="published"><button class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Publikasikan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></button></form>
                                @endif
                                @if($topik->status === 'published')
                                    <form method="POST" action="{{ route('admin.forum-topik.status', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="hidden"><button class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Sembunyikan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></button></form>
                                @endif
                                <button onclick="confirmDeleteTopik({{ $topik->id_forum_topic }}, `{{ addslashes($topik->judul) }}`)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Hapus permanen"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center"><div class="flex flex-col items-center"><div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4"><svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada topik</p><p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum ada topik diskusi yang cocok dengan filter.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List view -->
        <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-700/50">
            @forelse($topikPaginated as $topik)
            @php
                $statusBadge = match($topik->status) {
                    'published' => ['bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400', 'Dipublikasikan'],
                    'hidden' => ['bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400', 'Disembunyikan'],
                    'deleted' => ['bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400', 'Dihapus'],
                    default => ['bg-gray-100 text-gray-700', ucfirst($topik->status)],
                };
            @endphp
            <div class="p-5 space-y-3 bg-white dark:bg-gray-800">
                <!-- Topik -->
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Topik</span>
                    <div class="mt-1">
                        <div class="flex flex-wrap items-center gap-1.5 mb-2">
                            @if($topik->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>Pinned
                            </span>
                            @endif
                            @if($topik->is_locked)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-[10px] font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>Locked
                            </span>
                            @endif
                        </div>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.forum-komentar.topic') ? route('admin.forum-komentar.topic', $topik->id_forum_topic) : '#' }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 block leading-snug">{{ $topik->judul }}</a>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags((string) $topik->isi), 140) }}</p>
                        <p class="text-[10px] text-gray-400 mt-1.5">Update terakhir: {{ optional($topik->last_activity_at)->diffForHumans() ?? $topik->updated_at?->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Kategori</span>
                    <div class="mt-1">
                        @if($topik->category)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: {{ $topik->category->warna ?: '#3B82F6' }}1A; color: {{ $topik->category->warna ?: '#3B82F6' }};">{{ $topik->category->nama }}</span>
                        @else
                            <span class="text-xs text-gray-400">Tanpa kategori</span>
                        @endif
                    </div>
                </div>

                <!-- Author -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Author</span>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $topik->author?->name ?? 'Anonim' }}</p>
                </div>

                <!-- Statistik -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Statistik</span>
                    <div class="flex items-center gap-4 mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> {{ $topik->views }}</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72A3.989 3.989 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg> {{ $topik->comments_count }}</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                    </div>
                </div>

                <!-- Aksi -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-3">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Aksi</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="POST" action="{{ route('admin.forum-topik.pin', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<button class="w-10 h-10 flex items-center justify-center {{ $topik->is_pinned ? 'text-amber-600 bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/20 dark:text-amber-400' : 'text-gray-500 bg-gray-50 hover:bg-amber-50 dark:bg-gray-700 dark:text-gray-300' }} rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="{{ $topik->is_pinned ? 'Lepas Pin' : 'Pin Topik' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg></button></form>
                        <form method="POST" action="{{ route('admin.forum-topik.lock', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<button class="w-10 h-10 flex items-center justify-center {{ $topik->is_locked ? 'text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-500/20 dark:text-red-400' : 'text-gray-500 bg-gray-50 hover:bg-red-50 dark:bg-gray-700 dark:text-gray-300' }} rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="{{ $topik->is_locked ? 'Buka Kunci' : 'Kunci Topik' }}"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">@if($topik->is_locked)<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>@else<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>@endif</svg></button></form>
                        @if($topik->status !== 'published')
                            <form method="POST" action="{{ route('admin.forum-topik.status', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="published"><button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-emerald-600 bg-gray-50 hover:bg-emerald-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="Publikasikan"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></button></form>
                        @endif
                        @if($topik->status === 'published')
                            <form method="POST" action="{{ route('admin.forum-topik.status', $topik->id_forum_topic) }}" class="inline">@csrf @method('PUT')<input type="hidden" name="status" value="hidden"><button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-amber-600 bg-gray-50 hover:bg-amber-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="Sembunyikan"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg></button></form>
                        @endif
                        <button onclick="confirmDeleteTopik({{ $topik->id_forum_topic }}, `{{ addslashes($topik->judul) }}`)" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 bg-gray-50 hover:bg-red-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="Hapus permanen"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg></button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center bg-white dark:bg-gray-800">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada topik</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum ada topik diskusi yang cocok dengan filter.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination Footer -->
        @if($topikPaginated->total() > 0)
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700/50 flex flex-col gap-3 items-center md:flex-row md:justify-between">
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center md:text-left">
                Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $topikPaginated->firstItem() ?? 0 }}-{{ $topikPaginated->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-300">{{ $topikPaginated->total() }}</span> topik
            </p>
            <div class="flex items-center gap-1 justify-center flex-wrap">
                @if($topikPaginated->onFirstPage())<button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                @else<a href="{{ $topikPaginated->previousPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>@endif
                @for($i = 1; $i <= $topikPaginated->lastPage(); $i++)
                    @if($i <= 5 || $i === $topikPaginated->lastPage())
                        <a href="{{ $topikPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-medium rounded-lg transition {{ $i === $topikPaginated->currentPage() ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/25' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">{{ $i }}</a>
                    @elseif($i === 6)<span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor
                @if($topikPaginated->hasMorePages())<a href="{{ $topikPaginated->nextPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                @else<button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>@endif
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function confirmDeleteTopik(id, judul) {
            const proceed = window.Swal ? window.Swal.fire({
                title: 'Hapus Topik?',
                html: '<p class="text-gray-500">Topik <strong>' + (judul.length > 60 ? judul.slice(0, 60) + '…' : judul) + '</strong> dan semua komentar akan dihapus permanen.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(r => r.isConfirmed) : Promise.resolve(window.confirm('Hapus topik ini?'));
            proceed.then(ok => {
                if (!ok) return;
                const form = document.createElement('form');
                form.method = 'POST'; form.action = '/admin/forum/topik/' + id;
                const cs = document.createElement('input'); cs.type='hidden'; cs.name='_token'; cs.value='{{ csrf_token() }}'; form.appendChild(cs);
                const mt = document.createElement('input'); mt.type='hidden'; mt.name='_method'; mt.value='DELETE'; form.appendChild(mt);
                document.body.appendChild(form); form.submit();
            });
        }
    </script>
    @endpush
</x-layouts.admin>
