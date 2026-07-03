<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mustahiks', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif')
                ->after('jatah_beras_gram');
        });
    }

    public function down(): void
    {
        Schema::table('mustahiks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
