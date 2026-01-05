<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fighter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'region',
        'discipline',
        'stance',
        'experience',
        'level',
        'height',
        'weight',
        'age',
        'primary_profession',
        'category',
        'profile_image',
        'bio',
        'contact_info',
        'badge_level',
        'profession_count',
        'gym_type',
        'spar_amount',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'spar_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the fighters by category scope
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get active fighters scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get fighters by discipline scope
     */
    public function scopeByDiscipline($query, $discipline)
    {
        return $query->where('discipline', $discipline);
    }

    /**
     * Get fighters by region scope
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Get the photos for the fighter.
     */
    public function photos()
    {
        return $this->hasMany(FighterPhoto::class)->ordered();
    }

    /**
     * Get the primary photo for the fighter.
     */
    public function primaryPhoto()
    {
        return $this->hasOne(FighterPhoto::class)->where('is_primary', true);
    }
}
