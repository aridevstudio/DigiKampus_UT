@php
    $gradeCourses = $gradeCourses ?? [];
    $gradeRows = $gradeRows ?? [];
    $gradeApi = $gradeApi ?? [];

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

@push('styles')
    <style>
        .grade-manager-shell :is(button, input, select, textarea):focus-visible {
            outline: 2px solid rgb(59 130 246 / 0.75);
            outline-offset: 1px;
        }

        .grade-manager-shell .grade-soft-grid {
            background-image:
                linear-gradient(to right, rgba(14, 165, 233, 0.06) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(14, 165, 233, 0.06) 1px, transparent 1px);
            background-size: 22px 22px;
        }
    </style>
@endpush

<x-layouts.dosen title="Mengelola Nilai" active="nilai">
    <div
        x-data="gradeManager({
            courses: @js($gradeCourses),
            rows: @js($gradeRows),
            statusTone: @js($statusTone),
            gradeBands: @js($gradeBands),
            api: @js($gradeApi),
        })"
        x-init="init()"
        class="grade-manager-shell space-y-7 pb-6"
    >
        @if(request('source') === 'bootcamp')
            <div class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-800 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-200">
                <p class="font-semibold">Mode Nilai & Review dari Bootcamp</p>
                <p class="mt-1">Konteks batch: {{ request('batch', '-') }}.</p>
            </div>
        @endif

        <section class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
            <div class="grid gap-6 bg-[radial-gradient(circle_at_top_right,_rgba(59,130,246,0.16),_transparent_32%),linear-gradient(135deg,#ffffff_0%,#f8fbff_58%,#eef4ff_100%)] px-6 py-6 dark:bg-[linear-gradient(135deg,rgba(15,23,42,0.96),rgba(15,23,42,0.84))] lg:grid-cols-[1.45fr_0.9fr]">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                        Grade Center
                    </span>
                    <h1 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Mengelola Nilai Mahasiswa</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-gray-300 sm:text-base">
                        Workflow penilaian diringkas dalam satu halaman: pilih course aktif, validasi komposisi bobot, review mahasiswa pending, lalu publish ketika checklist selesai.
                    </p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Langkah 1</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Pilih Course</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-gray-400">Fokuskan workspace ke kelas atau webinar yang sedang dinilai.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Langkah 2</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Kunci Bobot</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-gray-400">Pastikan total bobot aktif tepat 100 persen sebelum publish.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Langkah 3</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Review dan Publish</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-gray-400">Cek mahasiswa pending, simpan draft, lalu publish ketika semua siap.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Mahasiswa Aktif</p>
                            <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white" x-text="activeCourse.students"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Baris Ditampilkan</p>
                            <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white" x-text="filteredRows.length"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Pending Review</p>
                            <p class="mt-2 text-xl font-bold text-amber-600 dark:text-amber-300" x-text="coursePendingCount"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/60">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Bobot Aktif</p>
                            <p class="mt-2 text-xl font-bold" :class="activeWeightTotal === 100 ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300'" x-text="`${activeWeightTotal}%`"></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-blue-100 bg-white/95 p-5 shadow-sm dark:border-blue-500/10 dark:bg-gray-900/70">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">Status Publish</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white" x-text="publishState.title"></h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-gray-400" x-text="publishState.helper"></p>
                        </div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="publishState.badgeClass" x-text="publishState.badge"></span>
                    </div>

                    <div class="mt-5 space-y-3">
                        <template x-for="item in publishState.checklist" :key="item.label">
                            <div class="flex items-start gap-3 rounded-2xl border px-3 py-3" :class="item.done ? 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-slate-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-800/80'">
                                <div class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-bold" :class="item.done ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600 dark:bg-gray-700 dark:text-gray-300'">
                                    <span x-text="item.done ? 'OK' : '!'"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="item.label"></p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="item.helper"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="saveDraft()"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-200 dark:hover:text-white"
                        >
                            Simpan Draft
                        </button>
                        <button
                            type="button"
                            @click="publishGrades()"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="!canPublish || isPublished"
                            x-text="isPublished ? 'Sudah Dipublish' : 'Publish Nilai'"
                        >
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.55fr)_390px]">
            <div class="space-y-6">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Course Context</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white" x-text="selectedCourse ? activeCourse.name : 'Pilih course untuk mulai review nilai'"></h2>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-gray-400">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold dark:bg-gray-700/70" x-text="activeCourse.code"></span>
                                <template x-if="selectedCourse">
                                    <span class="contents">
                                        <span x-text="activeCourse.period"></span>
                                        <span class="hidden sm:inline">/</span>
                                        <span x-text="`${activeCourse.students} mahasiswa`"></span>
                                    </span>
                                </template>
                                <template x-if="!selectedCourse">
                                    <span>Workspace akan aktif setelah course dipilih.</span>
                                </template>
                            </div>
                        </div>

                        <div class="w-full max-w-[460px]">
                            <label class="block rounded-[24px] border border-cyan-100 bg-white px-4 py-3 shadow-sm dark:border-cyan-500/10 dark:bg-gray-900/60">
                                <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Pilih Course</span>
                                <select
                                    x-model="selectedCourse"
                                    @change="setCourse($event.target.value)"
                                    class="w-full border-0 bg-transparent p-0 text-sm font-semibold text-slate-900 focus:ring-0 dark:text-white"
                                >
                                    <option value="">-- Pilih course yang akan direview --</option>
                                    <template x-for="course in courses" :key="`select-${course.id}`">
                                        <option :value="course.id" x-text="`${course.code} - ${course.name}`"></option>
                                    </template>
                                </select>
                            </label>
                            <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">
                                Pilih dulu satu course, lalu workspace review akan otomatis menyesuaikan.
                            </p>
                        </div>
                    </div>

                    <div x-show="!selectedCourse" class="mt-5">
                        <div class="grade-soft-grid overflow-hidden rounded-[24px] border border-cyan-100 bg-gradient-to-br from-cyan-50 via-white to-emerald-50 px-5 py-5 shadow-sm dark:border-cyan-500/10 dark:from-cyan-500/10 dark:via-gray-900 dark:to-emerald-500/10">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div class="max-w-2xl">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-600 text-white shadow-lg shadow-cyan-500/20">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9A2.25 2.25 0 0118.75 18.75H5.25A2.25 2.25 0 013 16.5v-9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 9.75h9m-9 4.5h5.25" />
                                        </svg>
                                    </div>
                                    <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">Mulai dari satu course aktif</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-gray-300">
                                        Pilih course pada dropdown untuk membuka daftar mahasiswa, komposisi bobot, dan panel review. Selama belum dipilih, halaman ini sengaja tetap bersih agar dosen tidak terdistraksi oleh data kosong.
                                    </p>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[360px]">
                                    <div class="rounded-2xl border border-white/70 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Course Tersedia</p>
                                        <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white" x-text="courses.length"></p>
                                    </div>
                                    <div class="rounded-2xl border border-white/70 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Draft Aktif</p>
                                        <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white" x-text="Object.values(publishedCourses).filter(value => !value).length"></p>
                                    </div>
                                    <div class="rounded-2xl border border-white/70 bg-white/90 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Sudah Publish</p>
                                        <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white" x-text="Object.values(publishedCourses).filter(Boolean).length"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedCourse" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <template x-for="item in summaryStats" :key="item.label">
                            <div class="rounded-2xl border px-4 py-4" :class="item.cardClass">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" :class="item.labelClass" x-text="item.label"></p>
                                <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white" x-text="item.value"></p>
                                <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-gray-400" x-text="item.helper"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Review Workspace</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Daftar Mahasiswa dan Nilai Akhir</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Pilih mahasiswa untuk melihat detail skornya. Baris pending akan terlihat lebih menonjol.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 xl:w-[520px]">
                            <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                                <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Cari Mahasiswa</span>
                                <input x-model="search" type="text" placeholder="Nama atau nomor induk" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 dark:text-white">
                            </label>

                            <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                                <span class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Status</span>
                                <select x-model="statusFilter" class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 focus:ring-0 dark:text-white">
                                    <option value="all">Semua Status</option>
                                    <option value="Perlu Review">Perlu Review</option>
                                    <option value="Lengkap">Lengkap</option>
                                    <option value="Revisi Tugas">Revisi Tugas</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white"><span x-text="filteredRows.length"></span> mahasiswa tampil di daftar review</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Klik satu baris untuk fokus review tanpa pindah halaman.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="exportGrades()"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-200 dark:hover:text-white"
                            >
                                Export Preview
                            </button>
                            <button
                                type="button"
                                @click="saveDraft()"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
                            >
                                Simpan Draft
                            </button>
                        </div>
                    </div>

                    <div x-show="filteredRows.length > 0" class="mt-5 hidden overflow-x-auto xl:block">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                            <thead class="bg-slate-50 dark:bg-gray-900/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Mahasiswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Komponen</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Nilai Akhir</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                                <template x-for="row in filteredRows" :key="getRowKey(row)">
                                    <tr
                                        class="cursor-pointer transition"
                                        :class="selectedStudentKey === getRowKey(row) ? 'bg-blue-50/80 dark:bg-blue-500/10' : 'hover:bg-slate-50/80 dark:hover:bg-gray-900/30'"
                                        @click="selectStudent(row)"
                                    >
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-sm font-bold text-white" x-text="initials(row.student)"></div>
                                                <div>
                                                    <p class="font-semibold text-slate-900 dark:text-white" x-text="row.student"></p>
                                                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="`${row.nomor_induk} / ${row.cohort}`"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="grid gap-2 text-xs text-slate-500 dark:text-gray-400 sm:grid-cols-3">
                                                <div>
                                                    <p class="uppercase tracking-[0.16em] text-slate-400">Pretest</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.pretest)"></p>
                                                </div>
                                                <div>
                                                    <p class="uppercase tracking-[0.16em] text-slate-400">Tugas</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="displayScore(row.assignment)"></p>
                                                </div>
                                                <div>
                                                    <p class="uppercase tracking-[0.16em] text-slate-400">Tugas Akhir</p>
                                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="displayScore(activeCourse.has_final_assignment ? row.final_assignment : null, activeCourse.has_final_assignment ? '-' : 'N/A')"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="finalScore(row)"></p>
                                            <p class="text-xs font-semibold" :class="gradeBand(row).tone" x-text="`Grade ${gradeBand(row).label}`"></p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold" :class="statusTone[row.status]" x-text="row.status"></span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    type="button"
                                                    @click.stop="selectStudent(row)"
                                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-200 dark:hover:text-white"
                                                >
                                                    Buka Detail
                                                </button>
                                                <button
                                                    type="button"
                                                    @click.stop="markReviewComplete(row)"
                                                    class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold transition"
                                                    :class="isReviewActionable(row)
                                                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                                        : 'border border-slate-200 text-slate-400 dark:border-gray-700 dark:text-gray-500'"
                                                    :disabled="!isReviewActionable(row)"
                                                >
                                                    <span x-text="isReviewActionable(row) ? 'Selesai Review' : 'Sudah Lengkap'"></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div x-show="filteredRows.length > 0" class="mt-5 grid gap-4 xl:hidden">
                        <template x-for="row in filteredRows" :key="`${getRowKey(row)}-mobile`">
                            <article
                                class="rounded-3xl border p-4 transition"
                                :class="selectedStudentKey === getRowKey(row) ? 'border-blue-300 bg-blue-50/70 dark:border-blue-500/30 dark:bg-blue-500/10' : 'border-slate-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-900/40'"
                                @click="selectStudent(row)"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-sm font-bold text-white" x-text="initials(row.student)"></div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white" x-text="row.student"></p>
                                            <p class="text-xs text-slate-500 dark:text-gray-400" x-text="`${row.nomor_induk} / ${row.cohort}`"></p>
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

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        @click.stop="selectStudent(row)"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-200 dark:hover:text-white"
                                    >
                                        Buka Detail
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="markReviewComplete(row)"
                                        class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-semibold transition"
                                        :class="isReviewActionable(row)
                                            ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                            : 'border border-slate-200 text-slate-400 dark:border-gray-700 dark:text-gray-500'"
                                        :disabled="!isReviewActionable(row)"
                                    >
                                        <span x-text="isReviewActionable(row) ? 'Selesai Review' : 'Sudah Lengkap'"></span>
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>

                    <div
                        x-show="filteredRows.length === 0"
                        class="mt-5 overflow-hidden rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center dark:border-gray-700 dark:bg-gray-900/40"
                    >
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-3M4.5 19.5h15" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-slate-900 dark:text-white" x-text="selectedCourse ? 'Tidak ada data mahasiswa pada filter ini' : 'Pilih course untuk menampilkan daftar review'"></p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="selectedCourse ? 'Ubah kata kunci pencarian atau pilih status lain untuk menampilkan data.' : 'Setelah course dipilih, tabel review mahasiswa akan muncul di area ini.'"></p>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Mahasiswa Terpilih</p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white" x-text="selectedRow ? selectedRow.student : 'Belum ada mahasiswa dipilih'"></h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400" x-text="selectedRow ? `${selectedRow.nomor_induk} / ${selectedRow.cohort}` : 'Klik salah satu baris mahasiswa untuk fokus review.'"></p>
                        </div>
                        <span x-show="selectedRow" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="selectedRow ? statusTone[selectedRow.status] : 'hidden'" x-text="selectedRow ? selectedRow.status : ''"></span>
                    </div>

                    <template x-if="!selectedRow">
                        <div class="mt-5 overflow-hidden rounded-[24px] border border-dashed border-slate-300 bg-gradient-to-br from-slate-50 via-white to-cyan-50 px-5 py-8 dark:border-gray-700 dark:from-gray-900/60 dark:via-gray-900 dark:to-cyan-500/5">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-cyan-600 shadow-sm dark:bg-gray-800 dark:text-cyan-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m10 0H7" />
                                </svg>
                            </div>
                            <p class="mt-4 text-center text-sm font-semibold text-slate-900 dark:text-white">Belum ada mahasiswa dipilih</p>
                            <p class="mt-1 text-center text-xs text-slate-500 dark:text-gray-400">Pilih satu mahasiswa dari tabel review untuk membuka skor, catatan dosen, dan input manual dalam satu panel penuh.</p>
                        </div>
                    </template>

                    <template x-if="selectedRow">
                        <div class="mt-5">
                            <div class="grid gap-5 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.15fr)]">
                                <div class="space-y-4">
                                    <div class="overflow-hidden rounded-[24px] border border-cyan-100 bg-gradient-to-br from-cyan-50 via-white to-blue-50 px-5 py-5 shadow-sm dark:border-cyan-500/10 dark:from-cyan-500/10 dark:via-gray-900 dark:to-blue-500/10">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-600 text-lg font-bold text-white shadow-lg shadow-cyan-500/20" x-text="initials(selectedRow.student)"></div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700/70 dark:text-cyan-300/80">Focus Review</p>
                                                    <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white" x-text="selectedRow.student"></p>
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400" x-text="`${selectedRow.nomor_induk} / ${selectedRow.cohort}`"></p>
                                                </div>
                                            </div>
                                            <div class="rounded-2xl bg-slate-900 px-4 py-3 text-white shadow-lg shadow-slate-900/10 dark:bg-white dark:text-slate-900">
                                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-300 dark:text-slate-500">Nilai Akhir</p>
                                                <p class="mt-2 text-3xl font-bold" x-text="finalScore(selectedRow)"></p>
                                                <p class="mt-1 text-xs font-semibold text-cyan-200 dark:text-cyan-600" x-text="`Grade ${gradeBand(selectedRow).label}`"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-3">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Pretest</p>
                                            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white" x-text="displayScore(selectedRow.pretest)"></p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Modul</p>
                                            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white" x-text="displayScore(selectedRow.assignment)"></p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Akhir</p>
                                            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white" x-text="displayScore(activeCourse.has_final_assignment ? selectedRow.final_assignment : null, activeCourse.has_final_assignment ? '-' : 'N/A')"></p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Catatan Review</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-gray-300" x-text="selectedRow.note"></p>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Input Manual</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">Masukkan nilai langsung</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Ketik nilai 0 sampai 100, lalu simpan ke preview tabel.</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-gray-700 dark:text-gray-300">
                                        Frontend Draft
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-3">
                                    <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/80">
                                        <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Pretest</span>
                                        <input
                                            x-model="scoreForm.pretest"
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="1"
                                            class="mt-2 w-full border-0 bg-transparent p-0 text-lg font-semibold text-slate-900 focus:ring-0 dark:text-white"
                                            placeholder="0 - 100"
                                        >
                                    </label>
                                    <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/80">
                                        <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Modul</span>
                                        <input
                                            x-model="scoreForm.assignment"
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="1"
                                            class="mt-2 w-full border-0 bg-transparent p-0 text-lg font-semibold text-slate-900 focus:ring-0 dark:text-white"
                                            placeholder="0 - 100"
                                        >
                                    </label>
                                    <label
                                        class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/80"
                                        :class="!activeCourse.has_final_assignment ? 'opacity-60' : ''"
                                    >
                                        <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tugas Akhir</span>
                                        <input
                                            x-model="scoreForm.final_assignment"
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="1"
                                            :disabled="!activeCourse.has_final_assignment"
                                            class="mt-2 w-full border-0 bg-transparent p-0 text-lg font-semibold text-slate-900 focus:ring-0 disabled:cursor-not-allowed disabled:text-slate-400 dark:text-white dark:disabled:text-gray-500"
                                            :placeholder="activeCourse.has_final_assignment ? '0 - 100' : 'Tidak dipakai'"
                                        >
                                    </label>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        @click="saveManualScores()"
                                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        Simpan Nilai Manual
                                    </button>
                                    <button
                                        type="button"
                                        @click="resetManualScores()"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-200 dark:hover:text-white"
                                    >
                                        Reset Input
                                    </button>
                                </div>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    @click="markReviewComplete(selectedRow)"
                                    class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition"
                                    :class="selectedRow && isReviewActionable(selectedRow)
                                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                        : 'border border-slate-200 text-slate-400 dark:border-gray-700 dark:text-gray-500'"
                                    :disabled="!selectedRow || !isReviewActionable(selectedRow)"
                                >
                                    <span x-text="selectedRow && isReviewActionable(selectedRow) ? 'Selesai Review Mahasiswa Ini' : 'Mahasiswa Sudah Lengkap'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <aside class="space-y-6 xl:sticky xl:top-24 xl:self-start">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Rumus Akhir</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Komposisi Nilai</h2>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-gray-700 dark:text-gray-300">Frontend Preview</span>
                    </div>

                    <div x-show="!selectedCourse" class="mt-4 rounded-[24px] border border-dashed border-slate-200 bg-slate-50 px-4 py-6 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Komposisi nilai akan muncul setelah course dipilih</p>
                        <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-gray-400">Panel ini akan menampilkan komponen aktif, formula akhir, dan distribusi bobot sesuai course yang sedang direview.</p>
                    </div>

                    <div x-show="selectedCourse" class="mt-4 space-y-3">
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

                    <div x-show="selectedCourse" class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Formula Aktif</p>
                        <p class="mt-2 text-sm font-medium text-slate-900 dark:text-white">
                            Nilai akhir = <span x-text="`${activeCourse.weights.pretest}% x pretest`"></span>
                            + <span x-text="`${activeCourse.weights.assignment}% x tugas`"></span>
                            + <span x-show="activeCourse.has_final_assignment" x-text="`${activeCourse.weights.final}% x tugas akhir`"></span>
                            <span x-show="!activeCourse.has_final_assignment">tanpa komponen tugas akhir.</span>
                        </p>
                        <p class="mt-2 text-xs text-slate-500 dark:text-gray-400">
                            Preview memakai total bobot aktif <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="`${activeWeightTotal}%`"></span> agar hasil tetap konsisten saat bobot sedang disesuaikan.
                        </p>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/50 dark:bg-gray-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Atur Bobot</p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Kontrol Persentase Nilai</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">
                                Atur bobot sekali lalu review hasil akhirnya di tabel dan kartu mahasiswa.
                            </p>
                        </div>

                        <div x-show="selectedCourse" class="flex items-center gap-3 rounded-2xl border border-slate-200 px-3 py-2 dark:border-gray-700">
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

                    <div x-show="!selectedCourse" class="mt-4 rounded-[24px] border border-dashed border-slate-200 bg-gradient-to-br from-slate-50 via-white to-cyan-50 px-4 py-6 dark:border-gray-700 dark:from-gray-900/60 dark:via-gray-900 dark:to-cyan-500/5">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Kontrol bobot belum aktif</p>
                        <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-gray-400">Pilih course dulu agar slider bobot, preset distribusi, dan normalisasi 100% bekerja pada konteks kelas yang benar.</p>
                    </div>

                    <div x-show="selectedCourse" class="mt-4 space-y-3">
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

                    <div x-show="selectedCourse" class="mt-4 flex flex-wrap items-center gap-2">
                        <template x-for="preset in weightPresets" :key="preset.label">
                            <button
                                type="button"
                                @click="applyPreset(preset)"
                                class="inline-flex items-center rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-300 dark:hover:text-white"
                                x-text="preset.label"
                            ></button>
                        </template>
                    </div>

                    <div x-show="selectedCourse" class="mt-4 rounded-2xl bg-slate-50 px-4 py-4 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Total Bobot Aktif</p>
                        <p class="mt-2 text-lg font-bold" :class="weightValidation.tone" x-text="`${activeWeightTotal}%`"></p>
                        <p class="mt-1 text-xs leading-5" :class="weightValidation.helperTone" x-text="weightValidation.helper"></p>

                        <div class="mt-4 flex flex-wrap gap-2">
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
            </aside>
        </section>

    </div>

    @push('scripts')
        <script>
            function gradeManager(config) {
                return {
                    courses: config.courses || [],
                    rows: config.rows || [],
                    statusTone: config.statusTone || {},
                    gradeBands: config.gradeBands || [],
                    api: config.api || {},
                    selectedCourse: null,
                    search: '',
                    statusFilter: 'all',
                    activeCourse: {
                        id: null,
                        name: 'Pilih course terlebih dahulu',
                        code: '-',
                        period: '-',
                        students: 0,
                        weights: { pretest: 0, assignment: 0, final: 0 },
                        has_final_assignment: false,
                        pending_reviews: 0,
                        is_published: false,
                    },
                    selectedStudentKey: null,
                    selectedRow: null,
                    filteredRows: [],
                    coursePendingCount: 0,
                    summaryStats: [],
                    compositionCards: [],
                    activeWeightTotal: 0,
                    editableWeights: [],
                    scoreForm: {
                        pretest: '',
                        assignment: '',
                        final_assignment: '',
                    },
                    publishedCourses: {},
                    isPublished: false,
                    publishState: {
                        title: 'Draft Nilai Semester',
                        helper: '',
                        badge: 'Draft',
                        badgeClass: 'bg-slate-100 text-slate-700 dark:bg-gray-700 dark:text-gray-200',
                        checklist: [],
                    },
                    canPublish: false,
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
                        this.publishedCourses = this.courses.reduce((acc, course) => {
                            acc[course.id] = !!course.is_published;
                            return acc;
                        }, {});
                        this.$watch('search', () => this.syncDerivedState());
                        this.$watch('statusFilter', () => this.syncDerivedState());
                        this.syncDerivedState();
                    },

                    setCourse(courseId) {
                        this.selectedCourse = courseId === '' ? null : courseId;
                        this.syncDerivedState();
                    },

                    syncDerivedState() {
                        this.courses.forEach((course) => {
                            course.pending_reviews = this.rows.filter((row) => String(row.course_id) === String(course.id) && row.status !== 'Lengkap').length;
                        });

                        const selectedCourse = this.selectedCourse
                            ? this.courses.find((course) => String(course.id) === String(this.selectedCourse))
                            : null;

                        this.activeCourse = selectedCourse
                            ?? {
                                id: null,
                                name: 'Pilih course terlebih dahulu',
                                code: '-',
                                period: '-',
                                students: 0,
                                weights: { pretest: 0, assignment: 0, final: 0 },
                                has_final_assignment: false,
                                pending_reviews: 0,
                                is_published: false,
                            };
                        const courseRows = selectedCourse
                            ? this.rows.filter((row) => String(row.course_id) === String(this.selectedCourse))
                            : [];

                        this.filteredRows = courseRows.filter((row) => {
                            const query = this.search.trim().toLowerCase();
                            const matchesSearch = !query
                                || String(row.student ?? '').toLowerCase().includes(query)
                                || String(row.nomor_induk ?? '').toLowerCase().includes(query);

                            const matchesStatus = this.statusFilter === 'all' || row.status === this.statusFilter;

                            return matchesSearch && matchesStatus;
                        });

                        this.coursePendingCount = courseRows.filter((row) => row.status !== 'Lengkap').length;

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
                        const readyCount = courseRows.filter((row) => row.status === 'Lengkap' && this.finalScore(row) !== 'Pending').length;
                        const scoredRows = courseRows
                            .map((row) => parseFloat(this.finalScore(row)))
                            .filter((score) => !Number.isNaN(score));
                        const averageScore = scoredRows.length
                            ? (scoredRows.reduce((sum, score) => sum + score, 0) / scoredRows.length).toFixed(1)
                            : '0.0';

                        this.summaryStats = [
                            {
                                label: 'Siap Publish',
                                value: `${readyCount}`,
                                helper: 'Mahasiswa yang komponennya sudah lengkap.',
                                cardClass: 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-500/20 dark:bg-emerald-500/10',
                                labelClass: 'text-emerald-600 dark:text-emerald-300',
                            },
                            {
                                label: 'Perlu Review',
                                value: `${this.coursePendingCount}`,
                                helper: 'Baris yang masih perlu dicek sebelum final.',
                                cardClass: 'border-amber-200 bg-amber-50/80 dark:border-amber-500/20 dark:bg-amber-500/10',
                                labelClass: 'text-amber-600 dark:text-amber-300',
                            },
                            {
                                label: 'Rata-rata Akhir',
                                value: averageScore,
                                helper: 'Rerata preview berdasarkan bobot aktif sekarang.',
                                cardClass: 'border-blue-200 bg-blue-50/80 dark:border-blue-500/20 dark:bg-blue-500/10',
                                labelClass: 'text-blue-600 dark:text-blue-300',
                            },
                            {
                                label: 'Bobot Aktif',
                                value: `${this.activeWeightTotal}%`,
                                helper: 'Target ideal 100 persen sebelum publish.',
                                cardClass: 'border-slate-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-900/40',
                                labelClass: 'text-slate-500 dark:text-gray-300',
                            },
                        ];

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
                        this.canPublish = !!selectedCourse && this.activeWeightTotal === 100 && this.coursePendingCount === 0 && readyCount > 0;
                        if (this.publishedCourses[this.selectedCourse] && !this.canPublish) {
                            this.publishedCourses[this.selectedCourse] = false;
                        }
                        this.isPublished = !!this.publishedCourses[this.selectedCourse];
                        this.publishState = {
                            title: this.isPublished
                                ? 'Sudah Dipublish ke Mahasiswa'
                                : this.canPublish
                                    ? 'Siap Publish ke Mahasiswa'
                                    : 'Draft Nilai Semester',
                            helper: this.isPublished
                                ? 'Nilai untuk course ini sudah dipublish dari backend.'
                                : this.canPublish
                                    ? 'Semua checklist utama terpenuhi. Draft bisa dipublish dari halaman ini.'
                                    : 'Masih ada item yang harus dibenahi sebelum nilai dikirim ke mahasiswa.',
                            badge: this.isPublished ? 'Published' : (this.canPublish ? 'Siap' : 'Draft'),
                            badgeClass: this.isPublished
                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'
                                : this.canPublish
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                    : 'bg-slate-100 text-slate-700 dark:bg-gray-700 dark:text-gray-200',
                            checklist: [
                                {
                                    label: 'Bobot aktif sudah 100%',
                                    helper: `Total aktif saat ini ${this.activeWeightTotal}%.`,
                                    done: this.activeWeightTotal === 100,
                                },
                                {
                                    label: 'Tidak ada mahasiswa pending review',
                                    helper: this.coursePendingCount === 0
                                        ? 'Semua mahasiswa sudah berstatus lengkap.'
                                        : `${this.coursePendingCount} mahasiswa masih perlu dicek.`,
                                    done: this.coursePendingCount === 0,
                                },
                                {
                                    label: 'Sudah ada nilai akhir yang siap dipublish',
                                    helper: readyCount > 0
                                        ? `${readyCount} mahasiswa siap masuk draft publish.`
                                        : 'Belum ada mahasiswa lengkap untuk dipublish.',
                                    done: readyCount > 0,
                                },
                                {
                                    label: 'Status publish backend',
                                    helper: this.isPublished
                                        ? 'Data publish sudah tersimpan di backend.'
                                        : 'Belum dipublish ke mahasiswa.',
                                    done: this.isPublished,
                                },
                            ],
                        };

                        const availableRows = this.filteredRows.length ? this.filteredRows : courseRows;
                        const nextSelected = availableRows.find((row) => this.getRowKey(row) === this.selectedStudentKey) ?? availableRows[0] ?? null;
                        this.selectedStudentKey = nextSelected ? this.getRowKey(nextSelected) : null;
                        this.selectedRow = nextSelected;
                        this.hydrateScoreForm(nextSelected);
                    },

                    buildApiUrl(template, replacements) {
                        if (!template) return '';

                        let url = template;
                        Object.entries(replacements || {}).forEach(([key, value]) => {
                            url = url.replace(`__${key.toUpperCase()}__`, encodeURIComponent(String(value ?? '')));
                        });

                        return url;
                    },

                    async postJson(url, payload = {}) {
                        if (!url) {
                            return { success: false, message: 'URL backend tidak ditemukan.' };
                        }

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '',
                                },
                                body: JSON.stringify(payload),
                            });

                            const data = await response.json().catch(() => ({}));
                            if (!response.ok || !data?.success) {
                                return {
                                    success: false,
                                    message: data?.message || 'Terjadi kesalahan pada server.',
                                    errors: data?.errors || null,
                                };
                            }

                            return data;
                        } catch (error) {
                            return {
                                success: false,
                                message: 'Gagal terhubung ke server.',
                            };
                        }
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

                    getRowKey(row) {
                        return `${row.student_id || row.nomor_induk}-${row.course_id}`;
                    },

                    selectStudent(row) {
                        this.selectedStudentKey = this.getRowKey(row);
                        this.selectedRow = row;
                        this.hydrateScoreForm(row);
                    },

                    hydrateScoreForm(row) {
                        this.scoreForm = {
                            pretest: Number.isFinite(row?.pretest) ? String(row.pretest) : '',
                            assignment: Number.isFinite(row?.assignment) ? String(row.assignment) : '',
                            final_assignment: Number.isFinite(row?.final_assignment) ? String(row.final_assignment) : '',
                        };
                    },

                    parseScoreInput(value) {
                        if (value === '' || value === null || typeof value === 'undefined') {
                            return null;
                        }

                        const parsed = Number.parseFloat(value);

                        if (Number.isNaN(parsed)) {
                            return null;
                        }

                        return Math.max(0, Math.min(100, parsed));
                    },

                    async saveManualScores() {
                        if (!this.selectedRow) {
                            return this.notify(
                                'Pilih mahasiswa dulu sebelum memasukkan nilai manual.',
                                'warning',
                                'Mahasiswa Belum Dipilih'
                            );
                        }

                        const target = this.rows.find((item) => this.getRowKey(item) === this.getRowKey(this.selectedRow));
                        const studentId = target?.student_id ?? this.selectedRow?.student_id;

                        if (!target || !studentId || !this.selectedCourse) {
                            return this.notify(
                                'Data mahasiswa tidak ditemukan di workspace nilai.',
                                'error',
                                'Simpan Gagal'
                            );
                        }

                        const parsedPretest = this.parseScoreInput(this.scoreForm.pretest);
                        const parsedAssignment = this.parseScoreInput(this.scoreForm.assignment);
                        const parsedFinal = this.activeCourse.has_final_assignment
                            ? this.parseScoreInput(this.scoreForm.final_assignment)
                            : null;

                        if (parsedPretest === null || parsedAssignment === null || (this.activeCourse.has_final_assignment && parsedFinal === null)) {
                            return this.notify(
                                'Lengkapi semua komponen nilai aktif dengan angka 0 sampai 100.',
                                'warning',
                                'Input Nilai Belum Lengkap'
                            );
                        }

                        const saveUrl = this.buildApiUrl(this.api.score, {
                            course: this.selectedCourse,
                            student: studentId,
                        });

                        const saveResponse = await this.postJson(saveUrl, {
                            pretest_score: parsedPretest,
                            assignment_score: parsedAssignment,
                            final_assignment_score: this.activeCourse.has_final_assignment ? parsedFinal : null,
                            status: 'lengkap',
                        });

                        if (!saveResponse.success) {
                            return this.notify(
                                saveResponse.message || 'Gagal menyimpan nilai ke backend.',
                                'error',
                                'Simpan Gagal'
                            );
                        }

                        const backendRow = saveResponse.data || {};
                        target.pretest = parsedPretest;
                        target.assignment = parsedAssignment;
                        target.final_assignment = this.activeCourse.has_final_assignment ? parsedFinal : null;
                        target.last_update = backendRow.last_update || 'Baru saja';
                        target.note = backendRow.note
                            || (this.activeCourse.has_final_assignment
                                ? 'Nilai manual dosen sudah disimpan. Komponen nilai siap direview atau langsung dipublish jika lengkap.'
                                : 'Nilai manual dosen sudah disimpan untuk webinar. Komponen aktif siap dipublish jika checklist terpenuhi.');
                        target.status = backendRow.status || 'Lengkap';

                        this.syncDerivedState();

                        const refreshed = this.rows.find((item) => this.getRowKey(item) === this.getRowKey(target));
                        if (refreshed) {
                            this.selectedStudentKey = this.getRowKey(refreshed);
                            this.selectedRow = refreshed;
                            this.hydrateScoreForm(refreshed);
                        }

                        await this.notify(
                            `Nilai manual untuk ${target.student} berhasil disimpan.`,
                            'success',
                            'Nilai Tersimpan',
                            { toast: true }
                        );

                        if (this.canPublish && !this.isPublished) {
                            await this.publishGrades({ skipConfirm: true, auto: true });
                        }
                    },

                    resetManualScores() {
                        this.hydrateScoreForm(this.selectedRow);
                    },

                    isReviewActionable(row) {
                        return row?.status !== 'Lengkap';
                    },

                    completeRowData(row) {
                        const target = this.rows.find((item) => this.getRowKey(item) === this.getRowKey(row));

                        if (!target) {
                            return null;
                        }

                        if (this.activeCourse.has_final_assignment && !Number.isFinite(target.final_assignment)) {
                            target.final_assignment = Math.round(((target.pretest || 0) + (target.assignment || 0)) / 2);
                        }

                        target.status = 'Lengkap';
                        target.last_update = 'Baru saja';
                        target.note = this.activeCourse.has_final_assignment
                            ? 'Review dosen selesai. Komponen nilai lengkap dan siap publish nilai akhir.'
                            : 'Review dosen selesai. Nilai webinar lengkap dan siap dipublish.';

                        return target;
                    },

                    async markReviewComplete(row) {
                        if (!row || !this.isReviewActionable(row)) {
                            return;
                        }

                        if (!this.selectedCourse || !row.student_id) {
                            return this.notify(
                                'Identitas mahasiswa atau course belum valid.',
                                'error',
                                'Review Gagal'
                            );
                        }

                        const reviewUrl = this.buildApiUrl(this.api.review, {
                            course: this.selectedCourse,
                            student: row.student_id,
                        });
                        const reviewResponse = await this.postJson(reviewUrl, {
                            pretest_score: Number.isFinite(row.pretest) ? row.pretest : null,
                            assignment_score: Number.isFinite(row.assignment) ? row.assignment : null,
                            final_assignment_score: Number.isFinite(row.final_assignment) ? row.final_assignment : null,
                        });

                        if (!reviewResponse.success) {
                            return this.notify(
                                reviewResponse.message || 'Gagal menandai review selesai.',
                                'error',
                                'Review Gagal'
                            );
                        }

                        const updatedRow = this.completeRowData(row);
                        if (updatedRow && reviewResponse.data) {
                            updatedRow.pretest = reviewResponse.data.pretest;
                            updatedRow.assignment = reviewResponse.data.assignment;
                            updatedRow.final_assignment = reviewResponse.data.final_assignment;
                            updatedRow.status = reviewResponse.data.status || 'Lengkap';
                            updatedRow.last_update = reviewResponse.data.last_update || 'Baru saja';
                            updatedRow.note = reviewResponse.data.note || updatedRow.note;
                        }
                        this.syncDerivedState();

                        if (updatedRow) {
                            this.selectedStudentKey = this.getRowKey(updatedRow);
                            this.selectedRow = updatedRow;
                        }

                        await this.notify(
                            `Review ${row.student} ditandai selesai dan statusnya berubah ke Lengkap.`,
                            'success',
                            'Review Selesai',
                            { toast: true }
                        );

                        if (this.canPublish && !this.isPublished) {
                            await this.publishGrades({ skipConfirm: true, auto: true });
                        }
                    },

                    notify(message, icon = 'info', title = 'Informasi', options = {}) {
                        if (typeof window.showAppAlert === 'function') {
                            return window.showAppAlert(message, icon, title, options);
                        }

                        if (window.Swal && typeof window.Swal.fire === 'function') {
                            return window.Swal.fire({
                                icon,
                                title,
                                text: message,
                                confirmButtonText: options.confirmButtonText || 'Oke',
                                showCancelButton: options.showCancelButton || false,
                                cancelButtonText: options.cancelButtonText || 'Batal',
                            });
                        }

                        window.alert(message);
                        return Promise.resolve();
                    },

                    async saveDraft() {
                        if (!this.selectedCourse) {
                            return this.notify('Pilih course terlebih dahulu.', 'warning', 'Course Belum Dipilih');
                        }

                        const draftUrl = this.buildApiUrl(this.api.draft, {
                            course: this.selectedCourse,
                        });
                        const draftResponse = await this.postJson(draftUrl, {
                            weights: this.activeCourse.weights,
                            has_final_assignment: this.activeCourse.has_final_assignment,
                        });

                        if (!draftResponse.success) {
                            return this.notify(
                                draftResponse.message || 'Gagal menyimpan draft ke backend.',
                                'error',
                                'Draft Gagal Disimpan'
                            );
                        }

                        if (draftResponse.data?.weights) {
                            this.activeCourse.weights = {
                                pretest: Number(draftResponse.data.weights.pretest || 0),
                                assignment: Number(draftResponse.data.weights.assignment || 0),
                                final: Number(draftResponse.data.weights.final || 0),
                            };
                        }
                        if (typeof draftResponse.data?.has_final_assignment === 'boolean') {
                            this.activeCourse.has_final_assignment = draftResponse.data.has_final_assignment;
                        }

                        this.syncDerivedState();

                        return this.notify(
                            `Draft nilai untuk ${this.activeCourse.name} berhasil disimpan ke backend.`,
                            'success',
                            'Draft Tersimpan',
                            { toast: true }
                        );
                    },

                    exportGrades() {
                        return this.notify(
                            `Export preview untuk ${this.activeCourse.name} siap dipakai setelah backend export diaktifkan.`,
                            'info',
                            'Export Preview',
                            { toast: true }
                        );
                    },

                    async publishGrades(options = {}) {
                        if (!this.selectedCourse) {
                            return this.notify('Pilih course terlebih dahulu.', 'warning', 'Course Belum Dipilih');
                        }

                        if (!this.canPublish) {
                            return this.notify(
                                'Selesaikan dulu checklist publish: total bobot 100 persen, tidak ada pending review, dan minimal satu mahasiswa sudah lengkap.',
                                'warning',
                                'Belum Bisa Publish'
                            );
                        }

                        if (this.isPublished) {
                            return this.notify(
                                `${this.activeCourse.name} sudah ditandai published di frontend preview.`,
                                'info',
                                'Sudah Published',
                                { toast: true }
                            );
                        }

                        if (!options.skipConfirm && window.Swal && typeof window.Swal.fire === 'function') {
                            const result = await window.Swal.fire({
                                icon: 'question',
                                title: 'Publish nilai sekarang?',
                                text: `${this.activeCourse.name} akan ditandai siap publish dari halaman preview ini.`,
                                confirmButtonText: 'Publish',
                                cancelButtonText: 'Batal',
                                showCancelButton: true,
                            });

                            if (!result.isConfirmed) {
                                return;
                            }
                        }

                        const publishUrl = this.buildApiUrl(this.api.publish, {
                            course: this.selectedCourse,
                        });
                        const publishResponse = await this.postJson(publishUrl, {});

                        if (!publishResponse.success) {
                            return this.notify(
                                publishResponse.message || 'Gagal publish nilai ke backend.',
                                'error',
                                'Publish Gagal'
                            );
                        }

                        this.publishedCourses[this.selectedCourse] = true;
                        this.syncDerivedState();

                        return this.notify(
                            options.auto
                                ? `Semua review untuk ${this.activeCourse.name} sudah lengkap, jadi draft langsung ditandai published di frontend preview.`
                                : `Draft nilai ${this.activeCourse.name} berhasil dipublish.`,
                            'success',
                            options.auto ? 'Auto Publish Selesai' : 'Nilai Siap Publish'
                        );
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
