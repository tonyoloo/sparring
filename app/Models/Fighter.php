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
        'country_id',
        'city_id',
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
     * Get fighters by country scope
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Get fighters by city scope
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
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

    /**
     * Get the country associated with this fighter.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the city associated with this fighter.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Get formatted location string (City, Country).
     */
    public function getLocationAttribute()
    {
        // If relationships are loaded, use them
        if ($this->relationLoaded('city') && $this->relationLoaded('country')) {
            $city = $this->getRelation('city');
            $country = $this->getRelation('country');

            if ($city && $country) {
                return $city->name . ', ' . $country->name;
            } elseif ($country) {
                return $country->name;
            } elseif ($city) {
                return $city->name;
            }
        }

        // Use the new country_id and city_id columns
        $location = '';

        if ($this->city_id) {
            $city = City::find($this->city_id);
            if ($city) {
                $country = $city->country;
                $location = $country ? $city->name . ', ' . $country->name : $city->name;
            }
        } elseif ($this->country_id) {
            $country = Country::find($this->country_id);
            if ($country) {
                $location = $country->name;
            }
        }

        // Fallback to region if new columns are empty (backward compatibility)
        if (empty($location) && $this->region) {
            $city = City::find($this->region);
            if ($city) {
                $country = $city->country;
                $location = $country ? $city->name . ', ' . $country->name : $city->name;
            } else {
                $country = Country::find($this->region);
                if ($country) $location = $country->name;
            }
        }

        return $location ?: 'Unknown Location';
    }
}
