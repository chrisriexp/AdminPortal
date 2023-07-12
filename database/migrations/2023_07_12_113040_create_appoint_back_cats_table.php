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
        Schema::create('appoint_back_cats', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->string('status')->default('Information Required');
            $table->string('document')->nullable();
            $table->json('data');
            $table->boolean('appointed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appoint_back_cats');
    }
};
