<x-layouts.dashboard :active="'apps'">
    @php
        $user = Auth::guard('mahasiswa')->user();
        $userName = $user?->name ?? 'Mahasiswa';
    @endphp

    <div class="space-y-6 sm:space-y-8" x-data="{ query: '' }">
        {{-- Hero Header --}}
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-700 px-5 py-7 sm:px-8 sm:py-10 text-white shadow-lg" data-aos="fade-up">
            <div class="absolute inset-0 opacity-20">
                <svg class="absolute -right-12 -top-12 h-72 w-72" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="2" />
                    <circle cx="100" cy="100" r="60" stroke="currentColor" stroke-width="2" />
                    <circle cx="100" cy="100" r="40" stroke="currentColor" stroke-width="2" />
                </svg>
            </div>
            <div class="relative">
                {{-- Breadcrumb --}}
                <nav class="mb-4 flex items-center gap-2 text-xs sm:text-sm text-white/80">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-white">Dashboard</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-white">Apps Hub</span>
                </nav>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <h1 class="text-2xl font-bold leading-tight sm:text-3xl lg:text-4xl">
                            Selamat datang, {{ explode(' ', $userName)[0] }} 👋
                        </h1>
                        <p class="mt-2 text-sm text-white/85 sm:text-base lg:text-lg">
                            Semua tools pembelajaranmu ada di sini. Pilih aplikasi untuk membukanya di tab baru — aplikasi berjalan di luar DigiKampus.
                        </p>
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 font-medium backdrop-blur">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                {{ $visibleTotal }} aplikasi aktif
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 font-medium backdrop-blur">
                                {{ count($categoryApps) }} kategori
                            </span>
                        </div>
                    </div>

                    {{-- Quick search --}}
                    <div class="w-full lg:w-80">
                        <label class="sr-only" for="appSearch">Cari aplikasi</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                type="search"
                                id="appSearch"
                                x-model.debounce.250ms="query"
                                placeholder="Cari aplikasi…"
                                class="w-full rounded-xl bg-white/15 px-9 py-3 text-sm text-white placeholder-white/60 ring-1 ring-white/20 backdrop-blur transition focus:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white/40"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Category sections --}}
        @if(empty($categoryApps))
            <section class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 sm:p-14 text-center" data-empty-state>
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Belum ada aplikasi aktif</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Admin belum mengaktifkan aplikasi untukmu. Hubungi admin jika ini tidak sesuai.</p>
            </section>
        @else
            @foreach($categoryApps as $category => $appsInCategory)
                @php $order = \App\Services\AppRegistryService::CATEGORY_ORDER[$category] ?? 999; @endphp
                <section data-aos="fade-up">
                    <header class="mb-3 flex items-center justify-between sm:mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white sm:text-xl">{{ $category }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                                {{ count($appsInCategory) }} aplikasi
                                @if($order === 1) · Rekomendasi @endif
                            </p>
                        </div>
                    </header>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($appsInCategory as $app)
                            @php
                                $slug = $app['key'] ?? $app['slug'];
                                $name = $app['display_name'] ?? 'Tanpa Nama';
                                $shortLabel = $app['short_label'] ?? \Str::upper(\Str::substr($name, 0, 3));
                                $desc = $app['description'] ?? '';
                                $color = $app['color'] ?? 'slate';
                                $icon = $app['icon'] ?? 'apps';
                                // Trim + whitelist http/https only (admin input is regex-validated
                                // server-side, but we re-check here so a stale cache from before
                                // the migration cannot render a malicious scheme).
                                $rawUrl = (string) ($app['external_url'] ?? '');
                                $trimmed = trim($rawUrl);
                                $schemeOk = (bool) preg_match('/^https?:\\/\\//i', $trimmed);
                                $urlAvailable = $trimmed !== '' && $schemeOk;
                                $launchUrl = $urlAvailable ? $trimmed : '';
                            @endphp

                            <article
                                data-app-card
                                data-app-name="{{ \Str::lower($name) }}"
                                data-app-desc="{{ \Str::lower($desc) }}"
                                x-show="!query || '{{ \Str::lower($name) }}'.includes(query.toLowerCase()) || '{{ \Str::lower($desc) }}'.includes(query.toLowerCase())"
                                class="group relative flex flex-col gap-3 overflow-hidden rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition dark:border-gray-700/50 dark:bg-gray-800
                                    {{ $urlAvailable ? 'hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md cursor-pointer dark:hover:border-blue-500/40' : 'cursor-not-allowed pointer-events-none opacity-70' }}"
                                @if(!$urlAvailable) aria-disabled="true" @else role="link" tabindex="0" @endif
                            >
                                {{-- Color stripe top --}}
                                <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r
                                    @switch($color)
                                        @case('violet') from-violet-500 to-violet-300 @break
                                        @case('emerald') from-emerald-500 to-emerald-300 @break
                                        @case('sky') from-sky-500 to-sky-300 @break
                                        @case('amber') from-amber-500 to-amber-300 @break
                                        @case('rose') from-rose-500 to-rose-300 @break
                                        @case('indigo') from-indigo-500 to-indigo-300 @break
                                        @default from-slate-500 to-slate-300
                                    @endswitch"></span>

                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg
                                        @switch($color)
                                            @case('violet') bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400 @break
                                            @case('emerald') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 @break
                                            @case('sky') bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400 @break
                                            @case('amber') bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 @break
                                            @case('rose') bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400 @break
                                            @case('indigo') bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 @break
                                            @default bg-slate-100 text-slate-600 dark:bg-slate-700/40 dark:text-slate-300
                                        @endswitch">
                                        @includeWhen($icon === 'sparkles', 'partials.apps._icon-sparkles')
                                        @includeWhen($icon === 'chip', 'partials.apps._icon-chip')
                                        @includeWhen($icon === 'github', 'partials.apps._icon-github')
                                        @includeWhen($icon === 'code', 'partials.apps._icon-code')
                                        @includeWhen($icon === 'video', 'partials.apps._icon-video')
                                        @includeWhen($icon === 'palette', 'partials.apps._icon-palette')
                                        @includeWhen($icon === 'library', 'partials.apps._icon-library')
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" title="Aplikasi dibuka di tab baru">
                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Launcher
                                    </span>
                                </div>

                                <div class="min-h-0 flex-1">
                                    <div class="flex items-baseline gap-2">
                                        <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $name }}</h3>
                                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium uppercase tracking-wide text-gray-500 dark:bg-gray-700 dark:text-gray-400">{{ $shortLabel }}</span>
                                    </div>
                                    <p class="mt-1.5 line-clamp-3 text-xs leading-relaxed text-gray-600 dark:text-gray-400">{{ $desc }}</p>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-[11px] text-gray-500 dark:border-gray-700/50 dark:text-gray-400">
                                    @if($urlAvailable)
                                        <a
                                            href="{{ $launchUrl }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            data-app-launcher
                                            class="inline-flex items-center gap-1.5 font-semibold text-blue-600 transition group-hover:text-blue-700 dark:text-blue-400 dark:group-hover:text-blue-300"
                                            aria-label="Buka {{ $name }} di tab baru"
                                        >
                                            Buka di tab baru
                                            <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    @else
                                        <div class="flex flex-col">
                                            <span class="inline-flex items-center gap-1.5 font-semibold text-amber-700 dark:text-amber-300">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                Aplikasi belum tersedia.
                                            </span>
                                            <span class="mt-0.5 text-[10px] text-gray-500 dark:text-gray-400">URL tujuan belum dikonfigurasi admin.</span>
                                        </div>
                                    @endif
                                    <svg class="pointer-events-none absolute right-3 top-3 h-3.5 w-3.5 text-gray-300 transition group-hover:text-blue-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif
    </div>
</x-layouts.dashboard>
