<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => User::factory(),
            'receiver_id' => User::factory(),
            'skill_id' => Skill::factory(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'scheduled_date' => $this->faker->optional(0.6)->dateTimeBetween('+1 day', '+30 days'),
        ];
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the transaction is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Set a specific provider user.
     */
    public function forProvider(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'provider_id' => $user->id,
        ]);
    }

    /**
     * Set a specific receiver user.
     */
    public function forReceiver(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'receiver_id' => $user->id,
        ]);
    }

    /**
     * Set a specific skill.
     */
    public function forSkill(Skill $skill): static
    {
        return $this->state(fn(array $attributes) => [
            'skill_id' => $skill->id,
        ]);
    }
}
