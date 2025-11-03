<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsos', function (Blueprint $table) {
            $table->string('rso_number')->primary();
            $table->string('rso_name'); // Added this line ✅
            $table->date('rso_date');
            $table->text('rso_subject');
            $table->date('rso_scheduled_dates_from')->nullable();
            $table->date('rso_scheduled_dates_to')->nullable();
            $table->foreignId('rso_signatory')->constrained('users')->onDelete('no action');
            $table->text('rso_remarks')->nullable();
            $table->string('rso_scan_copy')->nullable(); // filename of uploaded file
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsos');
    }
};
