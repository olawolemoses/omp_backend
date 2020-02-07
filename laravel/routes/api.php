<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




Route::group(['prefix' => 'v1'], function(){

    Route::post('/login', 'Auth\UserController@authenticate');
    Route::post('/registration', 'Auth\UserController@registration');
    // Route::post('/password/forgot', 'Auth\ForgotPasswordCtrl@requestEmail');
    // // Route::post('/password/reset', 'Auth\ForgotPasswordCtrl@reset');
    // Route::post('/password/change', 'Auth\ChangePasswordCtrl');

    // CATEGORY SECTION
    Route::get('/category/{slug}','Front\CatalogCtrl@category');
    Route::get('/category/{slug1}/{slug2}','Front\CatalogCtrl@subcategory');
    Route::get('/category/{slug1}/{slug2}/{slug3}','Front\CatalogCtrl@childcategory');
    Route::get('/categories','Front\CatalogCtrl@categories');
    // CATEGORY SECTION ENDS

    // CART SECTION
    Route::get('/carts','Front\CartCtrl@cart');
    Route::get('/addcart/{id}','Front\CartCtrl@addcart');
    Route::get('/addtocart/{id}','Front\CartCtrl@addtocart');
    Route::get('/addnumcart','Front\CartCtrl@addnumcart');
    Route::get('/addbyone','Front\CartCtrl@addbyone');
    Route::get('/reducebyone','Front\CartCtrl@reducebyone');
    Route::get('/upcolor','Front\CartCtrl@upcolor');
    Route::get('/removecart/{id}','Front\CartCtrl@removecart');
    Route::get('/carts/coupon','Front\CartCtrl@coupon');
    Route::get('/carts/coupon/check','Front\CartCtrl@couponcheck');
    // CART SECTION ENDS

    // CHECKOUT SECTION
    Route::get('/checkout/','Front\CheckoutCtrl@checkout');
    Route::get('/checkout/payment/{slug1}/{slug2}','Front\CheckoutCtrl@loadpayment');
    Route::get('/order/track/{id}','Front\FrontendCtrl@trackload');
    Route::get('/checkout/payment/return', 'Front\PaymentCtrl@payreturn');
    Route::get('/checkout/payment/cancle', 'Front\PaymentCtrl@paycancle');
    Route::get('/checkout/payment/notify', 'Front\PaymentCtrl@notify');

    Route::post('/paystack/submit', 'Front\PaystackCtrl@store');
    Route::post('/gateway', 'Front\CheckoutCtrl@gateway');
    // CHECKOUT SECTION ENDS

    Route::get('/', 'Front\FrontendCtrl@index');
    Route::get('/product/{slug}','Front\CatalogCtrl@product');
    Route::get('/product/{id}/{category}','Front\CatalogCtrl@relatedproduct');
    Route::get('/extras', 'Front\FrontendCtrl@extraIndex');
    Route::get('/currency/{id}', 'Front\FrontendCtrl@currency');
    Route::get('/language/{id}', 'Front\FrontendCtrl@language');

    // BLOG SECTION
    Route::get('/blog','Front\FrontendCtrl@blog');
    Route::get('/blog/{id}','Front\FrontendCtrl@blogshow');
    Route::get('/blog/category/{slug}','Front\FrontendCtrl@blogcategory');
    Route::get('/blog/tag/{slug}','Front\FrontendCtrl@blogtags');
    Route::get('/blog-search','Front\FrontendCtrl@blogsearch');
    Route::get('/blog/archive/{slug}','Front\FrontendCtrl@blogarchive');
    // BLOG SECTION ENDS

    // FAQ SECTION
    Route::get('/faq','Front\FrontendCtrl@faq');
    // FAQ SECTION ENDS

    // CONTACT SECTION
    Route::get('/contact','Front\FrontendCtrl@contact');
    Route::post('/contact','Front\FrontendCtrl@contactemail');
    Route::get('/contact/refresh_code','Front\FrontendCtrl@refresh_code');
    // CONTACT SECTION  ENDS

    // PRODCT AUTO SEARCH SECTION
    Route::get('/autosearch/product/{slug}','Front\FrontendCtrl@autosearch');
    // PRODCT AUTO SEARCH SECTION ENDS

    // TAG SECTION
    Route::get('/search/','Front\CatalogCtrl@search');
    // TAG SECTION ENDS

    // PAGE SECTION
    Route::get('/{slug}','Front\FrontendCtrl@page');
    // PAGE SECTION ENDS

});

