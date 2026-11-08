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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number')->nullable()->unique();

            $table->string('subject');
            $table->text('description');

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('priority_id')
                ->constrained()
                ->restrictOnDelete();

            // Customer who created the ticket.
            $table->foreignId('requester_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Assigned support agent.
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 30)->default('open');

            $table->timestamp('due_at')->nullable();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamp('first_response_at')->nullable();

            $table->timestamp('resolved_at')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->boolean('is_escalated')->default(false);

            $table->timestamps();

            $table->index(['status', 'due_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['requester_id', 'created_at']);
            $table->index(['priority_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
