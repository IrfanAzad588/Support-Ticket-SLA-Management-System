<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return false; // Admin is allowed by before().
    }

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isCustomer()) {
            return $ticket->requester_id === $user->id;
        }

        if ($user->isAgent()) {
            return $ticket->assigned_to === $user->id
                || $ticket->assigned_to === null;
        }

        return false;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAgent()
            && $ticket->assigned_to === $user->id;
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        if ($user->isCustomer()) {
            return $ticket->requester_id === $user->id;
        }

        return $user->isAgent()
            && $ticket->assigned_to === $user->id;
    }

    public function import(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function internalNote(User $user, Ticket $ticket): bool
    {
        return $user->isAgent()
            && $ticket->assigned_to === $user->id;
    }

    public function reopen(User $user, Ticket $ticket): bool
    {
        return $user->isCustomer()
            && $ticket->requester_id === $user->id
            && $ticket->status === Ticket::STATUS_RESOLVED;
    }
}
