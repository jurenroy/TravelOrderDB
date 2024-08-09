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
        Schema::create('form', function (Blueprint $table) {
            $table->increments('travel_order_id'); // Auto-incrementing primary key
            $table->unsignedInteger('name_id'); // Foreign key reference to name table
            $table->unsignedInteger('position_id'); // Foreign key reference to position table
            $table->unsignedInteger('division_id'); // Foreign key reference to division table
            $table->string('station', 20);
            $table->string('destination', 50);
            $table->string('purpose', 100);
            $table->date('departure');
            $table->date('arrival');
            $table->string('signature1', 500)->nullable();
            $table->string('signature2', 500)->nullable();
            $table->string('pdea', 100)->nullable();
            $table->string('ala', 100)->nullable();
            $table->string('appropriations', 100)->nullable();
            $table->string('remarks', 100)->nullable();
            $table->date('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('note', 255)->nullable();
            $table->unsignedInteger('sname')->nullable();
            $table->unsignedInteger('sdiv')->nullable();
            $table->unsignedInteger('to_num')->default(0);
            $table->string('initial', 255)->nullable();
            $table->unsignedInteger('intervals')->nullable();
            $table->unsignedInteger('aor')->nullable();

            // Foreign key constraints
            $table->foreign('name_id')->references('name_id')->on('name')->onDelete('cascade');
            $table->foreign('position_id')->references('position_id')->on('position')->onDelete('cascade');
            $table->foreign('division_id')->references('division_id')->on('division')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form');
    }
};
