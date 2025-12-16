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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('licence_plate');
            $table->string('car_type');
            $table->decimal('average_fuel_consumption', 5, 1);
            $table->integer('year');
            $table->integer('current_km');
            $table->integer('oil_change_cycle_km');
            $table->integer('oil_change_cycle_year');
            $table->integer('break_oil_cycle_year');
            $table->date('inspection_valid_from');
            $table->date('inspection_valid_until');
            $table->integer('last_oil_change_km');
            $table->date('last_oil_change_date');
            $table->date('last_break_oil_change_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
