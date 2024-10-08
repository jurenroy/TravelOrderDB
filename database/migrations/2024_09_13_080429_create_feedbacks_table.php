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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedbackid');
            $table->unsignedBigInteger('referenceid'); // This will be the foreign key to the services table
            $table->integer('evaluation1');
            $table->integer('evaluation2');
            $table->integer('evaluation3');
            $table->integer('evaluation4');
            $table->timestamp('date')->useCurrent(); // Default to current timestamp
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('referenceid')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};
