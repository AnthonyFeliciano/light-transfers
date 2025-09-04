<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('transfer_id');
            $t->uuid('wallet_id');
            $t->enum('type', ['credit','debit'])->index();
            $t->decimal('amount', 18, 2);
            $t->softDeletes();
            $t->timestamps();

            $t->foreign('transfer_id')->references('id')->on('transfers')->restrictOnDelete();
            $t->foreign('wallet_id')->references('id')->on('wallets')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
