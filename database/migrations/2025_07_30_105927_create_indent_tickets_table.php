<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indent_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indent_id'); // Sequential token number per department
            $table->unsignedBigInteger('department_id');
            $table->timestamps();

            // Foreign key constraint (assumes 'departments' table exists)
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indent_tickets');
    }
};
