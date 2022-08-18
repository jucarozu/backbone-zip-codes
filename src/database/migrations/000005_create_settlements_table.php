<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->integer('key');
            $table->string('name')->nullable();
            $table->string('zone_type')->nullable();
            $table->foreignId('settlement_type_id')->nullable()->constrained();
            $table->foreignId('zip_code_id')->nullable()->constrained();
            $table->unique(['key', 'zip_code_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settlements');
    }
};
