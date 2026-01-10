<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discipline;

class DisciplinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disciplines = [
            ['name' => 'Boxing', 'code' => 'boxing', 'sort_order' => 1],
            ['name' => 'MMA', 'code' => 'mma', 'sort_order' => 2],
            ['name' => 'Taekwondo', 'code' => 'taekwondo', 'sort_order' => 3],
            ['name' => 'Karate', 'code' => 'karate', 'sort_order' => 4],
            ['name' => 'Wrestling', 'code' => 'wrestling', 'sort_order' => 5],
            ['name' => 'Jiu Jitsu', 'code' => 'jiu_jitsu', 'sort_order' => 6],
            ['name' => 'Kick Boxing', 'code' => 'kick_boxing', 'sort_order' => 7],
            ['name' => 'Thai Boxing', 'code' => 'thai_boxing', 'sort_order' => 8],
            ['name' => 'Judo', 'code' => 'judo', 'sort_order' => 9],
            ['name' => 'Kung Fu', 'code' => 'kung_fu', 'sort_order' => 10],
            ['name' => 'Tai Chi', 'code' => 'tai_chi', 'sort_order' => 11],
            ['name' => 'Wing Chun', 'code' => 'wing_chun', 'sort_order' => 12],
            ['name' => 'Krav Maga', 'code' => 'krav_maga', 'sort_order' => 13],
            ['name' => 'Aikido', 'code' => 'aikido', 'sort_order' => 14],
            ['name' => 'Choi Kwang Do', 'code' => 'choi_kwang_do', 'sort_order' => 15],
            ['name' => 'Capoeira', 'code' => 'capoeira', 'sort_order' => 16],
            ['name' => 'Ninjutsu', 'code' => 'ninjutsu', 'sort_order' => 17],
            ['name' => 'Kendo', 'code' => 'kendo', 'sort_order' => 18],
            ['name' => 'Kobudo', 'code' => 'kobudo', 'sort_order' => 19],
            ['name' => 'Hapkido', 'code' => 'hapkido', 'sort_order' => 20],
            ['name' => 'Tang Soo Do', 'code' => 'tang_soo_do', 'sort_order' => 21],
        ];

        foreach ($disciplines as $discipline) {
            Discipline::firstOrCreate(
                ['code' => $discipline['code']],
                [
                    'name' => $discipline['name'],
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $discipline['sort_order'],
                ]
            );
        }
    }
}
