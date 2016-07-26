<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Shopify Product reminders table
        Schema::create('product_reminders', function(Blueprint $t) {
            $t->increments('id');

            $t->integer('shop_id')->unsigned();
            $t->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

            $t->string('shopify_product_id')->index();
            $t->string('shopify_variant_id')->nullable();

            $t->string('email');

            $t->timestamp('remind_at');
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
        // drop Shopify reminders table
        Schema::drop('product_reminders');
    }
}
