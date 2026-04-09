<x-layouts.dashboard :active="'courses'">

<div style="max-width: 960px; margin: 0 auto; padding: 32px 20px 56px;">
    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">Feedback & Nilai Kuiz</h1>
        <p style="color: #6b7280;">{{ $course->nama_course }} • {{ $feedback['module_name'] }}</p>
    </div>

    <div style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 20px; border: 1px solid #e5e7eb; padding: 28px; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; gap: 20px; flex-wrap: wrap; align-items: center;">
            <div>
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 6px;">Kuiz</p>
                <h2 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 10px;">{{ $feedback['quiz_title'] }}</h2>
                <p style="font-size: 14px; color: #6b7280;">Dikerjakan pada {{ $feedback['submitted_at']?->format('d M Y H:i') }}</p>
            </div>
            <div style="text-align: right; min-width: 220px;">
                <div style="margin-bottom: 10px;">
                    <span style="font-size: 56px; font-weight: 700; color: #3b82f6;">{{ $feedback['score'] }}</span>
                    <span style="font-size: 22px; color: #9ca3af;">/{{ $feedback['max_score'] }}</span>
                </div>
                @if($feedback['is_passed'])
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #d1fae5; color: #059669; border-radius: 999px; font-weight: 600; font-size: 14px;">Lulus</span>
                @else
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fee2e2; color: #dc2626; border-radius: 999px; font-weight: 600; font-size: 14px;">Belum Lulus</span>
                @endif
            </div>
        </div>

        <div style="height: 8px; background: #e5e7eb; border-radius: 999px; overflow: hidden; margin: 22px 0 18px;">
            <div style="height: 100%; width: {{ $feedback['score'] }}%; background: linear-gradient(90deg, #3b82f6, #06b6d4); border-radius: 999px;"></div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px;">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Jawaban benar</div>
                <div style="font-size: 20px; font-weight: 700; color: #1f2937;">{{ $feedback['correct_answers'] }}/{{ $feedback['total_questions'] }}</div>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px;">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Poin diperoleh</div>
                <div style="font-size: 20px; font-weight: 700; color: #1f2937;">{{ $feedback['earned_points'] }}/{{ $feedback['max_points'] }}</div>
            </div>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px;">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Nilai lulus</div>
                <div style="font-size: 20px; font-weight: 700; color: #1f2937;">{{ $feedback['passing_score'] }}</div>
            </div>
        </div>
    </div>

    <div style="background: white; border-radius: 18px; border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h3 style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 10px;">Ringkasan</h3>
        <p style="font-size: 15px; line-height: 1.7; color: #4b5563;">{{ $feedback['summary'] }}</p>
    </div>

    <div style="display: grid; gap: 16px; margin-bottom: 32px;">
        @foreach($feedback['question_reviews'] as $review)
        <div style="background: white; border-radius: 18px; border: 1px solid {{ $review['is_correct'] ? '#bbf7d0' : '#fecaca' }}; padding: 22px;">
            <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Soal {{ $review['number'] }} • {{ $review['type'] }}</div>
                    <h4 style="font-size: 16px; font-weight: 600; color: #1f2937; line-height: 1.6;">{{ $review['question'] }}</h4>
                </div>
                @if($review['is_correct'])
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #dcfce7; color: #166534; border-radius: 999px; font-size: 12px; font-weight: 600;">Benar</span>
                @else
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fee2e2; color: #b91c1c; border-radius: 999px; font-size: 12px; font-weight: 600;">Salah</span>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 14px;">
                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Jawaban Anda</div>
                    <div style="font-size: 14px; color: #1f2937; font-weight: 600;">{{ $review['selected_label'] !== '-' ? $review['selected_label'] . '.' : '' }} {{ $review['selected_text'] }}</div>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 14px 16px;">
                    <div style="font-size: 12px; color: #15803d; margin-bottom: 4px;">Jawaban benar</div>
                    <div style="font-size: 14px; color: #166534; font-weight: 600;">{{ $review['correct_label'] !== '-' ? $review['correct_label'] . '.' : '' }} {{ $review['correct_text'] }}</div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; font-size: 13px; color: #6b7280;">
                <span>Poin: {{ $review['points'] }}/{{ $review['max_points'] }}</span>
            </div>

            @if(!empty($review['explanation']))
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 14px 16px;">
                <div style="font-size: 12px; color: #2563eb; margin-bottom: 4px; font-weight: 600;">Penjelasan</div>
                <p style="font-size: 14px; line-height: 1.7; color: #1e3a8a; margin: 0;">{{ $review['explanation'] }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: white; color: #374151; border: 1px solid #d1d5db; border-radius: 12px; font-weight: 600; text-decoration: none;">Kembali ke Modul</a>
        <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $feedback['quiz_id']]) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #3b82f6; color: white; border-radius: 12px; font-weight: 600; text-decoration: none;">Kerjakan Ulang Kuiz</a>
    </div>
</div>

</x-layouts.dashboard>
