<?php

namespace Database\Factories;

use App\Domain\Enum\GroupCategoryEnum;
use App\Models\Customer;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{

    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category'   => GroupCategoryEnum::TRIP->value,
            'name'       => fake()->word(),
            'created_by' => Customer::factory(),
            'final_currency' => 'EUR'
        ];
    }
}
