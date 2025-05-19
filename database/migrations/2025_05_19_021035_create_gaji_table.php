<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('month');
            $table->integer('year');
            $table->integer('working_days');
            $table->integer('present_days');
            $table->integer('absent_days');
            $table->decimal('base_salary', 10, 2);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->string('status')->default('Pending'); // Pending, Paid
            $table->date('payment_date')->nullable();
            $table->timestamps();
            
            // Ensure one payroll record per employee per month/year
            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};