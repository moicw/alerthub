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
        Schema::create('subscribers', function (Blueprint $table) {
            /*$table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('email');
            $table->string('external_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('notification_count')->default(0);
            $table->timestamp('last_notified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();*/
            $table->id();
            $table->foreignId('project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('email')->nullable();
            $table->string('external_id')->nullable();
            $table->string('name')->nullable();

            $table->unsignedInteger('notification_count')->default(0);
            $table->timestamp('last_notified_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Prevent duplicate subscribers by email or external_id per project
            $table->unique(['project_id', 'email'], 'subscribers_project_email_unique');
            $table->unique(['project_id', 'external_id'], 'subscribers_project_external_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
