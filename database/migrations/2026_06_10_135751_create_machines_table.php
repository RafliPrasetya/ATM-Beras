<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {

            $table->id();

            $table->string('machine_code')->unique();

            $table->unsignedBigInteger('village_id');

            $table->string('lokasi_penempatan');

            $table->enum('status_mesin', [
                'aktif',
                'nonaktif',
                'maintenance'
            ])->default('nonaktif');

            $table->integer('stok_beras_kg')->default(0);

            $table->dateTime('jadwal_mulai')->nullable();

            $table->dateTime('jadwal_selesai')->nullable();

            $table->enum('status_penjadwalan', [
                'aktif',
                'nonaktif'
            ])->default('nonaktif');

            $table->timestamps();

            $table->foreign('village_id')
                ->references('id')
                ->on('villages')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
