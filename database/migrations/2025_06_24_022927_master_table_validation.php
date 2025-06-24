<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_table_validation', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // regex
            $table->string('value'); // ^[a-zA-Z0-9 ]+$
            $table->string('message'); // Format tidak valid
            $table->timestamps();
            $table->softDeletes();
        });
        Artisan::call('db:seed', [
            '--class' => 'MasterTableValidationSeeder',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('master_table_validation');
    }
};
