<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinPurchaseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_table');
            $table->integer('units');
            $table->string('pop_path')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->double('amount');
            $table->boolean('is_successful')->default(false);
            $table->string('payment_mode')->default('pop');
            $table->string('transaction_reference')->nullable();
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
        Schema::dropIfExists('pin_purchase_histories');
    }
}
