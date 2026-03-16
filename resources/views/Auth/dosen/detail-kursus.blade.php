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

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('dosen.kursus') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-gray-300 bg-gray-50 px-5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                Kembali
            </a>
            <a href="{{ route('dosen.kursus.edit', $detail['id_course']) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-blue-300 bg-white px-5 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-300 dark:border-blue-500/40 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-500/20">
                Edit Kursus
            </a>
            <a href="{{ route('dosen.kursus.modul', $detail['id_course']) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-300">
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
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Catatan Dosen</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Catatan ini tampil di halaman belajar mahasiswa yang mengikuti kursus ini.</p>
                    </div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                        {{ $course->instructorNotes->count() }} catatan
                    </span>
                </div>

                <form action="{{ route('dosen.kursus.notes.store', $detail['id_course']) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul catatan</label>
                            <input type="text" name="judul" placeholder="Contoh: Fokus modul minggu ini" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Isi catatan</label>
                        <textarea name="konten" rows="4" required placeholder="Tulis catatan yang perlu dilihat mahasiswa..." class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Kirim Catatan
                        </button>
                    </div>
                </form>

                <div class="mt-6 space-y-3">
                    @forelse($course->instructorNotes as $note)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $note->judul ?: 'Catatan Dosen' }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ optional($note->created_at)->diffForHumans() }}</p>
                                </div>
                                <form action="{{ route('dosen.kursus.notes.delete', ['id' => $detail['id_course'], 'noteId' => $note->id_course_instructor_note]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $note->konten }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 px-5 py-8 text-center dark:border-gray-600">
                            <p class="font-medium text-gray-700 dark:text-gray-200">Belum ada catatan dosen</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gunakan form di atas untuk mengirim catatan ke mahasiswa.</p>
                        </div>
                    @endforelse
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

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800" x-data="{ search: '' }">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Mahasiswa</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $course->enrollments->count() }} terdaftar</span>
                </div>

                @if($course->enrollments->count() > 5)
                <div class="mb-3">
                    <input type="text" x-model="search" placeholder="Cari nama atau Nomor Induk..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                @endif

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse($course->enrollments as $enrollment)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2.5 dark:bg-gray-700/40"
                             x-show="!search || '{{ strtolower($enrollment->mahasiswa?->name ?? '') }} {{ strtolower($enrollment->mahasiswa?->profile?->nomor_induk ?? '') }}'.includes(search.toLowerCase())"
                             x-transition>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ strtoupper(substr($enrollment->mahasiswa?->name ?? 'M', 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $enrollment->mahasiswa?->name ?? 'Mahasiswa' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $enrollment->mahasiswa?->profile?->nomor_induk ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="ml-3 shrink-0 flex items-center gap-2">
                                @php $prog = (int) round($enrollment->progress ?? 0); @endphp
                                <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $prog >= 100 ? 'bg-green-500' : ($prog >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width: {{ $prog }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 w-8 text-right">{{ $prog }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">Belum ada mahasiswa terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.dosen>
