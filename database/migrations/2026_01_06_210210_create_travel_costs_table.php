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
        Schema::create('travel_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->date('date');
            $table->string('direction');
            $table->integer('distance');
            $table->integer('travel_expenses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_costs');
    }
};
