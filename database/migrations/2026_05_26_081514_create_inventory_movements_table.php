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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('inventory_stocks')->onDelete('cascade');
            $table->string('type'); // IN, OUT, ADJUST, RETURN, TRANSFER
            $table->decimal('quantity', 15, 4);
            $table->decimal('qty_before', 15, 4);
            $table->decimal('qty_after', 15, 4);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('po_register_id')->nullable()->constrained('po_registers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference_no')->nullable();
            $table->text('remarks')->nullable();
            $table->json('metadata')->nullable();
            $table->date('movement_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
