<x-layouts.admin title="Sertifikasi Otomatis" active="sertifikasi">
    <div
        id="sertifikasiApp"
        data-templates='@json($initialTemplates)'
        data-certificates='@json($initialCertificates)'
        data-csrf='{{ csrf_token() }}'
        data-template-store-url='{{ route('admin.sertifikasi.templates.store', [], false) }}'
        data-template-update-url='{{ route('admin.sertifikasi.templates.update', ['id' => ':id'], false) }}'
        data-template-delete-url='{{ route('admin.sertifikasi.templates.delete', ['id' => ':id'], false) }}'
        data-certificate-store-url='{{ route('admin.sertifikasi.certificates.store', [], false) }}'
        data-certificate-update-url='{{ route('admin.sertifikasi.certificates.update', ['id' => ':id'], false) }}'
        data-certificate-delete-url='{{ route('admin.sertifikasi.certificates.delete', ['id' => ':id'], false) }}'
        class="space-y-7"
    >
        <style>
            .certificate-preview-card-header {
                align-items: flex-start;
                gap: 0.35rem;
            }

            .certificate-preview-meta {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .certificate-preview-canvas {
                container-type: inline-size;
                --cert-nomor-size: 26;
                --cert-nama-size: 42;
                --cert-program-size: 18;
                --cert-tanggal-size: 14;
            }

            .certificate-preview-text-group {
                max-width: 86%;
                overflow-wrap: anywhere;
                word-break: normal;
            }

            .certificate-preview-label {
                font-size: clamp(0.42rem, 1.95cqw, 0.72rem);
                line-height: 1.1;
                margin-bottom: clamp(0.02rem, 0.45cqw, 0.28rem);
            }

            #previewNomor {
                font-size: clamp(0.62rem, calc(var(--cert-nomor-size) * 0.148cqw), 2.2rem);
                line-height: 0.98;
                overflow-wrap: anywhere;
            }

            #previewNama {
                font-size: clamp(1rem, calc(var(--cert-nama-size) * 0.148cqw), 3.5rem);
                line-height: 0.92;
                overflow-wrap: anywhere;
            }

            #previewProgram {
                font-size: clamp(0.72rem, calc(var(--cert-program-size) * 0.148cqw), 1.55rem);
                line-height: 1.05;
                overflow-wrap: anywhere;
            }

            #previewTanggal {
                font-size: clamp(0.62rem, calc(var(--cert-tanggal-size) * 0.148cqw), 1.15rem);
                line-height: 1.1;
            }

            .certificate-preview-canvas.certificate-exporting #previewNomor {
                font-size: calc(var(--cert-nomor-size) * 1pt) !important;
            }

            .certificate-preview-canvas.certificate-exporting #previewNama {
                font-size: calc(var(--cert-nama-size) * 1pt) !important;
            }

            .certificate-preview-canvas.certificate-exporting #previewProgram {
                font-size: calc(var(--cert-program-size) * 1pt) !important;
            }

            .certificate-preview-canvas.certificate-exporting #previewTanggal {
                font-size: calc(var(--cert-tanggal-size) * 1pt) !important;
            }

            @media (max-width: 639px) {
                .certificate-preview-card {
                    padding: 1rem;
                }

                .certificate-preview-card-header {
                    flex-direction: column;
                    margin-bottom: 0.75rem;
                }

                .certificate-preview-meta {
                    font-size: 0.72rem;
                    white-space: normal;
                }

                .certificate-preview-canvas {
                    border-radius: 0.85rem;
                }

                .certificate-preview-canvas #certificateFrameOuter {
                    inset: 0.42rem;
                    border-width: 0.18rem;
                }

                .certificate-preview-canvas #certificateFrameInner {
                    inset: 0.72rem;
                }

                .certificate-preview-list {
                    min-width: 100% !important;
                    border-spacing: 0 0.62rem;
                }

                .certificate-preview-list tbody tr {
                    position: relative;
                    padding: 0.78rem;
                    border-radius: 0.875rem;
                    overflow: visible;
                }

                .certificate-preview-list tbody td {
                    border-bottom: 0;
                    padding: 0 !important;
                }

                .certificate-preview-list tbody td::before {
                    display: none;
                }

                .certificate-list-number {
                    display: block;
                    padding-right: 5.5rem !important;
                    font-size: 0.75rem !important;
                    line-height: 1.25;
                    word-break: break-word;
                }

                .certificate-list-name {
                    display: block;
                    margin-top: 0.42rem;
                    font-size: 0.9rem !important;
                    font-weight: 700;
                    line-height: 1.25;
                }

                .certificate-list-program {
                    display: inline-flex;
                    width: auto;
                    max-width: 100%;
                    margin-top: 0.32rem;
                    margin-right: 0.35rem;
                    vertical-align: middle;
                }

                .certificate-list-program > div {
                    gap: 0.4rem;
                }

                .certificate-list-program-name,
                .certificate-list-date {
                    font-size: 0.76rem;
                    line-height: 1.25;
                    color: rgb(100 116 139);
                }

                .dark .certificate-list-program-name,
                .dark .certificate-list-date {
                    color: rgb(148 163 184);
                }

                .certificate-list-source {
                    position: absolute;
                    top: 0.78rem;
                    right: 0.78rem;
                    padding: 0.18rem 0.48rem;
                    font-size: 0.62rem;
                    line-height: 1.1;
                }

                .certificate-list-date-cell {
                    display: inline-flex;
                    width: auto;
                    margin-top: 0.32rem;
                    vertical-align: middle;
                }

                .certificate-list-date-cell::before {
                    content: "-";
                    display: inline;
                    margin-right: 0.35rem;
                    color: rgb(148 163 184);
                    font-weight: 700;
                }

                .certificate-list-actions {
                    display: block;
                    margin-top: 0.68rem;
                }

                .certificate-list-action-group {
                    justify-content: flex-start;
                    gap: 0.35rem;
                }

                .certificate-list-action-group button {
                    min-height: 2rem;
                    padding: 0.42rem 0.55rem;
                    border-radius: 0.62rem;
                    font-size: 0.72rem;
                    font-weight: 700;
                }
            }
        </style>

        <section class="relative overflow-hidden rounded-3xl border border-orange-100/70 bg-gradient-to-br from-orange-50 via-white to-sky-50 p-5 shadow-sm dark:border-gray-700 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 sm:p-6">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-orange-200/40 blur-2xl dark:bg-orange-500/20"></div>
            <div class="absolute -left-8 -bottom-8 h-24 w-24 rounded-full bg-sky-200/50 blur-2xl dark:bg-sky-500/20"></div>

            <div class="relative flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-orange-700 ring-1 ring-orange-200 dark:bg-gray-900/60 dark:text-orange-300 dark:ring-orange-900/50">
                        Dashboard Sertifikasi
                    </p>
                    <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Sertifikat Otomatis</h1>
                    <p class="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-300">
                        Kelola blangko, atur posisi teks, dan pantau sertifikat yang diterbitkan otomatis saat mahasiswa menyelesaikan kursus atau webinar bersertifikat.
                    </p>
                </div>
                <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
                    <button id="btnOpenCreateCert" type="button" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Sertif Otomatis
                    </button>
                    <button id="btnExportCurrentPdf" type="button" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17a2 2 0 002 2h12a2 2 0 002-2M4 7a2 2 0 012-2h8l6 6v6a2 2 0 01-2 2"/></svg>
                        PDF Hasil Sertif
                    </button>
                </div>
            </div>

            <div class="relative mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Blangko</p>
                    <p id="statTemplateCount" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">0</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Sertifikat</p>
                    <p id="statCertificateCount" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">0</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Hasil Filter</p>
                    <p id="statFilteredCount" class="mt-1 text-xl font-bold text-gray-900 dark:text-white">0</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 lg:grid-cols-3">
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 text-sm text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-600 dark:text-emerald-300">Flow Otomatis</p>
                <p class="mt-1 font-semibold">Course/Webinar bersertifikat</p>
                <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-200">Dosen atau admin menyalakan sertifikasi otomatis saat membuat atau mengedit program.</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 text-sm text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/30 dark:text-blue-100">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-600 dark:text-blue-300">Penerbitan</p>
                <p class="mt-1 font-semibold">Selesai 100% atau webinar selesai</p>
                <p class="mt-1 text-xs text-blue-700 dark:text-blue-200">Sistem membuat nomor sertifikat, mengikatnya ke mahasiswa dan course, lalu memakai blangko aktif terbaru.</p>
            </div>
            <div class="rounded-2xl border border-orange-100 bg-orange-50/70 p-4 text-sm text-orange-900 dark:border-orange-900/50 dark:bg-orange-950/30 dark:text-orange-100">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-orange-600 dark:text-orange-300">Notifikasi</p>
                <p class="mt-1 font-semibold">Mahasiswa dapat link download</p>
                <p class="mt-1 text-xs text-orange-700 dark:text-orange-200">Notifikasi mengarah ke halaman belajar/detail course agar sertifikat bisa langsung diunduh sebagai PDF.</p>
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="certificate-preview-card bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-3xl p-4 shadow-sm sm:p-5">
                    <div class="certificate-preview-card-header flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Preview Sertifikat</h2>
                        <span id="previewMeta" class="certificate-preview-meta text-xs text-gray-500 dark:text-gray-400"></span>
                    </div>

                    <div id="certificateCanvas" class="certificate-preview-canvas relative w-full aspect-[297/210] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-inner bg-white">
                        <div id="certificateBgLayer" class="absolute inset-0"></div>
                        <div id="certificateOverlayTint" class="absolute inset-0 bg-white/15"></div>
                        <div id="certificateFrameOuter" class="absolute inset-3 sm:inset-4 rounded-lg border-4 border-amber-300/90"></div>
                        <div id="certificateFrameInner" class="absolute inset-5 sm:inset-7 rounded-md border border-amber-400/80"></div>

                        <div class="absolute inset-0 text-gray-800">
                            <div id="previewNomorGroup" class="certificate-preview-text-group absolute w-[72%] text-center" style="left: 50%; top: 24%; transform: translate(-50%, -50%);">
                                <p id="previewNomorLabel" class="certificate-preview-label uppercase tracking-[0.08em] text-gray-600">Nomor Sertifikat</p>
                                <p id="previewNomor" class="font-semibold text-gray-800">SRT-XXXX</p>
                            </div>

                            <div id="previewNamaGroup" class="certificate-preview-text-group absolute w-[80%] text-center" style="left: 50%; top: 43%; transform: translate(-50%, -50%);">
                                <p id="previewNamaLabel" class="certificate-preview-label uppercase tracking-[0.08em] text-gray-600">Nama Peserta</p>
                                <p id="previewNama" class="text-gray-900" style="font-family: 'Times New Roman', serif; font-weight: 700;">Nama Peserta</p>
                            </div>

                            <div id="previewProgramGroup" class="certificate-preview-text-group absolute w-[72%] text-center" style="left: 50%; top: 58%; transform: translate(-50%, -50%);">
                                <p id="previewProgramLabel" class="certificate-preview-label uppercase tracking-[0.08em] text-gray-600">Program</p>
                                <p id="previewProgram" class="font-semibold text-gray-800">Nama Program</p>
                            </div>

                            <div id="previewTanggalGroup" class="certificate-preview-text-group absolute w-[60%] text-center" style="left: 50%; top: 72%; transform: translate(-50%, -50%);">
                                <p id="previewTanggalLabel" class="certificate-preview-label uppercase tracking-[0.08em] text-gray-600">Tanggal</p>
                                <p id="previewTanggal" class="font-medium">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-3xl p-4 shadow-sm sm:p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Daftar Sertif Otomatis</h2>
                        <div class="relative w-full sm:w-72">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 110-15 7.5 7.5 0 010 15z"/></svg>
                            <input id="searchCertInput" type="text" placeholder="Cari nomor, nama, atau program..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto responsive-table">
                        <table class="certificate-preview-list w-full responsive-data-table admin-desktop-table admin-mobile-list min-w-[680px] lg:min-w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/40 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    <th class="text-left px-4 py-3">Nomor</th>
                                    <th class="text-left px-4 py-3">Nama Peserta</th>
                                    <th class="text-left px-4 py-3">Program</th>
                                    <th class="text-left px-4 py-3">Tanggal</th>
                                    <th class="text-center px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="certificateTableBody" class="divide-y divide-gray-100 dark:divide-gray-700/70"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-3xl p-4 shadow-sm sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tambah Blangko</h2>
                        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-black text-white text-xs font-semibold cursor-pointer transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Upload
                            <input id="uploadBlangkoInput" type="file" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Upload blangko baru untuk dipakai pada sertifikat otomatis. Format JPG/PNG/WebP maksimal 5MB.</p>

                    <div id="templateList" class="space-y-2"></div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-3xl p-4 shadow-sm sm:p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Posisi Teks Otomatis</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Atur posisi `X`, `Y`, dan ukuran teks untuk blangko yang sedang dipilih.</p>
                        </div>
                        <span id="selectedTemplateBadge" class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">-</span>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor Sertifikat</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">X (%)</label>
                                    <input id="settingNomorX" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Y (%)</label>
                                    <input id="settingNomorY" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Size (pt)</label>
                                    <input id="settingNomorSize" type="number" min="10" max="72" step="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Peserta</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">X (%)</label>
                                    <input id="settingNamaX" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Y (%)</label>
                                    <input id="settingNamaY" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Size (pt)</label>
                                    <input id="settingNamaSize" type="number" min="14" max="96" step="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Program</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">X (%)</label>
                                    <input id="settingProgramX" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Y (%)</label>
                                    <input id="settingProgramY" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Size (pt)</label>
                                    <input id="settingProgramSize" type="number" min="10" max="56" step="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">X (%)</label>
                                    <input id="settingTanggalX" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Y (%)</label>
                                    <input id="settingTanggalY" type="number" min="0" max="100" step="0.1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-gray-500 mb-1">Size (pt)</label>
                                    <input id="settingTanggalSize" type="number" min="10" max="42" step="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] text-gray-500 mb-1">Nama Blangko</label>
                            <input id="selectedTemplateName" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>

                        <button id="btnSaveTemplateSettings" type="button" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">
                            Simpan Pengaturan Blangko
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="certificateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="certificateModalOverlay"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl">
                <button id="btnCloseCertModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <form id="certificateForm" class="p-6">
                    <input id="certFormMode" type="hidden" value="create">
                    <input id="certFormId" type="hidden" value="">

                    <h3 id="certModalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Tambah Sertif Otomatis</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Isi data sertifikat. Nomor bisa dikosongkan untuk auto generate.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                        <div>
                            <label class="text-xs text-gray-500">Nomor Sertif</label>
                            <input id="inputNomor" type="text" placeholder="Kosongkan untuk auto generate" class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Tanggal Terbit</label>
                            <input id="inputTanggal" type="date" required class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Nama Peserta</label>
                            <input id="inputNama" type="text" required class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Program</label>
                            <input id="inputProgram" type="text" required class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500">Blangko</label>
                            <select id="inputTemplateId" required class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm"></select>
                        </div>
                    </div>

                    <div class="mt-6 admin-responsive-modal-actions flex justify-end gap-2">
                        <button id="btnCancelCertModal" type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .swal-cert-popup {
            border-radius: 1rem !important;
        }

        .swal-cert-actions {
            gap: 0.5rem !important;
        }

        .swal-cert-confirm,
        .swal-cert-cancel {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 112px !important;
            border-radius: 0.6rem !important;
            padding: 0.55rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            text-decoration: none !important;
        }

        .swal-cert-confirm {
            background-color: #2563eb !important;
            border: 1px solid #2563eb !important;
            color: #ffffff !important;
        }

        .swal-cert-confirm:hover {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
        }

        .swal-cert-confirm--warning {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
        }

        .swal-cert-confirm--warning:hover {
            background-color: #b45309 !important;
            border-color: #b45309 !important;
            color: #ffffff !important;
        }

        .swal-cert-confirm--danger {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }

        .swal-cert-confirm--danger:hover {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #ffffff !important;
        }

        .swal-cert-cancel {
            background-color: #ffffff !important;
            border: 1px solid #d1d5db !important;
            color: #374151 !important;
        }

        .swal-cert-cancel:hover {
            background-color: #f9fafb !important;
            border-color: #9ca3af !important;
            color: #1f2937 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        (() => {
            const app = document.getElementById('sertifikasiApp');
            if (!app) return;

            const csrfToken = app.dataset.csrf;
            const templateStoreUrl = app.dataset.templateStoreUrl;
            const templateUpdateUrl = app.dataset.templateUpdateUrl;
            const templateDeleteUrl = app.dataset.templateDeleteUrl;
            const certificateStoreUrl = app.dataset.certificateStoreUrl;
            const certificateUpdateUrl = app.dataset.certificateUpdateUrl;
            const certificateDeleteUrl = app.dataset.certificateDeleteUrl;

            let templates = JSON.parse(app.dataset.templates || '[]');
            let certificates = JSON.parse(app.dataset.certificates || '[]');
            let selectedTemplateId = templates[0]?.id || certificates[0]?.templateId || null;
            let selectedCertificateId = certificates[0]?.id || null;

            const templateList = document.getElementById('templateList');
            const certificateTableBody = document.getElementById('certificateTableBody');
            const searchCertInput = document.getElementById('searchCertInput');

            const previewMeta = document.getElementById('previewMeta');
            const previewNama = document.getElementById('previewNama');
            const previewNomor = document.getElementById('previewNomor');
            const previewProgram = document.getElementById('previewProgram');
            const previewTanggal = document.getElementById('previewTanggal');
            const previewNomorLabel = document.getElementById('previewNomorLabel');
            const previewNamaLabel = document.getElementById('previewNamaLabel');
            const previewProgramLabel = document.getElementById('previewProgramLabel');
            const previewTanggalLabel = document.getElementById('previewTanggalLabel');
            const previewNamaGroup = document.getElementById('previewNamaGroup');
            const previewNomorGroup = document.getElementById('previewNomorGroup');
            const previewProgramGroup = document.getElementById('previewProgramGroup');
            const previewTanggalGroup = document.getElementById('previewTanggalGroup');
            const certificateBgLayer = document.getElementById('certificateBgLayer');
            const certificateOverlayTint = document.getElementById('certificateOverlayTint');
            const certificateFrameOuter = document.getElementById('certificateFrameOuter');
            const certificateFrameInner = document.getElementById('certificateFrameInner');

            const selectedTemplateBadge = document.getElementById('selectedTemplateBadge');
            const selectedTemplateName = document.getElementById('selectedTemplateName');
            const settingNomorX = document.getElementById('settingNomorX');
            const settingNomorY = document.getElementById('settingNomorY');
            const settingNomorSize = document.getElementById('settingNomorSize');
            const settingNamaX = document.getElementById('settingNamaX');
            const settingNamaY = document.getElementById('settingNamaY');
            const settingNamaSize = document.getElementById('settingNamaSize');
            const settingProgramX = document.getElementById('settingProgramX');
            const settingProgramY = document.getElementById('settingProgramY');
            const settingProgramSize = document.getElementById('settingProgramSize');
            const settingTanggalX = document.getElementById('settingTanggalX');
            const settingTanggalY = document.getElementById('settingTanggalY');
            const settingTanggalSize = document.getElementById('settingTanggalSize');
            const btnSaveTemplateSettings = document.getElementById('btnSaveTemplateSettings');
            const uploadBlangkoInput = document.getElementById('uploadBlangkoInput');

            const certificateModal = document.getElementById('certificateModal');
            const certificateModalOverlay = document.getElementById('certificateModalOverlay');
            const btnOpenCreateCert = document.getElementById('btnOpenCreateCert');
            const btnCloseCertModal = document.getElementById('btnCloseCertModal');
            const btnCancelCertModal = document.getElementById('btnCancelCertModal');
            const certModalTitle = document.getElementById('certModalTitle');
            const certForm = document.getElementById('certificateForm');
            const certFormMode = document.getElementById('certFormMode');
            const certFormId = document.getElementById('certFormId');
            const inputNomor = document.getElementById('inputNomor');
            const inputTanggal = document.getElementById('inputTanggal');
            const inputNama = document.getElementById('inputNama');
            const inputProgram = document.getElementById('inputProgram');
            const inputTemplateId = document.getElementById('inputTemplateId');
            const btnExportCurrentPdf = document.getElementById('btnExportCurrentPdf');
            const statTemplateCount = document.getElementById('statTemplateCount');
            const statCertificateCount = document.getElementById('statCertificateCount');
            const statFilteredCount = document.getElementById('statFilteredCount');

            let filteredCertificateCount = certificates.length;

            function normalizeAppUrl(url) {
                if (!url) {
                    return url;
                }

                try {
                    const normalizedUrl = new URL(url, window.location.origin);
                    if (normalizedUrl.host === window.location.host) {
                        return `${window.location.origin}${normalizedUrl.pathname}${normalizedUrl.search}${normalizedUrl.hash}`;
                    }

                    return normalizedUrl.toString();
                } catch (error) {
                    return url;
                }
            }

            function replaceRouteId(url, id) {
                return url.replace(':id', id);
            }

            function getTemplateById(id) {
                return templates.find((template) => String(template.id) === String(id)) || templates[0] || null;
            }

            function getCertById(id) {
                return certificates.find((certificate) => String(certificate.id) === String(id)) || null;
            }

            function formatDateIndonesia(isoDate) {
                if (!isoDate) return '-';
                const dt = new Date(isoDate);
                return Number.isNaN(dt.getTime())
                    ? '-'
                    : dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            }

            async function parseResponsePayload(response) {
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    return await response.json();
                }

                const text = await response.text();

                try {
                    return JSON.parse(text);
                } catch (error) {
                    return {
                        message: text?.trim() || response.statusText || 'Terjadi kesalahan pada server.',
                    };
                }
            }

            async function requestJson(url, options = {}, fallbackMessage = 'Request gagal diproses.') {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    ...options,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(options.headers || {}),
                    },
                });

                const payload = await parseResponsePayload(response);

                if (!response.ok) {
                    throw new Error(
                        payload?.message
                        || Object.values(payload?.errors || {}).flat()?.[0]
                        || fallbackMessage
                    );
                }

                return payload;
            }

            function buildSettingsPayload() {
                return {
                    name: selectedTemplateName.value.trim(),
                    nomor_x: settingNomorX.value,
                    nomor_y: settingNomorY.value,
                    nomor_size: settingNomorSize.value,
                    nama_x: settingNamaX.value,
                    nama_y: settingNamaY.value,
                    nama_size: settingNamaSize.value,
                    program_x: settingProgramX.value,
                    program_y: settingProgramY.value,
                    program_size: settingProgramSize.value,
                    tanggal_x: settingTanggalX.value,
                    tanggal_y: settingTanggalY.value,
                    tanggal_size: settingTanggalSize.value,
                };
            }

            function applyPreviewSettings(template) {
                const settings = template?.settings || {};
                const nomor = settings.nomor || { x: 50, y: 24, size: 26 };
                const nama = settings.nama || { x: 50, y: 43, size: 42 };
                const program = settings.program || { x: 50, y: 58, size: 18 };
                const tanggal = settings.tanggal || { x: 50, y: 72, size: 14 };

                previewNomorGroup.style.left = `${nomor.x}%`;
                previewNomorGroup.style.top = `${nomor.y}%`;
                certificateCanvas.style.setProperty('--cert-nomor-size', nomor.size);

                previewNamaGroup.style.left = `${nama.x}%`;
                previewNamaGroup.style.top = `${nama.y}%`;
                certificateCanvas.style.setProperty('--cert-nama-size', nama.size);

                previewProgramGroup.style.left = `${program.x}%`;
                previewProgramGroup.style.top = `${program.y}%`;
                certificateCanvas.style.setProperty('--cert-program-size', program.size);

                previewTanggalGroup.style.left = `${tanggal.x}%`;
                previewTanggalGroup.style.top = `${tanggal.y}%`;
                certificateCanvas.style.setProperty('--cert-tanggal-size', tanggal.size);
            }

            function syncSettingsForm() {
                const template = getTemplateById(selectedTemplateId);
                if (!template) {
                    selectedTemplateBadge.textContent = 'Belum ada blangko';
                    selectedTemplateName.value = '';
                    return;
                }

                selectedTemplateBadge.textContent = template.name;
                selectedTemplateName.value = template.name || '';

                settingNomorX.value = template.settings.nomor.x;
                settingNomorY.value = template.settings.nomor.y;
                settingNomorSize.value = template.settings.nomor.size;
                settingNamaX.value = template.settings.nama.x;
                settingNamaY.value = template.settings.nama.y;
                settingNamaSize.value = template.settings.nama.size;
                settingProgramX.value = template.settings.program.x;
                settingProgramY.value = template.settings.program.y;
                settingProgramSize.value = template.settings.program.size;
                settingTanggalX.value = template.settings.tanggal.x;
                settingTanggalY.value = template.settings.tanggal.y;
                settingTanggalSize.value = template.settings.tanggal.size;

                applyPreviewSettings(template);
            }

            function renderTemplateOptions() {
                inputTemplateId.innerHTML = templates.map((template) => `<option value="${template.id}">${template.name}</option>`).join('');
            }

            function updateSummaryStats(filteredCount = filteredCertificateCount) {
                if (statTemplateCount) {
                    statTemplateCount.textContent = String(templates.length);
                }

                if (statCertificateCount) {
                    statCertificateCount.textContent = String(certificates.length);
                }

                if (statFilteredCount) {
                    statFilteredCount.textContent = String(filteredCount);
                }
            }

            function renderTemplateList() {
                templateList.innerHTML = templates.map((template) => {
                    const isActive = String(template.id) === String(selectedTemplateId);
                    const backgroundStyle = template.kind === 'image'
                        ? `background-image:url('${normalizeAppUrl(template.image)}');background-size:cover;background-position:center;`
                        : `background:${template.gradient};`;
                    const deleteButton = templates.length > 1
                        ? `<button type="button" data-delete-template-id="${template.id}" class="text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 hover:bg-rose-200 transition">Hapus</button>`
                        : '';

                    return `
                        <div data-template-id="${template.id}" class="w-full text-left p-2.5 rounded-xl border cursor-pointer ${isActive ? 'border-orange-400 ring-2 ring-orange-100 dark:ring-orange-900/30' : 'border-gray-200 dark:border-gray-600'} hover:border-orange-300 transition">
                            <div class="h-20 rounded-lg" style="${backgroundStyle}"></div>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">${template.name}</span>
                                <div class="flex items-center gap-1">
                                    ${deleteButton}
                                    <span class="text-[10px] px-2 py-0.5 rounded-full ${isActive ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600'}">${isActive ? 'Dipakai' : 'Pilih'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                if (!templates.length) {
                    templateList.innerHTML = '<div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 px-4 py-6 text-center text-xs text-gray-500">Belum ada blangko. Upload blangko terlebih dahulu.</div>';
                }

                updateSummaryStats();
            }

            function renderCertificateTable() {
                const keyword = (searchCertInput.value || '').toLowerCase().trim();
                const filtered = certificates.filter((certificate) => {
                    const haystack = `${certificate.nomor} ${certificate.nama} ${certificate.program}`.toLowerCase();
                    return haystack.includes(keyword);
                });

                certificateTableBody.innerHTML = filtered.map((certificate) => `
                    <tr class="hover:bg-orange-50/40 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="certificate-list-number px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-200" data-label="Nomor">${certificate.nomor}</td>
                        <td class="certificate-list-name px-4 py-3 text-sm text-gray-700 dark:text-gray-200" data-label="Nama Peserta">${certificate.nama}</td>
                        <td class="certificate-list-program px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-label="Program">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="certificate-list-program-name">${certificate.program}</span>
                                <span class="certificate-list-source inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide ${certificate.source === 'auto' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'}">${certificate.source === 'auto' ? 'Auto' : 'Manual'}</span>
                            </div>
                        </td>
                        <td class="certificate-list-date-cell px-4 py-3 text-sm text-gray-600 dark:text-gray-300" data-label="Tanggal"><span class="certificate-list-date">${formatDateIndonesia(certificate.tanggal)}</span></td>
                        <td class="certificate-list-actions px-4 py-3" data-label="Aksi">
                            <div class="certificate-list-action-group flex items-center justify-center gap-1">
                                <button type="button" data-action="preview" data-id="${certificate.id}" class="px-2.5 py-1 text-xs rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200">Preview</button>
                                <button type="button" data-action="edit" data-id="${certificate.id}" class="px-2.5 py-1 text-xs rounded-md bg-sky-100 text-sky-700 hover:bg-sky-200">Edit</button>
                                <button type="button" data-action="pdf" data-id="${certificate.id}" class="px-2.5 py-1 text-xs rounded-md bg-emerald-100 text-emerald-700 hover:bg-emerald-200">PDF</button>
                                <button type="button" data-action="delete" data-id="${certificate.id}" class="px-2.5 py-1 text-xs rounded-md bg-rose-100 text-rose-700 hover:bg-rose-200">Hapus</button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                if (!filtered.length) {
                    certificateTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Data sertifikat tidak ditemukan.</td>
                        </tr>
                    `;
                }

                filteredCertificateCount = filtered.length;
                updateSummaryStats(filteredCertificateCount);
            }

            function renderPreview() {
                const certificate = getCertById(selectedCertificateId);
                const template = getTemplateById(selectedTemplateId || certificate?.templateId);

                if (template) {
                    selectedTemplateId = template.id;
                    if (template.kind === 'image' && template.image) {
                        certificateBgLayer.style.backgroundImage = `url('${normalizeAppUrl(template.image)}')`;
                        certificateBgLayer.style.backgroundSize = 'cover';
                        certificateBgLayer.style.backgroundPosition = 'center';
                        certificateBgLayer.style.backgroundColor = 'transparent';
                        certificateOverlayTint.classList.add('hidden');
                        certificateFrameOuter.classList.add('hidden');
                        certificateFrameInner.classList.add('hidden');
                        previewNomorLabel.classList.add('hidden');
                        previewNamaLabel.classList.add('hidden');
                        previewProgramLabel.classList.add('hidden');
                        previewTanggalLabel.classList.add('hidden');
                    } else {
                        certificateBgLayer.style.backgroundImage = 'none';
                        certificateBgLayer.style.background = template.gradient || 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #bfdbfe 100%)';
                        certificateOverlayTint.classList.remove('hidden');
                        certificateFrameOuter.classList.remove('hidden');
                        certificateFrameInner.classList.remove('hidden');
                        previewNomorLabel.classList.remove('hidden');
                        previewNamaLabel.classList.remove('hidden');
                        previewProgramLabel.classList.remove('hidden');
                        previewTanggalLabel.classList.remove('hidden');
                    }
                }

                applyPreviewSettings(template);

                if (!certificate) {
                    previewNama.textContent = 'Nama Peserta';
                    previewNomor.textContent = 'SRT-XXXX';
                    previewProgram.textContent = 'Nama Program';
                    previewTanggal.textContent = '-';
                    previewMeta.textContent = template ? `Blangko: ${template.name}` : 'Belum ada data';
                    return;
                }

                previewNama.textContent = certificate.nama;
                previewNomor.textContent = certificate.nomor;
                previewProgram.textContent = certificate.program;
                previewTanggal.textContent = formatDateIndonesia(certificate.tanggal);
                previewMeta.textContent = `${certificate.nomor} - ${certificate.nama}`;
            }

            function renderAll() {
                renderTemplateOptions();
                renderTemplateList();
                renderCertificateTable();
                syncSettingsForm();
                renderPreview();
            }

            function openModal(mode, certificate = null) {
                certFormMode.value = mode;
                certFormId.value = certificate?.id || '';
                certModalTitle.textContent = mode === 'edit' ? 'Edit Sertif Otomatis' : 'Tambah Sertif Otomatis';

                inputNomor.value = certificate?.nomor || '';
                inputNama.value = certificate?.nama || '';
                inputProgram.value = certificate?.program || '';
                inputTanggal.value = certificate?.tanggal || new Date().toISOString().slice(0, 10);
                inputTemplateId.value = certificate?.templateId || selectedTemplateId || templates[0]?.id || '';

                certificateModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                certificateModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            async function exportCertificatePdf(certificateId) {
                const certificate = getCertById(certificateId || selectedCertificateId);
                if (!certificate) {
                    showInfoAlert('Pilih data sertifikat terlebih dahulu.');
                    return;
                }

                selectedCertificateId = certificate.id;
                selectedTemplateId = certificate.templateId;
                renderAll();

                const canvasNode = document.getElementById('certificateCanvas');

                try {
                    canvasNode.classList.add('certificate-exporting');
                    await new Promise((resolve) => requestAnimationFrame(resolve));

                    const scale = Math.min(Math.max(window.devicePixelRatio || 1, 2), 4);
                    const canvas = await html2canvas(canvasNode, {
                        scale,
                        useCORS: true,
                        allowTaint: false,
                        backgroundColor: '#ffffff',
                        logging: false,
                        imageTimeout: 15000,
                    });

                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4', compress: true });
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();
                    const pageRatio = pageWidth / pageHeight;
                    const canvasRatio = canvas.width / canvas.height;

                    let renderWidth = pageWidth;
                    let renderHeight = pageHeight;
                    let offsetX = 0;
                    let offsetY = 0;

                    // Fit image to page without stretching when browser rounding causes small ratio drift.
                    if (canvasRatio > pageRatio) {
                        renderHeight = pageWidth / canvasRatio;
                        offsetY = (pageHeight - renderHeight) / 2;
                    } else if (canvasRatio < pageRatio) {
                        renderWidth = pageHeight * canvasRatio;
                        offsetX = (pageWidth - renderWidth) / 2;
                    }

                    pdf.setFillColor(255, 255, 255);
                    pdf.rect(0, 0, pageWidth, pageHeight, 'F');
                    pdf.addImage(imgData, 'PNG', offsetX, offsetY, renderWidth, renderHeight, undefined, 'FAST');

                    const safeFileName = String(certificate.nomor || 'sertifikat')
                        .replace(/[^a-zA-Z0-9-_]+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');

                    pdf.save(`${safeFileName || 'sertifikat'}.pdf`);
                } catch (error) {
                    console.error(error);
                    showErrorAlert('Gagal membuat PDF. Coba ulangi lagi.');
                } finally {
                    canvasNode.classList.remove('certificate-exporting');
                }
            }

            function showAlert(options = {}) {
                const confirmVariant = options.confirmVariant || 'primary';
                const confirmVariantClass = confirmVariant === 'danger'
                    ? 'swal-cert-confirm--danger'
                    : (confirmVariant === 'warning' ? 'swal-cert-confirm--warning' : '');
                const customClass = {
                    popup: 'swal-cert-popup',
                    actions: 'swal-cert-actions',
                    confirmButton: `swal-cert-confirm ${confirmVariantClass}`.trim(),
                    cancelButton: 'swal-cert-cancel',
                    ...(options.customClass || {}),
                };
                const mergedOptions = {
                    confirmButtonText: 'Oke',
                    buttonsStyling: false,
                    customClass,
                    ...options,
                };

                delete mergedOptions.confirmVariant;

                return Swal.fire(mergedOptions);
            }

            function showInfoAlert(message, title = 'Info') {
                return showAlert({
                    icon: 'info',
                    title,
                    text: message,
                });
            }

            function showErrorAlert(message, title = 'Error') {
                return showAlert({
                    icon: 'error',
                    title,
                    text: message,
                    confirmVariant: 'danger',
                });
            }

            function showValidationError(message) {
                showAlert({
                    icon: 'warning',
                    title: 'Validasi',
                    text: message,
                    confirmVariant: 'warning',
                });
            }

            async function uploadTemplate(file) {
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('blangko', file);
                formData.append('name', file.name.replace(/\.[^.]+$/, ''));

                const payload = await requestJson(templateStoreUrl, {
                    method: 'POST',
                    body: formData,
                }, 'Gagal menambahkan blangko.');

                templates.unshift(payload.template);
                selectedTemplateId = payload.template.id;
                renderAll();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: payload.message,
                    timer: 1800,
                    showConfirmButton: false,
                });
            }

            async function saveTemplateSettings() {
                const template = getTemplateById(selectedTemplateId);
                if (!template) {
                    showValidationError('Pilih blangko terlebih dahulu.');
                    return;
                }

                const payload = buildSettingsPayload();
                const result = await requestJson(replaceRouteId(templateUpdateUrl, template.id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                }, 'Gagal menyimpan pengaturan blangko.');

                templates = templates.map((item) => String(item.id) === String(result.template.id) ? result.template : item);
                selectedTemplateId = result.template.id;
                renderAll();

                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan',
                    text: result.message,
                    timer: 1600,
                    showConfirmButton: false,
                });
            }

            async function saveCertificate() {
                const payload = {
                    certificate_template_id: inputTemplateId.value,
                    nomor_sertifikat: inputNomor.value.trim(),
                    nama_peserta: inputNama.value.trim(),
                    nama_program: inputProgram.value.trim(),
                    tanggal_terbit: inputTanggal.value,
                };

                if (!payload.certificate_template_id || !payload.nama_peserta || !payload.nama_program || !payload.tanggal_terbit) {
                    showValidationError('Lengkapi semua field wajib pada sertifikat.');
                    return;
                }

                const isEdit = certFormMode.value === 'edit' && certFormId.value;
                const url = isEdit ? replaceRouteId(certificateUpdateUrl, certFormId.value) : certificateStoreUrl;

                const result = await requestJson(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                }, 'Gagal menyimpan sertifikat otomatis.');

                if (isEdit) {
                    certificates = certificates.map((item) => String(item.id) === String(result.certificate.id) ? result.certificate : item);
                } else {
                    certificates.unshift(result.certificate);
                }

                selectedCertificateId = result.certificate.id;
                selectedTemplateId = result.certificate.templateId;
                closeModal();
                renderAll();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message,
                    timer: 1800,
                    showConfirmButton: false,
                });
            }

            async function deleteCertificate(certificate) {
                const result = await showAlert({
                    title: 'Hapus Sertifikat?',
                    html: `<p class="text-sm text-gray-500">Data <strong>${certificate.nomor}</strong> untuk <strong>${certificate.nama}</strong> akan dihapus.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmVariant: 'danger',
                });

                if (!result.isConfirmed) {
                    return;
                }

                const payload = await requestJson(replaceRouteId(certificateDeleteUrl, certificate.id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }, 'Gagal menghapus sertifikat otomatis.');

                certificates = certificates.filter((item) => String(item.id) !== String(certificate.id));
                if (String(selectedCertificateId) === String(certificate.id)) {
                    selectedCertificateId = certificates[0]?.id || null;
                    if (!selectedCertificateId) {
                        selectedTemplateId = templates[0]?.id || null;
                    }
                }

                renderAll();

                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus',
                    text: payload.message,
                    timer: 1600,
                    showConfirmButton: false,
                });
            }

            async function deleteTemplate(template) {
                const result = await showAlert({
                    title: 'Hapus Blangko?',
                    html: `<p class="text-sm text-gray-500">Blangko <strong>${template.name}</strong> akan dihapus.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmVariant: 'danger',
                });

                if (!result.isConfirmed) {
                    return;
                }

                const payload = await requestJson(replaceRouteId(templateDeleteUrl, template.id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }, 'Gagal menghapus blangko.');

                templates = templates.filter((item) => String(item.id) !== String(template.id));
                if (String(selectedTemplateId) === String(template.id)) {
                    selectedTemplateId = templates[0]?.id || null;
                }

                certificates = certificates.map((certificate) => {
                    if (String(certificate.templateId) === String(template.id)) {
                        return {
                            ...certificate,
                            templateId: templates[0]?.id || null,
                        };
                    }

                    return certificate;
                });

                renderAll();

                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus',
                    text: payload.message,
                    timer: 1600,
                    showConfirmButton: false,
                });
            }

            templateList.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('[data-delete-template-id]');
                if (deleteButton) {
                    event.preventDefault();
                    event.stopPropagation();

                    const template = getTemplateById(deleteButton.dataset.deleteTemplateId);
                    if (!template) return;

                    try {
                        await deleteTemplate(template);
                    } catch (error) {
                        console.error(error);
                        showErrorAlert(error.message || 'Gagal menghapus blangko.');
                    }
                    return;
                }

                const button = event.target.closest('[data-template-id]');
                if (!button) return;

                selectedTemplateId = button.dataset.templateId;
                renderAll();
            });

            certificateTableBody.addEventListener('click', async (event) => {
                const button = event.target.closest('button[data-action]');
                if (!button) return;

                const action = button.dataset.action;
                const certificate = getCertById(button.dataset.id);
                if (!certificate) return;

                if (action === 'preview') {
                    selectedCertificateId = certificate.id;
                    selectedTemplateId = certificate.templateId;
                    renderAll();
                    return;
                }

                if (action === 'edit') {
                    openModal('edit', certificate);
                    return;
                }

                if (action === 'pdf') {
                    await exportCertificatePdf(certificate.id);
                    return;
                }

                if (action === 'delete') {
                    try {
                        await deleteCertificate(certificate);
                    } catch (error) {
                        console.error(error);
                        showErrorAlert(error.message || 'Gagal menghapus sertifikat otomatis.');
                    }
                }
            });

            searchCertInput.addEventListener('input', renderCertificateTable);

            [
                settingNomorX,
                settingNomorY,
                settingNomorSize,
                settingNamaX,
                settingNamaY,
                settingNamaSize,
                settingProgramX,
                settingProgramY,
                settingProgramSize,
                settingTanggalX,
                settingTanggalY,
                settingTanggalSize,
            ].forEach((input) => {
                input.addEventListener('input', () => {
                    const template = getTemplateById(selectedTemplateId);
                    if (!template) return;

                    template.settings.nomor.x = Number(settingNomorX.value || 0);
                    template.settings.nomor.y = Number(settingNomorY.value || 0);
                    template.settings.nomor.size = Number(settingNomorSize.value || 0);
                    template.settings.nama.x = Number(settingNamaX.value || 0);
                    template.settings.nama.y = Number(settingNamaY.value || 0);
                    template.settings.nama.size = Number(settingNamaSize.value || 0);
                    template.settings.program.x = Number(settingProgramX.value || 0);
                    template.settings.program.y = Number(settingProgramY.value || 0);
                    template.settings.program.size = Number(settingProgramSize.value || 0);
                    template.settings.tanggal.x = Number(settingTanggalX.value || 0);
                    template.settings.tanggal.y = Number(settingTanggalY.value || 0);
                    template.settings.tanggal.size = Number(settingTanggalSize.value || 0);

                    applyPreviewSettings(template);
                });
            });

            selectedTemplateName.addEventListener('input', () => {
                const template = getTemplateById(selectedTemplateId);
                if (!template) return;
                template.name = selectedTemplateName.value;
                renderTemplateList();
                selectedTemplateBadge.textContent = selectedTemplateName.value || template.name;
            });

            uploadBlangkoInput.addEventListener('change', async (event) => {
                const file = event.target.files?.[0];
                if (!file) return;

                try {
                    await uploadTemplate(file);
                } catch (error) {
                    console.error(error);
                    showErrorAlert(error.message || 'Gagal menambahkan blangko.');
                } finally {
                    event.target.value = '';
                }
            });

            btnSaveTemplateSettings.addEventListener('click', async () => {
                try {
                    await saveTemplateSettings();
                } catch (error) {
                    console.error(error);
                    showErrorAlert(error.message || 'Gagal menyimpan pengaturan blangko.');
                }
            });

            btnOpenCreateCert.addEventListener('click', () => openModal('create'));
            btnCloseCertModal.addEventListener('click', closeModal);
            btnCancelCertModal.addEventListener('click', closeModal);
            certificateModalOverlay.addEventListener('click', closeModal);

            certForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                try {
                    await saveCertificate();
                } catch (error) {
                    console.error(error);
                    showErrorAlert(error.message || 'Gagal menyimpan sertifikat otomatis.');
                }
            });

            btnExportCurrentPdf.addEventListener('click', async () => {
                await exportCertificatePdf(selectedCertificateId);
            });

            renderAll();
        })();
    </script>
    @endpush
</x-layouts.admin>
