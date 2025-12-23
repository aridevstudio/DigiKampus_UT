<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Mahasiswa Notifications
 */
class NotificationController extends Controller
{
    /**
     * Get Notifications
     * 
     * Endpoint untuk mendapatkan daftar notifikasi.
     * Mendukung filter berdasarkan tipe dan status baca.
     * 
     * @security BearerToken
     * @queryParam tipe string Optional. Filter by tipe: kursus_pembelajaran, jadwal_ujian, pencapaian, umum
     * @queryParam is_read boolean Optional. Filter by read status: true/false
     * @queryParam search string Optional. Search keyword
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $tipe = $request->query('tipe');
        $isRead = $request->query('is_read');
        $search = $request->query('search');

        $query = Notification::where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc');

        if ($tipe) {
            $query->byTipe($tipe);
        }

        if ($isRead === 'false' || $isRead === '0') {
            $query->unread();
        } elseif ($isRead === 'true' || $isRead === '1') {
            $query->where('is_read', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $notifications = $query->get();

        $unreadCount = Notification::where('id_mahasiswa', $user->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully',
            'data' => $notifications->map(function ($notif) {
                return [
                    'id_notification' => $notif->id_notification,
                    'judul' => $notif->judul,
                    'konten' => $notif->konten,
                    'tipe' => $notif->tipe,
                    'icon' => $notif->icon,
                    'icon_color' => $notif->icon_color,
                    'is_read' => $notif->is_read,
                    'waktu_relatif' => $notif->waktu_relatif,
                    'created_at' => $notif->created_at->toDateTimeString(),
                ];
            }),
            'meta' => [
                'total' => $notifications->count(),
                'unread_count' => $unreadCount,
            ]
        ]);
    }

    /**
     * Mark as Read
     * 
     * Endpoint untuk menandai notifikasi sebagai sudah dibaca.
     * 
     * @security BearerToken
     * @param Request $request
     * @param int $id Notification ID
     * @return JsonResponse
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('id_mahasiswa', $user->id)
            ->where('id_notification', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca'
        ]);
    }

    /**
     * Mark All as Read
     * 
     * Endpoint untuk menandai semua notifikasi sebagai sudah dibaca.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        Notification::where('id_mahasiswa', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca'
        ]);
    }

    /**
     * Get Unread Count
     * 
     * Endpoint untuk mendapatkan jumlah notifikasi belum dibaca.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $count = Notification::where('id_mahasiswa', $user->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Unread count retrieved',
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }
}
