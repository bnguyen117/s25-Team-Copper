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
        Schema::table('what_if_reports', function (Blueprint $table) {
            //Change
            $table->renameColumn('current_monthly_debt_payment', 'original_monthly_debt_payment');
            $table->renameColumn('minimum_monthly_debt_payment', 'original_minimum_debt_payment');
            //Add
            $table->decimal('original_interest_rate', 5, 2)->nullable()->after('original_monthly_debt_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('what_if_reports', function (Blueprint $table) {
            // Revert
            $table->renameColumn('original_monthly_debt_payment', 'current_monthly_debt_payment');
            $table->renameColumn('original_minimum_debt_payment', 'minimum_monthly_debt_payment');
            // Drop
            $table->dropColumn('original_interest_rate');
        });
    }
};
