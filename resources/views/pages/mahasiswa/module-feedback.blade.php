<x-layouts.dashboard :active="'courses'">

{{-- Header --}}
<div style="text-align: center; margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Feedback & Nilai</h1>
    <p style="color: #6b7280;">Hasil evaluasi pembelajaran Anda</p>
</div>

{{-- Info Bar --}}
<div style="display: flex; justify-content: center; gap: 48px; margin-bottom: 32px; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <span style="color: #6b7280; font-size: 14px;">Kursus:</span>
        <span style="font-weight: 600; color: #1f2937;">{{ $course->nama_course }}</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <span style="color: #6b7280; font-size: 14px;">Modul:</span>
        <span style="font-weight: 600; color: #1f2937;">{{ $feedback['module_name'] }}</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span style="color: #6b7280; font-size: 14px;">Dosen:</span>
        <span style="font-weight: 600; color: #1f2937;">{{ $feedback['instructor']['name'] }}</span>
    </div>
</div>

{{-- Score Card --}}
<div style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 20px; padding: 40px; text-align: center; max-width: 600px; margin: 0 auto 32px; border: 1px solid #e5e7eb;">
    <div style="margin-bottom: 16px;">
        <span style="font-size: 64px; font-weight: 700; color: #3b82f6;">{{ $feedback['total_score'] }}</span>
        <span style="font-size: 24px; color: #9ca3af;">/{{ $feedback['max_score'] }}</span>
    </div>
    
    @if($feedback['is_passed'])
    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; background: #d1fae5; color: #059669; border-radius: 50px; font-weight: 600; font-size: 14px; margin-bottom: 20px;">
        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Lulus
    </div>
    @else
    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; background: #fee2e2; color: #dc2626; border-radius: 50px; font-weight: 600; font-size: 14px; margin-bottom: 20px;">
        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Tidak Lulus
    </div>
    @endif
    
    <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; max-width: 400px; margin: 0 auto 16px;">
        <div style="height: 100%; width: {{ $feedback['total_score'] }}%; background: linear-gradient(90deg, #3b82f6, #06b6d4); border-radius: 4px;"></div>
    </div>
    
    <p style="color: #6b7280; font-size: 14px;">Nilai ini merupakan hasil evaluasi dari kuis dan tugas akhir.</p>
</div>

{{-- Grade Breakdown --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; max-width: 600px; margin: 0 auto 24px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
        <svg style="width: 20px; height: 20px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h2 style="font-size: 18px; font-weight: 600; color: #1f2937;">Rincian Penilaian</h2>
    </div>
    
    @foreach($feedback['grade_breakdown'] as $item)
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 8px; height: 8px; background: {{ $loop->first ? '#3b82f6' : '#22c55e' }}; border-radius: 50%;"></div>
            <span style="color: #4b5563;">{{ $item['name'] }} ({{ $item['weight'] }}%)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 80px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                <div style="height: 100%; width: {{ $item['score'] }}%; background: {{ $loop->first ? '#3b82f6' : '#22c55e' }}; border-radius: 3px;"></div>
            </div>
            <span style="font-weight: 600; color: #1f2937; min-width: 60px; text-align: right;">{{ $item['score'] }}/{{ $item['max_score'] }}</span>
        </div>
    </div>
    @endforeach
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px;">
        <span style="font-weight: 600; color: #1f2937;">Total Nilai</span>
        <span style="font-size: 24px; font-weight: 700; color: #3b82f6;">{{ $feedback['total_score'] }}/{{ $feedback['max_score'] }}</span>
    </div>
</div>

{{-- Instructor Feedback --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; max-width: 600px; margin: 0 auto 24px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <svg style="width: 20px; height: 20px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <h2 style="font-size: 18px; font-weight: 600; color: #1f2937;">Feedback Dosen</h2>
    </div>
    
    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
        <span style="font-weight: 600; color: #1f2937;">{{ $feedback['instructor']['name'] }}</span>
        <span style="color: #9ca3af; font-size: 14px;">{{ $feedback['instructor_feedback']['date']->format('d F Y') }}</span>
    </div>
    
    <p style="color: #4b5563; line-height: 1.7; margin-bottom: 16px;">{{ $feedback['instructor_feedback']['text'] }}</p>
    
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @foreach($feedback['instructor_feedback']['tags'] as $tag)
        @if($tag['type'] === 'positive')
        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #d1fae5; color: #059669; border-radius: 50px; font-size: 12px; font-weight: 500;">
            <span style="color: #10b981;">✓</span> {{ $tag['text'] }}
        </span>
        @else
        <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background: #fef3c7; color: #d97706; border-radius: 50px; font-size: 12px; font-weight: 500;">
            <span>⚠</span> {{ $tag['text'] }}
        </span>
        @endif
        @endforeach
    </div>
</div>

{{-- Personal Notes --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; max-width: 600px; margin: 0 auto 32px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <svg style="width: 20px; height: 20px; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        <h2 style="font-size: 18px; font-weight: 600; color: #1f2937;">Catatan Pribadi</h2>
    </div>
    
    <p style="color: #4b5563; line-height: 1.7; margin-bottom: 16px;">{{ $feedback['personal_notes'] }}</p>
    
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="display: inline-flex; align-items: center; gap: 4px; color: #3b82f6; font-size: 14px; text-decoration: none;">
        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Edit Catatan
    </a>
</div>

{{-- Navigation Buttons --}}
<div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #374151; border: 1px solid #d1d5db; border-radius: 12px; font-weight: 600; text-decoration: none;">
        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>
        Kembali ke Modul
    </a>
    
    <a href="{{ route('mahasiswa.courses') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #3b82f6; color: white; border-radius: 12px; font-weight: 600; text-decoration: none;">
        Lihat Sertifikat
    </a>
</div>

</x-layouts.dashboard>
