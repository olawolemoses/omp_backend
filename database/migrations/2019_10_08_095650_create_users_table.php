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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->tinyInteger('is_provider')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->text('verification_link')->nullable();
            $table->enum('email_verified', ['Yes', 'No'])->default('No');
            $table->text('affiliate_code')->nullable();
            $table->double('affiliate_income')->default(0);
            $table->text('shop_name')->nullable();
            $table->text('owner_name')->nullable();
            $table->text('shop_number')->nullable();
            $table->text('shop_address')->nullable();
            $table->text('reg_number')->nullable();
            $table->text('shop_message')->nullable();
            $table->text('shop_details')->nullable();
            $table->string('shop_image')->nullable();
            $table->text('f_url')->nullable();
            $table->text('g_url')->nullable();
            $table->text('t_url')->nullable();
            $table->text('l_url')->nullable();
            $table->boolean('is_vendor')->default(false);
            $table->boolean('f_check')->default(false);
            $table->boolean('g_check')->default(false);
            $table->boolean('t_check')->default(false);
            $table->boolean('l_check')->default(false);
            $table->boolean('mail_sent')->default(false);
            $table->double('shipping_cost')->default(0);
            $table->double('current_balance')->default(0);
            $table->date('date')->nullable();
            $table->boolean('ban')->default(false);
            $table->timestamps();
            $table->rememberToken();
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
