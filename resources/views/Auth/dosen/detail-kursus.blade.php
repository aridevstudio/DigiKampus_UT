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
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Diskusi Kursus</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Thread ini sama dengan yang dilihat mahasiswa di halaman belajar kursus.</p>
                    </div>
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                        Live thread
                    </span>
                </div>

                <div id="dosen-discussion-container" class="space-y-4 rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-900/30" style="min-height: 340px; max-height: 460px; overflow-y: auto;"></div>

                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <textarea
                        id="dosen-discussion-input"
                        rows="3"
                        placeholder="Balas pertanyaan mahasiswa atau tulis pengumuman singkat terkait materi..."
                        class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pesan gagal kirim akan tetap tampil dan bisa dicoba ulang tanpa hilang.</p>
                        <button
                            type="button"
                            id="dosen-discussion-send-btn"
                            onclick="sendDosenCourseDiscussion()"
                            class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            Balas Diskusi
                        </button>
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

    @push('scripts')
    <script>
        const dosenDiscussionEndpoint = '{{ route('dosen.course-discussions.index', ['id' => $detail['id_course']], false) }}';
        const dosenDiscussionStoreEndpoint = '{{ route('dosen.course-discussions.store', ['id' => $detail['id_course']], false) }}';
        const dosenDiscussionCurrentUser = @json([
            'name' => auth('dosen')->user()?->name ?? 'Pengajar',
            'role' => 'Pengajar',
            'avatar' => auth('dosen')->user()?->profile?->foto_profile
                ? asset('storage/' . auth('dosen')->user()->profile->foto_profile)
                : 'https://ui-avatars.com/api/?name=' . urlencode(auth('dosen')->user()?->name ?? 'Pengajar') . '&background=2563EB&color=fff',
        ]);
        let dosenDiscussionComments = [];
        let dosenDiscussionPendingComments = [];
        let dosenDiscussionTempSeed = 0;
        let dosenDiscussionPoller = null;

        function escapeDosenDiscussionHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function getDosenDiscussionRenderableItems() {
            return [...dosenDiscussionComments, ...dosenDiscussionPendingComments];
        }

        function renderDosenDiscussionComments() {
            const container = document.getElementById('dosen-discussion-container');
            if (!container) return;

            const comments = getDosenDiscussionRenderableItems();

            if (!Array.isArray(comments) || comments.length === 0) {
                container.innerHTML = `
                    <div class="flex h-full min-h-[300px] flex-col items-center justify-center space-y-3 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-800">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Belum ada diskusi di kursus ini</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pertanyaan mahasiswa akan muncul di sini.</p>
                        </div>
                    </div>`;
                return;
            }

            container.innerHTML = comments.map((comment) => `
                <div class="flex gap-3">
                    <img src="${escapeDosenDiscussionHtml(comment.avatar)}" alt="${escapeDosenDiscussionHtml(comment.name)}" class="h-9 w-9 flex-shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700">
                    <div class="flex-1">
                        <div class="mb-1 flex items-baseline justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">${escapeDosenDiscussionHtml(comment.name)}</h4>
                                <span class="rounded px-1.5 py-0.5 text-[10px] font-bold ${comment.role === 'Pengajar' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'}">${escapeDosenDiscussionHtml(comment.role)}</span>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500">${escapeDosenDiscussionHtml(comment.time ?? '')}</span>
                        </div>
                        <div class="rounded-r-xl rounded-bl-xl border ${comment.local_status === 'failed' ? 'border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800/60'} p-3">
                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">${escapeDosenDiscussionHtml(comment.text)}</p>
                            ${comment.local_status ? `
                                <div class="mt-2 flex items-center justify-between gap-3 text-[11px]">
                                    <span class="${comment.local_status === 'failed' ? 'text-red-500 dark:text-red-300' : 'text-amber-500 dark:text-amber-300'}">
                                        ${comment.local_status === 'failed' ? escapeDosenDiscussionHtml(comment.error_message || 'Gagal dikirim') : 'Mengirim...'}
                                    </span>
                                    ${comment.local_status === 'failed' ? `<button type="button" onclick="retryDosenCourseDiscussion('${escapeDosenDiscussionHtml(comment.temp_id)}')" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200">Coba lagi</button>` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            container.scrollTop = container.scrollHeight;
        }

        async function fetchDosenCourseDiscussion() {
            const response = await fetch(dosenDiscussionEndpoint, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal memuat diskusi kursus.');
            }

            dosenDiscussionComments = Array.isArray(data.data) ? data.data : [];
            renderDosenDiscussionComments();
        }

        function createDosenPendingDiscussionComment(message) {
            dosenDiscussionTempSeed += 1;

            return {
                temp_id: `dosen-discussion-temp-${Date.now()}-${dosenDiscussionTempSeed}`,
                name: dosenDiscussionCurrentUser.name,
                role: dosenDiscussionCurrentUser.role,
                text: message,
                time: 'Baru saja',
                avatar: dosenDiscussionCurrentUser.avatar,
                local_status: 'sending',
            };
        }

        function upsertDosenPendingDiscussionComment(comment) {
            const existingIndex = dosenDiscussionPendingComments.findIndex((item) => item.temp_id === comment.temp_id);

            if (existingIndex >= 0) {
                dosenDiscussionPendingComments[existingIndex] = comment;
            } else {
                dosenDiscussionPendingComments.push(comment);
            }

            renderDosenDiscussionComments();
        }

        function removeDosenPendingDiscussionComment(tempId) {
            dosenDiscussionPendingComments = dosenDiscussionPendingComments.filter((item) => item.temp_id !== tempId);
            renderDosenDiscussionComments();
        }

        async function submitDosenCourseDiscussion(message, pendingComment) {
            const response = await fetch(dosenDiscussionStoreEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: (() => {
                    const formData = new FormData();
                    formData.append('message', message);
                    return formData;
                })(),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Balasan diskusi gagal dikirim.');
            }

            removeDosenPendingDiscussionComment(pendingComment.temp_id);
            dosenDiscussionComments = [...dosenDiscussionComments, data.data];
            renderDosenDiscussionComments();
        }

        async function sendDosenCourseDiscussion() {
            const input = document.getElementById('dosen-discussion-input');
            const button = document.getElementById('dosen-discussion-send-btn');
            if (!input || !button) return;

            const message = input.value.trim();
            if (!message) return;

            button.disabled = true;
            const pendingComment = createDosenPendingDiscussionComment(message);
            dosenDiscussionPendingComments.push(pendingComment);
            renderDosenDiscussionComments();
            input.value = '';

            try {
                await submitDosenCourseDiscussion(message, pendingComment);
            } catch (error) {
                pendingComment.local_status = 'failed';
                pendingComment.error_message = error.message || 'Balasan diskusi gagal dikirim.';
                upsertDosenPendingDiscussionComment(pendingComment);
            } finally {
                button.disabled = false;
            }
        }

        async function retryDosenCourseDiscussion(tempId) {
            const pendingComment = dosenDiscussionPendingComments.find((item) => item.temp_id === tempId);
            if (!pendingComment) return;

            pendingComment.local_status = 'sending';
            pendingComment.error_message = null;
            upsertDosenPendingDiscussionComment(pendingComment);

            try {
                await submitDosenCourseDiscussion(pendingComment.text, pendingComment);
            } catch (error) {
                pendingComment.local_status = 'failed';
                pendingComment.error_message = error.message || 'Balasan diskusi gagal dikirim.';
                upsertDosenPendingDiscussionComment(pendingComment);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            fetchDosenCourseDiscussion().catch(() => {});

            if (dosenDiscussionPoller) {
                window.clearInterval(dosenDiscussionPoller);
            }

            dosenDiscussionPoller = window.setInterval(() => {
                fetchDosenCourseDiscussion().catch(() => {});
            }, 7000);

            const input = document.getElementById('dosen-discussion-input');
            input?.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendDosenCourseDiscussion();
                }
            });
        });
    </script>
    @endpush
</x-layouts.dosen>
