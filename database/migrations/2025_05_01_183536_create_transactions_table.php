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
        Schema::create('new_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('debt_id')->nullable()->constrained('debts')->onDelete('set null');
            $table->string('category')->nullable();
            $table->string('name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('interest_paid', 10, 2)->nullable();
            $table->decimal('principal_paid', 10, 2)->nullable();
            $table->string('transaction_type')->nullable();
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
