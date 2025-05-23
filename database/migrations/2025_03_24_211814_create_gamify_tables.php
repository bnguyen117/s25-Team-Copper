<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamifyTables extends Migration
{
    public function up(): void
    {
        
        Schema::create('reputations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->mediumInteger('point', false)->default(0);
            $table->integer('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedInteger('payee_id')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();
        });
        

        Schema::create('badges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->tinyInteger('level')->default(config('gamify.badge_default_level', 1));
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->primary(['user_id', 'badge_id']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('badge_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('reputations');
    }
}
