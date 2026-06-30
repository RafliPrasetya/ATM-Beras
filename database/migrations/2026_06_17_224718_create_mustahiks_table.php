<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mustahiks', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table->string('rfid_uid')
                ->unique();

            $table->string('nik', 20)
                ->unique();

            $table->string('no_hp')
                ->nullable();

            $table->text('alamat');

            $table->unsignedBigInteger(
                'village_id'
            );

            $table->integer(
                'jatah_beras_gram'
            )->default(5000);

            $table->timestamps();

            $table->foreign('village_id')
                ->references('id')
                ->on('villages')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mustahiks');
    }
};
