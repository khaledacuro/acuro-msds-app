<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            ['field_name' => 'Trade name'],
            ['field_name' => 'Physical state'],
            ['field_name' => 'Boiling point'],
            ['field_name' => 'Flash point'],
            ['field_name' => 'Specific gravity'],
            ['field_name' => 'Density'],
            ['field_name' => 'Water solubility'],
            ['field_name' => 'Viscosity'],
            ['field_name' => 'UN number'],
            ['field_name' => 'Transport hazard class(es)'],
            ['field_name' => 'Packing group'],
        ];

        foreach ($fields as $field) {
            Field::create($field);
        }
    }
}