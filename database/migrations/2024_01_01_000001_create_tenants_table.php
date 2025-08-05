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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Club name (e.g., Crossgym Randers)');
            $table->string('domain')->unique()->comment('Custom domain for the tenant');
            $table->string('slug')->unique()->comment('URL-friendly identifier');
            $table->string('logo_url')->nullable()->comment('Club logo URL');
            $table->json('settings')->nullable()->comment('Tenant-specific settings');
            $table->string('stripe_account_id')->nullable()->comment('Stripe Connect account ID');
            $table->boolean('is_active')->default(true)->comment('Whether tenant is active');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('domain');
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};