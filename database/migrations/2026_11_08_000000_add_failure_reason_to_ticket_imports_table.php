<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_imports', function (Blueprint $table) {
            $table->text('failure_reason')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_imports', function (Blueprint $table) {
            $table->dropColumn('failure_reason');
        });
    }
};
