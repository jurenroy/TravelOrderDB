<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model'); // e.g., 'TravelClearance'
            $table->unsignedBigInteger('model_id'); // ID of the model instance
            $table->string('action'); // 'created', 'updated', 'deleted'
            $table->json('old_values')->nullable(); // Old values before update
            $table->json('new_values')->nullable(); // New values after update
            $table->unsignedBigInteger('user_id')->nullable(); // User who performed the action
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
        Schema::dropIfExists('audit_logs');
    }
}
