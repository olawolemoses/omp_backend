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
    Route::get('/search/{name}', 'Front\SearchCtrl@search');
    // Route::post('/password/forgot', 'Auth\ForgotPasswordCtrl@requestEmail');
    // Route::post('/password/reset', 'Auth\ForgotPasswordCtrl@reset');
    // Route::post('/password/change', 'Auth\ChangePasswordCtrl');

    // LOCATION SECTION
    Route::get('/countries','Front\VendorCtrl@countries');
    Route::get('/states/{id}','Front\VendorCtrl@states');
    // LOCATION SECTION ENDS

    // CATEGORY SECTION
    Route::get('/category/{slug}','Front\CatalogCtrl@category');
    Route::get('/category/{slug1}/{slug2}','Front\CatalogCtrl@subcategory');
    Route::get('/category/{slug1}/{slug2}/{slug3}','Front\CatalogCtrl@childcategory');
    Route::get('/categories','Front\CatalogCtrl@categories');
    Route::get('/allcategories','Front\CatalogCtrl@getcategories');
    // CATEGORY SECTION ENDS

    // CART SECTION
    Route::get('/carts','Front\CartCtrl@cart');
    Route::get('/addcart/{id}','Front\CartCtrl@addcart');
    Route::get('/addtocart/{id}','Front\CartCtrl@addtocart');
    Route::post('/addnumcart','Front\CartCtrl@addnumcart');
    Route::post('/addbyone','Front\CartCtrl@addbyone');
    Route::post('/reducebyone','Front\CartCtrl@reducebyone');
    Route::get('/upcolor','Front\CartCtrl@upcolor');
    Route::get('/removecart/{id}','Front\CartCtrl@removecart');
    Route::get('/carts/coupon','Front\CartCtrl@coupon');
    Route::get('/carts/coupon/check','Front\CartCtrl@couponcheck');
    // CART SECTION ENDS

    // CHECKOUT SECTION
    Route::get('/checkout','Front\CheckoutCtrl@checkout');
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
    Route::get('/categorysearch/','Front\CatalogCtrl@categorysearch');
    // TAG SECTION ENDS

    // PAGE SECTION
    Route::get('/{slug}','Front\FrontendCtrl@page');
    // PAGE SECTION ENDS

});

