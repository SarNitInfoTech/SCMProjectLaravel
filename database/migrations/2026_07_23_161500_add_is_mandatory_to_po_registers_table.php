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
            if (!Schema::hasColumn('po_registers', 'is_mandatory')) {
                $table->string('is_mandatory')->default('Mandatory')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_registers', function (Blueprint $table) {
            if (Schema::hasColumn('po_registers', 'is_mandatory')) {
                $table->dropColumn('is_mandatory');
            }
        });
    }
};
