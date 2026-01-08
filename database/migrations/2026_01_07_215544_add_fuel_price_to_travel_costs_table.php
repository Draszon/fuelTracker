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
        Schema::table('travel_costs', function (Blueprint $table) {
            $table->integer('fuel_costs')
                ->after('travel_expenses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_costs', function (Blueprint $table) {
            $table->dropColumn([
                'fuel_costs',
            ]);
        });
    }
};
