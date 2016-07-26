<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Shopify Shops table
        Schema::create('shops', function(Blueprint $t) {
            $t->increments('id');
            $t->string('shopify_id');
            $t->string('shop_owner_email');
            $t->string('myshopify_domain');
            $t->string('primary_domain');
            $t->string('access_token');
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
        // Drop shops table
        Schema::drop('shops');
    }
}
