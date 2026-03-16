<x-layouts.dashboard :active="'courses'">

<div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('mahasiswa.courses') }}" class="text-blue-500 hover:underline">Kursus</a>
    <span>&rsaquo;</span>
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" class="text-blue-500 hover:underline">{{ $course->nama_course }}</a>
    <span>&rsaquo;</span>
    <a href="{{ route('mahasiswa.assignment-detail', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}" class="text-blue-500 hover:underline">Tugas Akhir</a>
    <span>&rsaquo;</span>
    <span class="font-medium text-gray-800 dark:text-gray-100">Submit</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Submit Tugas</h1>
    <p class="text-gray-500 dark:text-gray-400">{{ $assignment['title'] }}</p>
</div>

<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-600/30 dark:bg-amber-500/10">
    <p class="font-semibold text-amber-800 dark:text-amber-300">Deadline: {{ $assignment['deadline']->format('d F Y, H:i') }}</p>
    <p class="mt-1 text-sm text-amber-700 dark:text-amber-200">Sisa waktu: {{ $assignment['deadline']->diffForHumans() }}</p>
</div>

@if($submission)
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 dark:border-green-600/30 dark:bg-green-500/10">
    <p class="font-semibold text-green-800 dark:text-green-300">Tugas sebelumnya sudah pernah dikirim.</p>
    <p class="mt-1 text-sm text-green-700 dark:text-green-200">Jika Anda unggah ulang, file lama akan diganti dengan file baru.</p>
    <a href="{{ route('mahasiswa.assignment-status', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}" class="mt-3 inline-flex rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700">
        Lihat Status Tugas
    </a>
</div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <h2 class="mb-5 text-lg font-semibold text-gray-800 dark:text-gray-100">Upload File Tugas</h2>

        <div id="dropzone" class="cursor-pointer rounded-2xl border-2 border-dashed border-gray-300 px-6 py-12 text-center transition hover:border-blue-500 hover:bg-blue-50/40 dark:border-gray-600 dark:hover:bg-blue-500/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" style="display:none" accept=".pdf,.docx,.doc,.zip" data-max-size-mb="10" onchange="handleFileSelect(this)">
            <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <p class="mb-1 font-semibold text-gray-800 dark:text-gray-100">Klik atau drag file ke sini</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Format: {{ $assignment['format'] }} • Maksimal {{ $assignment['max_size'] }}</p>
        </div>

        <div id="selectedFile" class="mt-4 hidden rounded-2xl border border-green-200 bg-green-50 p-4 dark:border-green-700/40 dark:bg-green-500/10">
            <div class="flex items-center gap-3">
                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="flex-1">
                    <p id="fileName" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                    <p id="fileSize" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
                <button type="button" onclick="removeFile()" class="text-red-500 transition hover:text-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="mt-5">
            <label for="catatan" class="mb-2 block font-medium text-gray-700 dark:text-gray-300">Catatan untuk dosen</label>
            <textarea id="catatan" rows="5" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-[#111827] dark:text-gray-100" placeholder="Tambahkan catatan jika diperlukan...">{{ old('catatan', $submission?->catatan_mahasiswa) }}</textarea>
        </div>

        <button type="button" id="submit-assignment-btn" onclick="submitAssignment()" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-500 py-3 font-semibold text-white transition hover:bg-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Submit Tugas
        </button>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-100">Informasi Tugas</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Bobot Nilai</span>
                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $assignment['weight'] }}%</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Format</span>
                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $assignment['format'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Ukuran Maks</span>
                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $assignment['max_size'] }}</span>
            </div>
        </div>

        <div class="mt-5 rounded-xl bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
            Pastikan file final dan ukuran tidak melebihi 10MB. Sistem akan menolak file yang terlalu besar.
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleFileSelect(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const maxBytes = 10 * 1024 * 1024;
    if (file.size > maxBytes) {
        Swal.fire({
            icon: 'warning',
            title: 'Ukuran file terlalu besar',
            text: 'Ukuran file maksimal 10MB.',
        });
        input.value = '';
        document.getElementById('selectedFile').classList.add('hidden');
        return;
    }

    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('selectedFile').classList.remove('hidden');
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('selectedFile').classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

async function submitAssignment() {
    const fileInput = document.getElementById('fileInput');
    const notesTextarea = document.getElementById('catatan');
    if (!fileInput.files || !fileInput.files[0]) {
        await Swal.fire({
            icon: 'warning',
            title: 'File belum dipilih',
            text: 'Pilih file tugas terlebih dahulu.',
        });
        return;
    }

    const submitBtn = document.getElementById('submit-assignment-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Mengunggah...';

    try {
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('catatan', notesTextarea.value.trim());

        const response = await fetch('{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal mengirim tugas.');
        }

        await Swal.fire({
            icon: 'success',
            title: 'Tugas berhasil dikirim',
            text: data.message || 'Tugas Anda berhasil disubmit.',
        });

        window.location.href = data.redirect;
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Submit gagal',
            text: error.message || 'Terjadi kesalahan saat mengirim tugas.',
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}
</script>
@endpush

</x-layouts.dashboard>
