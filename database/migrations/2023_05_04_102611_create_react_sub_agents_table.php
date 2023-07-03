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
        Schema::create('react_sub_agents', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('rocket_id')->unique();
            $table->string('name');
            $table->boolean('rocketPlus');
            $table->string('aon');
            $table->string('beyond');
            $table->string('cat');
            $table->string('flow');
            $table->string('neptune');
            $table->string('palomar');
            $table->string('sterling');
            $table->string('wright');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('react_sub_agents');
    }
};
