<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->unsignedInteger('level')->default(1);
            $t->unsignedInteger('xp')->default(0);
            $t->unsignedInteger('hp')->default(50);
            $t->unsignedInteger('gold')->default(0);
            $t->unsignedInteger('shields')->default(0);
            $t->unsignedInteger('total_done')->default(0);
            $t->date('last_day')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('habits', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->enum('type', ['daily', 'habit'])->default('daily');
            $t->unsignedTinyInteger('difficulty')->default(2);
            $t->json('days')->nullable();
            $t->unsignedInteger('streak')->default(0);
            $t->unsignedInteger('best_streak')->default(0);
            $t->date('last_date')->nullable();
            $t->timestamps();
        });

        Schema::create('habit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->unsignedInteger('count')->default(0);
            $t->unsignedInteger('neg')->default(0);
            $t->integer('xp')->default(0);
            $t->integer('gold')->default(0);
            $t->timestamps();
            $t->unique(['habit_id', 'date']);
            $t->index(['user_id', 'date']);
        });

        Schema::create('rewards', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->unsignedInteger('cost');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
        Schema::dropIfExists('users');
    }
};
