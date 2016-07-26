<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\MailChimpHelper;

class NewsletterController extends Controller
{

    // subscribe to the newsletter mailing list
    public function subscribe(Request $req) {

        $listId = '';
        $email  = $req->get('email');

        $shopDomain   = $req->get('shop_domain');

        $shop = Shop::where('myshopify_domain', $shopDomain)->first();
        if(!$shop) {
            return [
                'status' => 'error',
                'message' => 'Shop was not found.'
            ];
        }

        $mailChimp = new MailChimpHelper(config('mailchimp'));

        // check authentication
        if(!$mailChimp->checkAuth()) {
            return [
                'status' => 'error',
                'message' => 'Invalid MailChimp account credentials.'
            ];
        }

        $currentMember = $mailChimp->hasMember($listId, $email);
        if(!$currentMember) {
            $currentMember = $mailChimp->subscribeMember($listId, $email);
        }

        return [
            'status' => 'success',
            'message' => 'Thank you for subscribing to our weekly mailing list.'
        ];
    }
}
