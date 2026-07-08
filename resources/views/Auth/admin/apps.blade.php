<x-layouts.admin :active="'apps'">
    @php
        // Derive color dari icon via service — admin tidak atur color terpisah.
        // Pakai FQN inline (tanpa `use`) karena Blade compiler menolak
        // `use` statement di dalam `@php` block jika ada kode lain setelahnya.
        $iconTheme = fn(string $icon) => app(\App\Services\AppRegistryService::class)->iconTheme($icon);
    @endphp

    <div class="space-y-5 sm:space-y-6">

        {{-- Page header --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between" data-aos="fade-up">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Apps Management</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola launcher aplikasi di menu Apps Hub mahasiswa. Admin dapat menambah, mengubah, dan menghapus aplikasi.
                </p>
                @php
                    $totalAll = $apps->count();
                    $activeCount = $apps->where('is_active', true)->count();
                @endphp
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        {{ $totalAll }} aplikasi terdaftar
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        {{ $activeCount }} aktif
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                        {{ $totalAll - $activeCount }} nonaktif
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.apps.create') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/30">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Aplikasi
            </a>
        </header>

        {{-- Info banner (replaces old misleading restriction banner) --}}
        <div class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-800/40 dark:bg-blue-950/30">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm">
                <p class="font-semibold text-blue-900 dark:text-blue-200">Kelola aplikasi tersedia di panel admin</p>
                <p class="mt-0.5 text-xs text-blue-800 dark:text-blue-300 sm:text-sm">
                    Tambah, edit, aktif/nonaktif, atau hapus launcher kartu aplikasi untuk menu Apps di sisi mahasiswa. Slug bersifat permanen setelah aplikasi dibuat.
                </p>
            </div>
        </div>

        @if($apps->isEmpty())
            <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-gray-800 sm:p-14">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Belum ada aplikasi terdaftar</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan launcher aplikasi pertama untuk ditampilkan di menu Apps mahasiswa.</p>
                <a href="{{ route('admin.apps.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Aplikasi Pertama
                </a>
            </section>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($apps as $app)
                    @php
                        $color = $iconTheme($app->icon);
                        $isActive = (bool) $app->is_active;
                        $hasUrl = trim((string) ($app->url ?? '')) !== '';
                        $roles = $app->allowed_roles ?? [];
                    @endphp

                    <article data-aos="fade-up" class="admin-apps-card relative overflow-hidden rounded-xl border bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800 {{ $isActive ? 'border-gray-100 dark:border-gray-700/50' : 'border-rose-200/60 dark:border-rose-700/30' }}">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r
                            @switch($color)
                                @case('violet') from-violet-500 to-violet-300 @break
                                @case('emerald') from-emerald-500 to-emerald-300 @break
                                @case('sky') from-sky-500 to-sky-300 @break
                                @case('amber') from-amber-500 to-amber-300 @break
                                @case('rose') from-rose-500 to-rose-300 @break
                                @case('indigo') from-indigo-500 to-indigo-300 @break
                                @case('teal') from-teal-500 to-teal-300 @break
                                @default from-slate-500 to-slate-300
                            @endswitch"></span>

                        <div class="p-5">
                            <header class="mb-3 flex items-start gap-3">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg
                                    @switch($color)
                                        @case('violet') bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400 @break
                                        @case('emerald') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 @break
                                        @case('sky') bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400 @break
                                        @case('amber') bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 @break
                                        @case('rose') bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400 @break
                                        @case('indigo') bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 @break
                                        @case('teal') bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400 @break
                                        @default bg-slate-100 text-slate-600 dark:bg-slate-700/40 dark:text-slate-300
                                    @endswitch">
                                    @if(isset($app->image_icon) && $app->image_icon)
                                        <img src="{{ asset('storage/' . $app->image_icon) }}" alt="{{ $app->name }}" class="h-6 w-6 rounded object-contain">
                                    @else
                                        @include('partials.apps._icon', ['icon' => $app->icon, 'class' => 'h-6 w-6'])
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $app->name }}</h3>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                        <code>{{ $app->slug }}</code> · {{ $app->icon }}
                                    </p>
                                </div>
                            </header>

                            <p class="line-clamp-2 text-xs leading-relaxed text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($app->description ?? '', 140) }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-[10px]">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 font-semibold {{ $isActive ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' }}">
                                    <span class="h-1 w-1 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-2 py-0.5 font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    @if($app->open_mode === 'new_tab')
                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Tab Baru
                                    @else
                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M13 5l7 7-7 7"/></svg>
                                        Tab Saat Ini
                                    @endif
                                </span>
                                @if(! $hasUrl)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-100 px-2 py-0.5 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                        URL kosong
                                    </span>
                                @endif
                                @foreach($roles as $role)
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">{{ ucfirst($role) }}</span>
                                @endforeach
                            </div>
                        </div>

                        <footer class="flex flex-wrap gap-2 border-t border-gray-100 bg-gray-50/60 p-3 dark:border-gray-700/50 dark:bg-gray-900/30">
                            <a href="{{ route('admin.apps.edit', ['slug' => $app->slug]) }}" class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-600 min-w-[80px]">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.apps.toggle', ['slug' => $app->slug]) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $isActive ? 'border-rose-200 bg-white text-rose-700 hover:bg-rose-50 dark:border-rose-700/40 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20' : 'border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 dark:border-emerald-700/40 dark:bg-gray-800 dark:text-emerald-300 dark:hover:bg-emerald-900/20' }}">
                                    {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form
                                action="{{ route('admin.apps.destroy', ['slug' => $app->slug]) }}"
                                method="POST"
                                class="flex-shrink-0"
                                onsubmit="return confirm('Hapus aplikasi \'{{ addslashes($app->name) }}\'? Tindakan ini tidak dapat dibatalkan.');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-700/40 dark:bg-gray-800 dark:text-rose-300 dark:hover:bg-rose-900/20" title="Hapus aplikasi">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </footer>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>
