<x-layouts.dashboard :active="'courses'">

{{-- Page Header --}}
<div style="margin-bottom: 16px;">
    <h1 style="font-size: 24px; font-weight: 700; color: #1f2937;">{{ $quiz['title'] }}</h1>
    <p style="font-size: 14px; color: #6b7280;">{{ $quiz['course_name'] }} • {{ $quiz['module_name'] }}</p>
</div>

{{-- Main Layout --}}
<div style="display: flex; gap: 24px; flex-wrap: wrap;">
    
    {{-- Left Column: Quiz Content --}}
    <div style="flex: 1; min-width: 400px;">
        
        {{-- Quiz Info Bar --}}
        <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; margin-bottom: 16px;">
            <div style="display: flex; flex-wrap: wrap; gap: 24px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 16px; height: 16px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 10px; color: #9ca3af; text-transform: uppercase;">Jumlah Soal</p>
                        <p style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $quiz['total_questions'] }} Soal</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 16px; height: 16px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 10px; color: #9ca3af; text-transform: uppercase;">Durasi</p>
                        <p style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $quiz['duration'] }} Menit</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; background: #d1fae5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 16px; height: 16px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 10px; color: #9ca3af; text-transform: uppercase;">Nilai Lulus</p>
                        <p style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $quiz['passing_score'] }}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; background: #fee2e2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 16px; height: 16px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 10px; color: #9ca3af; text-transform: uppercase;">Aturan</p>
                        <p style="font-size: 14px; font-weight: 600; color: #1f2937;">Tidak dapat kembali</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Question Card --}}
        @php $question = $questions[$currentQuestion] ?? $questions[1]; @endphp
        <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h2 style="font-size: 20px; font-weight: 700; color: #1f2937;">Soal {{ $currentQuestion }}</h2>
                <span style="padding: 4px 12px; background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 500; border-radius: 20px;">{{ $question['type'] }}</span>
            </div>
            
            <p style="color: #374151; margin-bottom: 24px; line-height: 1.6;">{{ $question['text'] }}</p>
            
            <div style="display: flex; flex-direction: column; gap: 12px;" id="options-container">
                @foreach($question['options'] as $optionIndex => $option)
                @php $optionKey = (string) $optionIndex; @endphp
                <label style="cursor: pointer;" onclick="selectOption(this, '{{ $optionKey }}')">
                    <input type="radio" name="answer" value="{{ $optionKey }}" style="display: none;" {{ isset($userAnswers[$currentQuestion]) && (string) $userAnswers[$currentQuestion] === $optionKey ? 'checked' : '' }}>
                    <div class="option-box" id="option-{{ $optionKey }}" style="padding: 16px; border-radius: 12px; border: 2px solid #e5e7eb; display: flex; align-items: flex-start; gap: 12px; transition: all 0.2s; background: white;">
                        <div class="option-radio" style="width: 20px; height: 20px; border: 2px solid #d1d5db; border-radius: 50%; flex-shrink: 0; margin-top: 2px; display: flex; align-items: center; justify-content: center;">
                            <div class="option-dot" style="width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; display: none;"></div>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #374151;">{{ chr(65 + $optionIndex) }}.</span>
                            <span style="color: #4b5563;">{{ $option }}</span>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Navigation --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
            <a href="{{ $currentQuestion > 1 ? route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $quiz['id'], 'q' => $currentQuestion - 1]) : '#' }}" 
               style="display: flex; align-items: center; gap: 4px; font-size: 14px; color: {{ $currentQuestion <= 1 ? '#9ca3af' : '#4b5563' }}; text-decoration: none;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
            </a>
            
            <button type="button" id="flagBtn" onclick="toggleFlag()" style="display: flex; align-items: center; gap: 4px; font-size: 14px; color: {{ in_array($currentQuestion, $flaggedQuestions) ? '#dc2626' : '#f97316' }}; background: none; border: none; cursor: pointer;">
                <svg id="flagIcon" style="width: 16px; height: 16px;" fill="{{ in_array($currentQuestion, $flaggedQuestions) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span id="flagText">{{ in_array($currentQuestion, $flaggedQuestions) ? 'Ditandai' : 'Tandai' }}</span>
            </button>
            
            <a href="{{ $currentQuestion < $quiz['total_questions'] ? route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $quiz['id'], 'q' => $currentQuestion + 1]) : '#' }}"
               style="display: flex; align-items: center; gap: 4px; font-size: 14px; color: {{ $currentQuestion >= $quiz['total_questions'] ? '#9ca3af' : '#4b5563' }}; text-decoration: none;">
                Selanjutnya
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Right Column: Progress Panel --}}
    <div style="width: 200px; flex-shrink: 0;">
        <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; position: sticky; top: 100px;">
            <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 12px; font-size: 14px;">Progres Soal</h3>
            
            {{-- Question Grid --}}
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-bottom: 16px;">
                @for($i = 1; $i <= $quiz['total_questions']; $i++)
                @php
                    $bgColor = '#f3f4f6';
                    $textColor = '#6b7280';
                    if ($i == $currentQuestion) {
                        $bgColor = '#3b82f6';
                        $textColor = 'white';
                    } elseif (isset($userAnswers[$i])) {
                        $bgColor = '#dbeafe';
                        $textColor = '#2563eb';
                    } elseif (in_array($i, $flaggedQuestions)) {
                        $bgColor = '#fef3c7';
                        $textColor = '#d97706';
                    }
                @endphp
                <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $quiz['id'], 'q' => $i]) }}"
                   data-question="{{ $i }}"
                   style="aspect-ratio: 1; background: {{ $bgColor }}; color: {{ $textColor }}; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; text-decoration: none;">{{ $i }}</a>
                @endfor
            </div>
            
            {{-- Legend --}}
            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; font-size: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; background: #dbeafe; border-radius: 4px;"></span>
                    <span style="color: #6b7280;">Sudah dijawab ({{ count($userAnswers) }})</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; background: #fef3c7; border-radius: 4px;"></span>
                    <span style="color: #6b7280;">Ditandai ({{ count($flaggedQuestions) }})</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; background: #f3f4f6; border-radius: 4px;"></span>
                    <span style="color: #6b7280;">Belum dijawab ({{ $quiz['total_questions'] - count($userAnswers) }})</span>
                </div>
            </div>
            
            {{-- Action Button --}}
            @if($currentQuestion < $quiz['total_questions'])
            <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $quiz['id'], 'q' => $currentQuestion + 1]) }}" style="width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;">
                Soal Selanjutnya
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
            @else
            <button type="button" onclick="confirmSubmit()" style="width: 100%; padding: 12px; background: #ef4444; color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Selesaikan Kuis
            </button>
            @endif
        </div>
    </div>
