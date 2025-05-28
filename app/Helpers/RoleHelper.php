<?php

namespace App\Helpers;

use App\Models\User;

class RoleHelper
{
    /**
     * Checks if user has a specific role.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public static function hasRole(User $user, string $role): bool
    {
        return $user->role === $role;
    }

    /**
     * Checks if user has any of the given roles.
     *
     * @param User $user
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole(User $user, array $roles): bool
    {
        return in_array($user->role, $roles);
    }

    /**
     * Checks if user has all of the given roles.
     *
     * @param User $user
     * @param array $roles
     * @return bool
     */
    public static function hasAllRoles(User $user, array $roles): bool
    {
        // Only one role per user, so this is true if user's role is in the array and array has only one element
        return count($roles) === 1 && $user->role === $roles[0];
    }

    /**
     * Get formatted display name for a role.
     *
     * @param string $role
     * @return string
     */
    public static function getRoleDisplayName(string $role): string
    {
        return match ($role) {
            'admin' => 'Administrator',
            'agent' => 'Support Agent',
            'client' => 'Customer',
            default => ucfirst($role),
        };
    }
} 