<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:remind-assignments', function () {
    $this->info('Starting assignment reminders...');
    
    // Find all active enrollments for courses of category 'tiket' (Bootcamp)
    $enrollments = \App\Models\Enrollment::where('status', 'aktif')
        ->with(['course.materials', 'mahasiswa'])
        ->get();
        
    $count = 0;
    foreach ($enrollments as $enrollment) {
        $course = $enrollment->course;
        $user = $enrollment->mahasiswa;
        
        if (!$course || !$user) {
            continue;
        }
        
        foreach ($course->materials as $material) {
            $type = match ($material->tipe) {
                'tugas', 'assignment', 'tugas_akhir' => 'tugas',
                default => null,
            };
            if ($type !== 'tugas') {
                continue;
            }
            
            $payload = json_decode($material->konten, true);
            if (!is_array($payload) || empty($payload['is_tugas'])) {
                continue;
            }
            
            // Check if student has submitted
            $hasSubmitted = \App\Models\AssignmentSubmission::where('id_material', $material->id_material)
                ->where('id_mahasiswa', $user->id)
                ->exists();
                
            if ($hasSubmitted) {
                continue;
            }
            
            $deadlineStr = $payload['deadline'] ?? null;
            if (!$deadlineStr) {
                continue;
            }
            
            try {
                $deadline = \Illuminate\Support\Carbon::parse($deadlineStr);
            } catch (\Throwable $e) {
                continue;
            }
            
            $now = now();
            $hoursDiff = $now->diffInHours($deadline, false);
            
            $reminderType = null;
            if ($hoursDiff > 48 && $hoursDiff <= 72) {
                $reminderType = 'H-3';
            } elseif ($hoursDiff > 12 && $hoursDiff <= 24) {
                $reminderType = 'H-1';
            } elseif ($hoursDiff > 0 && $hoursDiff <= 12) {
                $reminderType = 'Hari H';
            }
            
            if ($reminderType) {
                $notifTitle = "Reminder {$reminderType}: " . ($material->judul_material ?: 'Tugas');
                $alreadyNotified = \App\Models\Notification::where('id_mahasiswa', $user->id)
                    ->where('judul', $notifTitle)
                    ->exists();
                    
                if (!$alreadyNotified) {
                    \App\Models\Notification::notifyMahasiswa(
                        $user->id,
                        $notifTitle,
                        "Tugas \"" . ($material->judul_material ?: 'Tugas') . "\" pada Bootcamp \"" . $course->nama_course . "\" mendekati deadline. Segera selesaikan!",
                        'kursus_pembelajaran',
                        'bell',
                        '#F59E0B'
                    );
                    $count++;
                }
            }
        }
    }
    
    $this->info("Reminders sent: {$count}");
})->purpose('Send notifications to students for upcoming assignment deadlines');

