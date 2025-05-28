<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sla extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'response_time_hours',
        'resolution_time_hours',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_time_hours' => 'integer',
        'resolution_time_hours' => 'integer',
    ];

    /**
     * Get the ticket SLAs for this SLA definition.
     */
    public function ticketSlas(): HasMany
    {
        return $this->hasMany(TicketSla::class);
    }

    /**
     * Scope a query to only include SLAs for a specific priority.
     */
    public function scopeForPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
} 