<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
// use App\Mail\SupportMailManager;
/* TODO: Start Routes for delete */
Route::group(['middleware' => ['debug_ip_only', 'no_cache']], function () {
    Route::get('/redis_data/{search_key?}', 'HomeController@redis_data');
    Route::get('/debug_code', 'HomeController@debug');
});

//Route::get('btms/item/{item_code}', 'BtmsController@get_item');
//Route::get('btms/import_data', 'Btms\ImportController@import_data')->name('btms.import_data');
//Route::get('btms/vat_codes_test', 'Btms\ImportController@updateVatOnCountries');
//Route::get('btms/export_data/{api}/{accounting_data?}', 'Btms\ImportController@export_data');
Route::get('/refresh_cache/', 'HomeController@refresh_cache');
//
Route::get('/optimize-web', 'HomeController@clear_cache');

Route::get('/dev/find_images', 'DevController@findUnusedImagesFromUploadsTable');
/* TODO: End Routes for delete */

Route::get('/sessions', 'HomeController@sessions')->middleware('no_cache');
Route::get('/debug', 'HomeController@debug'); //->middleware('no_cache');
Route::get('/coming-soon', 'HomeController@coming_soon')->name('coming_soon');

Route::group(['middleware' => ['coming_soon']], function () {
//demo
    Route::get('/demo/cron_1', 'DemoController@cron_1');
    Route::get('/demo/cron_2', 'DemoController@cron_2');
    Route::get('/convert_assets', 'DemoController@convert_assets');
    Route::get('/convert_category', 'DemoController@convert_category');
    Route::get('/convert_tax', 'DemoController@convertTaxes');
    Route::get('/refresh-csrf/', 'HomeController@refresh_csrf');

    Route::post('/sk-uploader', 'SkUploadController@show_uploader');
    Route::post('/sk-uploader/upload', 'SkUploadController@upload');
    Route::get('/sk-uploader/get_uploaded_files', 'SkUploadController@get_uploaded_files');
    Route::post('/sk-uploader/get_file_by_ids', 'SkUploadController@get_preview_files');
    Route::get('/sk-uploader/download/{id}', 'SkUploadController@attachment_download')->name('download_attachment');


    Auth::routes(['verify' => true]);
    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::get('/email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
    Route::get('/verification-confirmation/{code}', 'Auth\VerificationController@verification_confirmation')->name('email.verification.confirmation');
    Route::get('/email_change/callback', 'HomeController@email_change_callback')->name('email_change.callback');
    Route::post('/password/reset/email/submit', 'HomeController@reset_password_with_code')->name('password.update');


    Route::post('/language', 'LanguageController@changeLanguage')->name('language.change');
    Route::post('/currency', 'CurrencyController@changeCurrency')->name('currency.change');

    Route::get('/social-login/redirect/{provider}', 'Auth\LoginController@redirectToProvider')->name('social.login');
    Route::get('/social-login/{provider}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');
    Route::get('/users/login', 'HomeController@login')->name('user.login');


    Route::get('/rfid_card_exists', 'CardController@rfid_card_exists')->name('rfid-card-exists');
    Route::post('/rfid_card_available', 'CardController@rfid_card_available')->name('rfid-card-available');

    Route::post('/rfid-can-be-edited', 'CardController@rfid_can_be_edited')->name('rfid-can-be-edited');


    Route::get('/get_valid_dates', 'CardController@get_valid_dates')->name('get-valid-dates');


    Route::post('/register_customer', 'Auth\RegisterController@register_customer')->name('customer.register');


    Route::get('/users/registration', 'HomeController@registration')->name('user.registration');
//Route::post('/users/login', 'HomeController@user_login')->name('user.login.submit');
    Route::post('/users/login/cart', 'HomeController@cart_login')->name('cart.login.submit');

//Home Page
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/home/section/featured', 'HomeController@load_featured_section')->name('home.section.featured');
    Route::post('/home/section/best_selling', 'HomeController@load_best_selling_section')->name('home.section.best_selling');
    Route::post('/home/section/home_categories', 'HomeController@load_home_categories_section')->name('home.section.home_categories');
    Route::post('/home/section/best_sellers', 'HomeController@load_best_sellers_section')->name('home.section.best_sellers');
//category dropdown menu ajax call
    Route::post('/category/nav-element-list', 'HomeController@get_category_items')->name('category.elements');

//Flash Deal Details Page
    Route::get('/flash-deals', 'HomeController@all_flash_deals')->name('flash-deals');
    Route::get('/flash-deal/{slug}', 'HomeController@flash_deal_details')->name('flash-deal-details');

    Route::get('/sitemap.xml', 'HomeController@sitemap');

    Route::get('/customer-products', 'CustomerProductController@customer_products_listing')->name('customer.products');
    Route::get('/customer-products?category={category_slug}', 'CustomerProductController@search')->name('customer_products.category');
    Route::get('/customer-products?city={city_id}', 'CustomerProductController@search')->name('customer_products.city');
    Route::get('/customer-products?q={search}', 'CustomerProductController@search')->name('customer_products.search');
    Route::get('/customer-product/{slug}', 'CustomerProductController@customer_product')->name('customer.product');
    Route::get('/customer-packages', 'HomeController@premium_package_index')->name('customer_packages_list_show');

    Route::get('/search', 'HomeController@search')->name('search');
    Route::get('/search?q={search}', 'HomeController@search')->name('suggestion.search');
    Route::post('/ajax-search', 'HomeController@ajax_search')->name('search.ajax');
    Route::post('/load-search', 'HomeController@load_search')->name('load_search');
    Route::post('/search-ajax', 'HomeController@search_ajax')->name('search_ajax');


    Route::get('/product/{slug}', 'HomeController@product')->name('product');
    Route::get('/brand/{brand_slug}/product/{slug}', 'HomeController@productBrand')->name('product.brand');
    Route::get('/type/{type_slug}/product/{slug}', 'HomeController@productType')->name('product.type');
    Route::get('/category/{category_slug}', 'HomeController@listingByCategory')->name('products.category');
    Route::get('/brand/{brand_slug}', 'HomeController@listingByBrand')->name('products.brand');
    Route::get('/type/{type_slug}', 'HomeController@listingByType')->name('products.type');
    Route::post('/product/variant_price', 'HomeController@variant_price')->name('products.variant_price');
    Route::post('/product/request-product', 'HomeController@request_product')->name('product.request');
    Route::post('/product/used-request-product', 'HomeController@used_request_product')->name('product.used_request');
    Route::post('/product/spare-parts', 'HomeController@spare_parts')->name('product.spare_parts');
    Route::get('/shop/{slug}', 'HomeController@shop')->name('shop.visit');
    Route::get('/shop/{slug}/{type}', 'HomeController@filter_shop')->name('shop.visit.type');

    Route::get('/brand/{brand_slug}/category/{category_slug}', 'HomeController@listingByBrandAndCategory')->name('products.brand_category');
    Route::get('/type/{type_slug}/category/{category_slug}', 'HomeController@listingByTypeAndCategory')->name('products.type_category');

    Route::get('/main-category/{category_slug}', 'HomeController@main_category')->name('maincategory');
    Route::get('/brand/{brand_slug}/main-category/{category_slug}', 'HomeController@listingMainCategoryByBrand')->name('brand.maincategory');
    Route::get('/main-category/type/{type_slug}', 'HomeController@listingMainCategoryByType')->name('type.maincategory');
    Route::get('/type/{type_slug}/main-category/{category_slug}', 'HomeController@listingMainCategoryByCatType')->name('typecat.maincategory');


    Route::get('/cart', 'CartController@index')->name('cart')->middleware('no_cache');
    Route::post('/cart/nav-cart-items', 'CartController@updateNavCart')->name('cart.nav_cart');
    Route::post('/cart/show-cart-modal', 'CartController@showCartModal')->name('cart.showCartModal');
    Route::post('/cart/addtocart', 'CartController@addToCart')->name('cart.addToCart');
    Route::post('/cart/addCateringPlanToCart', 'CartController@addCateringPlanToCart')->name('cart.addCateringPlanToCart');
    Route::post('/cart/removeFromCart', 'CartController@removeFromCart')->name('cart.removeFromCart');
    Route::post('/cart/updateQuantity', 'CartController@updateQuantity')->name('cart.updateQuantity');
    Route::get('/cart/mergeSessionAndDBProductsCart', 'CartController@mergeSessionAndDBProductsCart')->name('cart.mergeSessionAndDBProductsCart');

//Checkout Routes
    Route::group(['middleware' => ['checkout', 'no_cache']], function () {
        Route::get('/checkout', 'CheckoutController@get_shipping_info')->name('checkout.shipping_info');
        Route::any('/checkout/delivery_info', 'CheckoutController@store_shipping_info')->name('checkout.store_shipping_infostore');
        Route::post('/checkout/payment_select', 'CheckoutController@store_delivery_info')->name('checkout.store_delivery_info');
    });

    Route::get('/checkout/order-pending/{order_code?}', 'CheckoutController@order_pending')->name('order_pending');
    Route::get('/checkout/order-confirmed/{order_code?}', 'CheckoutController@order_confirmed')->middleware('no_cache')->name('order_confirmed');
    Route::get('/checkout/order-failed', 'CheckoutController@order_failed')->name('order_failed')->middleware('no_cache');
    Route::post('/checkout/payment', 'CheckoutController@checkout')->middleware('no_cache')->name('payment.checkout');
    Route::post('/get_pick_ip_points', 'HomeController@get_pick_ip_points')->name('shipping_info.get_pick_ip_points');
    Route::get('/checkout/payment_select', 'CheckoutController@get_payment_info')->name('checkout.payment_info');
    Route::post('/checkout/apply_coupon_code', 'CheckoutController@apply_coupon_code')->name('checkout.apply_coupon_code');
    Route::post('/checkout/remove_coupon_code', 'CheckoutController@remove_coupon_code')->name('checkout.remove_coupon_code');
    Route::post('/checkout/get_shipping_methods', 'CheckoutController@get_shipping_methods')->name('checkout.get_shipping_methods');
    Route::post('/checkout/select_shipping_method', 'CheckoutController@select_shipping_method')->name('checkout.select_shipping_method');
//Club point
    Route::post('/checkout/apply-club-point', 'CheckoutController@apply_club_point')->name('checkout.apply_club_point');
    Route::post('/checkout/remove-club-point', 'CheckoutController@remove_club_point')->name('checkout.remove_club_point');

//Paypal START
    Route::get('/paypal/payment/done', 'PaypalController@getDone')->name('payment.done');
    Route::get('/paypal/payment/cancel', 'PaypalController@getCancel')->name('payment.cancel');
//Paypal END
// SSLCOMMERZ Start
    Route::get('/sslcommerz/pay', 'PublicSslCommerzPaymentController@index');
    Route::POST('/sslcommerz/success', 'PublicSslCommerzPaymentController@success');
    Route::POST('/sslcommerz/fail', 'PublicSslCommerzPaymentController@fail');
    Route::POST('/sslcommerz/cancel', 'PublicSslCommerzPaymentController@cancel');
    Route::POST('/sslcommerz/ipn', 'PublicSslCommerzPaymentController@ipn');
//SSLCOMMERZ END
//Stipe Start
    Route::get('stripe', 'StripePaymentController@stripe');
    Route::post('/stripe/create-checkout-session', 'StripePaymentController@create_checkout_session')->name('stripe.get_token');
    Route::any('/stripe/payment/callback', 'StripePaymentController@callback')->name('stripe.callback');
    Route::get('/stripe/success', 'StripePaymentController@success')->name('stripe.success');
    Route::get('/stripe/cancel', 'StripePaymentController@cancel')->name('stripe.cancel');
//Stripe END

    Route::get('/compare', 'CompareController@index')->name('compare');
    Route::get('/compare/reset', 'CompareController@reset')->name('compare.reset');
    Route::post('/compare/addToCompare', 'CompareController@addToCompare')->name('compare.addToCompare');

    Route::resource('subscribers', 'SubscriberController');
    Route::post('/subscribers-ajax', 'SubscriberController@subscribers_store_ajax')->name('subscribers_store_ajax');

    Route::get('/brands', 'HomeController@all_brands')->name('brands.all');
    Route::get('/categories', 'HomeController@all_categories')->name('categories.all');
    Route::get('/sellers', 'HomeController@all_seller')->name('sellers');

    Route::get('/terms-policies', 'HomeController@terms_policies')->name('termspolicies');
    Route::get('/sellerpolicy', 'HomeController@sellerpolicy')->name('sellerpolicy');
    Route::get('/returnpolicy', 'HomeController@returnpolicy')->name('returnpolicy');
    Route::get('/supportpolicy', 'HomeController@supportpolicy')->name('supportpolicy');
    Route::get('/terms', 'HomeController@terms')->name('terms');
    Route::get('/privacypolicy', 'HomeController@privacypolicy')->name('privacypolicy');
    /*Route::get('/about-us', 'HomeController@about_us')->name('about_us');
    Route::get('/services', 'HomeController@services')->name('services');
    Route::get('/partnership', 'HomeController@partnership')->name('partnership');
    Route::post('/partnership/request', 'HomeController@partnership_request')->name('b2b.send');
    Route::post('/partnership/request-ajax', 'HomeController@partnership_request_ajax')->name('b2b.send_ajax');
    Route::get('/stores', 'HomeController@stores_page')->name('stores');
    Route::get('/used-products', 'HomeController@used_page')->name('used_page');
    Route::get('/faqs', 'HomeController@faqs')->name('faqs');*/

    //Contact Us
    /*Route::get('/contact', 'HomeController@contact')->name('contact');
    Route::post('/contact-send', 'HomeController@contact_send')->name('contact.send');
    Route::post('/contact-send-ajax', 'HomeController@contact_send_ajax')->name('contact.send_ajax');*/

    Route::group(['middleware' => ['user', 'verified', 'unbanned']], function () {
        Route::get('/new-subscriptions/{card_id}', 'CateringPlanController@get_subscriptions')->name('new_subscriptions.get_subscriptions')->middleware([ 'no_cache']);

//        Route::get('/get_client_ip', 'CardController@get_client_ip')->name('get_client_ip');

        Route::get('/card/card_upcoming_meals', 'CardController@card_upcoming_meals')->name('card.card_upcoming_meals');
        Route::get('/card/register_new_card', 'CardController@register_new_card')->name('card.register_new_card');
        Route::post('/card/register_card/', 'CardController@register_card')->name('card.register_card');
        Route::post('/catering_plan/plan_max_quantities', 'CateringPlanController@plan_max_quantities')->name('catering_plan.plan_max_quantities');
        Route::post('/card/edit_card', 'CardController@edit_rfid_no')->name('card.edit_rfid_no');
        Route::post('/catering_plan/meal_description', 'CateringPlanController@meal_description')->name('catering_plan.meal_description');
        Route::post('/card/edit_card_name', 'CardController@edit_card_name')->name('card.edit_card_name');


        Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard')->middleware([ 'user']);
        Route::get('/dashboard/subscription_history/{card_id}', 'CardController@subscription_history')->name('dashboard.subscription_history');
        Route::get('/dashboard/meals_history/{card_id}', 'CardController@meals_history')->name('dashboard.meals_history');
        Route::post('/dashboard/app_order_details', 'HomeController@app_order_details')->name('user.app_order_details');



        Route::get('/profile', 'HomeController@profile')->name('profile');
        Route::post('/new-user-verification', 'HomeController@new_verify')->name('user.new.verify');
        Route::post('/new-user-email', 'HomeController@update_email')->name('user.change.email');
        Route::post('/customer/update-profile', 'HomeController@customer_update_profile')->name('customer.profile.update');
        Route::post('/seller/update-profile', 'HomeController@seller_update_profile')->name('seller.profile.update');

        Route::resource('purchase_history', 'PurchaseHistoryController');
        Route::post('/purchase_history/details', 'PurchaseHistoryController@purchase_history_details')->name('purchase_history.details');
        Route::get('/purchase_history/destroy/{id}', 'PurchaseHistoryController@destroy')->name('purchase_history.destroy');

        Route::resource('wishlists', 'WishlistController');
        Route::post('/wishlists/remove', 'WishlistController@remove')->name('wishlists.remove');

        Route::get('/wallet', 'WalletController@index')->name('wallet.index');
        Route::post('/recharge', 'WalletController@recharge')->name('wallet.recharge');

        Route::resource('support_ticket', 'SupportTicketController');
        Route::post('support_ticket/reply', 'SupportTicketController@seller_store')->name('support_ticket.seller_store');

        Route::post('/customer_packages/purchase', 'CustomerPackageController@purchase_package')->name('customer_packages.purchase');
        Route::resource('customer_products', 'CustomerProductController');
        Route::get('/customer_products/{id}/edit', 'CustomerProductController@edit')->name('customer_products.edit');
        Route::post('/customer_products/published', 'CustomerProductController@updatePublished')->name('customer_products.published');
        Route::post('/customer_products/status', 'CustomerProductController@updateStatus')->name('customer_products.update.status');

        Route::get('digital_purchase_history', 'PurchaseHistoryController@digital_index')->name('digital_purchase_history.index');
        Route::post('/user/close_account', 'HomeController@close_account')->name('user.close_account');

//        New website route
        Route::get('/credit_cards', 'CreditCardController@index')->name('credit_cards');
        Route::post('/credit_cards/delete_card', 'CreditCardController@delete_credit_card')->name('credit_card.delete_credit_card');

        Route::post('/store_canteen_user', 'CanteenAppUserController@store_ajax')->name('canteen_app_user.store_ajax');
        Route::post('/canteen_user/unassigned_credit_card', 'CanteenAppUserController@unassign_credit_card')->name('canteen_app_user.unassigned_credit_card');
        Route::post('/canteen_user/update_username', 'CanteenAppUserController@update_username_ajax')->name('canteen_app_user.update_username');
        Route::post('/canteen_user/update_daily_limit', 'CanteenAppUserController@update_daily_limit')->name('canteen_app_user.update_daily_limit');
        Route::post('/canteen_user/change_password', 'CanteenAppUserController@change_password')->name('canteen_app_user.change_password');
        Route::post('/user/credit_card/update_nickname', 'CreditCardController@update_nickname')->name('credit_card.update_nickname');
        Route::post('/user/credit_card/assigned_credit_card', 'CreditCardController@assigned_credit_card')->name('credit_card.assigned_credit_card');
        Route::get('/dashboard/canteen_orders_history/{canteen_user_id}', 'CanteenAppUserController@canteen_orders_history')->name('dashboard.canteen_orders_history');
//        Route::post('/delete_credit_card', 'CanteenAppUserController@destry')->name('canteen_app_user.unassigned_credit_card');

    });

    Route::get('/customer_products/destroy/{id}', 'CustomerProductController@destroy')->name('customer_products.destroy');

    Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user']], function () {
        Route::get('/products', 'HomeController@seller_product_list')->name('seller.products');
        Route::get('/product/upload', 'HomeController@show_product_upload_form')->name('seller.products.upload');
        Route::get('/product/{id}/edit', 'HomeController@show_product_edit_form')->name('seller.products.edit');
        Route::resource('payments', 'PaymentController');

        Route::get('/shop/apply_for_verification', 'ShopController@verify_form')->name('shop.verify');
        Route::post('/shop/apply_for_verification', 'ShopController@verify_form_store')->name('shop.verify.store');

        Route::get('/reviews', 'ReviewController@seller_reviews')->name('reviews.seller');

        //digital Product
        Route::get('/digitalproducts', 'HomeController@seller_digital_product_list')->name('seller.digitalproducts');
        Route::get('/digitalproducts/upload', 'HomeController@show_digital_product_upload_form')->name('seller.digitalproducts.upload');
        Route::get('/digitalproducts/{id}/edit', 'HomeController@show_digital_product_edit_form')->name('seller.digitalproducts.edit');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::post('/products/store/', 'ProductController@store')->name('products.store');
        Route::post('/products/update/{id}', 'ProductController@update')->name('products.update');
        Route::get('/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
        Route::get('/products/duplicate/{id}', 'ProductController@duplicate')->name('products.duplicate');
        Route::post('/products/sku_combination', 'ProductController@sku_combination')->name('products.sku_combination');
        Route::post('/products/sku_combination_edit', 'ProductController@sku_combination_edit')->name('products.sku_combination_edit');
        Route::post('/products/seller/featured', 'ProductController@updateSellerFeatured')->name('products.seller.featured');
        Route::post('/products/published', 'ProductController@updatePublished')->name('products.published');

        Route::get('invoice/{order_id}', 'InvoiceController@invoice_download')->name('invoice.download');
        Route::get('canteen_invoice/{order_id}', 'CanteenInvoiceController@invoice_download')->name('canteen_invoice.download');

        Route::resource('orders', 'OrderController');
        Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
        Route::post('/orders/details', 'OrderController@order_details')->name('orders.details');
        Route::post('/orders/update_delivery_status', 'OrderController@update_delivery_status')->name('orders.update_delivery_status');
        Route::post('/orders/update_payment_status', 'OrderController@update_payment_status')->name('orders.update_payment_status');


        Route::resource('/reviews', 'ReviewController');

        Route::resource('/withdraw_requests', 'SellerWithdrawRequestController');
        Route::get('/withdraw_requests_all', 'SellerWithdrawRequestController@request_index')->name('withdraw_requests_all');
        Route::post('/withdraw_request/payment_modal', 'SellerWithdrawRequestController@payment_modal')->name('withdraw_request.payment_modal');
        Route::post('/withdraw_request/message_modal', 'SellerWithdrawRequestController@message_modal')->name('withdraw_request.message_modal');

        Route::resource('conversations', 'ConversationController');
        Route::get('/conversations/destroy/{id}', 'ConversationController@destroy')->name('conversations.destroy');
        Route::post('conversations/refresh', 'ConversationController@refresh')->name('conversations.refresh');
        Route::post('conversations/store_account_chat', 'ConversationController@store_account_chat')->name('conversations.store_account_chat');
        Route::resource('messages', 'MessageController');

        //Product Bulk Upload
        Route::get('/product-bulk-upload/index', 'ProductBulkUploadController@index')->name('product_bulk_upload.index');
        Route::post('/bulk-product-upload', 'ProductBulkUploadController@bulk_upload')->name('bulk_product_upload');
        Route::get('/product-csv-download/{type}', 'ProductBulkUploadController@import_product')->name('product_csv.download');
        Route::get('/vendor-product-csv-download/{id}', 'ProductBulkUploadController@import_vendor_product')->name('import_vendor_product.download');
        Route::group(['prefix' => 'bulk-upload/download'], function () {
            Route::get('/category', 'ProductBulkUploadController@pdf_download_category')->name('pdf.download_category');
            Route::get('/brand', 'ProductBulkUploadController@pdf_download_brand')->name('pdf.download_brand');
            Route::get('/seller', 'ProductBulkUploadController@pdf_download_seller')->name('pdf.download_seller');
        });

        //Product Export
        Route::get('/product-bulk-export', 'ProductBulkUploadController@export')->name('product_bulk_export.index');

        Route::resource('digitalproducts', 'DigitalProductController');
        Route::get('/digitalproducts/edit/{id}', 'DigitalProductController@edit')->name('digitalproducts.edit');
        Route::get('/digitalproducts/destroy/{id}', 'DigitalProductController@destroy')->name('digitalproducts.destroy');
        Route::get('/digitalproducts/download/{id}', 'DigitalProductController@download')->name('digitalproducts.download');

        //Reports
        Route::get('/commission-log', 'ReportController@commission_history')->name('commission-log.index');
    });

    Route::resource('shops', 'ShopController');
    Route::get('/track_your_order', 'HomeController@trackOrder')->name('orders.track');

    Route::get('/instamojo/payment/pay-success', 'InstamojoController@success')->name('instamojo.success');

    Route::post('rozer/payment/pay-success', 'RazorpayController@payment')->name('payment.rozer');

    Route::get('/paystack/payment/callback', 'PaystackController@handleGatewayCallback');

    Route::get('/vogue-pay', 'VoguePayController@showForm');
    Route::get('/vogue-pay/success/{id}', 'VoguePayController@paymentSuccess');
    Route::get('/vogue-pay/failure/{id}', 'VoguePayController@paymentFailure');

    //Iyzico
    Route::any('/iyzico/payment/callback/{payment_type}/{amount?}/{payment_method?}/{order_id?}/{customer_package_id?}/{seller_package_id?}', 'IyzicoController@callback')->name('iyzico.callback');

    Route::post('/get-phone-code', 'CountryController@get_phone_code')->name('get-phone-code');

    Route::post('/get-city', 'CityController@get_city')->name('get-city');
    Route::post('/get-selected-city', 'CityController@get_selected_city')->name('get-selected-city');

    Route::resource('addresses', 'AddressController');
    Route::post('/addresses/update/{id}', 'AddressController@update')->name('addresses.update');
    Route::get('/addresses/destroy/{id}', 'AddressController@destroy')->name('addresses.destroy');
    Route::get('/addresses/set_default/{id}', 'AddressController@set_default')->name('addresses.set_default');

    //payhere below
    Route::get('/payhere/checkout/testing', 'PayhereController@checkout_testing')->name('payhere.checkout.testing');
    Route::get('/payhere/wallet/testing', 'PayhereController@wallet_testing')->name('payhere.checkout.testing');
    Route::get('/payhere/customer_package/testing', 'PayhereController@customer_package_testing')->name('payhere.customer_package.testing');

    Route::any('/payhere/checkout/notify', 'PayhereController@checkout_notify')->name('payhere.checkout.notify');
    Route::any('/payhere/checkout/return', 'PayhereController@checkout_return')->name('payhere.checkout.return');
    Route::any('/payhere/checkout/cancel', 'PayhereController@chekout_cancel')->name('payhere.checkout.cancel');

    Route::any('/payhere/wallet/notify', 'PayhereController@wallet_notify')->name('payhere.wallet.notify');
    Route::any('/payhere/wallet/return', 'PayhereController@wallet_return')->name('payhere.wallet.return');
    Route::any('/payhere/wallet/cancel', 'PayhereController@wallet_cancel')->name('payhere.wallet.cancel');

    Route::any('/payhere/seller_package_payment/notify', 'PayhereController@seller_package_notify')->name('payhere.seller_package_payment.notify');
    Route::any('/payhere/seller_package_payment/return', 'PayhereController@seller_package_payment_return')->name('payhere.seller_package_payment.return');
    Route::any('/payhere/seller_package_payment/cancel', 'PayhereController@seller_package_payment_cancel')->name('payhere.seller_package_payment.cancel');
    Route::get('/migrate/products/', 'PayhereController@migrate_seller_package_payment');

    Route::any('/payhere/customer_package_payment/notify', 'PayhereController@customer_package_notify')->name('payhere.customer_package_payment.notify');
    Route::any('/payhere/customer_package_payment/return', 'PayhereController@customer_package_return')->name('payhere.customer_package_payment.return');
    Route::any('/payhere/customer_package_payment/cancel', 'PayhereController@customer_package_cancel')->name('payhere.customer_package_payment.cancel');

    //N-genius
    Route::any('ngenius/cart_payment_callback', 'NgeniusController@cart_payment_callback')->name('ngenius.cart_payment_callback');
    Route::any('ngenius/wallet_payment_callback', 'NgeniusController@wallet_payment_callback')->name('ngenius.wallet_payment_callback');
    Route::get('/migrate/database', 'NgeniusController@migrate_ngenius');
    Route::any('ngenius/customer_package_payment_callback', 'NgeniusController@customer_package_payment_callback')->name('ngenius.customer_package_payment_callback');
    Route::any('ngenius/seller_package_payment_callback', 'NgeniusController@seller_package_payment_callback')->name('ngenius.seller_package_payment_callback');

    //bKash
    Route::post('/bkash/createpayment', 'BkashController@checkout')->name('bkash.checkout');
    Route::post('/bkash/executepayment', 'BkashController@excecute')->name('bkash.excecute');
    Route::get('/bkash/success', 'BkashController@success')->name('bkash.success');

    //Nagad
    Route::get('/nagad/callback', 'NagadController@verify')->name('nagad.callback');

    //Blog Section
    Route::get('/blog', 'BlogController@all_blog')->name('blog');
    Route::get('/blog/{slug}', 'BlogController@blog_details')->name('blog.details');
    Route::post('/blog/load_blog', 'BlogController@load_blog')->name('blog.load_blog');

    // Tutorials
    Route::get('/tutorial', 'TutorialController@tutorial_page')->name('tutorial');

    //Custom page
    Route::get('/{slug}', 'PageController@show_custom_page')->name('custom-pages.show_custom_page');

    //Brand Page
    Route::get('/brand-categories/{slug}', 'HomeController@brand_page')->name('brand_page');

//
//    Route::get('/cashier', 'HomeController@cashier_dashboard' )->name('cashier.dashboard')->middleware(['auth','cashier']);
//
//    Route::group(['prefix' => 'cashier', 'middleware' => ['auth', 'cashier']], function() {
//
//    });
});