Route::group(['middleware' => ['jwt.verify'],  'prefix' => 'v1/user'], function ()  {

    // User Profile
    Route::get('/profile', 'User\UserCtrl@index');
    Route::post('/profile', 'User\UserCtrl@profileupdate');
    // User Profile Ends

    // User Wishlist
    Route::get('/wishlists','User\WishlistCtrl@wishlists');
    Route::get('/wishlist/add/{id}','User\WishlistCtrl@addwish');
    Route::get('/wishlist/remove/{id}','User\WishlistCtrl@removewish');
    // User Wishlist Ends

    // User Orders
    Route::get('/orders', 'User\OrderCtrl@orders');
    Route::get('/order/tracking', 'User\OrderCtrl@ordertrack');
    Route::get('/order/trackings/{id}', 'User\OrderCtrl@trackload');
    Route::get('/order/{id}', 'User\OrderCtrl@order');
    Route::get('/download/order/{slug}/{id}', 'User\OrderCtrl@orderdownload');
    Route::get('print/order/print/{id}', 'User\OrderCtrl@orderprint');
    Route::get('/json/trans','User\OrderCtrl@trans');
    // User Orders Ends

    // User Logout
    Route::get('/logout', 'User\UserController@logout');
    // User Logout Ends

});

//..............Message Section.........
Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'v1/message'], function ()  {

Route::get('/{id}', 'Vendor\MessagesCtrl@messageshow');
Route::get('/recieved/{recieved_user}', 'Vendor\MessagesCtrl@recievedmsg');
Route::get('/sent/{sent_user}', 'Vendor\MessagesCtrl@sentmessage');
Route::delete('/delete/{id}', 'Vendor\MessagesCtrl@messagedelete');
Route::post('/', 'Vendor\MessagesCtrl@postmessage');


});

//------------ VENDOR SECTION ------------

//Login and Signup
Route::post('/v1/vendor/signup', 'Vendor\SignupCtrl@create');
Route::post('/v1/vendor/login', 'Vendor\SignupCtrl@authenticate');

Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'v1/vendor'], function ()  {

    //EditProfile
    Route::put('/update/{id}', 'Vendor\SignupCtrl@updated');
    Route::put('/password/{id}', 'Vendor\SignupCtrl@password');
    Route::post('/check/{id}', 'Vendor\SignupCtrl@checkpassword');


    // Order Notification
    Route::get('/order/notf/show/{id}', 'Vendor\NotificationController@order_notf_show');
    Route::get('/order/notf/count/{id}','Vendor\NotificationController@order_notf_count');
    Route::get('/order/notf/clear/{id}','Vendor\NotificationController@order_notf_clear');
    // Order Notification Ends

    // User Profile
    Route::get('/profile', 'Vendor\UserCtrl@index');
    Route::post('/profile', 'Vendor\UserCtrl@profileupdate');
    // User Profile Ends

    // Vendor Shipping Cost
    Route::get('/shipping-cost', 'Vendor\VendorCtrl@ship');

    //vendor details
    Route::get('all', 'Vendor\SignupCtrl@all');
    Route::post('all/{id}', 'Vendor\SignupCtrl@show');
    Route::post('delete/{id]', 'Vendor\SignupCtrl@delete');

    // Vendor Shipping Cost
    Route::get('/banner', 'Vendor\VendorCtrl@banner');

    // Vendor Social
    Route::get('/social', 'Vendor\VendorCtrl@social');
    Route::post('/social/update', 'Vendor\VendorCtrl@socialupdate');

    Route::get('/withdraw', 'Vendor\WithdrawCtrl@index');
    Route::post('/withdraw/create', 'Vendor\WithdrawCtrl@store');

    Route::get('/service', 'Vendor\ServiceCtrl@index');
    Route::post('/service/create', 'Vendor\ServiceCtrl@create');
    Route::put('/service/edit/{id}', 'Vendor\ServiceCtrl@update');
    Route::delete('/service/delete/{id}', 'Vendor\ServiceCtrl@destroy');


    //get user details
    Route::get('/{id}', 'Vendor\SignupCtrl@show');

    //Product
    Route::post('/product/create','Vendor\ProductCtrl@create');
    Route::put('/product/update/{id}','Vendor\ProductCtrl@edit');
    Route::get('/prod/{user_id}','Vendor\ProductCtrl@prodid');
    Route::get('/product/{id}','Vendor\ProductCtrl@view');
    Route::delete('/product/delete/{id}','Vendor\ProductCtrl@delete');


    //Category
    Route::get('category/main','Admin\CategoryCtrl@show');
    Route::get('category/sub','Admin\SubcategoryCtrl@show');
    Route::get('category/child','Admin\SubcategoryCtrl@show');

    //Order
    Route::get('/orders/{user_id}','Vendor\OrderCtrl@index');


});

//admin

