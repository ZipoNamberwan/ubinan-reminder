<?php

namespace Database\Factories;

use App\Models\MonthlySchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlySchedule>
 */
class MonthlyScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MonthlySchedule::class;

    public function definition()
    {
        return [
            'bs_id' => fake()->numberBetween(1, 3000),
            'name' => fake()->name(),
            'nks' => fake()->numberBetween(10000, 20000),
            'sample_number' => fake()->numberBetween(1, 20),
            'commodity_id' => fake()->numberBetween(1, 6),
            'sample_type_id' => 1,
            'month_id' => fake()->numberBetween(1, 12),
            'year_id' => fake()->numberBetween(1, 2),
            'user_id' => fake()->randomElement([3, 4, 6, 7]),
            'address' => fake()->name(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (MonthlySchedule $monthlySchedule) {
            // ...
        })->afterCreating(function (MonthlySchedule $monthlySchedule) {
            return 'sdasdsad';
        });
    }
}
