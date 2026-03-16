<x-layouts.dashboard :active="'courses'">

<div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
    <a href="{{ route('mahasiswa.courses') }}" class="text-blue-500 hover:underline">Kursus</a>
    <span>&rsaquo;</span>
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" class="text-blue-500 hover:underline">{{ $course->nama_course }}</a>
    <span>&rsaquo;</span>
    <span class="font-medium text-gray-800 dark:text-gray-100">Status Tugas</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Status Pengumpulan Tugas</h1>
    <p class="text-gray-500 dark:text-gray-400">{{ $submission->material?->judul_material ?? 'Tugas Akhir' }}</p>
</div>

<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <h2 class="mb-5 text-lg font-semibold text-gray-800 dark:text-gray-100">Ringkasan Pengumpulan</h2>

    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Status</span>
            @if($submission->status === 'reviewed')
            <span class="font-semibold text-green-600 dark:text-green-400">Sudah Direview</span>
            @elseif($submission->status === 'revision_requested')
            <span class="font-semibold text-amber-600 dark:text-amber-400">Perlu Revisi</span>
            @else
            <span class="font-semibold text-yellow-600 dark:text-yellow-400">Menunggu Review</span>
            @endif
        </div>

        <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Dikumpulkan</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ optional($submission->submitted_at)->format('d M Y, H:i') }}</span>
        </div>

        <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">File</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $submission->original_file_name }}</span>
        </div>

        <div class="flex items-center justify-between">
            <span class="text-gray-500 dark:text-gray-400">Ukuran</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ number_format(($submission->file_size ?? 0) / 1024 / 1024, 2) }} MB</span>
        </div>
    </div>
</div>

<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <div class="mb-5 flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/20">
            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Feedback Dosen</h2>
    </div>

    @if($submission->catatan_dosen)
    <div class="rounded-2xl bg-blue-50 p-4 text-sm leading-relaxed text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
        {{ $submission->catatan_dosen }}
    </div>
    @else
    <div class="rounded-2xl bg-gray-50 p-6 text-center dark:bg-gray-800/50">
        <p class="font-medium text-gray-700 dark:text-gray-200">Belum ada feedback dari dosen</p>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Feedback akan muncul setelah dosen menyelesaikan review.</p>
    </div>
    @endif
</div>

@if($submission->catatan_mahasiswa)
<div class="mt-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <h2 class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-100">Catatan Anda</h2>
    <p class="whitespace-pre-line text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $submission->catatan_mahasiswa }}</p>
</div>
@endif

</x-layouts.dashboard>
