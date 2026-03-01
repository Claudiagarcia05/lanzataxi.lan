<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pagos')) {
            return;
        }

        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'transaction_id')) {
                $table->string('transaction_id', 255)->nullable()->after('status');
            }
        });

        // Para instalaciones antiguas en MySQL donde method/status eran enum,
        // los convertimos a VARCHAR para soportar 'app', 'stripe', etc.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE pagos MODIFY method VARCHAR(20) NOT NULL");
            } catch (Throwable $e) {
                // No bloquear la migración si ya está modificado
            }

            try {
                DB::statement("ALTER TABLE pagos MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
            } catch (Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        // No revertimos el tipo de columnas; es una migración de compatibilidad.
        if (!Schema::hasTable('pagos')) {
            return;
        }

        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
        });
    }
};
