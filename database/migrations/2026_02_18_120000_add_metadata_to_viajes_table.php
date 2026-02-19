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
        Schema::table('viajes', function (Blueprint $table) {
            $table->string('pickup_address')->nullable()->after('dropoff_lng');
            $table->string('dropoff_address')->nullable()->after('pickup_address');
            $table->unsignedTinyInteger('rating')->nullable()->after('co2_saved');
            $table->text('comment')->nullable()->after('rating');
            $table->timestamp('end_time')->nullable()->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            $table->dropColumn(['pickup_address', 'dropoff_address', 'rating', 'comment', 'end_time']);
        });
    }
};

