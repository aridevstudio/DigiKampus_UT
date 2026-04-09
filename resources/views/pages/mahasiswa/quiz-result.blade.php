<x-layouts.dashboard :active="'courses'">

<div style="min-height: 80vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px;">
    <div style="text-align: center; margin-bottom: 32px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">{{ $result['quiz_title'] }}</h1>
        <p style="color: #6b7280;">{{ $course->nama_course }} • {{ $result['module_name'] }}</p>
    </div>

    <div style="background: white; border-radius: 20px; padding: 48px 64px; border: 1px solid #e5e7eb; text-align: center; max-width: 560px; width: 100%;">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 72px; font-weight: 700; color: #3b82f6;">{{ $result['score'] }}</span>
            <span style="font-size: 24px; color: #9ca3af;">/{{ $result['total_score'] }}</span>
        </div>

        @if($result['is_passed'])
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; background: #d1fae5; color: #059669; border-radius: 50px; font-weight: 600; margin-bottom: 24px;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Lulus
        </div>
        @else
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; background: #fee2e2; color: #dc2626; border-radius: 50px; font-weight: 600; margin-bottom: 24px;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tidak Lulus
        </div>
        @endif

        <div style="margin-bottom: 24px;">
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: {{ $result['score'] }}%; background: linear-gradient(90deg, #3b82f6, #06b6d4); border-radius: 4px;"></div>
            </div>
        </div>

        <div style="display: grid; gap: 10px; color: #6b7280; font-size: 14px; text-align: left; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 16px; padding: 18px;">
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <span>Jawaban benar</span>
                <strong style="color: #1f2937;">{{ $result['correct_answers'] }}/{{ $result['total_questions'] }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <span>Poin diperoleh</span>
                <strong style="color: #1f2937;">{{ $result['earned_points'] }}/{{ $result['max_points'] }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <span>Nilai lulus</span>
                <strong style="color: #1f2937;">{{ $result['passing_score'] }}</strong>
            </div>
            @if(!empty($result['time_taken']))
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <span>Waktu pengerjaan</span>
                <strong style="color: #1f2937;">{{ $result['time_taken'] }}</strong>
            </div>
            @endif
            @if(!empty($result['submitted_at']))
            <div style="display: flex; justify-content: space-between; gap: 12px;">
                <span>Dikerjakan pada</span>
                <strong style="color: #1f2937;">{{ $result['submitted_at']->format('d M Y H:i') }}</strong>
            </div>
            @endif
        </div>
    </div>

    <div style="display: flex; gap: 16px; margin-top: 32px; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #374151; border: 1px solid #d1d5db; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Kembali ke Modul
        </a>

        <a href="{{ route('mahasiswa.module-feedback', ['courseId' => $course->id_course, 'moduleId' => $result['module_id']]) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Lihat Feedback Kuiz
        </a>
    </div>
</div>

</x-layouts.dashboard>
