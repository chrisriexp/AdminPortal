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
        Schema::create('react_commissions', function (Blueprint $table) {
            $table->string('statement_id')->primary();
            $table->string('carrier');
            $table->string('month');
            $table->float('override');
            $table->float('prem');
            $table->float('comm');
            $table->integer('policies');
            $table->string('uploaded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('react_commissions');
    }
};
