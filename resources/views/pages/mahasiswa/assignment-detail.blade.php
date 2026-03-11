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
    <p style="color: #6b7280;">Tugas akhir bersifat opsional untuk modul ini.</p>
</div>

{{-- Informasi Tugas Section --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
        <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 20px; height: 20px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 style="font-size: 20px; font-weight: 600; color: #1f2937;">Informasi Tugas</h2>
    </div>
    
    <div style="display: flex; gap: 32px; flex-wrap: wrap;">
        {{-- Left: Description --}}
        <div style="flex: 1; min-width: 300px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 12px;">{{ $assignment['title'] }}</h3>
            <p style="color: #4b5563; line-height: 1.7; margin-bottom: 20px;">{{ $assignment['description'] }}</p>
            
            <h4 style="font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 12px;">Tujuan Pembelajaran</h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($assignment['learning_objectives'] as $objective)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                    <span style="color: #4b5563; font-size: 14px;">{{ $objective }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        {{-- Right: Details --}}
        <div style="width: 280px; display: flex; flex-direction: column; gap: 16px;">
            {{-- Deadline & Weight --}}
            <div style="display: flex; gap: 16px;">
                <div style="flex: 1; background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span style="font-size: 12px; color: #6b7280;">Tenggat Waktu</span>
                    </div>
                    <p style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $assignment['deadline']->format('d F Y') }}</p>
                </div>
                <div style="flex: 1; background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        </svg>
                        <span style="font-size: 12px; color: #6b7280;">Bobot Nilai</span>
                    </div>
                    <p style="font-size: 14px; font-weight: 600; color: #1f2937;">{{ $assignment['weight'] }}%</p>
                </div>
            </div>
            
            {{-- Format & Size --}}
            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span style="font-size: 12px; color: #6b7280;">Format & Ukuran File</span>
                </div>
                <p style="font-size: 14px; color: #4b5563;">Format: {{ $assignment['format'] }}</p>
                <p style="font-size: 14px; color: #4b5563;">Maksimal: {{ $assignment['max_size'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Instruksi Pengerjaan Section --}}
<div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
        <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 20px; height: 20px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h2 style="font-size: 20px; font-weight: 600; color: #1f2937;">Instruksi Pengerjaan</h2>
    </div>
    
    <div style="display: flex; gap: 48px; flex-wrap: wrap;">
        {{-- Steps --}}
        <div style="flex: 1; min-width: 280px;">
            <h4 style="font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Langkah Pengerjaan</h4>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($assignment['steps'] as $index => $step)
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 28px; height: 28px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">{{ $index + 1 }}</div>
                    <span style="color: #4b5563; font-size: 14px; padding-top: 4px;">{{ $step }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        {{-- Grading & Notes --}}
        <div style="width: 320px;">
            <h4 style="font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Kriteria Penilaian</h4>
            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px;">
                @foreach($assignment['grading_criteria'] as $criteria)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #4b5563; font-size: 14px;">{{ $criteria['name'] }}</span>
                    <span style="color: #3b82f6; font-weight: 600; font-size: 14px;">{{ $criteria['percentage'] }}%</span>
                </div>
                @endforeach
            </div>
            
            {{-- Instructor Note --}}
            <div style="background: #eff6ff; border-radius: 12px; padding: 16px; border-left: 4px solid #3b82f6;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <svg style="width: 16px; height: 16px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span style="font-size: 12px; font-weight: 600; color: #1e40af;">Catatan Dosen</span>
                </div>
                <p style="color: #1e40af; font-size: 13px; line-height: 1.6;">{{ $assignment['instructor_note'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Submit Button --}}
<a href="{{ route('mahasiswa.assignment-submission', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 16px; background: #3b82f6; color: white; border-radius: 12px; font-weight: 600; font-size: 16px; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
    Halaman Submission
</a>

</x-layouts.dashboard>
