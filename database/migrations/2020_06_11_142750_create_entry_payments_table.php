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
            $table->integer('user_id');
            $table->string('user_uuid')->nullable();
            $table->string('status');
            $table->string('pop_image');
            $table->string('payment_mode')->nullable();
            $table->string('reference_id')->nullable();
            $table->integer('month')->nullable();
            $table->string('year')->nullable();
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
