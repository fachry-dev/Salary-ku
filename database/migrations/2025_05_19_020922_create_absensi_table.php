<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status'); // Present, Absent, Late, On Leave
            $table->decimal('working_hours', 5, 2)->nullable();
            $table->timestamps();
            
            // Ensure one attendance record per employee per day
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};