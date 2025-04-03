<?php

namespace Database\Factories;

use App\Domain\Enum\GroupCategoryEnum;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{

    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->word(),
            'category' => fake()->randomElement(GroupCategoryEnum::values()),
            'created_at' => new \DateTime(),
            'final_currency' => 'EUR'
        ];
    }
}
