<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('module'); // auth, credentials, otp, admin
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->timestamps();
            
            $table->index(['module', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('level');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};