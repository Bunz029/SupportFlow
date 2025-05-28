<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tickets
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin can view all tickets
        if ($user->isAdmin()) {
            return true;
        }

        // Agent can view all assigned tickets and unassigned tickets
        if ($user->isAgent()) {
            return true;
        }

        // Client can only view their own tickets
        return $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create tickets
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Admin can update all tickets
        if ($user->isAdmin()) {
            return true;
        }

        // Agent can update tickets assigned to them
        if ($user->isAgent() && ($ticket->agent_id === $user->id || $ticket->agent_id === null)) {
            return true;
        }

        // Client can only update their own tickets and only certain fields (e.g., status to closed)
        return $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Only admin can delete tickets
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can assign agents to the model.
     */
    public function assignAgent(User $user, Ticket $ticket): bool
    {
        // Only admin and agents can assign agents
        return $user->isAdmin() || $user->isAgent();
    }

    /**
     * Determine whether the user can update the ticket's status.
     */
    public function updateStatus(User $user, Ticket $ticket): bool
    {
        // Admin and agents can update status
        if ($user->isAdmin() || ($user->isAgent() && $ticket->agent_id === $user->id)) {
            return true;
        }

        // Client can close their own tickets
        if ($user->id === $ticket->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the ticket's priority.
     */
    public function updatePriority(User $user, Ticket $ticket): bool
    {
        // Only admin and assigned agent can update priority
        return $user->isAdmin() || ($user->isAgent() && $ticket->agent_id === $user->id);
    }
} 