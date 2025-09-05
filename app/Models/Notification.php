<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'transfer_id', 'receiver_id', 'status', 'attempts', 'last_error'];

    protected $casts = ['attempts' => 'integer' ];
    
    //Relacionamentos
    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
