<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_table');
            $table->string('pop_path')->nullable();
            $table->string('status');
            $table->double('units');
            $table->double('amount');
            $table->string('payment_mode')->default('pop');
            $table->string('transaction_reference')->nullable();
            $table->timestamps();
            //$table->for
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_purchases');
    }
}
