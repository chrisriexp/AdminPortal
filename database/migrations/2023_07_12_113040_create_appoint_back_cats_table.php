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
            $table->longText('data')->default('{"agency_name": null, "agent_name": null, "phone": null, "email": null, "address1": null, "address2": null, "city": null, "state": null, "zip": null, "agency_type": null, "agency_tax_id": null, "agency_license": null, "agent_npn": null, "eo_policy": null, "eo_insurer": null, "eo_limit": null, "eo_exp": null}');
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
