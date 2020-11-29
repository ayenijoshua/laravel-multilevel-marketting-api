<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_table');
            $table->string('user_uuid')->references('uuid')->on('users');
            $table->string('status');
            $table->string('pop_path')->nullable();
            $table->string('payment_mode')->default('pop');
            $table->string('transaction_reference')->nullable();
            $table->integer('month')->nullable();
            $table->string('year')->nullable();
            $table->double('amount');
            $table->timestamps(); //date of pay and approved date
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_payments');
    }
}
