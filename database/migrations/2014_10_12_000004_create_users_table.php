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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('user');
            $table->boolean('new_account')->default(true);
            $table->boolean('new_update')->default(false);
            $table->boolean('onboarding')->default(false);
            $table->boolean('rover')->default(false);
            $table->boolean('react')->default(false);
            $table->string('notebook_folders')->nullable();
            $table->longText('personal_notes')->nullable();
            $table->string('theme')->default('light-mode');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
