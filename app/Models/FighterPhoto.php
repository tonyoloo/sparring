<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class FighterPhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fighter_id',
        'photo_path',
        'photo_name',
        'caption',
        'is_primary',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the fighter that owns the photo.
     */
    public function fighter(): BelongsTo
    {
        return $this->belongsTo(Fighter::class);
    }

    /**
     * Get the full URL for the photo.
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }

    /**
     * Scope for primary photos.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Set this photo as primary and unset others for the same fighter.
     */
    public function makePrimary(): bool
    {
        // First, unset all primary photos for this fighter
        static::where('fighter_id', $this->fighter_id)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);

        // Then set this one as primary
        return $this->update(['is_primary' => true]);
    }

    /**
     * Delete the photo file when the model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($photo) {
            // Delete the actual file
            if ($photo->photo_path) {
                try {
                    $fullPath = storage_path('app/public/' . $photo->photo_path);
                    
                    // Use direct file deletion instead of Storage::delete() to avoid finfo dependency
                    // This prevents "Class finfo not found" errors on servers without fileinfo extension
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                } catch (\Exception $e) {
                    // Log error but don't prevent deletion of the database record
                    Log::warning('Failed to delete photo file: ' . $e->getMessage(), [
                        'photo_id' => $photo->id,
                        'photo_path' => $photo->photo_path
                    ]);
                }
            }
        });
    }
}
