<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'payee_id', 'payer_id', 'amount', 'status', 'key', 'meta'];

    protected $casts = ['amount' => 'decimal:2', 'meta' => 'array'];

    //Relacionamentos
    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'transfer_id');
    }


}
