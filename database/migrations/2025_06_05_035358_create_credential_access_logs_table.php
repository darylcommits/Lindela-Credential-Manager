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
        Schema::table('credential_access_logs', function (Blueprint $table) {
            // Make credential_id nullable for system-wide actions like search
            $table->foreignId('credential_id')->nullable()->change();
            
            // Change user_id to set null on delete to preserve audit trail
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Remove enum constraint and increase action field length
            $table->string('action', 100)->change();
            
            // Improve ip_address field
            $table->ipAddress('ip_address')->nullable()->change();
            
            // Add additional indexes for better performance
            $table->index(['otp_request_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credential_access_logs', function (Blueprint $table) {
            // Revert changes (optional - be careful with data loss)
            $table->foreignId('credential_id')->nullable(false)->change();
            
            // Revert user_id foreign key
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Revert action field (careful - this might cause data loss if longer actions exist)
            // $table->enum('action', ['view', 'copy', 'edit', 'delete'])->change();
            
            // Drop additional indexes
            $table->dropIndex(['otp_request_id', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['created_at']);
        });
    }
};