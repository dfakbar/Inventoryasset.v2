<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketLog;

class TicketLogService
{
    public static function log(
        Ticket $ticket,
        ?int $userId,
        string $action,
        ?string $field = null,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?string $notes = null
    ): TicketLog {
        return TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $userId ?? auth()->id(),
            'action'    => $action,
            'field'     => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes'     => $notes,
        ]);
    }

    public static function statusChange(Ticket $ticket, string $oldStatus, string $newStatus, ?string $notes = null): TicketLog
    {
        return self::log(
            $ticket,
            null,
            'status_changed',
            'status',
            $oldStatus,
            $newStatus,
            $notes
        );
    }

    public static function assignment(Ticket $ticket, ?int $oldAgentId, int $newAgentId, string $notes = null): TicketLog
    {
        return self::log(
            $ticket,
            null,
            'assigned',
            'agent_id',
            $oldAgentId ? (string) $oldAgentId : null,
            (string) $newAgentId,
            $notes
        );
    }
}