</div>

<script>
// Track if current question is answered
let isCurrentQuestionAnswered = {{ isset($userAnswers[$currentQuestion]) ? 'true' : 'false' }};

function selectOption(label, key) {
    // Mark as answered
    isCurrentQuestionAnswered = true;
    // Reset all options
    document.querySelectorAll('.option-box').forEach(box => {
        box.style.borderColor = '#e5e7eb';
        box.style.background = 'white';
    });
    document.querySelectorAll('.option-radio').forEach(radio => {
        radio.style.borderColor = '#d1d5db';
    });
    document.querySelectorAll('.option-dot').forEach(dot => {
        dot.style.display = 'none';
    });
    
    // Highlight selected option
    const selectedBox = document.getElementById('option-' + key);
    if (selectedBox) {
        selectedBox.style.borderColor = '#3b82f6';
        selectedBox.style.background = '#eff6ff';
        const radio = selectedBox.querySelector('.option-radio');
        if (radio) radio.style.borderColor = '#3b82f6';
        const dot = selectedBox.querySelector('.option-dot');
        if (dot) dot.style.display = 'block';
    }
    
    // Save answer via AJAX
    fetch('{{ route('mahasiswa.quiz-answer', ['courseId' => $course->id_course, 'quizId' => $quiz['id']]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            question: {{ $currentQuestion }},
            answer: key
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update progress indicator for current question
            const progressItem = document.querySelector('[data-question="{{ $currentQuestion }}"]');
            if (progressItem) {
                progressItem.style.background = '#dbeafe';
                progressItem.style.color = '#2563eb';
            }
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
        alert('Gagal menyimpan jawaban. Periksa koneksi internet Anda.');
    });
}

function toggleFlag() {
    fetch('{{ route('mahasiswa.quiz-flag', ['courseId' => $course->id_course, 'quizId' => $quiz['id']]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            question: {{ $currentQuestion }}
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('flagBtn');
            const icon = document.getElementById('flagIcon');
            const text = document.getElementById('flagText');
            const progressItem = document.querySelector('[data-question="{{ $currentQuestion }}"]');
            
            if (data.isFlagged) {
                btn.style.color = '#dc2626';
                icon.setAttribute('fill', 'currentColor');
                text.textContent = 'Ditandai';
                if (progressItem) {
                    progressItem.style.background = '#fef3c7';
                    progressItem.style.color = '#d97706';
                }
            } else {
                btn.style.color = '#f97316';
                icon.setAttribute('fill', 'none');
                text.textContent = 'Tandai';
                // When unflagging, check if question is answered
                if (progressItem && !isCurrentQuestionAnswered) {
                    progressItem.style.background = '#f3f4f6';
                    progressItem.style.color = '#6b7280';
                } else if (progressItem && isCurrentQuestionAnswered) {
                    progressItem.style.background = '#dbeafe';
                    progressItem.style.color = '#2563eb';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error toggling flag:', error);
        alert('Gagal menandai soal. Periksa koneksi internet Anda.');
    });
}

function confirmSubmit() {
    if (confirm('Apakah Anda yakin ingin menyelesaikan kuis?')) {
        fetch('{{ route('mahasiswa.quiz-submit', ['courseId' => $course->id_course, 'quizId' => $quiz['id']]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) {
                throw new Error(data.message || 'Gagal menyelesaikan kuis.');
            }

            window.location.href = data.redirect_url;
        })
        .catch(error => {
            console.error('Error submitting quiz:', error);
            alert(error.message || 'Gagal menyelesaikan kuis. Coba lagi.');
        });
    }
}

// Initialize selection on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($userAnswers[$currentQuestion]))
    const savedAnswer = '{{ $userAnswers[$currentQuestion] ?? '' }}';
    if (savedAnswer) {
        const selectedBox = document.getElementById('option-' + savedAnswer);
        if (selectedBox) {
            selectedBox.style.borderColor = '#3b82f6';
            selectedBox.style.background = '#eff6ff';
            const radio = selectedBox.querySelector('.option-radio');
            if (radio) radio.style.borderColor = '#3b82f6';
            const dot = selectedBox.querySelector('.option-dot');
            if (dot) dot.style.display = 'block';
        }
    }
    @endif
});
</script>

</x-layouts.dashboard>
