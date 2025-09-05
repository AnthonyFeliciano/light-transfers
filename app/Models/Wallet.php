<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'balance', 'user_id'];

    protected $casts = ['balance' => 'decimal:2'];

    //Relacionamentos
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'wallet_id'); 
    }
}
