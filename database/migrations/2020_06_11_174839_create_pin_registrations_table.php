<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users_table','uuid');
            $table->foreignId('buyer_id')->constrained('users_table','uuid');
            $table->string('user_uuid')->references('uuid')->on('users');// represents seller uuid
            $table->string('status');
            //$table->boolean('is_approved')->default(false);
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
        Schema::dropIfExists('pin_registrations');
    }
}
