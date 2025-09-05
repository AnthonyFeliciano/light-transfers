<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerEntry extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'transfer_id', 'wallet_id', 'amount', 'type'];

    protected $casts = ['amount' => 'decimal:2'];
    
    //Relacionamentos
    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
