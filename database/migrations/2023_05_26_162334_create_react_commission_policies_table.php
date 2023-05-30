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
        Schema::create('react_commission_policies', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->string('carrier');
            $table->string('carrier_id');
            $table->string('policy');
            $table->string('insured');
            $table->string('eff');
            $table->string('trans_type');
            $table->string('policy_type');
            $table->float('prem');
            $table->float('comm');
            $table->float('override');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('react_commission_policies');
    }
};
