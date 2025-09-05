<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'document',
        'role',
        'password'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Relacionamentos
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'payer_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'payee_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }
}
