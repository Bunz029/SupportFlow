<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin and agents can view all feedback
        return $user->isAdmin() || $user->isAgent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Feedback $feedback): bool
    {
        // Admin can view all feedback
        if ($user->isAdmin()) {
            return true;
        }

        // Agent can view feedback for tickets they handled
        if ($user->isAgent()) {
            return $feedback->agent_id === $user->id;
        }

        // Client can only view their own feedback
        return $user->id === $feedback->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only clients can create feedback
        return $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Feedback $feedback): bool
    {
        // Admin can update any feedback
        if ($user->isAdmin()) {
            return true;
        }

        // Client can only update their own feedback
        return $user->id === $feedback->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        // Only admin can delete feedback
        return $user->isAdmin();
    }
} 