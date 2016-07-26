<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\MailChimpHelper;

class ProductRestockAlertController extends Controller
{
    // subscribe to a Shopify product's restock alert mailing list
    public function subscribe(Request $req) {

        $email = $req->get('email');
        $productTitle = $req->get('product_title');
        $productId = $req->get('product_id');
        $variantTitle = $req->get('variant_title');
        $variantId = $req->get('variant_id');

        $listName = '';
        if(!$variant_id) {
            $listName = 'Product Restock Alert: ' . $product_title;
        }
        else {
            $listName = 'Product Restock Alert: ' . $product_title . ' - '. $variant_title;
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

        return [
            'status' => 'error',
            'message' => 'We will get back to you shortly when your product is back in-stock.'
        ];
    }
}
