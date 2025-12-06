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
        Schema::create('switch_lamp', function (Blueprint $table) {
            $table->id();
            $table->enum('lampu1', ['on','off'])->default('off');
            $table->enum('lampu2', ['on','off'])->default('off');
            $table->enum('lampu3', ['on','off'])->default('off');
            $table->enum('lampu4', ['on','off'])->default('off');
            $table->enum('lampu5', ['on','off'])->default('off');
            $table->enum('lampu6', ['on','off'])->default('off');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switch_lamp');
    }
};
