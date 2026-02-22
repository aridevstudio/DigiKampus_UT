<x-layouts.dashboard :active="'courses'">

{{-- Breadcrumb --}}
<div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; margin-bottom: 16px;">
    <a href="{{ route('mahasiswa.courses') }}" style="color: #3b82f6; text-decoration: none;">Kursus</a>
    <span>›</span>
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="color: #3b82f6; text-decoration: none;">{{ $course->nama_course }}</a>
    <span>›</span>
    <a href="{{ route('mahasiswa.assignment-detail', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}" style="color: #3b82f6; text-decoration: none;">Tugas Akhir</a>
    <span>›</span>
    <span style="color: #1f2937; font-weight: 500;">Submit</span>
</div>

{{-- Page Header --}}
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Submit Tugas</h1>
    <p style="color: #6b7280;">{{ $assignment['title'] }}</p>
</div>

{{-- Deadline Alert --}}
<div style="background: #fef3c7; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border: 1px solid #fcd34d;">
    <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        <p style="font-weight: 600; color: #92400e;">Deadline: {{ $assignment['deadline']->format('d F Y, H:i') }}</p>
        <p style="font-size: 14px; color: #b45309;">Sisa waktu: {{ $assignment['deadline']->diffForHumans() }}</p>
    </div>
</div>

<div style="display: flex; gap: 24px; flex-wrap: wrap;">
    {{-- Left: Upload Form --}}
    <div style="flex: 1; min-width: 400px;">
        <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
            <h2 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Upload File Tugas</h2>
            
            {{-- Upload Area --}}
            <div id="dropzone" style="border: 2px dashed #d1d5db; border-radius: 12px; padding: 48px; text-align: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#3b82f6'; this.style.background='#eff6ff'" onmouseout="this.style.borderColor='#d1d5db'; this.style.background='white'" onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" style="display: none;" accept=".pdf,.docx,.doc,.zip" onchange="handleFileSelect(this)">
                <svg style="width: 48px; height: 48px; color: #9ca3af; margin: 0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p style="font-weight: 600; color: #1f2937; margin-bottom: 8px;">Klik atau drag file ke sini</p>
                <p style="font-size: 14px; color: #6b7280;">Format: {{ $assignment['format'] }} • Maks: {{ $assignment['max_size'] }}</p>
            </div>
            
            {{-- Selected File Display --}}
            <div id="selectedFile" style="display: none; margin-top: 16px; padding: 16px; background: #f0fdf4; border-radius: 12px; border: 1px solid #86efac;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 32px; height: 32px; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div style="flex: 1;">
                        <p id="fileName" style="font-weight: 600; color: #1f2937;"></p>
                        <p id="fileSize" style="font-size: 14px; color: #6b7280;"></p>
                    </div>
                    <button type="button" onclick="removeFile()" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Notes --}}
            <div style="margin-top: 20px;">
                <label style="display: block; font-weight: 600; color: #1f2937; margin-bottom: 8px;">Catatan (Opsional)</label>
                <textarea style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; resize: vertical; min-height: 100px; font-family: inherit;" placeholder="Tambahkan catatan untuk dosen..."></textarea>
            </div>
            
            {{-- Submit Button --}}
            <button type="button" onclick="submitAssignment()" style="width: 100%; margin-top: 20px; padding: 14px; background: #3b82f6; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Submit Tugas
            </button>
        </div>
    </div>
    
    {{-- Right: Info --}}
    <div style="width: 320px;">
        <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Informasi Tugas</h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 14px;">Bobot Nilai</span>
                    <span style="font-weight: 600; color: #1f2937;">{{ $assignment['weight'] }}%</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 14px;">Format</span>
                    <span style="font-weight: 600; color: #1f2937;">{{ $assignment['format'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 14px;">Ukuran Maks</span>
                    <span style="font-weight: 600; color: #1f2937;">{{ $assignment['max_size'] }}</span>
                </div>
            </div>
            
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">
            
            <div style="background: #f0f9ff; border-radius: 8px; padding: 12px;">
                <p style="font-size: 13px; color: #0369a1; line-height: 1.6;">
                    💡 Pastikan file yang diupload sudah final. Anda hanya dapat mengirim ulang sebelum deadline.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        document.getElementById('selectedFile').style.display = 'block';
    }
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('selectedFile').style.display = 'none';
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function submitAssignment() {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files || !fileInput.files[0]) {
        alert('Pilih file terlebih dahulu!');
        return;
    }

    const submitBtn = document.querySelector('button[onclick="submitAssignment()"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Mengunggah...';
    submitBtn.style.opacity = '0.7';

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    const notesTextarea = document.querySelector('textarea');
    if (notesTextarea && notesTextarea.value.trim()) {
        formData.append('catatan', notesTextarea.value.trim());
    }

    fetch('{{ route('mahasiswa.submit-assignment', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Tugas berhasil disubmit!');
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Gagal mengirim tugas. Silakan coba lagi.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            submitBtn.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        alert('Terjadi kesalahan saat mengirim tugas. Periksa koneksi dan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        submitBtn.style.opacity = '1';
    });
}
</script>

</x-layouts.dashboard>
