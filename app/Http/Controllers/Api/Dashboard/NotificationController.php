<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function count(Request $request): JsonResponse
    {
        $notifications = $request->user()->unreadNotifications()->get();
        $uuids = $notifications->pluck('data.ticket_uuid')->filter()->unique()->values();
        return response()->json([
            'count' => $notifications->count(),
            'ticket_uuids' => $uuids,
        ]);
    }

    public function unreadTickets(Request $request): JsonResponse
    {
        $uuids = $request->user()->unreadNotifications()
            ->get()
            ->pluck('data.ticket_uuid')
            ->filter()
            ->unique()
            ->values();
        return response()->json(['ticket_uuids' => $uuids]);
    }

    public function markRead(Request $request): JsonResponse
    {
        $ticketUuid = $request->input('ticket_uuid');
        if ($ticketUuid) {
            // Mark only notifications for this specific ticket as read
            $request->user()->unreadNotifications()
                ->where('data->ticket_uuid', $ticketUuid)
                ->update(['read_at' => now()]);
        } else {
            $request->user()->unreadNotifications()->update(['read_at' => now()]);
        }
        return response()->json(['message' => 'Notifications marked as read']);
    }
}
