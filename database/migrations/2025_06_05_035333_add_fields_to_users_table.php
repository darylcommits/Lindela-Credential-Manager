<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('it_staff'); // admin, it_staff
            $table->timestamp('last_login_at')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->json('two_factor_recovery_codes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'last_login_at', 'two_factor_secret', 
                'two_factor_enabled', 'two_factor_recovery_codes',
                'is_active', 'last_activity_at'
            ]);
        });
    }
};
