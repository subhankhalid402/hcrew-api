<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quotation_id')->constrained();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', '9', '2')->default(0)->nullable();
            $table->decimal('amount', '9', '2')->default(0);
            $table->string('vat')->nullable();
            $table->decimal('vat_amount', '9', '2')->default(0)->nullable();

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
        Schema::dropIfExists('quotation_details');
    }
}
