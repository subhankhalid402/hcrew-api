<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_head_id')->nullable()->constrained();
            $table->foreignId('employee_id')->nullable()->constrained();
            $table->foreignId('contract_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->decimal('amount', '9', '2')->default(0);
            $table->date('date')->nullable();
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('expenses');
    }
}
