<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->string('email')->unique();
            $t->unsignedBigInteger('document')->unique(); 
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->enum('role', ['user','merchant'])->index();
            $t->timestamps();
            $t->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
