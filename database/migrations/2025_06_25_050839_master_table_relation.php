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
        Schema::create('master_table_relation', function (Blueprint $table) {
            $table->id(); // id bigint primary key
            $table->bigInteger('master_table_id'); // master_table_id bigint
            $table->bigInteger('master_parent_id'); // master_parent_id bigint
            $table->bigInteger('master_child_id'); // master_child_id bigint
            $table->string('name'); // one_to_one, one_to_many, many_to_many
            $table->string('master_table_name'); // nama table biar tidak perlu join dengan master_table
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_table_relation');
    }
};
