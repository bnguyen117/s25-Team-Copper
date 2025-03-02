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
            $table->decimal('rent_payment', 10, 2);
            $table->decimal('car_payment', 10, 2)->default(0);
            $table->decimal('reccomended_savings', 10, 2)->default(0);
            $table->decimal('addtl_savings', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
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
