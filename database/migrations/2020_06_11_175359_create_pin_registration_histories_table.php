<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinRegistrationHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_registration_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users_table');
            $table->foreignId('buyer_id')->constrained('users_table');
            $table->string('user_uuid')->references('uuid')->on('users'); // represents seller uuid
            $table->string('request_date');
            $table->timestamps(); // date_of_approval
            $table->double('amount');
            $table->boolean('is_successful')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_registration_histories');
    }
}
