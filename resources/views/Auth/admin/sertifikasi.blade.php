<x-layouts.admin title="Sertifikasi Otomatis" active="sertifikasi">
    @php
        $initialTemplates = [
            [
                'id' => 'tpl-1',
                'name' => 'Blangko Biru Classic',
                'kind' => 'gradient',
                'gradient' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 40%, #bfdbfe 100%)',
            ],
            [
                'id' => 'tpl-2',
                'name' => 'Blangko Emas Formal',
                'kind' => 'gradient',
                'gradient' => 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 45%, #fed7aa 100%)',
            ],
        ];

        $initialCertificates = [
            [
                'id' => 'cert-1',
                'nomor' => 'SRT-2026-0001',
                'nama' => 'Rina Kurniawati',
                'program' => 'Kursus Dasar Manajemen Proyek',
                'tanggal' => '2026-03-05',
                'templateId' => 'tpl-1',
            ],
            [
                'id' => 'cert-2',
                'nomor' => 'SRT-2026-0002',
                'nama' => 'Agus Setiawan',
                'program' => 'Webinar Strategi Pembelajaran Digital',
                'tanggal' => '2026-03-08',
                'templateId' => 'tpl-2',
            ],
        ];
    @endphp

    <div
        id="sertifikasiApp"
        data-templates='@json($initialTemplates)'
        data-certificates='@json($initialCertificates)'
        class="space-y-6"
    >
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sertifikat Otomatis</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Frontend UI untuk upload blangko, atur ukuran teks, kelola sertifikat otomatis, dan export PDF.</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="btnOpenCreateCert" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Sertif Otomatis
                </button>
                <button id="btnExportCurrentPdf" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17a2 2 0 002 2h12a2 2 0 002-2M4 7a2 2 0 012-2h8l6 6v6a2 2 0 01-2 2"/></svg>
                    PDF Hasil Sertif
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Preview Sertifikat</h2>
                        <span id="previewMeta" class="text-xs text-gray-500 dark:text-gray-400"></span>
                    </div>

                    <div id="certificateCanvas" class="relative w-full aspect-[16/9] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-inner bg-slate-100">
                        <div id="certificateBgLayer" class="absolute inset-0"></div>
                        <div class="absolute inset-0 bg-slate-100/92"></div>

                        <div class="absolute -top-4 -right-8 w-52 h-24 bg-blue-400/35 rotate-[18deg]"></div>
                        <div class="absolute -top-8 -right-4 w-44 h-20 bg-blue-600/45 rotate-[18deg]"></div>
                        <div class="absolute -bottom-8 -left-8 w-64 h-24 bg-blue-500/40 -rotate-[20deg]"></div>
                        <div class="absolute -bottom-10 -left-2 w-48 h-20 bg-cyan-400/35 -rotate-[20deg]"></div>

                        <div class="absolute inset-4 sm:inset-5 border-[6px] border-blue-700/80"></div>
                        <div class="absolute inset-7 sm:inset-9 border-[5px] border-blue-100/90"></div>

                        <div class="absolute inset-0 px-7 py-6 sm:px-12 sm:py-8 md:px-16 md:py-10 flex flex-col items-center text-center text-slate-800">
                            <div class="w-full flex items-start justify-between">
                                <div class="flex flex-col items-center gap-0.5">
                                    <img
                                        src="{{ asset('assets/image/Logo/Logo_Universitas_Terbuka.png') }}"
                                        alt="Logo Universitas Terbuka Kiri"
                                        class="w-9 h-9 sm:w-11 sm:h-11 object-contain"
                                    >
                                    <span class="text-[10px] font-semibold tracking-[0.14em] text-slate-500">LOGO</span>
                                </div>

                                <div class="flex-1 px-3 sm:px-8">
                                    <h3 class="text-3xl sm:text-5xl font-black tracking-[0.12em] text-[#1d355d]">SERTIFIKAT</h3>
                                    <p class="mt-1 text-base sm:text-3xl tracking-[0.08em] text-[#1d355d]" style="font-family: 'Times New Roman', serif;">PENGHARGAAN</p>
                                </div>

                                <div class="flex flex-col items-center gap-0.5">
                                    <img
                                        src="{{ asset('assets/image/Logo/Logo_Universitas_Terbuka.png') }}"
                                        alt="Logo Universitas Terbuka Kanan"
                                        class="w-9 h-9 sm:w-11 sm:h-11 object-contain"
                                    >
                                    <span class="text-[10px] font-semibold tracking-[0.14em] text-slate-500">LOGO</span>
                                </div>
                            </div>

                            <p class="mt-4 text-sm sm:text-2xl text-slate-700">Dengan bangga diberikan kepada:</p>

                            <p id="previewNama" class="mt-2 leading-tight text-[#c79b3b]" style="font-family: 'Brush Script MT', 'Times New Roman', serif; font-size: 52px; font-weight: 400;">Nama Peserta</p>
                            <div class="w-40 sm:w-96 border-t border-slate-400/80 mt-1"></div>

                            <p class="mt-3 text-sm sm:text-2xl text-slate-700">Sebagai :</p>
                            <p id="previewRole" class="mt-1 text-xl sm:text-5xl font-black italic tracking-wide text-[#1d355d]">PESERTA PROGRAM</p>

                            <p id="previewProgram" class="mt-4 px-4 max-w-3xl text-xs sm:text-xl text-slate-700 leading-relaxed">
                                Telah menyelesaikan aktivitas pembelajaran pada platform LMS dengan hasil yang sangat baik.
                            </p>

                            <div class="mt-auto w-full grid grid-cols-3 items-end gap-3 sm:gap-6">
                                <div class="text-center">
                                    <div class="border-t border-slate-500/80 pt-1.5">
                                        <p class="text-sm sm:text-2xl font-bold text-[#1d355d]">NAMA</p>
                                        <p class="text-xs sm:text-lg text-slate-700">Pimpinan Yayasan</p>
                                    </div>
                                </div>

                                <div class="text-center pb-1">
                                    <p class="text-2xl sm:text-5xl text-amber-500 leading-none">❦</p>
                                    <p id="previewNomor" class="mt-1 text-[10px] sm:text-sm font-semibold tracking-wide text-slate-700">SRT-XXXX</p>
                                    <p id="previewTanggal" class="text-[10px] sm:text-sm text-slate-600">-</p>
                                </div>

                                <div class="text-center">
                                    <div class="border-t border-slate-500/80 pt-1.5">
                                        <p class="text-sm sm:text-2xl font-bold text-[#1d355d]">NAMA</p>
                                        <p class="text-xs sm:text-lg text-slate-700">Ketua Panitia</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 sm:p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Daftar Sertif Otomatis</h2>
                        <input id="searchCertInput" type="text" placeholder="Cari nomor, nama, atau program..." class="w-full sm:w-72 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px]">
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
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Tambah Blangko</h2>
                        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-black text-white text-xs font-semibold cursor-pointer transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Upload
                            <input id="uploadBlangkoInput" type="file" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Blangko yang diupload bersifat frontend-only (simulasi UI).</p>

                    <div id="templateList" class="space-y-2"></div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl p-4 sm:p-5">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Ukuran Teks Otomatis</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                <span>Nama Peserta</span>
                                <span id="fontSizeNamaLabel">52 px</span>
                            </div>
                            <input id="fontSizeNama" type="range" min="30" max="72" value="52" class="w-full">
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                <span>Nomor Sertif</span>
                                <span id="fontSizeNomorLabel">28 px</span>
                            </div>
                            <input id="fontSizeNomor" type="range" min="14" max="44" value="28" class="w-full">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="certificateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="certificateModalOverlay"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button id="btnCloseCertModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <form id="certificateForm" class="p-6">
                    <input id="certFormMode" type="hidden" value="create">
                    <input id="certFormId" type="hidden" value="">

                    <h3 id="certModalTitle" class="text-lg font-bold text-gray-900 dark:text-white">Tambah Sertif Otomatis</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Isi data sertifikat. Nomor bisa otomatis.</p>

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

                    <div class="mt-6 flex justify-end gap-2">
                        <button id="btnCancelCertModal" type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script>
        (() => {
            const app = document.getElementById('sertifikasiApp');
            if (!app) return;

            let templates = JSON.parse(app.dataset.templates || '[]');
            let certificates = JSON.parse(app.dataset.certificates || '[]');
            let selectedTemplateId = templates[0]?.id || null;
            let selectedCertificateId = certificates[0]?.id || null;

            const templateList = document.getElementById('templateList');
            const certificateTableBody = document.getElementById('certificateTableBody');
            const searchCertInput = document.getElementById('searchCertInput');

            const previewMeta = document.getElementById('previewMeta');
            const previewNama = document.getElementById('previewNama');
            const previewNomor = document.getElementById('previewNomor');
            const previewRole = document.getElementById('previewRole');
            const previewProgram = document.getElementById('previewProgram');
            const previewTanggal = document.getElementById('previewTanggal');
            const certificateBgLayer = document.getElementById('certificateBgLayer');

            const fontSizeNama = document.getElementById('fontSizeNama');
            const fontSizeNomor = document.getElementById('fontSizeNomor');
            const fontSizeNamaLabel = document.getElementById('fontSizeNamaLabel');
            const fontSizeNomorLabel = document.getElementById('fontSizeNomorLabel');

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

            function getTemplateById(id) {
                return templates.find(t => t.id === id) || templates[0] || null;
            }

            function getCertById(id) {
                return certificates.find(c => c.id === id) || certificates[0] || null;
            }

            function formatDateIndonesia(isoDate) {
                if (!isoDate) return '-';
                const dt = new Date(isoDate);
                return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            }

            function getLmsCertificateCopy(programName) {
                const text = (programName || '').toLowerCase();

                if (text.includes('webinar')) {
                    return {
                        role: 'PESERTA WEBINAR',
                        statement: `Telah mengikuti webinar ${programName} pada platform LMS dan menunjukkan partisipasi yang sangat baik.`,
                    };
                }

                if (text.includes('kursus') || text.includes('course')) {
                    return {
                        role: 'LULUS COURSE',
                        statement: `Telah menyelesaikan course ${programName} pada platform LMS sesuai standar kelulusan yang ditetapkan.`,
                    };
                }

                return {
                    role: 'PESERTA PROGRAM',
                    statement: `Telah menjalankan program ${programName} pada platform LMS dengan dedikasi dan performa yang baik.`,
                };
            }

            function renderTemplateOptions() {
                inputTemplateId.innerHTML = templates.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            }

            function renderTemplateList() {
                templateList.innerHTML = templates.map((tpl) => {
                    const isActive = tpl.id === selectedTemplateId;
                    const bgStyle = tpl.kind === 'image'
                        ? `background-image:url('${tpl.image}');background-size:cover;background-position:center;`
                        : `background:${tpl.gradient};`;

                    return `
                        <button type="button" data-template-id="${tpl.id}" class="w-full text-left p-2 rounded-lg border ${isActive ? 'border-blue-500 ring-2 ring-blue-100 dark:ring-blue-900/30' : 'border-gray-200 dark:border-gray-600'} hover:border-blue-400 transition">
                            <div class="h-16 rounded-md" style="${bgStyle}"></div>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">${tpl.name}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full ${isActive ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'}">${isActive ? 'Dipakai' : 'Pilih'}</span>
                            </div>
                        </button>
                    `;
                }).join('');
            }

            function renderCertificateTable() {
                const keyword = (searchCertInput.value || '').toLowerCase().trim();
                const filtered = certificates.filter(c => {
                    const haystack = `${c.nomor} ${c.nama} ${c.program}`.toLowerCase();
                    return haystack.includes(keyword);
                });

                certificateTableBody.innerHTML = filtered.map((c) => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-200">${c.nomor}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">${c.nama}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">${c.program}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">${formatDateIndonesia(c.tanggal)}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" data-action="preview" data-id="${c.id}" class="px-2.5 py-1 text-xs rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200">Preview</button>
                                <button type="button" data-action="edit" data-id="${c.id}" class="px-2.5 py-1 text-xs rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">Edit</button>
                                <button type="button" data-action="pdf" data-id="${c.id}" class="px-2.5 py-1 text-xs rounded-md bg-emerald-100 text-emerald-700 hover:bg-emerald-200">PDF</button>
                                <button type="button" data-action="delete" data-id="${c.id}" class="px-2.5 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200">Hapus</button>
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
            }

            function renderPreview() {
                const cert = getCertById(selectedCertificateId);
                if (!cert) {
                    previewNama.textContent = 'Nama Peserta';
                    previewNomor.textContent = 'SRT-XXXX';
                    previewRole.textContent = 'PESERTA PROGRAM';
                    previewProgram.textContent = 'Telah menjalankan aktivitas pembelajaran pada platform LMS dengan hasil yang sangat baik.';
                    previewTanggal.textContent = '-';
                    previewMeta.textContent = 'Belum ada data';
                    return;
                }

                const tpl = getTemplateById(cert.templateId || selectedTemplateId);
                if (tpl) {
                    selectedTemplateId = tpl.id;
                    if (tpl.kind === 'image') {
                        certificateBgLayer.style.backgroundImage = `url('${tpl.image}')`;
                        certificateBgLayer.style.backgroundSize = 'cover';
                        certificateBgLayer.style.backgroundPosition = 'center';
                        certificateBgLayer.style.backgroundColor = 'transparent';
                    } else {
                        certificateBgLayer.style.background = tpl.gradient;
                        certificateBgLayer.style.backgroundImage = 'none';
                    }
                }

                const copy = getLmsCertificateCopy(cert.program);

                previewNama.textContent = cert.nama;
                previewNomor.textContent = cert.nomor;
                previewRole.textContent = copy.role;
                previewProgram.textContent = copy.statement;
                previewTanggal.textContent = formatDateIndonesia(cert.tanggal);
                previewMeta.textContent = `${cert.nomor} - ${cert.nama}`;
            }

            function renderAll() {
                renderTemplateOptions();
                renderTemplateList();
                renderCertificateTable();
                renderPreview();
            }

            function openModal(mode, cert = null) {
                certFormMode.value = mode;
                certFormId.value = cert?.id || '';
                certModalTitle.textContent = mode === 'edit' ? 'Edit Sertif Otomatis' : 'Tambah Sertif Otomatis';

                inputNomor.value = cert?.nomor || '';
                inputNama.value = cert?.nama || '';
                inputProgram.value = cert?.program || '';
                inputTanggal.value = cert?.tanggal || new Date().toISOString().slice(0, 10);
                inputTemplateId.value = cert?.templateId || selectedTemplateId || templates[0]?.id || '';

                certificateModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                certificateModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            function generateNomor() {
                const year = new Date().getFullYear();
                const count = certificates.length + 1;
                return `SRT-${year}-${String(count).padStart(4, '0')}`;
            }

            async function exportCertificatePdf(certId) {
                const cert = getCertById(certId || selectedCertificateId);
                if (!cert) {
                    Swal.fire('Info', 'Pilih data sertifikat terlebih dahulu.', 'info');
                    return;
                }

                selectedCertificateId = cert.id;
                renderPreview();

                const canvasNode = document.getElementById('certificateCanvas');
                try {
                    const canvas = await html2canvas(canvasNode, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                    });

                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();

                    pdf.addImage(imgData, 'PNG', 0, 0, pageWidth, pageHeight);
                    pdf.save(`${cert.nomor}.pdf`);
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'Gagal membuat PDF. Coba ulangi lagi.', 'error');
                }
            }

            templateList.addEventListener('click', (e) => {
                const button = e.target.closest('[data-template-id]');
                if (!button) return;

                selectedTemplateId = button.dataset.templateId;
                const cert = getCertById(selectedCertificateId);
                if (cert) {
                    cert.templateId = selectedTemplateId;
                }
                renderAll();
            });

            certificateTableBody.addEventListener('click', (e) => {
                const button = e.target.closest('button[data-action]');
                if (!button) return;

                const action = button.dataset.action;
                const cert = getCertById(button.dataset.id);
                if (!cert) return;

                if (action === 'preview') {
                    selectedCertificateId = cert.id;
                    selectedTemplateId = cert.templateId;
                    renderAll();
                }

                if (action === 'edit') {
                    openModal('edit', cert);
                }

                if (action === 'pdf') {
                    exportCertificatePdf(cert.id);
                }

                if (action === 'delete') {
                    Swal.fire({
                        title: 'Hapus Sertifikat?',
                        html: `<p class="text-sm text-gray-500">Data <strong>${cert.nomor}</strong> untuk <strong>${cert.nama}</strong> akan dihapus.</p>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        certificates = certificates.filter(c => c.id !== cert.id);
                        if (selectedCertificateId === cert.id) {
                            selectedCertificateId = certificates[0]?.id || null;
                        }
                        renderAll();
                    });
                }
            });

            searchCertInput.addEventListener('input', renderCertificateTable);

            fontSizeNama.addEventListener('input', () => {
                const value = fontSizeNama.value;
                previewNama.style.fontSize = `${value}px`;
                fontSizeNamaLabel.textContent = `${value} px`;
            });

            fontSizeNomor.addEventListener('input', () => {
                const value = fontSizeNomor.value;
                previewNomor.style.fontSize = `${value}px`;
                fontSizeNomorLabel.textContent = `${value} px`;
            });

            uploadBlangkoInput.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                const objectUrl = URL.createObjectURL(file);
                const template = {
                    id: `tpl-${Date.now()}`,
                    name: file.name.replace(/\.[^/.]+$/, ''),
                    kind: 'image',
                    image: objectUrl,
                };

                templates.unshift(template);
                selectedTemplateId = template.id;

                const cert = getCertById(selectedCertificateId);
                if (cert) cert.templateId = template.id;

                renderAll();
                e.target.value = '';
            });

            btnOpenCreateCert.addEventListener('click', () => openModal('create'));
            btnCloseCertModal.addEventListener('click', closeModal);
            btnCancelCertModal.addEventListener('click', closeModal);
            certificateModalOverlay.addEventListener('click', closeModal);

            certForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const payload = {
                    id: certFormMode.value === 'edit' ? certFormId.value : `cert-${Date.now()}`,
                    nomor: (inputNomor.value || '').trim() || generateNomor(),
                    nama: (inputNama.value || '').trim(),
                    program: (inputProgram.value || '').trim(),
                    tanggal: inputTanggal.value,
                    templateId: inputTemplateId.value,
                };

                if (!payload.nama || !payload.program || !payload.tanggal || !payload.templateId) {
                    Swal.fire('Validasi', 'Lengkapi semua field wajib.', 'warning');
                    return;
                }

                if (certFormMode.value === 'edit') {
                    certificates = certificates.map(c => c.id === payload.id ? payload : c);
                } else {
                    certificates.unshift(payload);
                }

                selectedCertificateId = payload.id;
                selectedTemplateId = payload.templateId;

                closeModal();
                renderAll();
            });

            btnExportCurrentPdf.addEventListener('click', () => exportCertificatePdf(selectedCertificateId));

            renderAll();
        })();
    </script>
    @endpush
</x-layouts.admin>
