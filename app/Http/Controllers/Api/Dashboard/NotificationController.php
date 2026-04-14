<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
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
        if ((int) $request->user()->role_id === 1) {
            $uuids = $this->overdueTicketsQuery()
                ->pluck('uuid')
                ->filter()
                ->values();

            return response()->json([
                'count' => $uuids->count(),
                'ticket_uuids' => $uuids,
            ]);
        }

        $notifications = $request->user()->unreadNotifications()->get();
        $uuids = $notifications->pluck('data.ticket_uuid')->filter()->unique()->values();
        return response()->json([
            'count' => $notifications->count(),
            'ticket_uuids' => $uuids,
        ]);
    }

    public function unreadTickets(Request $request): JsonResponse
    {
        if ((int) $request->user()->role_id === 1) {
            $uuids = $this->overdueTicketsQuery()
                ->pluck('uuid')
                ->filter()
                ->values();

            return response()->json(['ticket_uuids' => $uuids]);
        }

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
        if ((int) $request->user()->role_id === 1) {
            return response()->json(['message' => 'No unread notifications to mark for admin overdue alerts']);
        }

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

    private function overdueTicketsQuery(): Builder
    {
        return Ticket::query()
            ->where('created_at', '<=', now()->subDay())
            ->whereHas('priority', function (Builder $priorityQuery) {
                $priorityQuery->whereRaw('LOWER(name) = ?', ['urgent']);
            })
            ->where(function (Builder $query) {
                $query->whereNull('status_id')
                    ->orWhereDoesntHave('status', function (Builder $statusQuery) {
                        $statusQuery->whereRaw('LOWER(name) LIKE ?', ['close%']);
                    });
            });
    }
}
