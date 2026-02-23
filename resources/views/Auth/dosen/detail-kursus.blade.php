<x-layouts.dosen title="Detail Kursus" active="kursus-saya">
    @php
        $statusColors = [
            'aktif' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            'draft' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            'nonaktif' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        ];
        $statusClass = $statusColors[$detail['status']] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300';

        $displayModules = $course->modules;
        if ($displayModules->isEmpty() && $course->materials->isNotEmpty()) {
            $displayModules = collect([
                (object) [
                    'judul_module' => 'Modul Utama',
                    'deskripsi' => 'Modul otomatis dari materi lama',
                    'materials' => $course->materials,
                ],
            ]);
        }
    @endphp

    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Detail Kursus</p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $detail['nama_course'] }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ ucfirst($detail['status']) }}
                </span>
                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                    {{ strtoupper($detail['kode_course']) }}
                </span>
                <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700 dark:bg-purple-500/20 dark:text-purple-300">
                    {{ ucfirst($detail['kategori']) }}
                </span>
            </div>
        </div>

        <div class="inline-flex w-full flex-col gap-2 rounded-2xl border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:w-auto sm:flex-row">
            <a href="{{ route('dosen.kursus') }}" class="inline-flex h-11 min-w-[128px] items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 transition duration-200 hover:-translate-y-0.5 hover:border-gray-400 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <a href="{{ route('dosen.kursus.edit', $detail['id_course']) }}" class="inline-flex h-11 min-w-[128px] items-center justify-center gap-2 rounded-xl border border-blue-300 bg-blue-50 px-4 text-sm font-semibold text-blue-700 transition duration-200 hover:-translate-y-0.5 hover:border-blue-400 hover:bg-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-300 dark:border-blue-500/40 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:border-blue-400/60 dark:hover:bg-blue-500/30">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Kursus
            </a>
            <a href="{{ route('dosen.kursus.modul', $detail['id_course']) }}" class="inline-flex h-11 min-w-[132px] items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                Kelola Modul
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Informasi Kursus</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Deskripsi</p>
                        <p class="mt-1 text-gray-800 dark:text-gray-100">{{ $detail['deskripsi'] ?: '-' }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Jurusan</p>
                            <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ $detail['jurusan'] ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Tipe Harga</p>
                            <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">{{ ucfirst($detail['tipe']) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Harga</p>
                            <p class="mt-1 font-semibold text-gray-800 dark:text-gray-100">
                                @if($detail['tipe'] === 'gratis')
                                    Gratis
                                @else
                                    Rp {{ number_format((float) $detail['harga'], 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Struktur Modul</h2>

                @if($displayModules->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 px-5 py-10 text-center dark:border-gray-600">
                        <p class="font-medium text-gray-700 dark:text-gray-200">Belum ada modul</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan modul untuk mulai menyusun materi kursus.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($displayModules as $idx => $module)
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Modul {{ $idx + 1 }}</p>
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $module->judul_module }}</h3>
                                        @if(!empty($module->deskripsi))
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $module->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $module->materials->count() }} materi
                                    </span>
                                </div>

                                @if($module->materials->isNotEmpty())
                                    <div class="mt-3 space-y-2">
                                        @foreach($module->materials as $material)
                                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-700/40">
                                                <div class="min-w-0">
                                                    <p class="truncate font-medium text-gray-800 dark:text-gray-100">{{ $material->judul_material }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($material->tipe) }}</p>
                                                </div>
                                                @if(!empty($material->durasi))
                                                    <span class="ml-3 shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ $material->durasi }} menit</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Statistik</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                        <p class="text-xs text-blue-600 dark:text-blue-300">Mahasiswa</p>
                        <p class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-200">{{ $detail['mahasiswa_count'] }}</p>
                    </div>
                    <div class="rounded-xl bg-green-50 p-4 dark:bg-green-500/10">
                        <p class="text-xs text-green-600 dark:text-green-300">Rata-rata Progres</p>
                        <p class="mt-1 text-xl font-bold text-green-700 dark:text-green-200">{{ $detail['progress_avg'] }}%</p>
                    </div>
                    <div class="rounded-xl bg-purple-50 p-4 dark:bg-purple-500/10">
                        <p class="text-xs text-purple-600 dark:text-purple-300">Total Modul</p>
                        <p class="mt-1 text-xl font-bold text-purple-700 dark:text-purple-200">{{ $detail['modules_count'] }}</p>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-4 dark:bg-amber-500/10">
                        <p class="text-xs text-amber-600 dark:text-amber-300">Total Materi</p>
                        <p class="mt-1 text-xl font-bold text-amber-700 dark:text-amber-200">{{ $detail['materials_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Mahasiswa Terbaru</h2>
                <div class="space-y-3">
                    @forelse($course->enrollments->take(5) as $enrollment)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-700/40">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100">
                                    {{ $enrollment->mahasiswa?->name ?? 'Mahasiswa' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->mahasiswa?->profile?->nim ?? '-' }}</p>
                            </div>
                            <span class="ml-3 shrink-0 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                {{ (int) round($enrollment->progress ?? 0) }}%
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada mahasiswa terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.dosen>
