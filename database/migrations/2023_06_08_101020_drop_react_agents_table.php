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
        Schema::dropIfExists('react_sub_agents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('react_sub_agents', function (Blueprint $table) {
            //
        });
    }
};
