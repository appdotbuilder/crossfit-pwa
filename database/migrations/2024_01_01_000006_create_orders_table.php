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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('stripe_payment_intent_id')->unique();
            $table->enum('type', ['subscription', 'drop_in', 'day_pass']);
            $table->decimal('amount', 8, 2);
            $table->decimal('platform_fee', 8, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('metadata')->nullable()->comment('Additional order details');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('stripe_payment_intent_id');
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};