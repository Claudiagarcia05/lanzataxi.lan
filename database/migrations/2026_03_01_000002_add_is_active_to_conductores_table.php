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

        if (!$tableName || Schema::hasColumn($tableName, 'is_active')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
        });
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('conductors')
            ? 'conductors'
            : (Schema::hasTable('conductores') ? 'conductores' : null);

        if (!$tableName || !Schema::hasColumn($tableName, 'is_active')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
