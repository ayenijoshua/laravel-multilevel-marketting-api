<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_table');
            $table->string('user_uuid')->nullable()->references('uuid')->on('users');
            $table->double('amount');
            //$table->string('request_date');
            $table->foreignId('level_id')->constrained('levels_table');
            $table->integer('month')->nullable();
            $table->string('year')->nullable();
            $table->boolean('is_successful')->default(false);
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
        Schema::dropIfExists('withdrawal_histories');
    }
}
