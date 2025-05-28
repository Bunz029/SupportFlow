<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view public articles
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Article $article): bool
    {
        // Public articles can be viewed by anyone
        if ($article->visibility === 'public') {
            return true;
        }

        // Must be logged in to view non-public articles
        if (!$user) {
            return false;
        }

        // Admin can view all articles
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Agents can view internal and private articles
        if ($user && $user->isAgent()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin and agents can create articles
        return $user->isAdmin() || $user->isAgent();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        // Admin can update any article
        if ($user->isAdmin()) {
            return true;
        }

        // Agents can only update their own articles or public articles
        if ($user->isAgent()) {
            return $article->author_id === $user->id || $article->visibility === 'public';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        // Admin can delete any article
        if ($user->isAdmin()) {
            return true;
        }

        // Agents can only delete their own articles
        if ($user->isAgent()) {
            return $article->author_id === $user->id;
        }

        return false;
    }
}

 