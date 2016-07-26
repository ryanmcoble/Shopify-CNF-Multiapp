<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Session;

use App\Helpers\MailChimpHelper;

use App\ProductReminder;
use App\Shop;

class ProductRemindMeLaterController extends Controller
{
    // subscribe to a Shopify product's remind me later mailing list
    public function subscribe(Request $req) {

        $email        = $req->get('email');
        $productTitle = $req->get('product_title');
        $productId    = $req->get('product_id');
        $variantTitle = $req->get('variant_title');
        $variantId    = $req->get('variant_id');
        $remind_at    = $req->get('remind_at');
        $shopDomain   = $req->get('shop_domain');

        $shop = Shop::where('myshopify_domain', $shopDomain)->first();
        if(!$shop) {
            return [
                'status' => 'error',
                'message' => 'Shop was not found.'
            ];
        }

        $listName = '';
        if(!$variantId) {
            $listName = 'Product Reminder: ' . $productTitle;
        }
        else {
            $listName = 'Product Reminder: ' . $productTitle . ' - '. $variantTitle;
        }

        $mailChimp = new MailChimpHelper(config('mailchimp'));

        // check authentication
        if(!$mailChimp->checkAuth()) {
            return [
                'status' => 'error',
                'message' => 'Invalid MailChimp account credentials.'
            ];
        }

        $currentList = $mailChimp->hasList($listName);
        if(!$currentList) {
            $currentList = $mailChimp->createList($listName);
        }

        $currentMember = $mailChimp->hasMember($currentList['id'], $email);
        if(!$currentMember) {
            $currentMember = $mailChimp->subscribeMember($currentList['id'], $email);
        }

        $productQuery = ProductReminder::where('email', $email)->where('shopify_product_id', $productId);
        
        // if variant found only
        if($variantId) {
            $productQuery->where('shopify_variant_id', $variantId);
        }
        $productReminder = $productQuery->first();
        if($productReminder) {
            return [
                'status' => 'error',
                'message' => 'Reminder already added.'
            ];
        }

        $productReminder = new ProductReminder;
        $productReminder->shop_id = $shop->id;
        $productReminder->shopify_product_id = $productId;

        // if variant found only
        if($variantId) {
            $productReminder->shopify_variant_id = $variantId;
        }

        $productReminder->remind_at = $remind_at;
        $productReminder->email = $email;
        $productReminder->save();

        return [
            'status' => 'error',
            'message' => 'We will remind you later.'
        ];
    }
}
