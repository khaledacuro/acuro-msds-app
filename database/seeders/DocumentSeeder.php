<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Create 100 random documents
        foreach (range(1, 1000) as $index) {
            Document::create([
                'file_name' => $faker->word . '.pdf',
                'created_at' => $faker->dateTimeBetween('2024-11-01', 'now'),
                'updated_at' => $faker->dateTimeBetween('2024-11-01', 'now'),
              
            ]);
        }
    }
}