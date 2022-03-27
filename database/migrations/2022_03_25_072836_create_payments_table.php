<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_detail_id')->constrained();
            $table->date('payment_date')->nullable();
            $table->integer('hours_worked')->nullable();
            $table->double('rate_per_day')->nullable();
            $table->decimal('subtotal_payment', [9 ,2]);
            $table->boolean('double_shift')->nullable();
            $table->integer('overtime_hours')->nullable();
            $table->double('overtime_hours_rate')->nullable();
            $table->decimal('overtime_payment', [9 ,2]);
            $table->decimal('net_payment', [9 ,2]);
            $table->softDeletes();
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
        Schema::dropIfExists('payments');
    }
}
