<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->timestamp('scheduled_for')->nullable()->after('end_time');
            $table->integer('pasajeros')->default(1)->after('scheduled_for');
            $table->string('luggage')->default('0')->after('pasajeros');
            $table->string('pago_method')->default('cash')->after('luggage');
            $table->text('notes')->nullable()->after('pago_method');
        });
    }

    public function down()
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropColumn(['scheduled_for', 'pasajeros', 'luggage', 'pago_method', 'notes']);
        });
    }
};

