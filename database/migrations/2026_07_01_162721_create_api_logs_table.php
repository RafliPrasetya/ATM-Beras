<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('machine_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('rfid_uid', 50)->nullable();

            $table->string('endpoint', 100);

            $table->string('ip_address', 45);

            $table->enum('status', ['success', 'failed', 'error']);

            $table->text('message')->nullable();

            $table->json('payload')->nullable()->comment('Request body yang diterima');

            $table->timestamps();

            // Index untuk query cepat
            $table->index(['machine_id', 'created_at']);
            $table->index('rfid_uid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
