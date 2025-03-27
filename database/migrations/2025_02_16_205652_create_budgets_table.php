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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // to associate the budget with a user
            $table->decimal('income', 10, 2);
            $table->decimal('needs_percentage', 5, 2)->nullable();
            $table->decimal('wants_percentage', 5, 2)->nullable();
            $table->decimal('savings_percentage', 5, 2)->nullable();
            $table->decimal('budgeted_needs', 10, 2)->nullable();
            $table->decimal('budgeted_wants', 10, 2)->nullable();
            $table->decimal('budgeted_savings', 10, 2)->nullable();
            $table->decimal('needs_progress', 10, 2)->nullable();
            $table->decimal('wants_progress', 10, 2)->nullable();
            $table->decimal('savings_progress', 10, 2)->nullable();
            $table->decimal('remaining_balance', 10, 2)->nullable();
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
