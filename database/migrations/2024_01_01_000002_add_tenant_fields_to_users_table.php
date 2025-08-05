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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->enum('role', ['super_admin', 'tenant_admin', 'instructor', 'member'])->default('member');
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('membership_type', [
                'standard', 
                'student', 
                'teen_1', 
                'teen_2', 
                'free', 
                'day_pass', 
                'single_drop_in',
                'monthly_winner'
            ])->nullable();
            $table->date('membership_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Indexes
            $table->index(['tenant_id', 'role']);
            $table->index(['tenant_id', 'membership_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn([
                'tenant_id',
                'role',
                'phone',
                'date_of_birth',
                'membership_type',
                'membership_expires_at',
                'is_active'
            ]);
        });
    }
};