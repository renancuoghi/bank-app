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
        /*
        * balance is the sum of all transactions and is unique for user 
        * could have more with we want working with many wallet or banks so in this case we'll need to remove unique constraint
        */
        Schema::create('balance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unique()->unsigned();                        
            $table->decimal('total', 14, 2);  
            $table->timestamps();
            // foreign keys
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance');
    }
};
