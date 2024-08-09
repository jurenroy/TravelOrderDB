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
        Schema::create('leaveform', function (Blueprint $table) {
            $table->increments('leaveform_id'); // Auto-incrementing primary key
            $table->unsignedInteger('name_id')->nullable(); // Foreign key reference to name table
            $table->unsignedInteger('position_id')->nullable(); // Foreign key reference to position table
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('type', 255)->nullable();
            $table->text('detail')->nullable();
            $table->text('description')->nullable();
            $table->string('days', 500)->nullable();
            $table->text('dates')->nullable();
            $table->string('commutation', 255)->nullable();
            $table->string('applicant', 255)->nullable();
            $table->string('asof', 500)->nullable();
            $table->integer('tevl')->nullable();
            $table->integer('tesl')->nullable();
            $table->integer('ltavl')->nullable();
            $table->integer('ltasl')->nullable();
            $table->integer('bvl')->nullable();
            $table->integer('vsl')->nullable();
            $table->text('certification')->nullable();
            $table->text('reco')->nullable();
            $table->text('recodesc')->nullable();
            $table->text('recommendation')->nullable();
            $table->integer('dayswpay')->nullable();
            $table->integer('dayswopay')->nullable();
            $table->text('others')->nullable();
            $table->text('disapproved')->nullable();
            $table->text('approval')->nullable();

            // Foreign key constraints
            $table->foreign('name_id')->references('name_id')->on('name')->onDelete('set null');
            $table->foreign('position_id')->references('position_id')->on('position')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaveform');
    }
};
