<?php

namespace Database\Factories;

use App\Models\HarvestSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HarvestSchedule>
 */
class HarvestScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = HarvestSchedule::class;

    public function definition()
    {
        return [
            'date' => '2023-12-' . sprintf("%02d", fake()->numberBetween(1, 31)),
            // 'date' => '2023-12-' . sprintf("%02d", fake()->numberBetween(l2, 4)),            
            'respondent_name' => fake()->name(),
            'monthly_schedule_id' => fake()->unique()->numberBetween(1, 500),
        ];
    }
}
