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
            //Rename columns
            $table->renameColumn('algorithm', 'what_if_scenario');
            $table->renameColumn('original_amount', 'original_debt_amount');
            $table->renameColumn('current_payment', 'current_monthly_debt_payment');
            $table->renameColumn('minimum_payment', 'minimum_monthly_debt_payment');
            $table->renameColumn('new_payment', 'new_monthly_debt_payment');
            
            // Add columns
            $table->foreignId('financial_goal_id')->nullable()->constrained('financial_goals')->onDelete('set null')->after('debt_id');
            $table->json('goal_impact')->nullable()->after('timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('what_if_reports', function (Blueprint $table) {
            // Change the names back
            $table->renameColumn('what_if_scenario', 'algorithm');
            $table->renameColumn('original_debt_amount', 'original_amount');
            $table->renameColumn('current_monthly_debt_payment', 'current_payment');
            $table->renameColumn('minimum_monthly_debt_payment', 'minimum_payment');
            $table->renameColumn('new_monthly_debt_payment', 'new_payment');

            //Drop the added columns
            $table->dropForeign(['financial_goal_id']);
            $table->dropColumn('financial_goal_id');
            $table->dropColumn('goal_impact');
        });
    }
};
