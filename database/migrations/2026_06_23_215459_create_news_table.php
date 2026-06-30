<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {

            $table->id();

            $table->string('judul');

            $table->longText('konten');

            $table->string('gambar')->nullable();

            $table->enum('status', [
                'publish',
                'draft'
            ])->default('draft');

            $table->unsignedBigInteger('created_by');

            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('admins')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
