<x-layouts.admin :active="'apps'">
    @php
        $name = old('name', '');
        $slug = old('slug', '');
        $description = old('description', '');
        $url = old('url', '');
        $icon = old('icon', 'globe');
        $openMode = old('open_mode', 'new_tab');
        $isActive = (bool) old('is_active', true);
        $allowedRoles = old('allowed_roles', ['mahasiswa']);
        if (! is_array($allowedRoles)) $allowedRoles = [$allowedRoles];
        // View-only normalization: bila 'all' tersimpan, tampilkan 4 kotak
        // sebagai terceklis (admin tidak perlu klik 3 role satu-satu).
        $allowedRolesNormalized = in_array('all', $allowedRoles, true)
            ? ['mahasiswa', 'dosen', 'admin', 'all']
            : array_values(array_intersect($allowedRoles, ['mahasiswa', 'dosen', 'admin']));
    @endphp

    <div class="space-y-5 sm:space-y-6">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between" data-aos="fade-up">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 sm:text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-500">Dashboard</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('admin.apps.index') }}" class="hover:text-blue-500">Apps Management</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-gray-900 dark:text-white">Tambah Aplikasi</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Tambah Aplikasi Baru</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Daftarkan launcher aplikasi baru. Setelah disimpan, slug tidak dapat diubah.
                </p>
            </div>
            <a href="{{ route('admin.apps.index') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </header>

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-700/40 dark:bg-rose-950/30 dark:text-rose-200">
                <p class="font-semibold">Validasi gagal:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <style>[x-cloak]{display:none!important}</style>

        <form
            action="{{ route('admin.apps.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5"
            x-data="{
                descLength: {{ strlen((string) $description) }},
                updateDesc(ev) { this.descLength = (ev.target.value || '').length; },
                checkedRoles: @js($allowedRolesNormalized),
                isChecked(role) { return this.checkedRoles.includes(role); },
                toggle(role, newChecked) {
                    const set = new Set(this.checkedRoles);
                    if (newChecked) set.add(role); else set.delete(role);
                    if (role === 'all') {
                        if (newChecked) ['mahasiswa', 'dosen', 'admin', 'all'].forEach(r => set.add(r));
                        else ['mahasiswa', 'dosen', 'admin', 'all'].forEach(r => set.delete(r));
                    } else {
                        const allThreePresent = ['mahasiswa', 'dosen', 'admin'].every(r => set.has(r));
                        if (allThreePresent) set.add('all'); else set.delete('all');
                    }
                    this.checkedRoles = Array.from(set);
                },
            }"
            data-aos="fade-up"
            data-aos-delay="100"
        >
            @csrf

            {{-- Name + Slug --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">Nama Aplikasi <span class="text-rose-600">*</span></label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Nama tampilan yang muncul di kartu mahasiswa.</p>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ $name }}"
                            maxlength="120"
                            required
                            placeholder="Contoh: Microsoft Copilot"
                            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        >
                        @error('name')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="slug" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">Slug <span class="text-rose-600">*</span></label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Identifier URL-safe (huruf kecil, angka, tanda hubung). <strong>Tidak dapat diubah setelah disimpan.</strong></p>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="{{ $slug }}"
                            maxlength="64"
                            required
                            pattern="[a-z0-9\-]+"
                            placeholder="contoh: ms-copilot"
                            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-xs text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        >
                        @error('slug')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- Description --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <label for="description" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">Deskripsi</label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Deskripsi singkat yang muncul di kartu aplikasi. Maks 2000 karakter.</p>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    maxlength="2000"
                    placeholder="Jelaskan kegunaan aplikasi untuk mahasiswa…"
                    @input="updateDesc($event)"
                    class="block w-full resize-y rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                >{{ $description }}</textarea>
                <p class="mt-1 text-right text-[11px] text-gray-500 dark:text-gray-500"><span x-text="descLength"></span>/2000</p>
            </section>

            {{-- URL + Icon --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <label for="url" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">URL Tujuan <span class="ml-1 inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-gray-700 dark:text-gray-300">Opsional</span></label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">URL eksternal yang dibuka saat mahasiswa klik kartu. Kosongkan jika belum tersedia.</p>
                        <input
                            type="url"
                            id="url"
                            name="url"
                            value="{{ $url }}"
                            maxlength="2048"
                            placeholder="https://contoh-aplikasi.ac.id/"
                            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-xs text-gray-800 placeholder-gray-400 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
                        >
                        @error('url')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="icon" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">Icon Default <span class="text-rose-600">*</span></label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Theme color otomatis mengikuti icon.</p>
                        <select
                            id="icon"
                            name="icon"
                            required
                            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                        >
                            @foreach($iconOptions as $key => $label)
                                <option value="{{ $key }}" {{ $icon === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('icon')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <label for="image_icon" class="mb-1 block text-sm font-semibold text-gray-900 dark:text-white">Icon Custom (Format WebP) <span class="ml-1 inline-flex items-center rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-gray-700 dark:text-gray-300">Opsional</span></label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Jika diisi, icon ini menggantikan Icon Default. Maks 2MB, format .webp. <strong>Rekomendasi ukuran: 128x128 pixel (atau rasio 1:1)</strong>.</p>
                        <input
                            type="file"
                            id="image_icon"
                            name="image_icon"
                            accept="image/webp"
                            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                        >
                        @error('image_icon')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            {{-- Open Mode --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <p class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Mode Buka <span class="text-rose-600">*</span></p>
                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Bagaimana URL dibuka saat mahasiswa mengklik kartu.</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border-2 p-3 transition {{ $openMode === 'new_tab' ? 'border-blue-400 bg-blue-50/40 dark:border-blue-500/50 dark:bg-blue-900/10' : 'border-gray-200 hover:border-blue-300 dark:border-gray-700' }}">
                        <input type="radio" name="open_mode" value="new_tab" class="mt-1 h-4 w-4 text-blue-500 focus:ring-blue-500" {{ $openMode === 'new_tab' ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Tab Baru</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Aplikasi terbuka di tab browser baru — mahasiswa tetap bisa kembali ke DigiKampus dengan satu klik.</p>
                        </div>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border-2 p-3 transition {{ $openMode === 'same_tab' ? 'border-blue-400 bg-blue-50/40 dark:border-blue-500/50 dark:bg-blue-900/10' : 'border-gray-200 hover:border-blue-300 dark:border-gray-700' }}">
                        <input type="radio" name="open_mode" value="same_tab" class="mt-1 h-4 w-4 text-blue-500 focus:ring-blue-500" {{ $openMode === 'same_tab' ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Tab Saat Ini</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Browser meninggalkan DigiKampus dan navigasi ke URL aplikasi langsung di tab ini.</p>
                        </div>
                    </label>
                </div>
                @error('open_mode')<p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </section>

            {{-- Allowed Roles + Active --}}
            <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800 sm:p-6">
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <p class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Hak Akses <span class="text-rose-600">*</span></p>
                        <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Pilih minimal satu role. Pilih "Semua" jika aplikasi untuk seluruh pengguna.</p>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach (['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'admin' => 'Admin', 'all' => 'Semua'] as $role => $label)
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 transition hover:border-blue-300"
                                       :class="isChecked('{{ $role }}') ? 'border-blue-400 bg-blue-50/40 dark:border-blue-500/50 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-700'">
                                    <input type="checkbox" name="allowed_roles[]" value="{{ $role }}" class="h-4 w-4 rounded border-gray-300 text-blue-500 focus:ring-blue-500"
                                           :checked="isChecked('{{ $role }}')"
                                           @change="toggle('{{ $role }}', $event.target.checked)">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('allowed_roles')<p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Status</p>
                        <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Nonaktif = kartu tidak tampil ke user.</p>
                        <label class="inline-flex cursor-pointer items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ $isActive ? 'checked' : '' }}>
                            <span class="relative h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-emerald-500 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/30 dark:bg-gray-700"></span>
                            <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                        </label>
                    </div>
                </div>
            </section>

            <div class="admin-responsive-modal-actions flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.apps.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/50">Batal</a>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/30">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Aplikasi
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
