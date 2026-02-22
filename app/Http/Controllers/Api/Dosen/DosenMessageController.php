<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @tags Dosen Messages
 */
class DosenMessageController extends Controller
{
    /**
     * Get Conversations List
     * 
     * Endpoint untuk mendapatkan daftar percakapan dengan mahasiswa.
     * Menampilkan pesan terakhir dan jumlah pesan belum dibaca.
     *
     * @security BearerToken
     * @queryParam search string Cari berdasarkan nama mahasiswa.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;
        $search = $request->query('search');

        // Get students who have conversations with this dosen
        // FIX P0: SQL injection — replaced string concatenation with parameter binding
        $conversationStudents = Message::where('id_sender', $dosenId)
            ->orWhere('id_receiver', $dosenId)
            ->selectRaw('CASE WHEN id_sender = ? THEN id_receiver ELSE id_sender END as student_id', [$dosenId])
            ->distinct()
            ->pluck('student_id');

        // Also include students enrolled in dosen's courses (potential contacts)
        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
        $enrolledStudents = Enrollment::whereIn('id_course', $dosenCourseIds)
            ->pluck('id_mahasiswa');

        $allStudentIds = $conversationStudents->merge($enrolledStudents)->unique();

        // Query students
        $studentsQuery = User::whereIn('id', $allStudentIds)
            ->where('role', 'mahasiswa')
            ->with('profile');

        if ($search) {
            $studentsQuery->where('name', 'like', "%{$search}%");
        }

        $students = $studentsQuery->get();

        // Build conversations data
        $conversations = $students->map(function ($student) use ($dosenId) {
            // Get last message
            $lastMessage = Message::conversation($dosenId, $student->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Count unread messages from this student
            $unreadCount = Message::where('id_sender', $student->id)
                ->where('id_receiver', $dosenId)
                ->where('is_read', false)
                ->count();

            // Get enrolled course
            $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
            $enrollment = Enrollment::where('id_mahasiswa', $student->id)
                ->whereIn('id_course', $dosenCourseIds)
                ->with('course')
                ->first();

            return [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'avatar' => $student->profile->avatar ?? null,
                    'is_online' => $student->is_online ?? false
                ],
                'course' => $enrollment ? $enrollment->course->nama_course : null,
                'last_message' => $lastMessage ? [
                    'content' => strlen($lastMessage->content) > 50
                        ? substr($lastMessage->content, 0, 50) . '...'
                        : $lastMessage->content,
                    'time' => $lastMessage->created_at->diffForHumans(),
                    'is_from_me' => $lastMessage->id_sender === $dosenId
                ] : null,
                'unread_count' => $unreadCount
            ];
        })->sortByDesc(function ($conv) {
            return $conv['unread_count'] > 0 ? 1 : 0;
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar percakapan berhasil diambil.',
            'data' => [
                'conversations' => $conversations
            ]
        ], 200);
    }

    /**
     * Get Chat History with Student
     * 
     * Endpoint untuk mendapatkan riwayat chat dengan mahasiswa tertentu.
     * Otomatis menandai pesan dari mahasiswa sebagai sudah dibaca.
     *
     * @security BearerToken
     * @queryParam per_page int Jumlah item per halaman. Defaults to 20.
     * @param Request $request
     * @param int $studentId Student ID
     * @return JsonResponse
     */
    public function show(Request $request, int $studentId): JsonResponse
    {
        $dosenId = $request->user()->id;
        $perPage = $request->query('per_page', 20);

        // Get student info
        $student = User::where('id', $studentId)
            ->where('role', 'mahasiswa')
            ->with('profile')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Get messages
        $messages = Message::conversation($dosenId, $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Mark messages from student as read
        Message::where('id_sender', $studentId)
            ->where('id_receiver', $dosenId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get student enrollment info
        $dosenCourseIds = Course::where('id_dosen', $dosenId)->pluck('id_course');
        $enrollment = Enrollment::where('id_mahasiswa', $studentId)
            ->whereIn('id_course', $dosenCourseIds)
            ->with('course')
            ->first();

        // Format messages (reverse for chronological order in response)
        $messagesData = $messages->getCollection()->reverse()->values()->map(function ($message) use ($dosenId) {
            return [
                'id' => $message->id_message,
                'content' => $message->content,
                'is_from_me' => $message->id_sender === $dosenId,
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->format('Y-m-d'),
                'is_read' => $message->is_read
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat chat berhasil diambil.',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->profile->nim ?? null,
                    'email' => $student->email,
                    'avatar' => $student->profile->avatar ?? null,
                    'is_online' => $student->is_online ?? false,
                    'bergabung_sejak' => $student->created_at->format('F Y')
                ],
                'enrollment' => $enrollment ? [
                    'course' => $enrollment->course->nama_course,
                    'progress' => round($enrollment->progress, 0)
                ] : null,
                'messages' => $messagesData,
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total()
                ]
            ]
        ], 200);
    }

    /**
     * Send Message to Student
     * 
     * Endpoint untuk mengirim pesan ke mahasiswa.
     *
     * @security BearerToken
     * @bodyParam student_id int required ID mahasiswa penerima.
     * @bodyParam content string required Isi pesan. Max 2000 karakter.
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Verify student exists and is a mahasiswa
        $student = User::where('id', $validated['student_id'])
            ->where('role', 'mahasiswa')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan.'
            ], 404);
        }

        $message = Message::create([
            'id_sender' => $dosenId,
            'id_receiver' => $validated['student_id'],
            'content' => $validated['content'],
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim.',
            'data' => [
                'id' => $message->id_message,
                'content' => $message->content,
                'time' => $message->created_at->format('H:i'),
                'is_from_me' => true
            ]
        ], 201);
    }

    /**
     * Send Broadcast Announcement
     * 
     * Endpoint untuk mengirim pengumuman ke semua mahasiswa di kursus tertentu.
     *
     * @security BearerToken
     * @bodyParam course_id int required ID kursus.
     * @bodyParam content string required Isi pengumuman. Max 2000 karakter.
     * @param Request $request
     * @return JsonResponse
     */
    public function broadcast(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|integer',
            'content' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Verify course belongs to dosen
        $course = Course::where('id_course', $validated['course_id'])
            ->where('id_dosen', $dosenId)
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.'
            ], 404);
        }

        // Get all enrolled students
        $studentIds = Enrollment::where('id_course', $validated['course_id'])
            ->pluck('id_mahasiswa');

        if ($studentIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mahasiswa yang terdaftar di kursus ini.'
            ], 400);
        }

        // Send message to each student
        $messages = $studentIds->map(function ($studentId) use ($dosenId, $validated) {
            return [
                'id_sender' => $dosenId,
                'id_receiver' => $studentId,
                'content' => "[PENGUMUMAN] " . $validated['content'],
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        Message::insert($messages);

        return response()->json([
            'success' => true,
            'message' => "Pengumuman berhasil dikirim ke {$studentIds->count()} mahasiswa."
        ], 201);
    }

    /**
     * Get Unread Messages Count
     * 
     * Endpoint untuk mendapatkan jumlah total pesan belum dibaca.
     *
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $dosenId = $request->user()->id;

        $count = Message::where('id_receiver', $dosenId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Jumlah pesan belum dibaca berhasil diambil.',
            'data' => [
                'unread_count' => $count
            ]
        ], 200);
    }
}
