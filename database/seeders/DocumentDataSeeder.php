<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\DocumentData;
use App\Models\Field;
use Faker\Factory as Faker;

class DocumentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $documents = Document::all();
        $fields = Field::all();

        foreach ($documents as $document) {
            foreach ($fields as $field) {
                DocumentData::create([
                    'document_id' => $document->id,
                    'field_id' => $field->id,
                    'value' => match ($field->field_name) {
                        'Trade name' => $faker->word,
                        'Physical state' => $faker->randomElement(['Solid', 'Liquid', 'Gas']),
                        'Boiling point' => $faker->randomFloat(2, 0, 100),
                        'Flash point' => $faker->randomFloat(2, 0, 100),
                        'Specific gravity' => $faker->randomFloat(2, 0, 100),
                        'Density' => $faker->randomFloat(2, 0, 100),
                        'Water solubility' => $faker->randomElement(['Soluble', 'Insoluble']),
                        'Viscosity' => $faker->randomFloat(2, 0, 100),
                        'UN number' => 'UN' . $faker->randomNumber(4),
                        'Transport hazard class(es)' => $faker->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9']),
                        'Packing group' => $faker->randomElement(['I', 'II', 'III', 'IIII']),
                    },
                    'confidence' => $faker->randomFloat(2, 80, 100),
                ]);
            }
        }
    }
}
