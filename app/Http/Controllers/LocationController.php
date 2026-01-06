<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Get all active countries.
     */
    public function getCountries(): JsonResponse
    {
        $countries = Country::active()->ordered()->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Get cities for a specific country.
     */
    public function getCities(Request $request): JsonResponse
    {
        $countryId = $request->get('country_id');

        if (!$countryId) {
            return response()->json([
                'success' => false,
                'message' => 'Country ID is required'
            ], 400);
        }

        $cities = City::active()
            ->inCountry($countryId)
            ->ordered()
            ->get(['id', 'name', 'region']);

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    /**
     * Get regions (legacy mapping for backward compatibility).
     * This maps the old static regions to new dynamic cities.
     */
    public function getRegions(): JsonResponse
    {
        // For now, return the static regions as they were
        // Later this can be replaced with actual city data
        $regions = [
            ['id' => 'east', 'name' => 'East'],
            ['id' => 'south_east', 'name' => 'South East'],
            ['id' => 'south_west', 'name' => 'South West'],
            ['id' => 'west_midlands', 'name' => 'West Midlands'],
            ['id' => 'london', 'name' => 'London'],
            ['id' => 'north_east', 'name' => 'North East'],
            ['id' => 'north_west', 'name' => 'North West'],
            ['id' => 'yorkshire_humber', 'name' => 'Yorkshire & Humber'],
            ['id' => 'east_midlands', 'name' => 'East Midlands'],
            ['id' => 'northern_ireland', 'name' => 'Northern Ireland'],
            ['id' => 'scotland', 'name' => 'Scotland'],
            ['id' => 'wales', 'name' => 'Wales']
        ];

        return response()->json([
            'success' => true,
            'data' => $regions
        ]);
    }

    /**
     * Seed basic countries and cities data.
     * This is a temporary method until proper migrations work.
     */
    public function seedLocations(): JsonResponse
    {
        try {
            // Check if UK already exists
            $uk = Country::where('code', 'GBR')->first();

            if (!$uk) {
                $uk = Country::create([
                    'name' => 'United Kingdom',
                    'code' => 'GBR',
                    'region' => 'Europe',
                    'is_active' => true
                ]);

                // Create cities/regions for UK
                $citiesData = [
                    ['name' => 'London', 'region' => 'London'],
                    ['name' => 'Manchester', 'region' => 'north_west'],
                    ['name' => 'Birmingham', 'region' => 'west_midlands'],
                    ['name' => 'Liverpool', 'region' => 'north_west'],
                    ['name' => 'Leeds', 'region' => 'yorkshire_humber'],
                    ['name' => 'Glasgow', 'region' => 'scotland'],
                    ['name' => 'Edinburgh', 'region' => 'scotland'],
                    ['name' => 'Cardiff', 'region' => 'wales'],
                    ['name' => 'Bristol', 'region' => 'south_west'],
                    ['name' => 'Newcastle', 'region' => 'north_east'],
                    ['name' => 'Brighton', 'region' => 'south_east'],
                    ['name' => 'Cambridge', 'region' => 'east'],
                    ['name' => 'Oxford', 'region' => 'south_east'],
                    ['name' => 'Nottingham', 'region' => 'east_midlands'],
                    ['name' => 'Leicester', 'region' => 'east_midlands'],
                    ['name' => 'Belfast', 'region' => 'northern_ireland'],
                ];

                foreach ($citiesData as $cityData) {
                    City::create([
                        'name' => $cityData['name'],
                        'country_id' => $uk->id,
                        'region' => $cityData['region'],
                        'is_active' => true
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Locations seeded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error seeding locations: ' . $e->getMessage()
            ], 500);
        }
    }
}
