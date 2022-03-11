<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone_no')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number')->nullable();
            $table->foreignId('client_category_id')->constrained();
            $table->text('notes')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('currency_id')->constrained();
            $table->string('focal_name')->nullable();
            $table->string('focal_phone_no')->nullable();
            $table->string('focal_email')->unique();
            $table->string('website')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
