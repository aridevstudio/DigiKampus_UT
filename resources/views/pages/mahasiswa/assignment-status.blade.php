<x-layouts.dashboard :active="'courses'">

{{-- Breadcrumb --}}
<div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; margin-bottom: 16px;">
    <a href="{{ route('mahasiswa.courses') }}" style="color: #3b82f6; text-decoration: none;">Kursus</a>
    <span>›</span>
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="color: #3b82f6; text-decoration: none;">{{ $course->nama_course }}</a>
    <span>›</span>
    <span style="color: #1f2937; font-weight: 500;">Tugas Akhir</span>
</div>

{{-- Page Header --}}
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Tugas Akhir</h1>
    <p style="color: #6b7280;">Status pengumpulan tugas akhir (opsional).</p>
</div>

{{-- Ringkasan Tugas --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Ringkasan Tugas</h2>
    
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6;">
            <span style="color: #6b7280;">Status:</span>
            @if($submission['status'] === 'pending')
            <span style="color: #f59e0b; font-weight: 600;">Menunggu Penilaian</span>
            @elseif($submission['status'] === 'graded')
            <span style="color: #22c55e; font-weight: 600;">Sudah Dinilai</span>
            @endif
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6;">
            <span style="color: #6b7280;">Dikumpulkan:</span>
            <span style="font-weight: 600; color: #1f2937;">{{ $submission['submitted_at']->format('d M Y, H:i') }}</span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #6b7280;">File:</span>
            <span style="font-weight: 600; color: #1f2937;">{{ $submission['file_name'] }}</span>
        </div>
    </div>
</div>

{{-- Feedback Dosen --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <div style="width: 48px; height: 48px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 24px; height: 24px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
        <h2 style="font-size: 20px; font-weight: 600; color: #1f2937;">Feedback Dosen</h2>
    </div>
    
    @if($submission['status'] === 'pending')
    {{-- Waiting for grading --}}
    <div style="text-align: center; padding: 40px 20px;">
        <div style="width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg style="width: 32px; height: 32px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p style="font-size: 18px; font-weight: 600; color: #4b5563; margin-bottom: 8px;">Menunggu penilaian dari dosen</p>
        <p style="color: #9ca3af;">Feedback akan muncul setelah dosen menyelesaikan penilaian</p>
    </div>
    @else
    {{-- Graded --}}
    <div style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="background: #dcfce7; border-radius: 12px; padding: 16px 24px; text-align: center;">
                <p style="font-size: 12px; color: #16a34a; margin-bottom: 4px;">Nilai</p>
                <p style="font-size: 32px; font-weight: 700; color: #16a34a;">{{ $submission['grade'] }}</p>
            </div>
            <div>
                <p style="font-size: 14px; color: #6b7280;">Dinilai pada:</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $submission['graded_at']?->format('d M Y, H:i') }}</p>
            </div>
        </div>
        
        <div style="background: #f9fafb; border-radius: 12px; padding: 16px;">
            <p style="font-weight: 600; color: #1f2937; margin-bottom: 8px;">Komentar Dosen:</p>
            <p style="color: #4b5563; line-height: 1.7;">{{ $submission['feedback'] }}</p>
        </div>
    </div>
    @endif
</div>

</x-layouts.dashboard>
