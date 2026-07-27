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
        if (!Schema::hasTable('po_audit_logs')) {
            Schema::create('po_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('po_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('action'); // created, updated, receipt_added, receipt_edited, closed, reopened, status_changed
                $table->string('previous_status')->nullable();
                $table->string('new_status')->nullable();
                $table->text('reason')->nullable();
                $table->json('details')->nullable();
                $table->timestamps();

                $table->index('po_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_audit_logs');
    }
};
