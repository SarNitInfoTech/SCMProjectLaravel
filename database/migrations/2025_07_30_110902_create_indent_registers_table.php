<?php use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indent_registers', function (Blueprint $table) {
            $table->id();
            $table->string('indent_id'); // Manually generated token (e.g., CSE-4)
            $table->date('indent_date');
            $table->string('indent_department');
            $table->string('indent_project')->nullable();
            $table->json('items_description');
            $table->string('unit')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('quantity_required')->default(0);
            $table->text('purchased_order')->nullable(); // text instead of boolean
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_balance')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indent_registers');
    }
};