//Admin Register
Route::post('v1/admin/create','Admin\RegisterCtrl@register');
Route::post('v1/admin/signup','Admin\LoginCtrl@authenticate');

Route::group(['middleware'=>'jwt.verify','prefix' => 'v1/admin'], function () {
    //Roles
    Route::post('role/create','Admin\RoleCtrl@create');
    Route::get('role','Admin\RoleCtrl@show');
    Route::get('role/{id}','Admin\RoleCtrl@view');
    Route::put('role/edit/{id}','Admin\RoleCtrl@edit');
    Route::delete('role/delete/{id}', 'Admin\RoleCtrl@delete');



    //Category
    Route::post('category/create','Admin\CategoryCtrl@create');
    Route::put('category/update/{id}','Admin\CategoryCtrl@edit');
    Route::get('category','Admin\CategoryCtrl@show');
    Route::get('category/{id}','Admin\CategoryCtrl@view');
    Route::delete('category/delete/{id}','Admin\CategoryCtrl@delete');

    //SubCategory
    Route::post('subcategory/create','Admin\SubcategoryCtrl@create');
    Route::put('subcategory/update/{id}','Admin\SubcategoryCtrl@edit');
    Route::get('subcategory','Admin\SubcategoryCtrl@show');
    Route::get('subcategory/{id}','Admin\SubcategoryCtrl@view');
    Route::get('/subcats/{category_name}','Admin\SubcategoryCtrl@get');
    Route::delete('subcategory/delete/{id}','Admin\SubcategoryCtrl@delete');


    //ChildCategory
    Route::post('childcategory/create','Admin\ChildcategoryCtrl@create');
    Route::put('childcategory/update/{id}','Admin\ChildcategoryCtrl@edit');
    Route::get('childcategory','Admin\ChildcategoryCtrl@show');
    Route::get('childcategory/{id}','Admin\ChildcategoryCtrl@view');
    Route::get('/childcats/{subcategory_name}','Admin\ChildcategoryCtrl@get');
    Route::delete('childcategory/delete/{id}','Admin\ChildcategoryCtrl@delete');

    //Order
    Route::get('order/pending','Admin\OrderCtrls@index');
    Route::get('order/process','Admin\OrderCtrls@process');
    Route::get('order/complete','Admin\OrderCtrls@complete');

    Route::get('order/declined','Admin\OrderCtrls@decline');
    Route::get('order/all','Admin\OrderCtrls@all');
    Route::get('order/recent','Admin\OrderCtrls@recent');
    Route::get('order/all/{id}','Admin\OrderCtrls@view');
    Route::delete('order/delete/{id}','Admin\OrderCtrls@delete');




    //Product
    Route::post('product/create','Admin\ProductCtrl@create');
    Route::put('product/update/{id}','Admin\ProductCtrl@edit');
    Route::get('product','Admin\ProductCtrl@show');
    Route::get('recent','Admin\ProductCtrl@recent');
    Route::get('product/{id}','Admin\ProductCtrl@view');
    Route::delete('product/delete/{id}','Admin\ProductCtrl@delete');

    //------------ ADMIN ORDER SECTION ------------

    Route::get('/orders', 'Vendor\OrderCtrl@index');
    Route::get('/order/{id}/show', 'Vendor\OrderCtrl@show');
    Route::get('/order/{id}/invoice', 'Vendor\OrderCtrl@invoice');
    Route::get('/order/{id}/print', 'Vendor\OrderCtrl@printpage');
    Route::get('/order/{id1}/status/{status}', 'Vendor\OrderCtrl@status');
    Route::post('/order/email/', 'Vendor\OrderCtrl@emailsub');
    Route::post('/order/{slug}/license', 'Vendor\OrderCtrl@license');
    //   ------------ ADMIN ORDER SECTION ENDS------------


    //see all users in the system
    Route::get('/users/all', 'Admin\UserCtrl@all');
    Route::get('/users/recent', 'Admin\UserCtrl@recent');
    Route::get('/users/{id}', 'Admin\UserCtrl@view');
    Route::delete('/users/delete/{id}', 'Admin\UserCtrl@delete');

    //get vendor
    Route::get('/vendor/all', 'Vendor\SignupCtrl@all');
    Route::delete('/vendor/{id}', 'Vendor\SignupCtrl@delete');
    Route::get('/vendor/{id}', 'Vendor\SignupCtrl@show');

    //see all subscription in the system
    Route::get('/sub/all', 'Admin\SubscriptionCtrl@all');
    Route::get('/sub/{id}', 'Admin\SubscriptionCtrl@view');
    Route::delete('/sub/delete/{id}', 'Admin\SubscriptionCtrl@delete');
});
