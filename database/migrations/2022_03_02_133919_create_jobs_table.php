<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained();
            $table->foreignId('employee_category_id')->nullable()->constrained();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->integer('hours_in_day')->nullable();
            $table->double('rate_per_day')->nullable();
            $table->boolean('has_double_shift')->nullable();
            $table->integer('double_shift_starts_hours')->nullable();
            $table->double('overtime_rate_per_hour')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
