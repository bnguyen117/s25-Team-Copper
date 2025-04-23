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
        Schema::create('savings_what_if_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('algorithm');
            $table->decimal('original_savings', 10, 2);              // The original amount of the savings
            $table->decimal('current_monthly_savings', 10, 2);      // The current savings rate
            $table->decimal('current_interest_rate', 5, 2);                // The current interest rate
            $table->decimal('new_interest_rate', 5, 2)->nullable(); // The new interest rate for the `interest rate` algorithm
            $table->decimal('new_monthly_savings', 10, 2)->nullable();      // The new monthly savings amount for the `payment change` algorithm
            $table->integer('months_to_save')->nullable();                              //The amount of months the user wants to save
            $table->integer('total_months');                        // Total months to reach savings goal
            $table->decimal('total_interest_earned', 10, 2);          // The total interest earned in dollars over time
            $table->json('timeline');                               // Json array of monthly savings details (balance, interest earned)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_what_if_reports');
    }
};
