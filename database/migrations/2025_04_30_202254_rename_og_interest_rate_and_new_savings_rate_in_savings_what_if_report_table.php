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
            // Rename columns
            $table->renameColumn('current_interest_rate', 'original_interest_rate');
            $table->renameColumn('new_monthly_savings', 'new_monthly_savings_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_what_if_reports', function (Blueprint $table) {
            //Change names back
            $table->renameColumn('original_interest_rate', 'current_interest_rate');
            $table->renameColumn('new_monthly_savings_rate', 'new_monthly_savings');
        });
    }
};