Route::group(['middleware' => ['jwt.verify', 'sessions'],  'prefix' => 'v1/user'], function ()  {

    // User Profile
    Route::get('/profile', 'User\UserCtrl@index');
    Route::post('/profile', 'User\UserCtrl@profileupdate');
    // User Profile Ends

    // User Reset 
    Route::post('/reset', 'User\UserController@reset');
    Route::post('/password/change', 'Auth\ChangePasswordCtrl');
    // User Reset End

    // User Wishlist
    Route::get('/wishlists','User\WishlistCtrl@wishlists');
    Route::get('/wishlist/add/{id}','User\WishlistCtrl@addwish');
    Route::get('/wishlist/remove/{id}','User\WishlistCtrl@removewish');
   


    // User Wishlist Ends

    Route::post('/paystack/submit', 'Front\PaystackController@store');

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
Route::group(['middleware' => ['jwt.verify', 'sessions'], 'prefix' => 'v1/message'], function ()  {

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

//forgot password
Route::post('/v1/vendor/email','Vendor\SignupCtrl@checkemail' );

Route::post('v1/vendor/forgot-password', 'Vendor\ForgotPwdCtrl@create');
Route::post('v1/vendor/reset-password', 'Vendor\ForgotPwdCtrl@reset');
// handle reset password form process
Route::post('v1/admin/reset/password', 'Vendor\SignupCtrl@callResetPassword');


Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'v1/vendor'], function ()  {

    //EditProfile
    Route::post('/update/{id}', 'Vendor\SignupCtrl@updated');
    Route::put('/password/{id}', 'Vendor\SignupCtrl@password');
    Route::post('/check/{id}', 'Vendor\SignupCtrl@checkpassword');

    //vendor wishlist
    Route::get('/wishlists/{id}','User\WishlistCtrl@show');
    // Route::get('/wishlist/vend/{user_id}','User\WishlistCtrl@vendorWish');
    Route::get('/wishlist/vends','User\WishlistCtrl@vendoWish');


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
    Route::get('/deactivated/{id}','Vendor\ProductCtrl@deactivated');
    Route::delete('/product/delete/{id}','Vendor\ProductCtrl@delete');
    Route::get('/viewdeactivated/{user_id}','Vendor\ProductCtrl@viewdeactivated');
    Route::get('/activated/{id}','Vendor\ProductCtrl@activated');


    //Category
    Route::get('category/main','Admin\CategoryCtrl@show');
    Route::get('category/sub','Admin\SubcategoryCtrl@show');
    Route::get('category/child','Admin\SubcategoryCtrl@show');

    //Order
    Route::get('/orders/{user_id}','Vendor\OrderCtrl@index');
    Route::get('/pendingorder/{user_id}','Vendor\OrderCtrl@pending');
    Route::get('/completedorder/{user_id}','Vendor\OrderCtrl@complete');
    Route::get('order/all/{id}','Admin\OrderCtrls@view');
});

//admin

//Admin Register
Route::post('v1/admin/create','Admin\RegisterCtrl@register');
Route::post('v1/admin/signup','Admin\LoginCtrl@authenticate');

Route::post('v1/admin/forgot-password', 'Admin\ForgotPasswdCtrl@create');
Route::post('v1/admin/reset-password', 'Admin\ForgotPasswdCtrl@reset');

// handle reset password form process
Route::post('v1/admin/reset/password', 'Admin\LoginCtrl@callResetPassword');


    
Route::group(['middleware'=>'jwt.verify','prefix' => 'v1/admin'], function () {
    //Roles
    Route::post('role/create','Admin\RoleCtrl@create');
    Route::get('role','Admin\RoleCtrl@show');
    Route::get('usernames','Admin\RoleCtrl@showUsernames');
    Route::get('role/{id}','Admin\RoleCtrl@view');
    Route::put('role/edit/{id}','Admin\RoleCtrl@edit');
    Route::delete('role/delete/{id}', 'Admin\RoleCtrl@delete');


    //get user details
    Route::post('/update/{id}', function (Request $request,$id) {
        
        $vendor = App\Models\Admin::findOrFail($id);

        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $cloudder = Cloudder::upload($request->file('photo')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->photo = $file_url;
            
        }
        // else {
        //        # code...
        //        return response()->json([
        //          'status' => 'Failed',
        //          'message' => 'Select image first.'
        //        ], 201);
        //      }

        $vendor->phone = $request->phone;
        $vendor->name = $request->name;
        // $vendor->password = Hash::make($request->password);
        $vendor->email = $request->email;


        $vendor->save();

        if($vendor){
            return response() ->json([
                'status' =>'success',
                'message' =>'Profile updated successfully'
            ], 200);  
        }

        return response() ->json([
            'status' =>false,
            'message' =>'Failed to update profile'
        ], 200);

    });
    
    //get user details
    Route::get('/details/{id}', function (Request $request, $id) {
        $vendor = App\Models\Admin::where('name', $id)->first();
        if(!$vendor){
            return response() ->json([
                'status' =>false,
                'data' => 'Admin could not be found'
            ]);
        }
    
        return response() ->json([
            'status' =>true,
            'data' => [
            'vendor' =>$vendor
            ],
        ], 200);
    });    
    
    
    // check user password
    Route::post('/check/{id}', function (Request $request, $id){
        $vendor = App\Models\Admin::where('name', $id)->first();
        if(!$vendor) {
            return response() ->json([
                'status' =>false,
                'data' => 'vendor could not be found'
            ]);
        } else {
            $hasher = app()->make('hash');
            $gs = $hasher->check($request->input('password'), $vendor->password);
            if($gs) {
                    return response() ->json([
                        'status' =>true,
                        'message' =>'Password exists',
                    ], 200);
            } else {
                return response()->json([
                    'status' =>false,
                    'message' =>'Incorrect password',
                ],200);
            }
        }
    });
    
    
    // fix password 
    Route::put('/password/{id}', function (Request $request, $id){
       
        $vendor = App\Models\Admin::findOrFail($id);
       
        $vendor->password = Hash::make($request->password);

        $vendor->update();

        if($vendor){
            return response() ->json([
                'status' =>'success',
                'message' =>'Password updated successfully'
            ], 200);  
        }

        return response() ->json([
            'status' =>false,
            'message' =>'Failed to update password'
        ], 200);

    });
    
    
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
    Route::post('product/new','Admin\ProductCtrl@save');
    Route::get('product/deactivated','Admin\ProductCtrl@showDeactivated');
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
    Route::put('/users/status/{id}', 'Admin\UserCtrl@editStatus');
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
