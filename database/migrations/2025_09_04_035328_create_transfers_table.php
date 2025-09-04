<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('payer_id');
            $t->uuid('payee_id');
            $t->decimal('amount', 18, 2)->default('0');
            $t->enum('status', ['pending', 'authorized', 'completed', 'failed'])->index();
            $t->string('key')->unique();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index('created_at');
            $t->softDeletes();

            $t->foreign('payer_id')->references('id')->on('users')->restrictOnDelete();
            $t->foreign('payee_id')->references('id')->on('users')->restrictOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
