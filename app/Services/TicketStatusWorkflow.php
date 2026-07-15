<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketStatusWorkflow
{
    private const TRANSITIONS = [
        'Draft' => ['New'],
        'New' => ['Assigned', 'Rejected'],
        'Assigned' => ['In Progress', 'Assigned', 'Rejected'],
        'In Progress' => ['Pending', 'Resolved', 'Assigned'],
        'Pending' => ['In Progress', 'Resolved'],
        'Resolved' => ['Closed', 'Reopen'],
        'Closed' => ['Reopen'],
        'Rejected' => ['New'],
        'Reopen' => ['Assigned', 'Rejected'],
    ];

    private const ROLE_PERMISSIONS = [
        'New' => ['requester' => ['submit'], 'agent' => [], 'admin' => ['submit', 'reject']],
        'Assigned' => ['requester' => [], 'agent' => ['start', 'reassign', 'reject'], 'admin' => ['start', 'reassign', 'reject']],
        'In Progress' => ['requester' => [], 'agent' => ['pause', 'resolve', 'reassign'], 'admin' => ['pause', 'resolve', 'reassign']],
        'Pending' => ['requester' => ['respond'], 'agent' => ['resume', 'resolve'], 'admin' => ['resume', 'resolve']],
        'Resolved' => ['requester' => ['confirm', 'reopen'], 'agent' => [], 'admin' => ['confirm']],
        'Closed' => ['requester' => ['reopen'], 'agent' => [], 'admin' => ['reopen']],
        'Rejected' => ['requester' => ['resubmit'], 'agent' => [], 'admin' => ['resubmit']],
        'Reopen' => ['requester' => [], 'agent' => ['accept', 'reject'], 'admin' => ['accept', 'reject']],
    ];

    public function canTransition(Ticket $ticket, string $newStatus, string $role): bool
    {
        $currentStatus = $ticket->status->value;

        if (!isset(self::TRANSITIONS[$currentStatus])) {
            return false;
        }

        if (!in_array($newStatus, self::TRANSITIONS[$currentStatus])) {
            return false;
        }

        $allowedRoles = $this->getAllowedRoles($currentStatus, $newStatus);

        return in_array($role, $allowedRoles);
    }

    public function getAllowedRoles(string $currentStatus, string $newStatus): array
    {
        $roleMap = [
            'requester' => ['requester' => true, 'staff' => true],
            'agent' => ['agent' => true],
            'admin' => ['admin' => true],
        ];

        $allowed = [];

        if (!isset(self::ROLE_PERMISSIONS[$currentStatus])) {
            return [];
        }

        foreach (self::ROLE_PERMISSIONS[$currentStatus] as $role => $actions) {
            $transitionMap = [
                'New' => ['submit', 'resubmit'],
                'Assigned' => ['accept', 'start', 'reassign'],
                'In Progress' => ['start', 'resume'],
                'Pending' => ['pause', 'respond'],
                'Resolved' => ['resolve', 'confirm'],
                'Closed' => ['confirm'],
                'Rejected' => ['reject'],
                'Reopen' => ['reopen'],
            ];

            $action = $this->getActionForTransition($currentStatus, $newStatus);

            if ($action && in_array($action, $actions)) {
                $allowed = array_merge($allowed, array_keys($roleMap[$role] ?? []));
            }
        }

        if ($newStatus === 'Assigned' && $currentStatus === 'New') {
            $allowed[] = 'admin';
            $allowed[] = 'agent';
        }

        if ($newStatus === 'Closed' && $currentStatus === 'Resolved') {
            $allowed[] = 'requester';
            $allowed[] = 'admin';
        }

        if ($newStatus === 'Reopen' && in_array($currentStatus, ['Resolved', 'Closed'])) {
            $allowed[] = 'requester';
        }

        return array_unique($allowed);
    }

    private function getActionForTransition(string $from, string $to): ?string
    {
        $map = [
            'Draft-New' => 'submit',
            'New-Assigned' => 'accept',
            'New-Rejected' => 'reject',
            'Assigned-In Progress' => 'start',
            'Assigned-Assigned' => 'reassign',
            'Assigned-Rejected' => 'reject',
            'In Progress-Pending' => 'pause',
            'In Progress-Resolved' => 'resolve',
            'In Progress-Assigned' => 'reassign',
            'Pending-In Progress' => 'resume',
            'Pending-Resolved' => 'resolve',
            'Resolved-Closed' => 'confirm',
            'Resolved-Reopen' => 'reopen',
            'Closed-Reopen' => 'reopen',
            'Rejected-New' => 'resubmit',
            'Reopen-Assigned' => 'accept',
            'Reopen-Rejected' => 'reject',
        ];

        return $map["{$from}-{$to}"] ?? null;
    }

    public function getAvailableTransitions(Ticket $ticket, string $role): array
    {
        $currentStatus = $ticket->status->value;
        $available = [];

        if (!isset(self::TRANSITIONS[$currentStatus])) {
            return [];
        }

        foreach (self::TRANSITIONS[$currentStatus] as $nextStatus) {
            if ($this->canTransition($ticket, $nextStatus, $role)) {
                $available[] = $nextStatus;
            }
        }

        return $available;
    }
}
