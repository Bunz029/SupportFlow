<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this role is an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Check if this role is an agent role.
     */
    public function isAgent(): bool
    {
        return $this->name === 'agent';
    }

    /**
     * Check if this role is a client role.
     */
    public function isClient(): bool
    {
        return $this->name === 'client';
    }
} 