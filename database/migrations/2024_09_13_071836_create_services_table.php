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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->integer('serviceRequestNo')->unique(); // Change to integer and unique
            $table->timestamp('date')->useCurrent();
            $table->unsignedBigInteger('division_id');
            $table->string('typeOfService');
            $table->text('note')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('requestedBy');
            $table->unsignedBigInteger('approvedBy')->nullable();
            $table->unsignedBigInteger('servicedBy')->nullable();
            $table->boolean('feedback_filled')->default(false);
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
        Schema::dropIfExists('services');
    }
};
