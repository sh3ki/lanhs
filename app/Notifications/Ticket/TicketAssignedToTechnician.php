<?php

namespace App\Notifications\Ticket;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketAssignedToTechnician extends Notification
{
    use Queueable;

    private Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'ticket_uuid'    => $this->ticket->uuid,
            'ticket_subject' => $this->ticket->subject,
            'message'        => 'You have been assigned to ticket: ' . $this->ticket->subject,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
