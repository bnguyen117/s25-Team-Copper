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
        Schema::table('savings_what_if_reports', function (Blueprint $table) {
            //Change
            $table->renameColumn('algorithm', 'what_if_scenario');
            //Add
            $table->foreignId('financial_goal_id')->nullable()->constrained('financial_goals')->onDelete('set null')->after('user_id'); // Links a report to a goal
            $table->json('goal_impact')->nullable()->after('timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_what_if_reports', function (Blueprint $table) {
            // Change the names back
            $table->renameColumn('what_if_scenario', 'algorithm');
            //Drop the added columns
            $table->dropForeign(['financial_goal_id']);
            $table->dropColumn('financial_goal_id');
            $table->dropColumn('goal_impact');
        });
    }
};
