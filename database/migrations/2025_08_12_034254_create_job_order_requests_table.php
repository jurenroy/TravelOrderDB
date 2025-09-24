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
        Schema::create('job_order_requests', function (Blueprint $table) {
            $table->id();
            $table->string('job_order_no')->unique();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('serial')->nullable();
            $table->string('property_no')->nullable();
            $table->date('date_of_aquisition')->nullable();
            $table->decimal('aquisition_cost', 15, 2)->nullable();
            $table->date('date_of_last_repair')->nullable();
            $table->string('nature_of_last_repair')->nullable();
            $table->text('nature_and_scope')->nullable();
            $table->text('parts')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('performed_by')->nullable();
            $table->string('noted_by')->nullable();
            $table->date('date_finished')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('job_order_requests');
    }
};
