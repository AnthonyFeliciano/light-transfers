<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Wallet;

class UserWalletSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            User::factory()
                ->count(10)
                ->user()
                ->has(
                    Wallet::factory()->state(fn () => [
                        'balance' => fake()->randomFloat(2, 50, 1500),
                    ])
                )
                ->create();

            User::factory()
                ->count(10)
                ->merchant()
                ->has(
                    Wallet::factory()->state(fn () => [
                        'balance' => fake()->randomFloat(2, 500, 10000),
                    ])
                )
                ->create();

            User::factory()
                ->user()
                ->has(Wallet::factory()->state(['balance' => 1000.00]))
                ->create([
                    'name'  => 'Usuário Demo',
                    'email' => 'demo@example.com',
                ]);
        });
    }
}
