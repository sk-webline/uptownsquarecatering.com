<?php

/*
  |--------------------------------------------------------------------------
  | Admin Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register admin routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
Route::get('/clear-cache', 'HomeController@clear_cache');
Route::post('/update', 'UpdateController@step0')->name('update');
Route::get('/update/step1', 'UpdateController@step1')->name('update.step1');
Route::get('/update/step2', 'UpdateController@step2')->name('update.step2');

Route::get('/admin', 'HomeController@admin_dashboard')->name('admin.dashboard')->middleware(['auth', 'admin']);
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function() {
    //Update Routes

    Route::POST('/rfid_search', 'HomeController@rfid_search')->name('admin.rfid_search');

    Route::resource('organisations', 'OrganisationController');
    Route::get('/organisations/destroy/{id}', 'OrganisationController@destroy')->name('organisations.destroy');
    Route::POST('/organisations/update/{id}', 'OrganisationController@update')->name('organisations.update');
    Route::post('/organisations/import', 'OrganisationController@import')->name('organisations.import');
    Route::post('/organisations/get_selected_organisations', 'OrganisationController@get_selected_organisations')->name('organisations.get_selected_organisations');

//    Route::resource('organisation_settings', 'OrganisationSettingsController');
    Route::get('/organisation_settings/{organisation_id}', 'OrganisationSettingsController@index')->name('organisation_settings.index');
    Route::get('/organisation_settings/create/{organisation_id}', 'OrganisationSettingsController@create')->name('organisation_settings.create');
    Route::get('/organisation_settings/edit/{id}', 'OrganisationSettingsController@edit')->name('organisation_settings.edit');
    Route::post('/organisation_settings/store/{organisation_id}', 'OrganisationSettingsController@store')->name('organisation_settings.store');
    Route::post('/organisation_settings/update/{id}', 'OrganisationSettingsController@update')->name('organisation_settings.update');
    Route::get('/organisation_settings/delete/{id}', 'OrganisationSettingsController@destroy')->name('organisation_settings.destroy');

    Route::get('/organisation_locations/{organisation_id}', 'OrganisationLocationController@index')->name('organisation_locations.index');
    Route::get('/organisation_locations/create/{organisation_id}', 'OrganisationLocationController@create')->name('organisation_locations.create');
    Route::get('/organisation_locations/edit/{id}', 'OrganisationLocationController@edit')->name('organisation_locations.edit');
    Route::post('/organisation_locations/store/{organisation_id}', 'OrganisationLocationController@store')->name('organisation_locations.store');
    Route::post('/organisation_locations/update/{id}', 'OrganisationLocationController@update')->name('organisation_locations.update');
    Route::get('/organisation_locations/delete/{id}', 'OrganisationLocationController@destroy')->name('organisation_locations.destroy');

    Route::get('/organisation_cards/{organisation_id}', 'CardController@index')->name('organisation_cards.index');
    Route::get('/organisation_cards/sync/{organisation_id}', 'CardController@sync')->name('organisation_cards.sync');
    Route::post('/organisation_cards/generate_custom_cards/{organisation_id}', 'CardController@generate_custom_cards')->name('organisation_cards.generate_custom_cards');
    Route::post('/card/change_card_details', 'CardController@change_card_details')->name('card.change_card_details');
    Route::post('/rfid-can-be-edited', 'CardController@rfid_can_be_edited')->name('rfid-can-be-edited');
    Route::post('/card/change_card_name', 'CardController@change_card_name')->name('card.change_card_name');
    Route::post('/card/get_on_going_subscriptions', 'CardController@get_on_going_subscriptions')->name('card.get_on_going_subscriptions');
    Route::get('/card/remove_card_from_user/{card_id}', 'CardController@remove_card_from_user')->name('card.remove_card_from_user');



//    Route::get('/organisation_cards/rfid_card_exists', 'CardController@rfid_card_exists')->name('organisation_cards.rfid_card_exists');

//    Route::resource('organisation_prices', 'OrganisationSettingsController');
    Route::get('/organisation_prices/create/{organisation_setting_id}', 'OrganisationPriceController@create')->name('organisation_prices.create');
    Route::post('/organisation_prices/store/{organisation_setting_id}', 'OrganisationPriceController@store')->name('organisation_prices.store');
    Route::post('/organisation_prices/update/{organisation_setting_id}', 'OrganisationPriceController@update')->name('organisation_prices.update');

    Route::post('/organisation_extra_days/store/{organisation_setting_id}', 'OrganisationExtraDayController@store')->name('organisation_extra_days.store');

    Route::get('/organisation_settings/{organisation_setting_id}/catering_plans', 'CateringPlanController@index')->name('catering_plans.index');
    Route::get('/catering_plans/create/{organisation_setting_id}', 'CateringPlanController@create')->name('catering_plans.create');
    Route::post('/catering_plans/store/{organisation_setting_id}', 'CateringPlanController@store')->name('catering_plans.store');
    Route::get('/catering_plans/edit/{id}', 'CateringPlanController@edit')->name('catering_plans.edit');
    Route::post('/catering_plans/update/{id}', 'CateringPlanController@update')->name('catering_plans.update');

    Route::get('/catering_plans_purchases/destroy/{id}', 'CateringPlanPurchaseController@destroy')->name('catering_plan_purchases.destroy');

    Route::get('/catering_reports', 'CateringReportController@index')->name('catering_reports.index');
    Route::get('/catering_report/show', 'CateringReportController@show')->name('catering_reports.show');

    Route::get('/meal_reports', 'MealReportController@index')->name('meal_reports.index');
    Route::get('/meal_report/show', 'MealReportController@show')->name('meal_reports.show');


    Route::resource('categories', 'CategoryController');
    Route::get('/categories/edit/{id}', 'CategoryController@edit')->name('categories.edit');
    Route::get('/categories/brands-edit/{id}', 'CategoryController@brands_edit')->name('categories.brands_edit');
    Route::get('/categories/destroy/{id}', 'CategoryController@destroy')->name('categories.destroy');
    Route::post('/categories/brand-edit/{id}', 'CategoryController@update_brands')->name('categories.brand_update');
    Route::post('/categories/featured', 'CategoryController@updateFeatured')->name('categories.featured');
    Route::post('/categories/forsale', 'CategoryController@updateForSale')->name('categories.forsale');
    Route::post('/categories/show_b2b', 'CategoryController@updateShowB2B')->name('categories.show_b2b');
    Route::post('/categories/show_header', 'CategoryController@updateShowHeader')->name('categories.show_header');

    Route::resource('category_brands', 'CategoryBrandController');

    Route::get('sizes', 'SizeController@index')->name('sizes.index');
    Route::post('sizes/update_order', 'SizeController@update_order')->name('sizes.update_order');

    Route::resource('services', 'ServiceController');
    Route::get('/services/edit/{id}', 'ServiceController@edit')->name('services.edit');
    Route::get('/services/destroy/{id}', 'ServiceController@destroy')->name('services.destroy');

    Route::resource('brands', 'BrandController');
    Route::get('/brands/edit/{id}', 'BrandController@edit')->name('brands.edit');
    Route::get('/brands/destroy/{id}', 'BrandController@destroy')->name('brands.destroy');

    Route::resource('product_types', 'ProductTypeController');
    Route::get('/product-types/edit/{id}', 'ProductTypeController@edit')->name('product_types.edit');
    Route::get('/product-types/destroy/{id}', 'ProductTypeController@destroy')->name('product_types.destroy');

    Route::get('/products/admin', 'ProductController@admin_products')->name('products.admin');
    Route::get('/products/seller', 'ProductController@seller_products')->name('products.seller');
    Route::get('/products/all', 'ProductController@all_products')->name('products.all');
    Route::get('/products/create', 'ProductController@create')->name('products.create');
    Route::get('/products/admin/{id}/edit', 'ProductController@admin_product_edit')->name('products.admin.edit');
    Route::get('/products/seller/{id}/edit', 'ProductController@seller_product_edit')->name('products.seller.edit');
    Route::post('/products/todays_deal', 'ProductController@updateTodaysDeal')->name('products.todays_deal');
    Route::post('/products/update_cyprus_shipping', 'ProductController@updateCyprusShippingOnly')->name('products.update_cyprus_shipping');
    Route::post('/products/featured', 'ProductController@updateFeatured')->name('products.featured');
    Route::post('/products/get_products_by_subcategory', 'ProductController@get_products_by_subcategory')->name('products.get_products_by_subcategory');
    Route::post('/products/show_subtitle', 'ProductController@updateSubtitle')->name('product.showSubtitle');

    Route::resource('sellers', 'SellerController');
    Route::get('sellers_ban/{id}', 'SellerController@ban')->name('sellers.ban');
    Route::get('/sellers/destroy/{id}', 'SellerController@destroy')->name('sellers.destroy');
    Route::get('/sellers/view/{id}/verification', 'SellerController@show_verification_request')->name('sellers.show_verification_request');
    Route::get('/sellers/approve/{id}', 'SellerController@approve_seller')->name('sellers.approve');
    Route::get('/sellers/reject/{id}', 'SellerController@reject_seller')->name('sellers.reject');
    Route::get('/sellers/login/{id}', 'SellerController@login')->name('sellers.login');
    Route::post('/sellers/payment_modal', 'SellerController@payment_modal')->name('sellers.payment_modal');
    Route::get('/seller/payments', 'PaymentController@payment_histories')->name('sellers.payment_histories');
    Route::get('/seller/payments/show/{id}', 'PaymentController@show')->name('sellers.payment_history');

    Route::resource('customers', 'CustomerController');
    Route::get('customers_ban/{customer}', 'CustomerController@ban')->name('customers.ban');
    Route::get('/customers/login/{id}', 'CustomerController@login')->name('customers.login');
    Route::get('/customers/destroy/{id}', 'CustomerController@destroy')->name('customers.destroy');
    Route::post('/customers/pay-on-credit', 'CustomerController@updatePayOnCredit')->name('customers.pay_on_credit');
    Route::post('/customers/pay-on-delivery', 'CustomerController@updatePayOnDelivery')->name('customers.pay_on_delivery');
    Route::post('/customers/excluded_vat', 'CustomerController@excluded_vat')->name('customers.excluded_vat');
    Route::get('/customers/edit/{id}', 'CustomerController@edit')->name('customers.edit');
    Route::get('/customers/view_catering_plans/{id}', 'CustomerController@view_catering_plans')->name('customers.view_catering_plans');

    Route::resource('cashiers', 'CashierController');
    Route::get('/cashiers/destroy/{id}', 'CashierController@destroy')->name('cashiers.destroy');
    Route::post('/cashiers/update/{id}', 'CashierController@update')->name('cashiers.update');

    Route::get('/newsletter', 'NewsletterController@index')->name('newsletters.index');
    Route::post('/newsletter/send', 'NewsletterController@send')->name('newsletters.send');
    Route::post('/newsletter/test/smtp', 'NewsletterController@testEmail')->name('test.smtp');

    Route::resource('profile', 'ProfileController');

    Route::resource('platform_settings', 'PlatformSettingsController');

    Route::post('/business-settings/update', 'BusinessSettingsController@update')->name('business_settings.update');
    Route::post('/business-settings/update/activation', 'BusinessSettingsController@updateActivationSettings')->name('business_settings.update.activation');
    Route::get('/general-setting', 'BusinessSettingsController@general_setting')->name('general_setting.index');
    Route::get('/activation', 'BusinessSettingsController@activation')->name('activation.index');

    Route::post('/platform-settings/update-vat', 'PlatformSettingsController@setVAT')->name('platform_settings.set_vat');
    Route::post('/platform-settings/update-minutes-for-meal-cancel', 'PlatformSettingsController@set_minutes_for_cancel')->name('platform_settings.set_minutes_cancel');
    Route::post('/platform-settings//update_max_failed_login_attempts', 'PlatformSettingsController@set_max_failed_login_attempts')->name('platform_settings.set_max_failed_login_attempts');
    Route::post('/platform-settings/update-lock-minutes', 'PlatformSettingsController@set_lock_minutes')->name('platform_settings.set_lock_minutes');
    Route::post('/platform-settings/update-check-lock-minutes', 'PlatformSettingsController@set_check_lock_minutes')->name('platform_settings.set_check_lock_minutes');

    Route::get('/payment-method', 'BusinessSettingsController@payment_method')->name('payment_method.index');
    Route::get('/file_system', 'BusinessSettingsController@file_system')->name('file_system.index');
    Route::get('/social-login', 'BusinessSettingsController@social_login')->name('social_login.index');
    Route::get('/smtp-settings', 'BusinessSettingsController@smtp_settings')->name('smtp_settings.index');
    Route::get('/google-analytics', 'BusinessSettingsController@google_analytics')->name('google_analytics.index');
    Route::get('/google-recaptcha', 'BusinessSettingsController@google_recaptcha')->name('google_recaptcha.index');

    //Facebook Settings
    Route::get('/facebook-chat', 'BusinessSettingsController@facebook_chat')->name('facebook_chat.index');
    Route::post('/facebook_chat', 'BusinessSettingsController@facebook_chat_update')->name('facebook_chat.update');
    Route::get('/facebook-comment', 'BusinessSettingsController@facebook_comment')->name('facebook-comment');
    Route::post('/facebook-comment', 'BusinessSettingsController@facebook_comment_update')->name('facebook-comment.update');
    Route::post('/facebook_pixel', 'BusinessSettingsController@facebook_pixel_update')->name('facebook_pixel.update');

    Route::post('/env_key_update', 'BusinessSettingsController@env_key_update')->name('env_key_update.update');
    Route::post('/payment_method_update', 'BusinessSettingsController@payment_method_update')->name('payment_method.update');
    Route::post('/google_analytics', 'BusinessSettingsController@google_analytics_update')->name('google_analytics.update');
    Route::post('/google_recaptcha', 'BusinessSettingsController@google_recaptcha_update')->name('google_recaptcha.update');
    //Currency
    Route::get('/currency', 'CurrencyController@currency')->name('currency.index');
    Route::post('/currency/update', 'CurrencyController@updateCurrency')->name('currency.update');
    Route::post('/your-currency/update', 'CurrencyController@updateYourCurrency')->name('your_currency.update');
    Route::get('/currency/create', 'CurrencyController@create')->name('currency.create');
    Route::post('/currency/store', 'CurrencyController@store')->name('currency.store');
    Route::post('/currency/currency_edit', 'CurrencyController@edit')->name('currency.edit');
    Route::post('/currency/update_status', 'CurrencyController@update_status')->name('currency.update_status');

    //Tax
    Route::resource('tax', 'TaxController');
    Route::get('/tax/edit/{id}', 'TaxController@edit')->name('tax.edit');
    Route::get('/tax/destroy/{id}', 'TaxController@destroy')->name('tax.destroy');
    Route::post('tax-status', 'TaxController@change_tax_status')->name('taxes.tax-status');


    Route::get('/verification/form', 'BusinessSettingsController@seller_verification_form')->name('seller_verification_form.index');
    Route::post('/verification/form', 'BusinessSettingsController@seller_verification_form_update')->name('seller_verification_form.update');
    Route::get('/vendor_commission', 'BusinessSettingsController@vendor_commission')->name('business_settings.vendor_commission');
    Route::post('/vendor_commission_update', 'BusinessSettingsController@vendor_commission_update')->name('business_settings.vendor_commission.update');

    Route::resource('/languages', 'LanguageController');
    Route::post('/languages/{id}/update', 'LanguageController@update')->name('languages.update');
    Route::get('/languages/destroy/{id}', 'LanguageController@destroy')->name('languages.destroy');
    Route::post('/languages/update_rtl_status', 'LanguageController@update_rtl_status')->name('languages.update_rtl_status');
    Route::post('/languages/key_value_store', 'LanguageController@key_value_store')->name('languages.key_value_store');

    // website setting
    Route::group(['prefix' => 'website'], function() {
        Route::view('/header', 'backend.website_settings.header')->name('website.header');
        Route::view('/footer', 'backend.website_settings.footer')->name('website.footer');
        Route::view('/pages', 'backend.website_settings.pages.index')->name('website.pages');
        Route::view('/appearance', 'backend.website_settings.appearance')->name('website.appearance');
        Route::resource('custom-pages', 'PageController');
        Route::get('/custom-pages/edit/{id}', 'PageController@edit')->name('custom-pages.edit');
        Route::get('/custom-pages/destroy/{id}', 'PageController@destroy')->name('custom-pages.destroy');
    });

    Route::resource('roles', 'RoleController');
    Route::get('/roles/edit/{id}', 'RoleController@edit')->name('roles.edit');
    Route::get('/roles/destroy/{id}', 'RoleController@destroy')->name('roles.destroy');

    Route::resource('staffs', 'StaffController');
    Route::get('/staffs/destroy/{id}', 'StaffController@destroy')->name('staffs.destroy');

    Route::resource('flash_deals', 'FlashDealController');
    Route::get('/flash_deals/edit/{id}', 'FlashDealController@edit')->name('flash_deals.edit');
    Route::get('/flash_deals/destroy/{id}', 'FlashDealController@destroy')->name('flash_deals.destroy');
    Route::post('/flash_deals/update_status', 'FlashDealController@update_status')->name('flash_deals.update_status');
    Route::post('/flash_deals/update_featured', 'FlashDealController@update_featured')->name('flash_deals.update_featured');
    Route::post('/flash_deals/product_discount', 'FlashDealController@product_discount')->name('flash_deals.product_discount');
    Route::post('/flash_deals/product_discount_edit', 'FlashDealController@product_discount_edit')->name('flash_deals.product_discount_edit');

    //Subscribers
    Route::get('/subscribers', 'SubscriberController@index')->name('subscribers.index');
    Route::get('/subscribers/destroy/{id}', 'SubscriberController@destroy')->name('subscriber.destroy');

    // Route::get('/orders', 'OrderController@admin_orders')->name('orders.index.admin');
    // Route::get('/orders/{id}/show', 'OrderController@show')->name('orders.show');
    // Route::get('/sales/{id}/show', 'OrderController@sales_show')->name('sales.show');
    // Route::get('/sales', 'OrderController@sales')->name('sales.index');
    // All Orders
    Route::get('/all_orders', 'OrderController@all_orders')->name('all_orders.index')->middleware('no_cache');
    Route::get('/all_orders/{id}/show', 'OrderController@all_orders_show')->name('all_orders.show');
    Route::post('/update_tracking_code', 'OrderController@update_tracking_code')->name('all_orders.update_tracking_code');

    // Inhouse Orders
    Route::get('/inhouse-orders', 'OrderController@admin_orders')->name('inhouse_orders.index');
    Route::get('/inhouse-orders/{id}/show', 'OrderController@show')->name('inhouse_orders.show');

    // Seller Orders
    Route::get('/seller_orders', 'OrderController@seller_orders')->name('seller_orders.index');
    Route::get('/seller_orders/{id}/show', 'OrderController@seller_orders_show')->name('seller_orders.show');

    // Pickup point orders
    Route::get('orders_by_pickup_point', 'OrderController@pickup_point_order_index')->name('pick_up_point.order_index');
    Route::get('/orders_by_pickup_point/{id}/show', 'OrderController@pickup_point_order_sales_show')->name('pick_up_point.order_show');

    Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');

    Route::post('/pay_to_seller', 'CommissionController@pay_to_seller')->name('commissions.pay_to_seller');

    //Reports
    Route::get('/stock_report', 'ReportController@stock_report')->name('stock_report.index');
    Route::get('/in_house_sale_report', 'ReportController@in_house_sale_report')->name('in_house_sale_report.index');
    Route::get('/seller_sale_report', 'ReportController@seller_sale_report')->name('seller_sale_report.index');
    Route::get('/wish_report', 'ReportController@wish_report')->name('wish_report.index');
    Route::get('/user_search_report', 'ReportController@user_search_report')->name('user_search_report.index');
    Route::get('/wallet-history', 'ReportController@wallet_transaction_history')->name('wallet-history.index');

    //Partners Section
    Route::resource('partnership-user', 'PartnershipUserController');
    Route::get('/partnership-user/destroy/{id}', 'PartnershipUserController@destroy')->name('partnership-user.destroy');
    Route::post('/partnership-user/change-accept', 'PartnershipUserController@change_accept')->name('partnership-user.change-accept');
    Route::post('/partnership-user/accept_request', 'PartnershipUserController@accept_partner_request')->name('partnership-user.accept_partner_request');

    //Blog Section
    Route::resource('blog-category', 'BlogCategoryController');
    Route::get('/blog-category/destroy/{id}', 'BlogCategoryController@destroy')->name('blog-category.destroy');
    Route::resource('blog', 'BlogController');
    Route::get('/blog/destroy/{id}', 'BlogController@destroy')->name('blog.destroy');
    Route::post('/blog/change-status', 'BlogController@change_status')->name('blog.change-status');

    //Faqs
    Route::resource('faq', 'FaqController');
    Route::get('/faq/edit/{id}', 'FaqController@edit')->name('faq.edit');
    Route::get('/faq/destroy/{id}', 'FaqController@destroy')->name('faq.destroy');

    //Coupons
    Route::resource('coupon', 'CouponController');
    Route::post('/coupon/get_form', 'CouponController@get_coupon_form')->name('coupon.get_coupon_form');
    Route::post('/coupon/get_form_edit', 'CouponController@get_coupon_form_edit')->name('coupon.get_coupon_form_edit');
    Route::get('/coupon/destroy/{id}', 'CouponController@destroy')->name('coupon.destroy');

    //Reviews
    Route::get('/reviews', 'ReviewController@index')->name('reviews.index');
    Route::post('/reviews/published', 'ReviewController@updatePublished')->name('reviews.published');

    //Stores
    Route::resource('stores', 'StoreController');
    Route::get('/stores', 'StoreController@index')->name('stores.index');
    Route::get('/stores/edit/{id}', 'StoreController@edit')->name('stores.edit');
    Route::get('/stores/destroy/{id}', 'StoreController@destroy')->name('stores.destroy');

    //Stores Cities
    Route::resource('store_cities', 'StoreCityController');
    Route::get('/store_cities', 'StoreCityController@index')->name('store_cities.index');
    Route::get('/store_cities/edit/{id}', 'StoreCityController@edit')->name('store_cities.edit');
    Route::get('/store_cities/destroy/{id}', 'StoreCityController@destroy')->name('store_cities.destroy');

    //Support_Ticket
    Route::get('support_ticket/', 'SupportTicketController@admin_index')->name('support_ticket.admin_index');
    Route::get('support_ticket/{id}/show', 'SupportTicketController@admin_show')->name('support_ticket.admin_show');
    Route::post('support_ticket/reply', 'SupportTicketController@admin_store')->name('support_ticket.admin_store');

    //Pickup_Points
    Route::resource('pick_up_points', 'PickupPointController');
    Route::get('/pick_up_points/edit/{id}', 'PickupPointController@edit')->name('pick_up_points.edit');
    Route::get('/pick_up_points/destroy/{id}', 'PickupPointController@destroy')->name('pick_up_points.destroy');

    //conversation of seller customer
    Route::get('conversations', 'ConversationController@admin_index')->name('conversations.admin_index');
    Route::get('conversations/customer-messages', 'ConversationController@customer_chats')->name('conversations.customer_chats');
    Route::get('conversations/{id}/show', 'ConversationController@admin_show')->name('conversations.admin_show');

    Route::post('/sellers/profile_modal', 'SellerController@profile_modal')->name('sellers.profile_modal');
    Route::post('/sellers/approved', 'SellerController@updateApproved')->name('sellers.approved');

    Route::resource('attributes', 'AttributeController');
    Route::get('/attributes/edit/{id}', 'AttributeController@edit')->name('attributes.edit');
    Route::get('/attributes/destroy/{id}', 'AttributeController@destroy')->name('attributes.destroy');

    //Colors
    Route::get('/colors', 'AttributeController@colors')->name('colors');
    Route::post('/colors/store', 'AttributeController@store_color')->name('colors.store');
    Route::get('/colors/edit/{id}', 'AttributeController@edit_color')->name('colors.edit');
    Route::post('/colors/update/{id}', 'AttributeController@update_color')->name('colors.update');
    Route::get('/colors/destroy/{id}', 'AttributeController@destroy_color')->name('colors.destroy');

    Route::resource('addons', 'AddonController');
    Route::post('/addons/activation', 'AddonController@activation')->name('addons.activation');

    Route::get('/customer-bulk-upload/index', 'CustomerBulkUploadController@index')->name('customer_bulk_upload.index');
    Route::post('/bulk-user-upload', 'CustomerBulkUploadController@user_bulk_upload')->name('bulk_user_upload');
    Route::post('/bulk-customer-upload', 'CustomerBulkUploadController@customer_bulk_file')->name('bulk_customer_upload');
    Route::get('/user', 'CustomerBulkUploadController@pdf_download_user')->name('pdf.download_user');
    //Customer Package

    Route::resource('customer_packages', 'CustomerPackageController');
    Route::get('/customer_packages/edit/{id}', 'CustomerPackageController@edit')->name('customer_packages.edit');
    Route::get('/customer_packages/destroy/{id}', 'CustomerPackageController@destroy')->name('customer_packages.destroy');

    //Classified Products
    Route::get('/classified_products', 'CustomerProductController@customer_product_index')->name('classified_products');
    Route::post('/classified_products/published', 'CustomerProductController@updatePublished')->name('classified_products.published');

    //Shipping Configuration
    Route::get('/shipping_configuration', 'BusinessSettingsController@shipping_configuration')->name('shipping_configuration.index');
    Route::post('/shipping_configuration/update', 'BusinessSettingsController@shipping_configuration_update')->name('shipping_configuration.update');

    Route::post('/shipping_weight_range_cost/update', 'ShippingWeightRangeCostController@shipping_weight_range_cost_update')->name('shipping_weight_range_cost.update');
    Route::post('/shipping_weight_range_cost_acs/update', 'ShippingWeightRangeAcsCostController@shipping_weight_range_cost_acs_update')->name('shipping_weight_range_cost_acs.update');

    // Route::resource('pages', 'PageController');
    // Route::get('/pages/destroy/{id}', 'PageController@destroy')->name('pages.destroy');

    Route::resource('countries', 'CountryController');
    Route::get('/countries/edit/{id}', 'CountryController@edit')->name('countries.edit');
    Route::post('/countries/status', 'CountryController@updateStatus')->name('countries.status');
    Route::post('/countries/vat_include', 'CountryController@updateVatInclude')->name('countries.vat_include');

    Route::resource('cities', 'CityController');
    Route::get('/cities/edit/{id}', 'CityController@edit')->name('cities.edit');
    Route::get('/cities/destroy/{id}', 'CityController@destroy')->name('cities.destroy');

    Route::view('/system/update', 'backend.system.update')->name('system_update');
    Route::view('/system/server-status', 'backend.system.server_status')->name('system_server');

    // uploaded files
    Route::any('/uploaded-files/file-info', 'SkUploadController@file_info')->name('uploaded-files.info');
    Route::resource('/uploaded-files', 'SkUploadController');
    Route::get('/uploaded-files/destroy/{id}', 'SkUploadController@destroy')->name('uploaded-files.destroy');
});
