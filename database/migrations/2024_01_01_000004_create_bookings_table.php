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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->enum('status', ['confirmed', 'waiting_list', 'cancelled'])->default('confirmed');
            $table->enum('booking_type', ['membership', 'drop_in', 'day_pass'])->default('membership');
            $table->decimal('amount_paid', 8, 2)->nullable()->comment('Amount paid for drop-in/day pass');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->boolean('is_refundable')->default(true);
            $table->timestamps();
            
            // Prevent duplicate bookings
            $table->unique(['user_id', 'class_id']);
            
            // Indexes
            $table->index(['class_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('booking_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};