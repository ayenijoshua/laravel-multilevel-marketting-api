<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreatGrandChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('great_grand_children', function (Blueprint $table) {
            $table->id();
            $table->integer('great_grand_cildren_id');
            $table->integer('parent_id');
            $table->integer('great_grand_parent_id');
            $table->integer('level_id');
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
        Schema::dropIfExists('great_grand_children');
    }
}
