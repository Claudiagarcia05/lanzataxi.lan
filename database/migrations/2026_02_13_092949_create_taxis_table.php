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
        Schema::create('taxis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conductor_id')->constrained()->onDelete('cascade');
            $table->string('plate')->unique();
            $table->string('model');
            $table->integer('capacity');
            $table->string('color')->nullable();
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxis');
    }
};

