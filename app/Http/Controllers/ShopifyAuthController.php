<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use App;
use Hash;
use Session;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shop;
use App\APIKey;

class ShopifyAuthController extends Controller
{
    // install or authenticate with Shopify route
    public function installOrAuthenticate(Request $req, $code = null)
    {
        // request has code, so we know it is a new install
        if($req->has('code')) {

            // INSTALL

            // get request variables
            $authCode        = $req->get('code');
            $myshopifyDomain = $req->get('shop');
            $accessToken     = '';

            // create Shopify API wrapper instance
            $sh = App::make('ShopifyAPI', [
                'API_KEY'     => config('shopify.API_KEY'),
                'API_SECRET'  => config('shopify.API_SECRET'),
                'SHOP_DOMAIN' => $myshopifyDomain
            ]);

            try {

                // attempt to verify if request is authentic
                if($sh->verifyRequest($req->all())) {
                    $accessToken = $sh->getAccessToken($authCode);
                }

            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
                return '<pre>Error: ' . $e->getMessage() . '</pre>';
            }

            // attempt to get shop from database
            $shop = Shop::where('myshopify_domain', $myshopifyDomain)->first();

            // create new shop if not found
            if(!$shop) {
                $shop = new Shop;
            }

            // save shop info
            $shop->setMyshopifyDomain($myshopifyDomain);
            $shop->setAccessToken($accessToken);
            $shop->save();

            // update other shop info
            $this->updateShopInfo($shop);

            // create the shop's first api key automatically, on install
            $apiKey = new ApiKey;
            $apiKey->shop_id = $shop->id;
            $apiKey->public_key = Hash::make($shop->myshopify_domain . '_CUTENFUZZIES');
            $apiKey->save();

            // create webhook for uninstall
            $hookData = array('webhook' => array('topic' => 'app/uninstalled', 'address' => 'https://' . env('APP_HOST', 'cnfmultiapp.dev') . '/hooks/app/uninstall', 'format' => 'json'));
            try {
                $sh->setup(['ACCESS_TOKEN' => $shop->getAccessToken()]);
                $sh->call(['URL' => 'webhooks.json', 'METHOD' => 'POST', 'DATA' => $hookData]);
            }
            catch (Exception $e) {
                Log::error('Issue creating uninstall webhook - ' . $shop->myshopify_domain . ' : ' . $e->getMessage());
            }
            
            // set myshopify domain as session variable
            Session::put('shop', $shop->myshopify_domain);

            // redirect to app dashboard
            return redirect('/');
        }
        else {

            // AUTHENTICATE

            // get request variables
            $myshopifyDomain = $req->get('shop');

            // attempt to get shop by myshopify domain
            $shop = Shop::where('myshopify_domain', $myshopifyDomain)->first();
            if($shop) {

                // update shop info
                $this->updateShopInfo($shop);

                // set shop session variable
                Session::put('shop', $myshopifyDomain);

                // redirect to app dashboard
                return redirect('/');
            }

            // failed to authenticate to current shop
            Log::warning('Something fishy happened: App was installed but then was unable to authenticate.');

            // generate install url and redirect to it
            $sh = App::make('ShopifyAPI', ['API_KEY' => config('shopify.API_KEY'), 'SHOP_DOMAIN' => $myshopifyDomain]);
            return $sh->installURL(['permissions' => config('shopify.APP_SCOPE'), 'redirect' => 'https://' . env('APP_HOST', 'cnfmultiapp.dev') . '/auth']);
        }
    }


    // generate an install url
    public function generateInstallURL(Request $req, $myshopifyURL) {
        $sh = App::make('ShopifyAPI', ['API_KEY' => config('shopify.API_KEY'), 'SHOP_DOMAIN' => $myshopifyURL]);
        return $sh->installURL(['permissions' => config('shopify.APP_SCOPE'), 'redirect' => 'https://' . env('APP_HOST', 'cnfmultiapp.dev') . '/auth']);
    }


    // uninstall webhook route
    public function uninstallHook(Request $req) {
        
        // get shop
        $shop = Shop::where('myshopify_domain', $req->get('myshopify_domain'))->firstOrFail();
        
        // delete shop
        $shop->delete();
        
        // shop thank you message
        return 'Thank You!';
    }


    // updates several parameters on app install and on auth check
    private function updateShopInfo($shop = NULL) {
        try {
            $sh = App::make('ShopifyAPI', ['API_KEY' => config('shopify.API_KEY'), 'API_SECRET' => config('shopify.API_SECRET'), 'SHOP_DOMAIN' => $shop->myshopify_domain, 'ACCESS_TOKEN' => $shop->getAccessToken() ]);
            $data = $sh->call(['URL' => 'shop.json']);

            if ($data) {
                Session::put('shopInfo', $data->shop);
                $shop->shopify_id = $data->shop->id;
                $shop->shop_owner_email = $data->shop->email;
                $shop->setPrimaryDomain($data->shop->domain);
                $shop->save();
            }
            return TRUE;
        }
        catch (\Exception $e) {
            Log::error('[AuthController::updateShopInfo() - ' . $shop->domain . '] ' . $e->getMessage());
            return FALSE;
        }
    }
}
