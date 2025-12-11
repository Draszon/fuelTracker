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
            $table->date('inspection_valid_from')
                ->nullable()
                ->after('break_oil_cycle_km');

            $table->date('inspection_valid_until')
                ->nullable()
                ->after('break_oil_cycle_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'inspection_valid_from',
                'inspection_valid_until'
            ]);
        });
    }
};
