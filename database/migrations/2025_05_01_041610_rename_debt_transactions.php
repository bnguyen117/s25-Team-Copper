<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete old records
        DB::table('debt_transactions')->delete();

        // Rename table
        Schema::rename('debt_transactions', 'transactions');

        Schema::table('transactions', function (Blueprint $table) {
            // Add
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable()->after('debt_id');
            $table->string('name')->nullable()->after('category');

            // Modify
            $table->foreignId('debt_id')->nullable()->change();
            $table->dropForeign(['debt_id']);
            $table->foreign('debt_id')->references('id')->on('debts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename
        Schema::rename('transactions', 'debt_transactions');
        // Revert
        Schema::table('debt_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'category', 'name']);
            $table->foreignId('debt_id')->change();
            $table->dropForeign(['debt_id']);
            $table->foreign('debt_id')->references('id')->on('debts')->onDelete('cascade');
        });
    }
};
