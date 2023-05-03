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
        Schema::create('notebooks', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('title')->default('Untitled Note');
            $table->longText('body')->nullable();
            $table->boolean('favorite')->default(false);
            $table->boolean('important')->default(false);
            $table->string('folder')->default('notebooks');
            $table->string('shared')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notebooks');
    }
};
