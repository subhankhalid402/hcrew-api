<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('passport_number')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('basic_salary')->default(0);
//            $table->foreignId('countries_id')->constrained();
            $table->text('address')->nullable();
            $table->string('bio')->nullable();
            $table->string('picture')->nullable();
            $table->string('phone_no')->nullable();
            $table->foreignId('employee_category_id')->constrained();
            $table->date('dob')->nullable();
            $table->integer('hours_in_day')->nullable();
            $table->decimal('rate_per_day')->default(0);
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
        Schema::dropIfExists('employees');
    }
}
