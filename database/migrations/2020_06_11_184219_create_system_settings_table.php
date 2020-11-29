<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->double('referral_bonus')->nullable();
            $table->double('entry_payment')->nullable();
            $table->double('dollar_exchange_rate')->nullable();
            $table->double('minimum_withdrawal')->nullable();
            $table->double('maximum_withdrawal')->nullable();
            $table->double('welcome_bonus')->nullable();
            $table->double('pin_price')->nullable();
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
        Schema::dropIfExists('system_settings');
    }
}
