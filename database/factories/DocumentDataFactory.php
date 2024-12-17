<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentData>
 */
class DocumentDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'field_id' => \App\Models\Field::factory(),
            'document_id' => \App\Models\Document::factory(),
            'value' => $this->faker->text,
            'confidence' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
