<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // App API Access Keys table
        Schema::create('api_keys', function(Blueprint $t) {
            $t->increments('id');

            $t->integer('shop_id')->unsigned();
            $t->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

            $t->string('public_key')->index();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop API Access Keys table
        Schema::drop('api_keys');
    }
}
