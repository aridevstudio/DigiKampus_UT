<x-layouts.admin :active="'apps-hub'">
    @php
        $user = Auth::guard('admin')->user();
        $userName = $user?->name ?? 'Admin';
        $iconTheme = fn(string $icon) => app(\App\Services\AppRegistryService::class)->iconTheme($icon);

        $gradientClass = [
            'violet'  => 'from-violet-500 to-violet-300',
            'emerald' => 'from-emerald-500 to-emerald-300',
            'sky'     => 'from-sky-500 to-sky-300',
            'amber'   => 'from-amber-500 to-amber-300',
            'rose'    => 'from-rose-500 to-rose-300',
            'indigo'  => 'from-indigo-500 to-indigo-300',
            'teal'    => 'from-teal-500 to-teal-300',
            'slate'   => 'from-slate-500 to-slate-300',
        ];
        $surfaceClass = [
            'violet'  => 'bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400',
            'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
            'sky'     => 'bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400',
            'amber'   => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
            'rose'    => 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
            'indigo'  => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
            'teal'    => 'bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400',
            'slate'   => 'bg-slate-100 text-slate-600 dark:bg-slate-700/40 dark:text-slate-300',
        ];
    @endphp

    <div class="space-y-6 sm:space-y-8" x-data="{ query: '' }">
        {{-- Header ringkas (admin context) --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between" data-aos="fade-up">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-500">Dashboard</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('admin.apps.index') }}" class="hover:text-blue-500">Apps Management</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-gray-900 dark:text-white">Apps Hub</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Apps Hub</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tampilan launcher dari sisi user. Klik kartu untuk menguji URL aplikasi yang Anda konfigurasi.
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $visibleTotal }} aplikasi aktif</span> untuk role Admin.
                </p>
            </div>
            <a href="{{ route('admin.apps.index') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Apps Management
            </a>
        </header>

        @if($visibleTotal === 0)
            <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-gray-800 sm:p-14">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Belum ada aplikasi untuk Anda</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Untuk melihat launcher sebagai Admin, tambahkan aplikasi yang mengizinkan role "Admin" atau "Semua" di halaman Apps Management.
                </p>
            </section>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($items as $app)
                    @php
                        $color = $iconTheme($app->icon);
                        $desc = $app->description ?? '';
                        $rawUrl = (string) ($app->url ?? '');
                        $trimmed = trim($rawUrl);
                        $launchUrl = ($trimmed !== '' && (bool) preg_match('#^https?://#i', $trimmed)) ? $trimmed : '';
                        $isNewTab = $app->open_mode === 'new_tab';
                    @endphp

                    <article
                        data-app-card
                        data-app-name="{{ \Illuminate\Support\Str::lower($app->name) }}"
                        data-app-desc="{{ \Illuminate\Support\Str::lower($desc) }}"
                        x-show="!query || '{{ \Illuminate\Support\Str::lower($app->name) }}'.includes(query.toLowerCase()) || '{{ \Illuminate\Support\Str::lower($desc) }}'.includes(query.toLowerCase())"
                        class="group relative flex flex-col gap-3 overflow-hidden rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition dark:border-gray-700/50 dark:bg-gray-800
                            {{ $launchUrl ? 'hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md cursor-pointer dark:hover:border-blue-500/40' : 'cursor-not-allowed pointer-events-none opacity-70' }}"
                        @if(! $launchUrl) aria-disabled="true" @else role="link" tabindex="0" @endif
                    >
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r {{ $gradientClass[$color] ?? $gradientClass['slate'] }}"></span>

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg {{ $surfaceClass[$color] ?? $surfaceClass['slate'] }}">
                                @if(isset($app->image_icon) && $app->image_icon)
                                    <img src="{{ asset('storage/' . $app->image_icon) }}" alt="{{ $app->name }}" class="h-6 w-6 rounded object-contain">
                                @else
                                    @include('partials.apps._icon', ['icon' => $app->icon, 'class' => 'h-6 w-6'])
                                @endif
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                @if($isNewTab) bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @else bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 @endif"
                                title="{{ $isNewTab ? 'Aplikasi dibuka di tab baru' : 'Navigator ke URL di tab ini' }}">
                                @if($isNewTab)
                                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Tab Baru
                                @else
                                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M13 5l7 7-7 7"/></svg>
                                    Tab Ini
                                @endif
                            </span>
                        </div>

                        <div class="min-h-0 flex-1">
                            <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $app->name }}</h3>
                            <p class="mt-1.5 line-clamp-3 text-xs leading-relaxed text-gray-600 dark:text-gray-400">{{ $desc }}</p>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-[11px] text-gray-500 dark:border-gray-700/50 dark:text-gray-400">
                            @if($launchUrl)
                                <a
                                    href="{{ $launchUrl }}"
                                    @if($isNewTab) target="_blank" rel="noopener noreferrer" @endif
                                    data-app-launcher
                                    class="inline-flex items-center gap-1.5 font-semibold text-blue-600 transition group-hover:text-blue-700 dark:text-blue-400 dark:group-hover:text-blue-300"
                                    aria-label="Buka {{ $app->name }}{{ $isNewTab ? ' di tab baru' : '' }}"
                                >
                                    {{ $isNewTab ? 'Buka di tab baru' : 'Buka aplikasi' }}
                                    @if($isNewTab)
                                        <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    @else
                                        <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    @endif
                                </a>
                            @else
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-amber-700 dark:text-amber-300">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Aplikasi belum tersedia.
                                    </span>
                                </div>
                            @endif
                            <a href="{{ route('admin.apps.edit', ['slug' => $app->slug]) }}" class="text-[10px] font-medium text-gray-400 transition hover:text-blue-500 dark:text-gray-500 dark:hover:text-blue-400" title="Sunting aplikasi">
                                Edit
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>
