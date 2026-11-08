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
        Schema::create('ticket_import_errors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_import_id')
                ->constrained('ticket_imports')
                ->cascadeOnDelete();

            $table->unsignedInteger('row_number');

            $table->json('row_data')->nullable();

            $table->text('error_message');

            $table->timestamps();

            $table->index(['ticket_import_id', 'row_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_import_errors');
    }
};
