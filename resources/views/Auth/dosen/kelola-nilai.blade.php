@php
    $gradeCourses = [
        [
            'id' => 'course-ui-ux',
            'name' => 'UI/UX Design Sprint',
            'code' => 'DSN-UIX-24',
            'period' => 'Semester Genap 2025/2026',
            'students' => 28,
            'weights' => ['pretest' => 20, 'assignment' => 35, 'final' => 45],
            'has_final_assignment' => true,
            'pending_reviews' => 9,
        ],
        [
            'id' => 'course-elektro',
            'name' => 'Elektronika Dasar',
            'code' => 'DSN-ELK-11',
            'period' => 'Semester Genap 2025/2026',
            'students' => 34,
            'weights' => ['pretest' => 25, 'assignment' => 40, 'final' => 35],
            'has_final_assignment' => true,
            'pending_reviews' => 6,
        ],
        [
            'id' => 'course-webinar',
            'name' => 'Webinar AI Product Thinking',
            'code' => 'WEB-AI-08',
            'period' => 'Batch Maret 2026',
            'students' => 120,
            'weights' => ['pretest' => 30, 'assignment' => 70, 'final' => 0],
            'has_final_assignment' => false,
            'pending_reviews' => 2,
        ],
    ];

    $gradeRows = [
        [
            'student' => 'Nabila Putri',
            'nomor_induk' => '20240149',
            'course_id' => 'course-ui-ux',
            'course' => 'UI/UX Design Sprint',
            'cohort' => 'Kelas A',
            'status' => 'Perlu Review',
            'pretest' => 82,
            'assignment' => 76,
            'final_assignment' => null,
            'last_update' => '10 menit lalu',
            'note' => 'Tugas akhir sudah masuk, belum diberi skor dosen.',
        ],
        [
            'student' => 'Gilang Pratama',
            'nomor_induk' => '20240122',
            'course_id' => 'course-ui-ux',
            'course' => 'UI/UX Design Sprint',
            'cohort' => 'Kelas A',
            'status' => 'Lengkap',
            'pretest' => 88,
            'assignment' => 84,
            'final_assignment' => 90,
            'last_update' => '1 jam lalu',
            'note' => 'Komponen nilai lengkap, siap publish nilai akhir.',
        ],
        [
            'student' => 'Salsa Maharani',
            'nomor_induk' => '20240177',
            'course_id' => 'course-elektro',
            'course' => 'Elektronika Dasar',
            'cohort' => 'Kelas B',
            'status' => 'Revisi Tugas',
            'pretest' => 74,
            'assignment' => 61,
            'final_assignment' => 72,
            'last_update' => '3 jam lalu',
            'note' => 'Mahasiswa sudah kirim revisi, cek ulang rubrik tugas.',
        ],
        [
            'student' => 'Ryan Champilin DDS',
            'nomor_induk' => '20240145',
            'course_id' => 'course-elektro',
            'course' => 'Elektronika Dasar',
            'cohort' => 'Kelas B',
            'status' => 'Lengkap',
            'pretest' => 91,
            'assignment' => 87,
            'final_assignment' => 89,
            'last_update' => 'Kemarin',
            'note' => 'Nilai stabil, bisa masuk shortlist mahasiswa unggulan.',
        ],
        [
            'student' => 'Jayde Abbott',
            'nomor_induk' => '20240133',
            'course_id' => 'course-webinar',
            'course' => 'Webinar AI Product Thinking',
            'cohort' => 'Batch 2',
            'status' => 'Lengkap',
            'pretest' => 79,
            'assignment' => 92,
            'final_assignment' => null,
            'last_update' => 'Hari ini',
            'note' => 'Webinar tanpa tugas akhir, nilai final diambil dari dua komponen.',
        ],
    ];

    $statusTone = [
        'Perlu Review' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:border-amber-500/20',
        'Lengkap' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
        'Revisi Tugas' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20',
    ];

    $gradeBands = [
        ['label' => 'A', 'min' => 85, 'tone' => 'text-emerald-600 dark:text-emerald-400'],
        ['label' => 'B', 'min' => 70, 'tone' => 'text-blue-600 dark:text-blue-400'],
        ['label' => 'C', 'min' => 55, 'tone' => 'text-amber-600 dark:text-amber-400'],
        ['label' => 'D', 'min' => 0, 'tone' => 'text-rose-600 dark:text-rose-400'],
    ];
