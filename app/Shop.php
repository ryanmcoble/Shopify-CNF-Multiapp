<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shops';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shopify_id', 'shop_owner_email', 'myshopify_domain', 'primary_domain', 'access_token'];

    // format domain
    public function formatDomain($domain = '')
    {
        $noProtocol = preg_replace('/(http(s)?:\/\/)?/', '', $domain);
        if (strpos($noProtocol, '/') !== FALSE)
        {
            return substr($noProtocol, 0, strpos($noProtocol, '/'));
        }
        else
        {
            return $noProtocol;
        }
    }

    // get access token
    public function getAccessToken()
    {
        return $this->decrypt($this->access_token, config('shopify.API_ACTK_KEY'));
    }

    // set access token
    public function setAccessToken($token = '')
    {
        $this->access_token = $this->encrypt($token, config('shopify.API_ACTK_KEY'));
    }
    public function setMyshopifyDomain($domain = '')
    {
        $this->myshopify_domain = $this->formatDomain($domain);
    }
    public function setPrimaryDomain($domain = '') {
        $this->primary_domain = $this->formatDomain($domain);
    }
    private function encrypt($text = '', $key = '')
    {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
    private function decrypt($text = '', $key = '')
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }
    
}
