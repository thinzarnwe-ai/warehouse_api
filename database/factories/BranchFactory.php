<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'branch_name' => $this->faker->name(),
            'branch_code' => $this->faker->name(),
            'branch_short_name' => $this->faker->name(),
        ];
    }
}
