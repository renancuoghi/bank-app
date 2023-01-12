<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_transaction', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();                        
            $table->decimal('amount', 14, 2);
            $table->string('description', 255);
            // transaction type  must be C = CREDIT OR D = DEBIT
            $table->char('transaction_type',1)->default('D');
            // transaction status must be P = pending, A = approved, R = Rejected
            $table->char('status',1)->default('P');
            $table->bigInteger('approved_user_id')->nullable()->unsigned();
            $table->timestamps();
            $table->bigInteger('balance_id')->unsigned(); 
            // foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('balance_id')->references('id')->on('balance');
            $table->foreign('approved_user_id')->nullable()->constrained()->references('id')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_transaction');
    }
};
