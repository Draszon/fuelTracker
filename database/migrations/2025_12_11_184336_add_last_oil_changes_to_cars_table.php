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
        Schema::table('cars', function (Blueprint $table) {
            $table->integer('last_oil_change_km')
                ->nullable()
                ->after('oil_change_cycle_km');

            $table->date('last_oil_change_date')
                ->nullable()
                ->after('oil_change_cycle_year');

            $table->date('last_break_oil_change_date')
                ->nullable()
                ->after('break_oil_cycle_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'last_oil_change_km',
                'last_oil_change_date',
                'last_break_oil_change_date'
            ]);
        });
    }
};
