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
        Schema::create('employee', function (Blueprint $table) {
            $table->bigIncrements('employee_id');
            $table->unsignedBigInteger('name_id');
            $table->unsignedBigInteger('division_id');
            $table->unsignedBigInteger('position_id');
            $table->timestamps();

            $table->foreign('name_id')->references('name_id')->on('name');
            $table->foreign('division_id')->references('division_id')->on('division');
            $table->foreign('position_id')->references('position_id')->on('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee');
    }
};
