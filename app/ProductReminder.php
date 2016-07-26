<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductReminder extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_reminders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop_id', 'shopify_product_id', 'shopify_variant_id', 'remind_at'];
}
