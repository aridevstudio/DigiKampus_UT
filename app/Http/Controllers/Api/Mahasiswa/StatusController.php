<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * @tags Mahasiswa Status
 */
class StatusController extends Controller
{
    /**
     * Get Online Status
     * 
     * Endpoint untuk mendapatkan status online user.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Status retrieved successfully',
            'data' => [
                'is_online' => $user->is_online,
                'last_activity' => $user->last_activity?->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Set Online
     * 
     * Endpoint untuk mengubah status menjadi online.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function setOnline(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->setOnline();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah ke online',
            'data' => [
                'is_online' => true,
                'last_activity' => $user->last_activity->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Set Offline
     * 
     * Endpoint untuk mengubah status menjadi offline.
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function setOffline(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->setOffline();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah ke offline',
            'data' => [
                'is_online' => false,
            ]
        ]);
    }

    /**
     * Update Activity (Heartbeat)
     * 
     * Endpoint untuk update last activity (dipanggil berkala dari client).
     * 
     * @security BearerToken
     * @param Request $request
     * @return JsonResponse
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->last_activity = Carbon::now();
        $user->is_online = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Activity updated'
        ]);
    }
}
