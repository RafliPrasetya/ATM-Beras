<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (
            Blueprint $table
        ) {

            $table->id();

            $table->foreignId('mustahik_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('machine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('jumlah_ambil_gram');

            $table->dateTime('tanggal_pengambilan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
