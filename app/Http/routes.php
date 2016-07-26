<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Generate Install Auth URL
 */
Route::get('/installURL/{myshopify_url}', 'ShopifyAuthController@generateInstallURL');

/**
 * Shopify Install/Authentication
 */
Route::get('/auth/{code?}', 'ShopifyAuthController@installOrAuthenticate');

/**
 * Shopify Webhooks route
 */
Route::post('/hooks/app/uninstall', 'ShopifyAuthController@uninstallHook');

/**
 * App dashboard
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * Shopify Product Restock Alert route
 */
Route::post('/api/products/restock_alerts/subscribe', 'ProductRestockAlertController@subscribe');

/**
 * Shopify Product Remind Me Later route
 */
Route::post('/api/products/reminders/subscribe', 'ProductRemindMeLaterController@subscribe');

/**
 * Newsletter route
 */
Route::post('/api/newsletter/subscribe', 'NewsletterController@subscribe');

/**
 * Shopify Product reviews routes
 */
// Route::get('/api/products/{shopify_product_id}/reviews', 'ProductReviewController@get');
// Route::post('/api/products/{shopify_product_id}/reviews/add', 'ProductReviewController@add');
// Route::put('/api/products/{shopify_product_id}/reviews/{review_id}/edit', 'ProductReviewController@edit');
// Route::delete('/api/products/{shopify_product_id}/reviews/{review_id}/delete', 'ProductReviewController@delete');


/**
 * Shopify Blog Article related-to Product routes
 */
// Route::get('/api/products/{shopify_product_id}/related_article', 'ProductRelatedArticleController@get');
// Route::post('/api/products/{shopify_product_id}/related_article/add', 'ProductRelatedArticleController@add');
// Route::put('/api/products/{shopify_product_id}/related_article/edit', 'ProductRelatedArticleController@edit');
// Route::delete('/api/products/{shopify_product_id}/related_article', 'ProductRelatedArticleController@delete');

/**
 * Shopify Product up-sells routes
 */
// Route::get('/api/products/{shopify_product_id}/upsells', 'ProductUpsellController@get');
// Route::post('/api/products/{shopify_product_id}/upsells/{upsell_id}/add', 'ProductUpsellController@add');
// Route::put('/api/products/{shopify_product_id}/upsells/{upsell_id}/edit', 'ProductUpsellController@edit');
// Route::delete('/api/products/{shopify_product_id}/upsells/{upsell_id}/delete', 'ProductUpsellController@delete');

/**
 * Promotional bar routes (countdown timers, product promo bar, etc)
 */
// Route::get('/api/promos', 'PromoController@index');
// Route::post('/api/promos/{promo_id}/add', 'PromoController@add');
// Route::put('/api/promos/{promo_id}/edit', 'PromoController@edit');
// Route::delete('/api/promos/{promo_id}/delete', 'PromoController@delete');



