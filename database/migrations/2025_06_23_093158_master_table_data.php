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
        Schema::create('master_table_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('parent_table_name')->nullable();
            $table->unsignedBigInteger('master_table_id');
            $table->string('master_table_name');
            $table->jsonb('data');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('master_table_id')->references('id')->on('master_table')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_table_data');
    }
};
