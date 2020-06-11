<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentiveClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentive_claims', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_uuid');
            $table->integer('level_id');
            $table->string('status');
            $table->timestamps(); //request date and approved date
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incentive_claims');
    }
}
