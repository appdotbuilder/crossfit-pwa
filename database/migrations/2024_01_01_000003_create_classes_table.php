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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->comment('Class name (e.g., CrossFit WOD)');
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->comment('Class start time');
            $table->integer('duration_minutes')->default(60)->comment('Class duration in minutes');
            $table->integer('max_participants')->comment('Maximum number of participants');
            $table->boolean('teen_approved')->default(false)->comment('Whether teens can join this class');
            $table->decimal('drop_in_price', 8, 2)->nullable()->comment('Price for drop-in booking');
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['tenant_id', 'starts_at']);
            $table->index(['instructor_id', 'starts_at']);
            $table->index('starts_at');
            $table->index(['teen_approved', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};