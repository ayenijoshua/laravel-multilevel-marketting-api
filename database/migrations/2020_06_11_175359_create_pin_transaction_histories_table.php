<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('buyer_id');
            $table->string('request_date');
            $table->timestamps(); // date_of_approval
            $table->double('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_transaction_histories');
    }
}
