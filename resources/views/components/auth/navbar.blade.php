@props(['loginRoute' => null])

{{-- Auth Page Navbar - Same style as landing page --}}
<nav class="fixed top-0 left-0 right-0 z-50 px-4 sm:px-6 pt-4">
    <div class="nav-glass max-w-6xl mx-auto px-5 sm:px-6 h-14 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5" style="text-decoration:none">
            <img src="{{ asset('assets/image/dashboard/Logo Salut Cendikia Sukabumi.png') }}" alt="DigiKampus" class="h-8" loading="eager">
            <div>
                <span style="font-family:'Outfit',sans-serif;font-weight:700;font-size:15px;color:#1E293B">DigiKampus</span>
                <span style="font-size:11px;color:#94A3B8;margin-left:3px;font-weight:500">UT</span>
            </div>
        </a>
        <div class="hidden sm:flex items-center" style="gap:24px">
            <a href="/" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Beranda</a>
            <a href="/#fitur" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Fitur</a>
            <a href="/#portal" class="text-sm text-gray-500 hover:text-blue-500 transition-colors duration-200" style="text-decoration:none;font-weight:500">Portal</a>
        </div>
        @if($loginRoute)
        <a href="{{ $loginRoute }}" class="inline-flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-5 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md" style="text-decoration:none">
            Masuk
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
        </a>
        @else
        <a href="/" class="inline-flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-5 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md" style="text-decoration:none">
            Beranda
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
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
