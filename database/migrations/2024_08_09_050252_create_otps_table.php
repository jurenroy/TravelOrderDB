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
        Schema::create('otps', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto-incrementing ID
            $table->string('code'); // OTP code
            $table->unsignedBigInteger('account_id'); // Foreign key reference to accounts table
            $table->timestamps(); // Created at and updated at timestamps
            $table->timestamp('expires_at'); // Expiration time of the OTP

            // Foreign key constraint
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
