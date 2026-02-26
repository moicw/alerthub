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
        Schema::create('notifications', function (Blueprint $table) {
            /*$table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->string('subject');
            $table->string('status');
            $table->json('payload')->nullable();
            $table->timestamps();*/
             $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('subscriber_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('alert_rule_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('channel')->default('email');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('payload');

            $table->enum('status', ['pending', 'sent', 'failed', 'escalated'])
                  ->default('pending');

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // 🔥 prevents cross-project metric pollution
            $table->index(['project_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
