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
        Schema::create('what_if_reports', function (Blueprint $table) {
            $table->string('analysis_type');                        // The type of analysis performed (debt or savings)
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');
            $table->string('algorithm');                            // The what-if scenario algorithm used (payment-change, interest-rate)
            $table->decimal('original_amount', 10, 2);              // The original amount of the debt
            $table->decimal('current_payment', 10, 2);              // The current monthly payment for a debt
            $table->decimal('minimum_payment', 10, 2)->nullable();  // The minimum required payment on a debt
            $table->decimal('new_interest_rate', 5, 2)->nullable(); // The new interest rate for the `interest rate` algorithm
            $table->decimal('new_payment', 10, 2)->nullable();      // The new monthly payment amount for the `payment change` algorithm
            $table->integer('total_months');                        // Total months to pay off debt in full
            $table->decimal('total_interest_paid', 10, 2);          // The total interest paid in dollars over time
            $table->decimal('original_savings_interest_rate', 5, 2);        // The original interest rate for the debt
            $table->decimal('original_savings_amount', 10, 2);      // The original amount of savings
            $table->decimal('original_monthly_savings', 10, 2);     // The original monthly savings amount
            $table->decimal('new_monthly_savings', 10, 2)->nullable(); // The new monthly savings amount for the `savings change` algorithm
            $table->decimal('total_interest_earned', 10, 2);        // The total interest earned in dollars over time
            $table->decimal('total_saved', 10, 2);                  // The total amount saved in dollars over time
            $table->json('timeline');                               // Json array of monthly repayment details (balance, interest paid)
            $table->timestamps();                                   // Created/edited at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('what_if_reports');
    }
};
