<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = config('institutions');

        foreach ($institutions['banks'] as $slug => $data) {
            Institution::firstOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'type' => 'bank',
                'color' => $data['color'],
                'slug' => $slug,
                'is_active' => true,
            ]);
        }

        foreach ($institutions['ewallets'] as $slug => $data) {
            Institution::firstOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'type' => 'ewallet',
                'color' => $data['color'],
                'slug' => $slug,
                'is_active' => true,
            ]);
        }

        foreach ($institutions['cash'] as $slug => $data) {
            Institution::firstOrCreate(['slug' => $slug], [
                'name' => $data['name'],
                'type' => 'cash',
                'color' => $data['color'],
                'slug' => $slug,
                'is_active' => true,
            ]);
        }
    }
}
