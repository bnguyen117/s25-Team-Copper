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
            // Add a column
            $table->string('savings_name')->after('financial_goal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_what_if_reports', function (Blueprint $table) {
            // Remove the column
            $table->dropColumn('savings_name');
        });
    }
};
