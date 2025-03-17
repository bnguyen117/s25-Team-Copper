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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('budget_type', ['Standard', 'Custom'])->default('Standard');
            $table->decimal('monthly_income', 10, 2)->default(0);
            $table->decimal('budgeted_needs', 10, 2);
            $table->decimal('budgeted_wants', 10, 2);
            $table->decimal('budgeted_savings', 10, 2);
            $table->decimal('needs_spending_this_month', 10, 2)->default(0);
            $table->decimal('wants_spending_this_month', 10, 2)->default(0);
            $table->decimal('amount_saved_this_month', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
