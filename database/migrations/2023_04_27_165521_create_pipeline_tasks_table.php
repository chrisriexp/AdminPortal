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
        Schema::create('pipeline_tasks', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('project_id');
            $table->string('section_id')->nullable();
            $table->string('name');
            $table->string('assigned')->nullable();
            $table->string('due_date')->nullable();
            $table->string('priority')->nullable();
            $table->string('progress')->nullable();
            $table->longText('tags')->nullable();
            $table->longText('desc')->nullable();
            $table->string('created_by');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_tasks');
    }
};
