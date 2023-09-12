<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_no')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('po_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_revised')->default(0);
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->foreignId('quotation_id')->nullable()->constrained();
            $table->text('customer_notes')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->decimal('discount_amount', '9', '2')->default(0)->nullable();
            $table->float('vat_percentage')->nullable();
            $table->decimal('vat_amount', '9', '2')->default(0)->nullable();
            $table->decimal('sub_total', '9', '2')->default(0)->nullable();
            $table->decimal('total', '9', '2')->default(0)->nullable();
            $table->foreignId('created_by')->nullable()->constrained();
            $table->string('requested_by')->nullable();
            $table->string('trn')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->integer('total_revised')->nullable()->default(0);
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
        Schema::dropIfExists('invoices');
    }
}
