<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('transfer_id');
            $t->uuid('receiver_id');
            $t->enum('status', ['pending', 'send', 'failed'])->index();
            $t->unsignedInteger('attempts')->default(0);
            $t->text('last_error')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('transfer_id')->references('id')->on('transfers')->restrictOnDelete();
            $t->foreign('receiver_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
