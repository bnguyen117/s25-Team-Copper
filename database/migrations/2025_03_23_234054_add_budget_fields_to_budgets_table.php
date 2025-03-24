<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('budgeted_needs', 10, 2)->nullable();
            $table->decimal('budgeted_wants', 10, 2)->nullable();
            $table->decimal('budgeted_savings', 10, 2)->nullable();
            $table->decimal('remaining_balance', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['budgeted_needs', 'budgeted_wants', 'budgeted_savings', 'remaining_balance']);
        });
    }
};

