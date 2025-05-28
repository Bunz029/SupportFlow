<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSla extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_sla';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'sla_id',
        'response_due_at',
        'resolution_due_at',
        'response_breached',
        'resolution_breached',
        'first_response_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_due_at' => 'datetime',
        'resolution_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'response_breached' => 'boolean',
        'resolution_breached' => 'boolean',
    ];

    /**
     * Get the ticket that this SLA belongs to.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the SLA definition.
     */
    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class);
    }

    /**
     * Check if response SLA is breached.
     */
    public function isResponseBreached(): bool
    {
        if ($this->first_response_at) {
            return $this->first_response_at->gt($this->response_due_at);
        }

        return now()->gt($this->response_due_at);
    }

    /**
     * Check if resolution SLA is breached.
     */
    public function isResolutionBreached(): bool
    {
        if ($this->ticket->status === 'closed') {
            return $this->ticket->updated_at->gt($this->resolution_due_at);
        }

        return now()->gt($this->resolution_due_at);
    }

    /**
     * Scope a query to only include breached SLAs.
     */
    public function scopeBreached($query)
    {
        return $query->where(function ($query) {
            $query->where('response_breached', true)
                  ->orWhere('resolution_breached', true);
        });
    }

    /**
     * Scope a query to only include non-breached SLAs.
     */
    public function scopeNonBreached($query)
    {
        return $query->where('response_breached', false)
                     ->where('resolution_breached', false);
    }
} 