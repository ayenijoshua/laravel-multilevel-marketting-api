<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->boolean('is_approved')->default(false);
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('image_path')->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->foreignId('level_id');//->constrained('levels_table');
            $table->string('country')->nullable();
            $table->boolean('cycled_out')->default(false);
            $table->string('uuid');
            $table->string('uuids');
            $table->string('email');//->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('reset_token')->nullable();
            $table->string('reset_type')->nullable();
            $table->string('auth_qrcode')->nullable();
            $table->string('auth_qrsecret')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('password');
            $table->bigInteger('pin_units')->default(0);
            $table->integer('month')->nullable();
            $table->string('year')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
