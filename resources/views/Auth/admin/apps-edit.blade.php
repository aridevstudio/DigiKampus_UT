<x-layouts.admin :active="'apps'">
    @php
        $displayName = old('display_name', $app['display_name'] ?? '');
        $description = old('description', $app['description'] ?? '');
        $externalUrl = old('external_url', $app['external_url'] ?? '');
        $isActive = (bool) old('is_active', $app['is_active'] ?? false);
        $accessRoles = old('access_roles', $app['access_roles'] ?? ['mahasiswa']);
        if (!is_array($accessRoles)) $accessRoles = [$accessRoles];
        $color = $app['color'] ?? 'slate';
        $icon = $app['icon'] ?? 'apps';
        $hasOverride = $app['_has_override'] ?? false;
        $catalogUrl = $app['external_url'] ?? '';
        $urlTrimmed = trim((string) $externalUrl);
        $urlValidHttp = $urlTrimmed !== '' && (str_starts_with(\Str::lower($urlTrimmed), 'http://') || str_starts_with(\Str::lower($urlTrimmed), 'https://'));
    @endphp

    <div class="space-y-5 sm:space-y-6">

        {{-- Page header --}}
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between" data-aos="fade-up">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-500">Dashboard</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('admin.apps.index') }}" class="hover:text-blue-500">Apps Management</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $app['display_name'] ?? $slug }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                    Edit {{ $app['display_name'] ?? $slug }}
                </h1>
                <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 dark:bg-gray-700/50">{{ $slug }}</code>
                    <span>·</span>
                    <span>{{ $app['category'] ?? '-' }}</span>
                    <span>·</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" title="Aplikasi dibuka di tab baru">Launcher</span>
                    @if($hasOverride)
                        <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                            Custom Override
                        </span>
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.apps.index') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </header>

        {{-- Info banner: developer-locked fields --}}
        <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/70 p-4 dark:border-amber-800/30 dark:bg-amber-950/20">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Field yang dapat diedit admin</p>
                <p class="mt-0.5 text-xs text-amber-800 dark:text-amber-300 sm:text-sm">
                    Admin dapat mengubah <strong>display name</strong>, <strong>deskripsi</strong>, <strong>URL tujuan</strong> (opsional override), <strong>status aktif</strong>, dan <strong>hak akses</strong>. Field pengembangan (slug, kategori) adalah tanggung jawab developer di registry service.
                </p>
            </div>
        </div>

        {{-- Edit form --}}
        <form
            action="{{ route('admin.apps.update', ['slug' => $slug]) }}"
            method="POST"
            class="space-y-5"
            data-aos="fade-up"
            data-aos-delay="100"
            x-data="{
                descLength: {{ strlen((string) $description) }},
                urlValue: @js($externalUrl),
                selectedRoles: {{ json_encode(old('access_roles', $app['access_roles'] ?? ['mahasiswa'])) }},
                isSelected(role) {
                    return Array.isArray(this.selectedRoles) && this.selectedRoles.indexOf(role) !== -1;
                },
                updateDesc(ev) { this.descLength = (ev.target.value || '').length; },
                updateUrl(ev) { this.urlValue = ev.target.value || ''; },
                toggleRole(ev) {
                    const role = ev.target.value;
                    if (!Array.isArray(this.selectedRoles)) this.selectedRoles = [];
                    if (ev.target.checked) {
                        if (!this.isSelected(role)) this.selectedRoles = this.selectedRoles.concat([role]);
                    } else {
                        this.selectedRoles = this.selectedRoles.filter(r => r !== role);
                    }
                },
            }"
        >
            @csrf
            @method('PUT')

            {{-- Display Name --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <label for="display_name" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">
                    Display Name
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                    Nama yang ditampilkan ke mahasiswa. Kosongkan untuk menggunakan nama bawaan dari catalog.
                </p>
                <input
                    type="text"
                    id="display_name"
                    name="display_name"
                    value="{{ $displayName }}"
                    maxlength="120"
                    placeholder="{{ $app['display_name'] ?? 'Nama tampilan aplikasi' }}"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                >
                @error('display_name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </section>

            {{-- Description --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <label for="description" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">
                    Deskripsi
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                    Deskripsi singkat yang muncul di kartu aplikasi. Maks 2000 karakter.
                </p>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    maxlength="2000"
                    placeholder="{{ $app['description'] ?? 'Deskripsi aplikasi…' }}"
                    @input="updateDesc($event)"
                    class="block w-full resize-y rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                >{{ $description }}</textarea>
                <p class="mt-1 text-right text-[11px] text-gray-500 dark:text-gray-500">
                    <span x-text="descLength"></span>/2000
                </p>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </section>

            {{-- Inline CSS for x-cloak (Alpine anti-FOUC helper) --}}
            <style>[x-cloak]{display:none!important}</style>

            {{-- External URL (admin override — optional) --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <label for="external_url" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">
                    URL Tujuan
                    <span class="ml-1 inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Opsional</span>
                </label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                    URL yang dibuka saat mahasiswa mengklik kartu. Kosongkan untuk menggunakan URL bawaan developer pada CATALOG.
                </p>

                {{-- Catalog baseline --}}
                @if(!empty($catalogUrl))
                    <div class="mb-2 flex items-center gap-2 rounded-lg border border-dashed border-gray-200 bg-gray-50/70 px-3 py-2 text-[11px] dark:border-gray-700 dark:bg-gray-900/30">
                        <span class="inline-flex items-center gap-1 font-semibold text-gray-700 dark:text-gray-300">Bawaan:</span>
                        <a href="{{ $catalogUrl }}" target="_blank" rel="noopener noreferrer" class="break-all font-mono text-blue-600 hover:underline dark:text-blue-400">{{ $catalogUrl }}</a>
                        <span class="ml-auto rounded bg-gray-200 px-1.5 py-0.5 text-[10px] uppercase tracking-wide text-gray-500 dark:bg-gray-700 dark:text-gray-400">catalog</span>
                    </div>
                @else
                    <div class="mb-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] text-amber-800 dark:border-amber-700/40 dark:bg-amber-950/30 dark:text-amber-200">
                        ⚠️ Tidak ada URL bawaan dari catalog — admin <strong>wajib</strong> mengisi URL agar kartu dapat diklik.
                    </div>
                @endif

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input
                        type="url"
                        id="external_url"
                        name="external_url"
                        value="{{ $externalUrl }}"
                        maxlength="2048"
                        placeholder="https://contoh-aplikasi.ac.id/"
                        @input="updateUrl($event)"
                        autocomplete="off"
                        spellcheck="false"
                        class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-xs text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                    >
                    <button
                        type="button"
                        x-show="urlValue && urlValue.length > 0"
                        x-cloak
                        @click="window.open(urlValue, '_blank', 'noopener,noreferrer')"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Tes URL
                    </button>
                </div>
                @error('external_url')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    Format: protokol <code>https://</code> atau <code>http://</code>. Disimpan di kolom
                    <code>app_registry_overrides.external_url</code> dan di-cache otomatis.
                </p>
            </section>

            {{-- Status toggle --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Status Aktif</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Nonaktifkan jika aplikasi sedang tidak tersedia atau maintenance. Kartu nonaktif akan disembunyikan dari mahasiswa.
                        </p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ $isActive ? 'checked' : '' }}>
                        <span class="relative h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-emerald-500 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/30 dark:bg-gray-700"></span>
                        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                        <span class="sr-only">Aktifkan aplikasi</span>
                    </label>
                </div>
            </section>

            {{-- Access Roles --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <p class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Hak Akses</p>
                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                    Pilih role yang boleh membuka aplikasi ini. Minimal satu role harus dipilih.
                </p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach (['mahasiswa' => ['color' => 'emerald', 'label' => 'Mahasiswa', 'desc' => 'Seluruh mahasiswa aktif'],
                                'dosen' => ['color' => 'sky', 'label' => 'Dosen', 'desc' => 'Dosen pengajar & mentor'],
                                'admin' => ['color' => 'amber', 'label' => 'Admin', 'desc' => 'Administrator sistem']] as $role => $meta)
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-3 transition hover:border-blue-300 dark:border-gray-700 dark:bg-gray-800"
                            :class="isSelected('{{ $role }}') ? 'border-blue-400 bg-blue-50/40 dark:border-blue-500/50 dark:bg-blue-900/10' : ''"
                        >
                            <input
                                type="checkbox"
                                name="access_roles[]"
                                value="{{ $role }}"
                                @change="toggleRole($event)"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500"
                                {{ in_array($role, $accessRoles, true) ? 'checked' : '' }}
                            >
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $meta['label'] }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $meta['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('access_roles')
                    <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
                @error('access_roles.*')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </section>

            {{-- Submit --}}
            <div class="admin-responsive-modal-actions flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.apps.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50">
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
