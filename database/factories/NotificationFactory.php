<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'id'          => (string) Str::uuid(),
            'transfer_id' => Transfer::factory(),
            'receiver_id' => User::factory(),     
            'status'      => 'pending',          
            'attempts'    => 0,
            'last_error'  => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }

    public function pending(): static { return $this->state(fn() => ['status' => 'pending']); }
    public function send(): static    { return $this->state(fn() => ['status' => 'send']); }
    public function failed(): static  { return $this->state(fn() => ['status' => 'failed']); }
}
