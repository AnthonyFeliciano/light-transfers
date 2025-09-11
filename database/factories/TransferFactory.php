<?php

namespace Database\Factories;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();

        return [
            'id'        => (string) Str::uuid(),
            'payer_id'  => $payer->id,
            'payee_id'  => $payee->id,
            'amount'    => $this->faker->randomFloat(2, 1, 1000),
            'status'    => 'completed',
            'key'       => (string) Str::uuid(),
            'created_at'=> now(),
            'updated_at'=> now(),
        ];
    }
}
