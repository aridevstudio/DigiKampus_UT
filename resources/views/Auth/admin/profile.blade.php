<x-layouts.admin title="Profil Admin" active="profile">

{{-- Header --}}
<div class="mb-8 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Profil Admin</h1>
    <p class="text-gray-500 dark:text-gray-400">Kelola informasi akun dan password Anda</p>
</div>





<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" x-data="{ isLoading: false }" @submit="isLoading = true">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Info --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Informasi Profil</h2>
            
            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- Photo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Foto Profil</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400 file:font-medium file:cursor-pointer hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 transition">
                    <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP. Maks 2MB.</p>
                </div>
            </div>
        </div>

        {{-- Avatar Preview --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 text-center">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Preview</h2>
            @php
                $foto = $admin->profile->foto_profile ?? null;
            @endphp
            @if($foto)
                <img src="{{ asset('storage/' . $foto) }}" alt="Profile" class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-blue-100 dark:border-blue-500/20">
            @else
                <div class="w-24 h-24 rounded-full mx-auto bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-blue-100 dark:border-blue-500/20">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
            @endif
            <p class="mt-4 font-semibold text-gray-800 dark:text-white">{{ $admin->name }}</p>
            <p class="text-sm text-gray-500">{{ $admin->email }}</p>
            <span class="inline-block mt-2 px-3 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-full">Admin</span>
        </div>
    </div>

    {{-- Password Section --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Ubah Password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Lama</label>
                <input type="password" name="current_password"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
                <input type="password" name="new_password"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation"
                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="mt-6 flex justify-end">
        <button type="submit" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
            Simpan Perubahan
        </button>
    </div>
</form>

</x-layouts.admin>
