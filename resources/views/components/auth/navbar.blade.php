@props(['loginRoute' => null])

{{-- Auth Page Navbar - Same style as landing page --}}
<nav class="fixed top-0 left-0 right-0 z-50 px-3 sm:px-6 pt-3 sm:pt-4">
    <div class="nav-glass max-w-6xl mx-auto px-3.5 sm:px-6 h-14 flex items-center justify-between gap-3">
        <a href="/" class="flex min-w-0 flex-1 items-center gap-2 sm:gap-2.5 overflow-hidden" style="text-decoration:none">
            <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}?v={{ @filemtime(public_path('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png')) }}" alt="DigiKampus" class="h-7 max-w-[78px] shrink-0 object-contain sm:h-8 sm:max-w-[132px]" loading="eager">
            <div class="flex min-w-0 flex-col leading-none sm:flex-row sm:items-baseline sm:gap-1">
                <span class="truncate" style="font-family:'Outfit',sans-serif;font-weight:700;font-size:clamp(13px,3.4vw,15px);color:#1E293B">DigiKampus</span>
                <span class="mt-0.5 inline-flex w-fit shrink-0 rounded-full bg-blue-50 px-1.5 py-0.5 text-[9px] font-semibold leading-none text-blue-500 sm:mt-0 sm:bg-transparent sm:px-0 sm:py-0 sm:text-[11px] sm:text-slate-400">UT</span>
            </div>
        </a>
        <div class="hidden sm:flex items-center" style="gap:24px">
            <a href="/" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Beranda</a>
            <a href="/#fitur" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Fitur</a>
            <a href="/#portal" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Portal</a>
        </div>
        @if($loginRoute)
        <div class="relative shrink-0" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-1.5 whitespace-nowrap bg-blue-500 hover:bg-blue-600 text-white text-xs sm:text-sm font-medium px-3 sm:px-5 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md" style="text-decoration:none">
                Masuk
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :class="{'rotate-180': open}" class="transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
            </button>
            <div x-show="open" x-transition.opacity.scale.95 style="display: none;" class="absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-56 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50">
                <a href="{{ route('mahasiswa.login') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" style="text-decoration:none; font-weight:500;">Masuk sebagai Mahasiswa</a>
                <a href="{{ route('dosen.login') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" style="text-decoration:none; font-weight:500;">Masuk sebagai Dosen</a>
                <a href="{{ route('admin.login') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors" style="text-decoration:none; font-weight:500;">Masuk sebagai Admin</a>
            </div>
        </div>
        @else
        <a href="/" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap bg-blue-500 hover:bg-blue-600 text-white text-xs sm:text-sm font-medium px-3 sm:px-5 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md" style="text-decoration:none">
            Beranda
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m-9-9l9 9-9 9"/></svg>
        </a>
        @endif
    </div>
</nav>

<style>
    .nav-glass {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226,232,240,0.6);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0,0,0,0.04);
    }
</style>
