<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conductors', function (Blueprint $table) {
            $table->unsignedBigInteger('online_seconds_month')->default(0)->after('online_since');
            $table->string('online_month', 7)->nullable()->after('online_seconds_month');
        });
    }

    public function down(): void
    {
        Schema::table('conductors', function (Blueprint $table) {
            $table->dropColumn(['online_month', 'online_seconds_month']);
        });
    }
};
