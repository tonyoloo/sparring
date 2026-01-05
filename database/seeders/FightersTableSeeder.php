<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fighter;

class FightersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Professional data based on Spartner website
        $professionals = [
            [
                'name' => 'Bobby Mills',
                'primary_profession' => 'boxing_coach',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Matt Tucker',
                'primary_profession' => 'strength_conditioning',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'JJA Striking Coach',
                'primary_profession' => 'striking_coach',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Liam Hardman',
                'primary_profession' => 'boxing_coach',
                'category' => 'professionals',
                'region' => 'north_west',
                'profession_count' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Callum O\'Connor',
                'primary_profession' => 'nutritionist',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Rory Robertson',
                'primary_profession' => 'muay_thai_coach',
                'category' => 'professionals',
                'region' => 'scotland',
                'profession_count' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Terrence Yu',
                'primary_profession' => 'bjj_coach',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Chris Clayton',
                'primary_profession' => 'nutritionist',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Liam McCully',
                'primary_profession' => 'bjj_coach',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Wes Pauline',
                'primary_profession' => 'coaching',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Sean Headon',
                'primary_profession' => 'physiotherapist',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'badge_level' => 'bronze',
                'is_active' => true,
            ],
            [
                'name' => 'Paul Atkinson',
                'primary_profession' => 'strength_conditioning',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dan Carter',
                'primary_profession' => 'sports_psychologist',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mahdi Maryan',
                'primary_profession' => 'physiotherapist',
                'category' => 'professionals',
                'region' => 'london',
                'profession_count' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ben Thomas',
                'primary_profession' => 'strength_conditioning',
                'category' => 'professionals',
                'region' => 'yorkshire_humber',
                'profession_count' => 2,
                'is_active' => true,
            ],
        ];

        // Sample Fighters data
        $fighters = [
            [
                'name' => 'Tony Oloo',
                'category' => 'fighters',
                'discipline' => 'boxing',
                'experience' => 'beginner',
                'level' => 'amateur',
                'is_active' => true,
            ],
            [
                'name' => 'Naffay Mahmood',
                'category' => 'fighters',
                'discipline' => 'boxing',
                'experience' => 'intermediate',
                'level' => 'amateur',
                'is_active' => true,
            ],
            [
                'name' => 'Lee',
                'category' => 'fighters',
                'discipline' => 'mma',
                'age' => 38,
                'weight' => 80,
                'experience' => 'intermediate',
                'level' => 'amateur',
                'region' => 'london',
                'is_active' => true,
            ],
            [
                'name' => 'Diyan Uzunov',
                'category' => 'fighters',
                'discipline' => 'mma',
                'age' => 27,
                'weight' => 87,
                'experience' => 'beginner',
                'level' => 'amateur',
                'region' => 'london',
                'is_active' => true,
            ],
            [
                'name' => 'Conner Foley',
                'category' => 'fighters',
                'discipline' => 'boxing',
                'age' => 31,
                'weight' => 73,
                'experience' => 'beginner',
                'level' => 'amateur',
                'region' => 'london',
                'is_active' => true,
            ],
        ];

        foreach ($professionals as $professional) {
            Fighter::create($professional);
        }

        foreach ($fighters as $fighter) {
            Fighter::create($fighter);
        }
    }
}
