<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create UK country
        $uk = Country::firstOrCreate(
            ['code' => 'GBR'],
            [
                'name' => 'United Kingdom',
                'region' => 'Europe',
                'is_active' => true
            ]
        );

        // Cities data with their regions
        $citiesData = [
            ['name' => 'London', 'region' => 'london'],
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
            ['name' => 'Sheffield', 'region' => 'yorkshire_humber'],
            ['name' => 'Bristol', 'region' => 'south_west'],
            ['name' => 'Leicester', 'region' => 'east_midlands'],
            ['name' => 'Coventry', 'region' => 'west_midlands'],
            ['name' => 'Hull', 'region' => 'yorkshire_humber'],
            ['name' => 'Plymouth', 'region' => 'south_west'],
            ['name' => 'Stoke-on-Trent', 'region' => 'west_midlands'],
            ['name' => 'Wolverhampton', 'region' => 'west_midlands'],
            ['name' => 'Derby', 'region' => 'east_midlands'],
        ];

        foreach ($citiesData as $cityData) {
            City::firstOrCreate(
                [
                    'name' => $cityData['name'],
                    'country_id' => $uk->id
                ],
                [
                    'region' => $cityData['region'],
                    'is_active' => true
                ]
            );
        }

        // Create additional countries
        $countriesData = [
            ['name' => 'United States', 'code' => 'USA', 'region' => 'North America'],
            ['name' => 'Canada', 'code' => 'CAN', 'region' => 'North America'],
            ['name' => 'Australia', 'code' => 'AUS', 'region' => 'Oceania'],
            ['name' => 'Germany', 'code' => 'DEU', 'region' => 'Europe'],
            ['name' => 'France', 'code' => 'FRA', 'region' => 'Europe'],
            ['name' => 'Spain', 'code' => 'ESP', 'region' => 'Europe'],
            ['name' => 'Italy', 'code' => 'ITA', 'region' => 'Europe'],
            ['name' => 'Netherlands', 'code' => 'NLD', 'region' => 'Europe'],
            ['name' => 'Belgium', 'code' => 'BEL', 'region' => 'Europe'],
            ['name' => 'Sweden', 'code' => 'SWE', 'region' => 'Europe'],
            ['name' => 'Norway', 'code' => 'NOR', 'region' => 'Europe'],
            ['name' => 'Denmark', 'code' => 'DNK', 'region' => 'Europe'],
            ['name' => 'Finland', 'code' => 'FIN', 'region' => 'Europe'],
            ['name' => 'Ireland', 'code' => 'IRL', 'region' => 'Europe'],
        ];

        foreach ($countriesData as $countryData) {
            Country::firstOrCreate(
                ['code' => $countryData['code']],
                [
                    'name' => $countryData['name'],
                    'region' => $countryData['region'],
                    'is_active' => true
                ]
            );
        }
    }
}
