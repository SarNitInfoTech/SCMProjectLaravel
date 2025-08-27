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
       Schema::create('po_registers', function (Blueprint $table) {
    $table->id();

    // New fields
    $table->unsignedBigInteger('indent_id')->nullable();
    $table->string('department_id')->nullable();
    $table->string('status')->default('Pending'); // Enum: Pending, Cancel, Close
    $table->string('invoice')->nullable();

    // Existing fields
    $table->date('po_date')->nullable();
    $table->string('party_name')->nullable();
    $table->string('po_wo_no')->nullable();
    $table->text('item_description')->nullable();
    $table->decimal('po_amount', 12, 2)->nullable();
    $table->string('debit_head')->nullable();
    $table->string('expected_days')->nullable();
    $table->date('expected_date')->nullable();
    $table->date('invoice_date')->nullable();
    $table->string('receiving_date')->nullable();
    $table->integer('delay_in_days')->nullable();
    $table->text('remarks')->nullable();
    $table->string('store_indent_no')->nullable();
    
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_registers');
    }
};
