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
        Schema::create('mga_companies', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->string('aon')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('beyond')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('cat')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('dual')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('flow')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('neptune')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('palomar')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('sterling')->default('{"carrier_username": "", "commission_id": ""}');
            $table->string('wright')->default('{"carrier_username": "", "commission_id": ""}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mga_companies');
    }
};
