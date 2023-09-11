<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('account_no')->nullable();
            $table->string('iban_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('phone_no_2')->nullable();
            $table->string('address')->nullable();
            $table->text('terms_and_conditions')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
