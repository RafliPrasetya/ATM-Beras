<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_tokens', function (Blueprint $table) {

            $table->id();

            $table->foreignId('machine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('token', 80)->unique();

            $table->string('name')->nullable()->comment('Label token, misal: RPi-ATM001');

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_tokens');
    }
};