@endphp

<x-layouts.dosen title="Mengelola Nilai" active="nilai">
    <div
        x-data="gradeManager({
            courses: @js($gradeCourses),
            rows: @js($gradeRows),
            statusTone: @js($statusTone),
            gradeBands: @js($gradeBands),
        })"
        x-init="init()"
        class="space-y-6"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                    Grade Center
                </span>
                <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Mengelola Nilai Mahasiswa</h1>
                <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-gray-400 sm:text-base">
                    Akumulasi nilai dari pretest, tugas, dan tugas akhir dalam satu panel supaya dosen bisa review, finalisasi, dan publish nilai akhir dengan alur yang rapi.
                </p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-white px-4 py-3 shadow-sm dark:border-blue-500/10 dark:bg-gray-800">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">Status Publish</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">Draft Nilai Semester</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Perubahan di halaman ini masih frontend preview. Logic sinkronisasi backend disimpan di docs.</p>
            </div>
        </div>

        <section class="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Kursus Aktif</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white" x-text="activeCourse.name"></h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-gray-400">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 font-medium dark:bg-gray-700/70" x-text="activeCourse.code"></span>
                            <span x-text="activeCourse.period"></span>
                            <span>&bull;</span>
                            <span x-text="`${activeCourse.students} mahasiswa`"></span>
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3 xl:w-[360px]">
                        <template x-for="course in courses" :key="course.id">
                            <button
                                type="button"
                                @click="setCourse(course.id)"
                                class="rounded-2xl border px-3 py-3 text-left transition"
                                :class="selectedCourse === course.id
                                    ? 'border-blue-400 bg-blue-50 shadow-sm dark:border-blue-500/30 dark:bg-blue-500/10'
                                    : 'border-slate-200 bg-slate-50 hover:border-slate-300 dark:border-gray-700 dark:bg-gray-900/40'"
                            >
                                <p class="text-xs font-semibold uppercase tracking-[0.16em]" :class="selectedCourse === course.id ? 'text-blue-600 dark:text-blue-300' : 'text-slate-400'" x-text="course.code"></p>
                                <p class="mt-2 line-clamp-2 text-sm font-semibold text-slate-900 dark:text-white" x-text="course.name"></p>
                                <p class="mt-2 text-xs text-slate-500 dark:text-gray-400" x-text="`${course.pending_reviews} butuh review`"></p>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Pretest</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white" x-text="`${activeCourse.weights.pretest}%`"></p>
                        <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">Bobot nilai pemahaman awal.</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Modul</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white" x-text="`${activeCourse.weights.assignment}%`"></p>
                        <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">Akumulasi tugas rutin dan penugasan modul.</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Akhir</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white" x-text="activeCourse.has_final_assignment ? `${activeCourse.weights.final}%` : 'Opsional'"></p>
                        <p class="mt-2 text-xs text-slate-500 dark:text-gray-400" x-text="activeCourse.has_final_assignment ? 'Digunakan dalam nilai akhir.' : 'Course ini tidak memakai tugas akhir.'"></p>
                    </div>
                    <div class="rounded-2xl bg-blue-600 p-4 text-white shadow-lg shadow-blue-600/20">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-100">Pending Review</p>
                        <p class="mt-2 text-3xl font-bold" x-text="coursePendingCount"></p>
                        <p class="mt-2 text-xs text-blue-100">Mahasiswa yang perlu dicek sebelum publish.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Rumus Akhir</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Komposisi Nilai</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-gray-700 dark:text-gray-300">Frontend Preview</span>
                </div>

                <div class="mt-4 space-y-3">
                    <template x-for="item in compositionCards" :key="item.key">
                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="item.label"></p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="item.helper"></p>
                                </div>
                                <span class="text-lg font-bold text-slate-900 dark:text-white" x-text="item.value"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Contoh Formula</p>
                    <p class="mt-2 text-sm font-medium text-slate-900 dark:text-white">
                        Nilai akhir = <span x-text="`${activeCourse.weights.pretest}% x pretest`"></span>
                        + <span x-text="`${activeCourse.weights.assignment}% x tugas`"></span>
                        + <span x-show="activeCourse.has_final_assignment" x-text="`${activeCourse.weights.final}% x tugas akhir`"></span>
                        <span x-show="!activeCourse.has_final_assignment">tanpa komponen tugas akhir.</span>
                    </p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">
                        Preview frontend selalu membagi dengan total bobot aktif <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="`${activeWeightTotal}%`"></span> supaya hasil sementara tetap konsisten.
                    </p>
                    <p
                        x-show="activeWeightTotal !== 100"
                        class="mt-2 text-xs font-medium text-amber-600 dark:text-amber-300"
                    >
                        Total bobot belum 100 persen. Nilai akhir di preview dinormalisasi otomatis sampai dosen merapikan komposisinya.
                    </p>
                </div>

                <div class="mt-5 rounded-3xl border border-slate-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Atur Bobot</p>
                            <h3 class="mt-2 text-base font-semibold text-slate-900 dark:text-white">Kontrol Persentase Nilai</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                                Dosen bisa tentukan komposisi pretest, tugas modul, dan tugas akhir sesuai format course.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-3 py-2 dark:border-gray-700">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Tugas Akhir</p>
                                <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="activeCourse.has_final_assignment ? 'Aktif' : 'Nonaktif'"></p>
                            </div>
                            <button
                                type="button"
                                @click="toggleFinalAssignment()"
                                class="relative inline-flex h-7 w-12 items-center rounded-full transition"
                                :class="activeCourse.has_final_assignment ? 'bg-blue-600' : 'bg-slate-300 dark:bg-gray-600'"
                                :aria-pressed="activeCourse.has_final_assignment"
                            >
                                <span
                                    class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                                    :class="activeCourse.has_final_assignment ? 'translate-x-6' : 'translate-x-1'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <template x-for="item in editableWeights" :key="item.key">
                            <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="item.label"></p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="item.helper"></p>
                                    </div>
                                    <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="5"
                                            :disabled="item.disabled"
                                            :value="item.value"
                                            @input="updateWeight(item.key, $event.target.value)"
                                            class="w-16 border-0 bg-transparent p-0 text-right text-sm font-semibold text-slate-900 focus:ring-0 disabled:cursor-not-allowed disabled:text-slate-400 dark:text-white dark:disabled:text-gray-500"
                                        >
                                        <span class="text-sm font-semibold text-slate-500 dark:text-gray-400">%</span>
                                    </label>
                                </div>

                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    step="5"
                                    :disabled="item.disabled"
                                    :value="item.value"
                                    @input="updateWeight(item.key, $event.target.value)"
                                    class="mt-3 h-2 w-full cursor-pointer rounded-full accent-blue-600 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <template x-for="preset in weightPresets" :key="preset.label">
                            <button
                                type="button"
                                @click="applyPreset(preset)"
                                class="inline-flex items-center rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-300 dark:hover:text-white"
                                x-text="preset.label"
                            ></button>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Bobot Aktif</p>
                            <p class="mt-1 text-lg font-bold" :class="weightValidation.tone" x-text="`${activeWeightTotal}%`"></p>
                            <p class="mt-1 text-xs" :class="weightValidation.helperTone" x-text="weightValidation.helper"></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="normalizeWeights()"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
                            >
                                Normalkan ke 100%
                            </button>
                            <button
                                type="button"
                                @click="resetWeights()"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-300 dark:hover:text-white"
                            >
                                Reset Bobot
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Antrian Penilaian</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Daftar Mahasiswa & Nilai Akhir</h2>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:w-[720px]">
                    <label class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-gray-700 dark:bg-gray-900/40">
                        <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Cari Mahasiswa</span>
                        <input x-model="search" type="text" placeholder="Nama / Nomor Induk" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 dark:text-white">
                    </label>

                    <label class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-gray-700 dark:bg-gray-900/40">
                        <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Status</span>
                        <select x-model="statusFilter" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 focus:ring-0 dark:text-white">
                            <option value="all">Semua Status</option>
                            <option value="Perlu Review">Perlu Review</option>
                            <option value="Lengkap">Lengkap</option>
                            <option value="Revisi Tugas">Revisi Tugas</option>
                        </select>
                    </label>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-gray-700 dark:bg-gray-900/40">
                        <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Aksi Cepat</span>
                        <div class="flex gap-2">
                            <button type="button" class="inline-flex flex-1 items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                Publish Nilai
                            </button>
                            <button type="button" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-300 dark:hover:text-white">
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                    <thead class="bg-slate-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Mahasiswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Pretest</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Tugas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Tugas Akhir</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Nilai Akhir</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                        <template x-for="row in filteredRows" :key="`${row.nomor_induk}-${row.course_id}`">
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-900/30">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-sm font-bold text-white" x-text="initials(row.student)"></div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white" x-text="row.student"></p>
                                            <p class="text-xs text-slate-500 dark:text-gray-400" x-text="row.nomor_induk"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-slate-800 dark:text-gray-100" x-text="row.cohort"></p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="row.last_update"></p>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.pretest)"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.assignment)"></td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-900 dark:text-white" x-text="displayScore(activeCourse.has_final_assignment ? row.final_assignment : null, activeCourse.has_final_assignment ? '-' : 'N/A')"></td>
                                <td class="px-4 py-4 text-center">
                                    <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="finalScore(row)"></p>
                                    <p class="text-xs font-semibold" :class="gradeBand(row).tone" x-text="`Grade ${gradeBand(row).label}`"></p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold" :class="statusTone[row.status]" x-text="row.status"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 grid gap-4 lg:hidden">
                <template x-for="row in filteredRows" :key="`${row.nomor_induk}-${row.course_id}-mobile`">
                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-sm font-bold text-white" x-text="initials(row.student)"></div>
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white" x-text="row.student"></p>
                                    <p class="text-xs text-slate-500 dark:text-gray-400" x-text="`${row.nomor_induk} â€¢ ${row.cohort}`"></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold" :class="statusTone[row.status]" x-text="row.status"></span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl bg-white px-3 py-3 dark:bg-gray-800">
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Pretest</p>
                                <p class="mt-2 font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.pretest)"></p>
                            </div>
                            <div class="rounded-2xl bg-white px-3 py-3 dark:bg-gray-800">
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Tugas</p>
                                <p class="mt-2 font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.assignment)"></p>
                            </div>
                            <div class="rounded-2xl bg-white px-3 py-3 dark:bg-gray-800">
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Tugas Akhir</p>
                                <p class="mt-2 font-semibold text-slate-900 dark:text-white" x-text="displayScore(activeCourse.has_final_assignment ? row.final_assignment : null, activeCourse.has_final_assignment ? '-' : 'N/A')"></p>
                            </div>
                            <div class="rounded-2xl bg-slate-900 px-3 py-3 text-white dark:bg-blue-500/20">
                                <p class="text-xs uppercase tracking-[0.16em] text-slate-300 dark:text-blue-200">Nilai Akhir</p>
                                <p class="mt-2 text-lg font-bold" x-text="finalScore(row)"></p>
                                <p class="text-xs font-semibold text-blue-200" x-text="`Grade ${gradeBand(row).label}`"></p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-dashed border-slate-200 px-3 py-3 text-xs text-slate-500 dark:border-gray-700 dark:text-gray-400">
                            <span class="font-semibold text-slate-700 dark:text-gray-200">Catatan:</span>
                            <span x-text="row.note"></span>
                        </div>
                    </article>
                </template>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function gradeManager(config) {
                return {
                    courses: config.courses,
                    rows: config.rows,
                    statusTone: config.statusTone,
                    gradeBands: config.gradeBands,
                    selectedCourse: config.courses[0]?.id ?? null,
                    search: '',
                    statusFilter: 'all',
                    activeCourse: config.courses[0] ?? { weights: { pretest: 0, assignment: 0, final: 0 }, has_final_assignment: false },
                    filteredRows: [],
                    coursePendingCount: 0,
                    compositionCards: [],
                    activeWeightTotal: 0,
                    editableWeights: [],
                    weightValidation: {
                        tone: 'text-slate-900 dark:text-white',
                        helperTone: 'text-slate-500 dark:text-gray-400',
                        helper: '',
                    },
                    weightPresets: [
                        { label: 'Balanced 30/35/35', weights: { pretest: 30, assignment: 35, final: 35 } },
                        { label: 'Tugas Dominan', weights: { pretest: 20, assignment: 50, final: 30 } },
                        { label: 'Final Dominan', weights: { pretest: 20, assignment: 30, final: 50 } },
                    ],

                    init() {
                        this.$watch('search', () => this.syncDerivedState());
                        this.$watch('statusFilter', () => this.syncDerivedState());
                        this.syncDerivedState();
                    },

                    setCourse(courseId) {
                        this.selectedCourse = courseId;
                        this.syncDerivedState();
                    },

                    syncDerivedState() {
                        this.activeCourse = this.courses.find((course) => course.id === this.selectedCourse) ?? this.courses[0];

                        this.filteredRows = this.rows.filter((row) => {
                            if (row.course_id !== this.selectedCourse) return false;

                            const query = this.search.trim().toLowerCase();
                            const matchesSearch = !query
                                || row.student.toLowerCase().includes(query)
                                || row.nomor_induk.toLowerCase().includes(query);

                            const matchesStatus = this.statusFilter === 'all' || row.status === this.statusFilter;

                            return matchesSearch && matchesStatus;
                        });

                        this.coursePendingCount = this.rows.filter((row) => row.course_id === this.selectedCourse && row.status !== 'Lengkap').length;

                        this.compositionCards = [
                            {
                                key: 'pretest',
                                label: 'Pretest',
                                value: `${this.activeCourse.weights.pretest}%`,
                                helper: 'Nilai awal untuk mengukur kesiapan mahasiswa.',
                            },
                            {
                                key: 'assignment',
                                label: 'Tugas Modul',
                                value: `${this.activeCourse.weights.assignment}%`,
                                helper: 'Bobot dari tugas mingguan atau penugasan modul.',
                            },
                            {
                                key: 'final',
                                label: 'Tugas Akhir',
                                value: this.activeCourse.has_final_assignment ? `${this.activeCourse.weights.final}%` : 'Tidak Dipakai',
                                helper: this.activeCourse.has_final_assignment
                                    ? 'Aktif sebagai komponen akhir kelulusan.'
                                    : 'Course ini menonaktifkan tugas akhir.',
                            },
                        ];

                        this.activeWeightTotal = this.getActiveWeightTotal();
                        this.editableWeights = [
                            {
                                key: 'pretest',
                                label: 'Pretest',
                                value: this.activeCourse.weights.pretest,
                                helper: 'Mengukur kesiapan awal sebelum mahasiswa masuk materi.',
                                disabled: false,
                            },
                            {
                                key: 'assignment',
                                label: 'Tugas Modul',
                                value: this.activeCourse.weights.assignment,
                                helper: 'Akumulasi tugas mingguan, proyek kecil, dan penugasan modul.',
                                disabled: false,
                            },
                            {
                                key: 'final',
                                label: 'Tugas Akhir',
                                value: this.activeCourse.weights.final,
                                helper: this.activeCourse.has_final_assignment
                                    ? 'Dipakai saat course mewajibkan tugas akhir.'
                                    : 'Aktifkan toggle tugas akhir untuk memberi bobot komponen ini.',
                                disabled: !this.activeCourse.has_final_assignment,
                            },
                        ];

                        this.weightValidation = this.getWeightValidation();
                    },

                    getActiveWeightTotal() {
                        const weights = this.activeCourse.weights;

                        return this.activeCourse.has_final_assignment
                            ? weights.pretest + weights.assignment + weights.final
                            : weights.pretest + weights.assignment;
                    },

                    getWeightValidation() {
                        if (this.activeWeightTotal === 100) {
                            return {
                                tone: 'text-emerald-600 dark:text-emerald-400',
                                helperTone: 'text-emerald-600 dark:text-emerald-300',
                                helper: 'Komposisi sudah valid. Nilai akhir bisa dihitung tanpa koreksi tambahan.',
                            };
                        }

                        if (this.activeWeightTotal > 100) {
                            return {
                                tone: 'text-rose-600 dark:text-rose-400',
                                helperTone: 'text-rose-600 dark:text-rose-300',
                                helper: 'Total bobot melebihi 100 persen. Rapikan sebelum publish nilai ke mahasiswa.',
                            };
                        }

                        return {
                            tone: 'text-amber-600 dark:text-amber-400',
                            helperTone: 'text-amber-600 dark:text-amber-300',
                            helper: 'Total bobot masih kurang dari 100 persen. Preview dinormalisasi sementara.',
                        };
                    },

                    updateWeight(key, value) {
                        if (key === 'final' && !this.activeCourse.has_final_assignment) {
                            return;
                        }

                        const parsed = Number.parseInt(value, 10);
                        const nextValue = Number.isNaN(parsed) ? 0 : Math.max(0, Math.min(100, parsed));

                        this.activeCourse.weights[key] = nextValue;
                        this.syncDerivedState();
                    },

                    toggleFinalAssignment() {
                        this.activeCourse.has_final_assignment = !this.activeCourse.has_final_assignment;

                        if (!this.activeCourse.has_final_assignment) {
                            this.activeCourse.weights.final = 0;
                        } else if (this.activeCourse.weights.final === 0) {
                            this.activeCourse.weights.final = 30;
                        }

                        this.normalizeWeights();
                    },

                    applyPreset(preset) {
                        this.activeCourse.weights.pretest = preset.weights.pretest;
                        this.activeCourse.weights.assignment = preset.weights.assignment;
                        this.activeCourse.weights.final = this.activeCourse.has_final_assignment ? preset.weights.final : 0;
                        this.syncDerivedState();
                    },

                    normalizeWeights() {
                        const defaults = this.activeCourse.has_final_assignment
                            ? { pretest: 25, assignment: 35, final: 40 }
                            : { pretest: 30, assignment: 70, final: 0 };
                        const activeKeys = this.activeCourse.has_final_assignment
                            ? ['pretest', 'assignment', 'final']
                            : ['pretest', 'assignment'];
                        const currentTotal = activeKeys.reduce((sum, key) => sum + this.activeCourse.weights[key], 0);

                        if (currentTotal === 0) {
                            activeKeys.forEach((key) => {
                                this.activeCourse.weights[key] = defaults[key];
                            });
                        } else {
                            let remaining = 100;

                            activeKeys.forEach((key, index) => {
                                if (index === activeKeys.length - 1) {
                                    this.activeCourse.weights[key] = remaining;
                                    return;
                                }

                                const normalized = Math.round((this.activeCourse.weights[key] / currentTotal) * 100);
                                this.activeCourse.weights[key] = normalized;
                                remaining -= normalized;
                            });
                        }

                        if (!this.activeCourse.has_final_assignment) {
                            this.activeCourse.weights.final = 0;
                        }

                        this.syncDerivedState();
                    },

                    resetWeights() {
                        this.activeCourse.weights = this.activeCourse.has_final_assignment
                            ? { pretest: 20, assignment: 35, final: 45 }
                            : { pretest: 30, assignment: 70, final: 0 };

                        this.syncDerivedState();
                    },

                    initials(name) {
                        return name
                            .split(' ')
                            .map((part) => part.charAt(0))
                            .join('')
                            .slice(0, 2)
                            .toUpperCase();
                    },

                    displayScore(value, fallback = '-') {
                        return Number.isFinite(value) ? `${value}` : fallback;
                    },

                    finalScore(row) {
                        const weights = this.activeCourse.weights;
                        const pretest = Number.isFinite(row.pretest) ? row.pretest : 0;
                        const assignment = Number.isFinite(row.assignment) ? row.assignment : 0;
                        const activeWeightTotal = this.getActiveWeightTotal();

                        if (activeWeightTotal === 0) {
                            return '0.0';
                        }

                        if (this.activeCourse.has_final_assignment) {
                            if (!Number.isFinite(row.final_assignment)) return 'Pending';
                            const result = (pretest * weights.pretest) + (assignment * weights.assignment) + (row.final_assignment * weights.final);
                            return (result / activeWeightTotal).toFixed(1);
                        }

                        const result = (pretest * weights.pretest) + (assignment * weights.assignment);
                        return (result / activeWeightTotal).toFixed(1);
                    },

                    gradeBand(row) {
                        const score = parseFloat(this.finalScore(row));
                        if (Number.isNaN(score)) {
                            return { label: 'P', tone: 'text-amber-600 dark:text-amber-400' };
                        }

                        return this.gradeBands.find((band) => score >= band.min) ?? this.gradeBands[this.gradeBands.length - 1];
                    },
                };
            }
        </script>
    @endpush
</x-layouts.dosen>
