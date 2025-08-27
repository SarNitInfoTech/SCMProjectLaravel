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
       Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('icon')->nullable(); // e.g., 'la la-file-alt'
    $table->string('bg_color')->nullable(); // e.g., 'bg-pinkmain'
    $table->text('link')->nullable();
    $table->string('subtext')->nullable(); // e.g., "2 days ago"
    $table->boolean('is_read')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
