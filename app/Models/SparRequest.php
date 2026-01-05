<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'message',
        'requested_date',
        'location',
        'notes',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_date' => 'datetime',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender of the spar request.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Fighter::class, 'sender_id');
    }

    /**
     * Get the receiver of the spar request.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Fighter::class, 'receiver_id');
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted requests.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for requests sent by a user.
     */
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * Scope for requests received by a user.
     */
    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * Check if request can be cancelled by sender.
     */
    public function canBeCancelledBy($userId): bool
    {
        return $this->sender_id === $userId && in_array($this->status, ['pending', 'accepted']);
    }

    /**
     * Check if request can be responded to by receiver.
     */
    public function canBeRespondedToBy($userId): bool
    {
        return $this->receiver_id === $userId && $this->status === 'pending';
    }

    /**
     * Check if request can be marked as completed.
     */
    public function canBeCompletedBy($userId): bool
    {
        return ($this->sender_id === $userId || $this->receiver_id === $userId) && $this->status === 'accepted';
    }
}
