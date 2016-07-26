<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APIKey extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop_id', 'public_key'];
}
