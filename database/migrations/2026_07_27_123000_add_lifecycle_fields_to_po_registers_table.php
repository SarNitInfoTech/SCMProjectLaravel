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
        Schema::table('po_registers', function (Blueprint $table) {
            if (!Schema::hasColumn('po_registers', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('po_registers', 'closed_by')) {
                $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
            }
            if (!Schema::hasColumn('po_registers', 'reopened_at')) {
                $table->timestamp('reopened_at')->nullable()->after('closed_by');
            }
            if (!Schema::hasColumn('po_registers', 'reopened_by')) {
                $table->unsignedBigInteger('reopened_by')->nullable()->after('reopened_at');
            }
            if (!Schema::hasColumn('po_registers', 'close_reason')) {
                $table->text('close_reason')->nullable()->after('reopened_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_registers', function (Blueprint $table) {
            $table->dropColumn(['closed_at', 'closed_by', 'reopened_at', 'reopened_by', 'close_reason']);
        });
    }
};
