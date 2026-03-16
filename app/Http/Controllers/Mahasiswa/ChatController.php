<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    /**
     * Show mahasiswa chat page.
     */
    public function index()
    {
        return view('pages.mahasiswa.chat');
    }

    /**
     * Get dosen conversations for mahasiswa sidebar.
     */
    public function getConversations(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $mahasiswaId = $mahasiswa->id;
        $search = trim((string) $request->query('search', ''));

        $availableDosenIds = $this->getAvailableDosenIds($mahasiswaId);

        if ($availableDosenIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $dosenQuery = User::whereIn('id', $availableDosenIds)
            ->where('role', 'dosen')
            ->with('profile');

        if ($search !== '') {
            $dosenQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('nomor_induk', 'like', "%{$search}%");
                    });
            });
        }

        $dosens = $dosenQuery->get();

        $conversations = $dosens->map(function (User $dosen) use ($mahasiswaId) {
            $lastMessage = Message::conversation($mahasiswaId, $dosen->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = Message::where('id_sender', $dosen->id)
                ->where('id_receiver', $mahasiswaId)
                ->where('is_read', false)
                ->count();

            $fotoProfile = $dosen->profile->foto_profile ?? null;
            $avatar = $fotoProfile
                ? asset('storage/' . $fotoProfile)
                : 'https://ui-avatars.com/api/?name=' . urlencode($dosen->name) . '&background=random';

            return [
                'dosen_id' => $dosen->id,
                'dosen_name' => $dosen->name,
                'dosen_email' => $dosen->email ?? '-',
                'dosen_avatar' => $avatar,
                'last_message' => $lastMessage
                    ? (strlen($lastMessage->content) > 50 ? substr($lastMessage->content, 0, 50) . '...' : $lastMessage->content)
                    : null,
                'last_message_time' => $lastMessage ? $lastMessage->created_at->toISOString() : null,
                'unread_count' => $unreadCount,
            ];
        })
            ->sort(function (array $left, array $right) {
                $leftUnread = (int) ($left['unread_count'] ?? 0);
                $rightUnread = (int) ($right['unread_count'] ?? 0);

                if (($leftUnread > 0) !== ($rightUnread > 0)) {
                    return $leftUnread > 0 ? -1 : 1;
                }

                $leftTime = $left['last_message_time'] ?? '';
                $rightTime = $right['last_message_time'] ?? '';

                if ($leftTime !== $rightTime) {
                    return $rightTime <=> $leftTime;
                }

                return strcmp($left['dosen_name'], $right['dosen_name']);
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Get messages between mahasiswa and selected dosen.
     */
    public function getChatMessages($dosenId)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $mahasiswaId = $mahasiswa->id;
        $dosenId = (int) $dosenId;

        if (!$this->canAccessDosen($mahasiswaId, $dosenId)) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak dapat diakses.',
            ], 403);
        }

        $dosen = User::where('id', $dosenId)->where('role', 'dosen')->first();
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan.',
            ], 404);
        }

        Message::where('id_sender', $dosenId)
            ->where('id_receiver', $mahasiswaId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::conversation($mahasiswaId, $dosenId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function (Message $msg) use ($mahasiswaId) {
                return [
                    'id' => $msg->id_message,
                    'content' => $msg->content,
                    'sender_type' => $msg->id_sender === $mahasiswaId ? 'mahasiswa' : 'dosen',
                    'created_at' => $msg->created_at->toISOString(),
                    'is_read' => $msg->is_read,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send message from mahasiswa to dosen.
     */
    public function sendChatMessage(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $mahasiswaId = $mahasiswa->id;

        $validated = $request->validate([
            'dosen_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:2000',
        ]);

        $dosenId = (int) $validated['dosen_id'];
        if (!$this->canAccessDosen($mahasiswaId, $dosenId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghubungi dosen ini.',
            ], 403);
        }

        $dosen = User::where('id', $dosenId)->where('role', 'dosen')->first();
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan.',
            ], 404);
        }

        $message = Message::create([
            'id_sender' => $mahasiswaId,
            'id_receiver' => $dosenId,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id_message,
                'content' => $message->content,
                'sender_type' => 'mahasiswa',
                'created_at' => $message->created_at->toISOString(),
                'is_read' => false,
            ],
        ]);
    }

    /**
     * Get all dosen IDs available for mahasiswa chat.
     */
    private function getAvailableDosenIds(int $mahasiswaId): Collection
    {
        $conversationDosenIds = Message::where(function ($q) use ($mahasiswaId) {
            $q->where('id_sender', $mahasiswaId)->orWhere('id_receiver', $mahasiswaId);
        })
            ->selectRaw('CASE WHEN id_sender = ? THEN id_receiver ELSE id_sender END as dosen_id', [$mahasiswaId])
            ->pluck('dosen_id');

        $enrolledCourseIds = Enrollment::where('id_mahasiswa', $mahasiswaId)
            ->pluck('id_course');

        $enrolledDosenIds = Course::whereIn('id_course', $enrolledCourseIds)
            ->pluck('id_dosen');

        return $conversationDosenIds
            ->merge($enrolledDosenIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * Check whether mahasiswa can access selected dosen conversation.
     */
    private function canAccessDosen(int $mahasiswaId, int $dosenId): bool
    {
        if ($dosenId <= 0) {
            return false;
        }

        return $this->getAvailableDosenIds($mahasiswaId)->contains($dosenId);
    }
}
