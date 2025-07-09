<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('credential_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('username')->nullable();
            $table->text('email')->nullable();
            $table->text('password'); // Will be encrypted
            $table->text('notes')->nullable();
            $table->string('website_url')->nullable();
            $table->json('backup_codes')->nullable(); // For storing backup/recovery codes
            $table->json('additional_fields')->nullable(); // For custom fields
            $table->date('expires_at')->nullable();
            $table->boolean('password_never_expires')->default(false);
            $table->integer('password_strength')->nullable(); // 1-5 rating
            $table->timestamp('last_accessed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credentials');
    }
};