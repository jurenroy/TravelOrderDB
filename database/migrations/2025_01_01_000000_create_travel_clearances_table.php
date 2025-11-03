<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelClearancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_clearances', function (Blueprint $table) {
            $table->id();
            $table->integer('name_id');
            $table->integer('position_id');
            $table->integer('division_id');
            $table->integer('travel_order_id');
            $table->date('date')->default(now());
            $table->string('station');
            $table->string('destination');
            $table->string('purpose');
            $table->date('departure');
            $table->date('arrival');
            $table->string('pap_code');
            $table->text('basis_of_approval');
            $table->text('remarks')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->string('clearance_number')->nullable();
            $table->string('signature')->nullable();
            $table->integer('approved_by')->nullable();
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
        Schema::dropIfExists('travel_clearances');
    }
}
