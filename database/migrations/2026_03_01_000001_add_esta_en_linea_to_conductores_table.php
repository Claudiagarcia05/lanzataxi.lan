<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = Schema::hasTable('conductors')
            ? 'conductors'
            : (Schema::hasTable('conductores') ? 'conductores' : null);

        if (!$tableName || Schema::hasColumn($tableName, 'esta_en_linea')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->boolean('esta_en_linea')->default(false);
        });
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('conductors')
            ? 'conductors'
            : (Schema::hasTable('conductores') ? 'conductores' : null);

        if (!$tableName || !Schema::hasColumn($tableName, 'esta_en_linea')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('esta_en_linea');
        });
    }
};
