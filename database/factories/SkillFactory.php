<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement([
                'Technology',
                'Design',
                'Business',
                'Language',
                'Music',
                'Sports',
                'Crafts',
                'Education',
                'Finance',
                'Health',
            ]),
            'type' => $this->faker->randomElement(['offered', 'requested']),
        ];
    }

    /**
     * Indicate that the skill is offered.
     */
    public function offered(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'offered',
        ]);
    }

    /**
     * Indicate that the skill is requested.
     */
    public function requested(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'requested',
        ]);
    }
}
