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
        Schema::table('indent_tickets', function (Blueprint $table) {
            $table->string('indent_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indent_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('indent_id')->change();
        });
    }
};
