<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'password'          => Hash::make('password'),
            'role'              => 'user',
            'document'          => (int) $this->faker->numerify('###########'),
            'email_verified_at' => now(),
        ];
    }

    public function user(): static
    {
        return $this->state(fn () => [
            'role'     => 'user',
            'name'     => $this->faker->name(),
            'document' => (int) $this->faker->numerify('###########'),
        ]);
    }

    public function merchant(): static
    {
        return $this->state(fn () => [
            'role'     => 'merchant',
            'name'     => $this->faker->company(),
            'document' => (int) $this->faker->numerify('##############'), 
        ]);
    }
}
