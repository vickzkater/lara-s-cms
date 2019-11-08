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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'Admin\AuthController@login');

// Route::get('/', 'Web\HomeController@index')->name('home');

// Route::get('/login', 'Web\AuthController@login')->name('login');
// Route::post('/do_login', 'Web\AuthController@do_login')->name('do_login');
// Route::get('/logout', 'Web\AuthController@logout')->name('logout');

// PRODUCTS
// Route::get('/products', 'Web\ProductController@list')->name('product_list');
// Route::get('/product/{id}', 'Web\ProductController@detail')->name('product_detail');

Route::group(['prefix' => env('ADMIN_DIR')], function() {
    Route::get('/login', 'Admin\AuthController@login')->name('admin_login');
    Route::post('/do_login', 'Admin\AuthController@do_login')->name('admin_do_login');
    Route::get('/logout', 'Admin\AuthController@logout')->name('admin_logout');

    Route::group(['middleware' => 'check.admin'], function () {
        Route::get('/', 'Admin\HomeController@index')->name('admin_home');

        Route::get('profile', 'Admin\UserController@profile')->name('admin_profile');
        Route::post('edit_profile', 'Admin\UserController@profile_edit')->name('admin_profile_edit');

        Route::get('change_language/{alias}', 'Admin\UserController@change_language')->name('admin_change_language');

        // USER MANAGER
        Route::group(['prefix' => 'user'], function() {
            Route::get('/', 'Admin\UserController@list')->name('admin_user_manager');
            Route::get('/create', 'Admin\UserController@create')->name('admin_user_create');
            Route::post('/do_create', 'Admin\UserController@do_create')->name('admin_user_do_create');
            Route::get('/edit/{id}', 'Admin\UserController@edit')->name('admin_user_edit');
            Route::post('/do_edit/{id}', 'Admin\UserController@do_edit')->name('admin_user_do_edit');
            Route::get('/delete/{id}', 'Admin\UserController@delete')->name('admin_user_delete');
            Route::get('/deleted', 'Admin\UserController@list_deleted')->name('admin_user_manager_deleted');
            Route::get('/restore/{id}', 'Admin\UserController@restore')->name('admin_user_restore');
        });

        // USERGROUP MANAGER
        Route::group(['prefix' => 'usergroup'], function() {
            Route::get('/', 'Admin\UsergroupController@list')->name('admin_group_manager');
            Route::get('/create', 'Admin\UsergroupController@create')->name('admin_group_create');
            Route::post('/do_create', 'Admin\UsergroupController@do_create')->name('admin_group_do_create');
            Route::get('/edit/{id}', 'Admin\UsergroupController@edit')->name('admin_group_edit');
            Route::post('/do_edit/{id}', 'Admin\UsergroupController@do_edit')->name('admin_group_do_edit');
            Route::get('/delete/{id}', 'Admin\UsergroupController@delete')->name('admin_group_delete');
            Route::get('/deleted', 'Admin\UsergroupController@list_deleted')->name('admin_group_manager_deleted');
            Route::get('/restore/{id}', 'Admin\UsergroupController@restore')->name('admin_group_restore');
        });

        // BRAND
        Route::group(['prefix' => 'brand'], function() {
            Route::get('/', 'Admin\BrandController@list')->name('admin_brand_list');
            Route::get('/create', 'Admin\BrandController@create')->name('admin_brand_create');
            Route::post('/do_create', 'Admin\BrandController@do_create')->name('admin_brand_do_create');
            Route::get('/edit/{id}', 'Admin\BrandController@edit')->name('admin_brand_edit');
            Route::post('/do_edit/{id}', 'Admin\BrandController@do_edit')->name('admin_brand_do_edit');
            Route::get('/delete/{id}', 'Admin\BrandController@delete')->name('admin_brand_delete');
            Route::get('/deleted', 'Admin\BrandController@list_deleted')->name('admin_brand_deleted');
            Route::get('/restore/{id}', 'Admin\BrandController@restore')->name('admin_brand_restore');
        });

        // PRODUCT
        Route::group(['prefix' => 'product'], function() {
            Route::get('/', 'Admin\ProductController@list')->name('admin_product_list');
            Route::get('/create', 'Admin\ProductController@create')->name('admin_product_create');
            Route::post('/do_create', 'Admin\ProductController@do_create')->name('admin_product_do_create');
            Route::get('/edit/{id}', 'Admin\ProductController@edit')->name('admin_product_edit');
            Route::post('/do_edit/{id}', 'Admin\ProductController@do_edit')->name('admin_product_do_edit');
            Route::get('/delete/{id}', 'Admin\ProductController@delete')->name('admin_product_delete');
            Route::get('/deleted', 'Admin\ProductController@list_deleted')->name('admin_product_deleted');
            Route::get('/restore/{id}', 'Admin\ProductController@restore')->name('admin_product_restore');
            Route::post('/submit-qc/{id}', 'Admin\ProductController@submit_qc')->name('admin_product_submit_qc');
            Route::post('/upload-photos/{id}', 'Admin\ProductController@upload_photos')->name('admin_product_upload_photos');
            Route::post('/publish/{id}', 'Admin\ProductController@publish')->name('admin_product_publish');
            Route::post('/booking/{id}', 'Admin\ProductController@set_booked')->name('admin_product_booking');
        });

        // INCOMING UNIT
        Route::group(['prefix' => 'incoming'], function() {
            Route::get('/', 'Admin\IncomingController@list')->name('admin_incoming_list');
            Route::get('/edit/{id}', 'Admin\ProductController@edit')->name('admin_incoming_edit');
            Route::post('/do_edit/{id}', 'Admin\ProductController@do_edit')->name('admin_incoming_do_edit');
        });

        // BANNER
        Route::group(['prefix' => 'banner'], function() {
            Route::get('/', 'Admin\BannerController@list')->name('admin_banner_list');
            Route::get('/create', 'Admin\BannerController@create')->name('admin_banner_create');
            Route::post('/do_create', 'Admin\BannerController@do_create')->name('admin_banner_do_create');
            Route::get('/edit/{id}', 'Admin\BannerController@edit')->name('admin_banner_edit');
            Route::post('/do_edit/{id}', 'Admin\BannerController@do_edit')->name('admin_banner_do_edit');
            Route::get('/delete/{id}', 'Admin\BannerController@delete')->name('admin_banner_delete');
            Route::get('/deleted', 'Admin\BannerController@list_deleted')->name('admin_banner_deleted');
            Route::get('/restore/{id}', 'Admin\BannerController@restore')->name('admin_banner_restore');
        });

        // DIVISION
        Route::group(['prefix' => 'division'], function() {
            Route::get('/', 'Admin\DivisionController@list')->name('admin_division_list');
            Route::get('/create', 'Admin\DivisionController@create')->name('admin_division_create');
            Route::post('/do_create', 'Admin\DivisionController@do_create')->name('admin_division_do_create');
            Route::get('/edit/{id}', 'Admin\DivisionController@edit')->name('admin_division_edit');
            Route::post('/do_edit/{id}', 'Admin\DivisionController@do_edit')->name('admin_division_do_edit');
            Route::get('/delete/{id}', 'Admin\DivisionController@delete')->name('admin_division_delete');
            Route::get('/deleted', 'Admin\DivisionController@list_deleted')->name('admin_division_deleted');
            Route::get('/restore/{id}', 'Admin\DivisionController@restore')->name('admin_division_restore');
        });

        // BRANCH
        Route::group(['prefix' => 'branch'], function() {
            Route::get('/', 'Admin\BranchController@list')->name('admin_branch_list');
            Route::get('/create', 'Admin\BranchController@create')->name('admin_branch_create');
            Route::post('/do_create', 'Admin\BranchController@do_create')->name('admin_branch_do_create');
            Route::get('/edit/{id}', 'Admin\BranchController@edit')->name('admin_branch_edit');
            Route::post('/do_edit/{id}', 'Admin\BranchController@do_edit')->name('admin_branch_do_edit');
            Route::get('/delete/{id}', 'Admin\BranchController@delete')->name('admin_branch_delete');
            Route::get('/deleted', 'Admin\BranchController@list_deleted')->name('admin_branch_deleted');
            Route::get('/restore/{id}', 'Admin\BranchController@restore')->name('admin_branch_restore');
        });

        // CUSTOMER
        Route::group(['prefix' => 'customer'], function() {
            Route::get('/', 'Admin\CustomerController@list')->name('admin_customer_list');
            Route::get('/create', 'Admin\CustomerController@create')->name('admin_customer_create');
            Route::post('/do_create', 'Admin\CustomerController@do_create')->name('admin_customer_do_create');
            Route::get('/edit/{id}', 'Admin\CustomerController@edit')->name('admin_customer_edit');
            Route::post('/do_edit/{id}', 'Admin\CustomerController@do_edit')->name('admin_customer_do_edit');
            Route::get('/delete/{id}', 'Admin\CustomerController@delete')->name('admin_customer_delete');
            Route::get('/deleted', 'Admin\CustomerController@list_deleted')->name('admin_customer_deleted');
            Route::get('/restore/{id}', 'Admin\CustomerController@restore')->name('admin_customer_restore');
        });

        // RULE
        Route::group(['prefix' => 'rule'], function() {
            Route::get('/', 'Admin\RuleController@list')->name('admin_rule_list');
            Route::get('/create', 'Admin\RuleController@create')->name('admin_rule_create');
            Route::post('/do_create', 'Admin\RuleController@do_create')->name('admin_rule_do_create');
            Route::get('/edit/{id}', 'Admin\RuleController@edit')->name('admin_rule_edit');
            Route::post('/do_edit/{id}', 'Admin\RuleController@do_edit')->name('admin_rule_do_edit');
            Route::get('/delete/{id}', 'Admin\RuleController@delete')->name('admin_rule_delete');
            Route::get('/deleted', 'Admin\RuleController@list_deleted')->name('admin_rule_deleted');
            Route::get('/restore/{id}', 'Admin\RuleController@restore')->name('admin_rule_restore');
        });

        // LANGUAGE
        Route::group(['prefix' => 'language'], function() {
            Route::get('/', 'Admin\LanguageController@list')->name('admin_language_list');
            Route::get('/create', 'Admin\LanguageController@create')->name('admin_language_create');
            Route::post('/do_create', 'Admin\LanguageController@do_create')->name('admin_language_do_create');
            Route::get('/edit/{id}', 'Admin\LanguageController@edit')->name('admin_language_edit');
            Route::post('/do_edit/{id}', 'Admin\LanguageController@do_edit')->name('admin_language_do_edit');
        });

        // LANGUAGE MASTER
        Route::group(['prefix' => 'language_master'], function() {
            Route::get('/', 'Admin\LangMasterController@list')->name('admin_langmaster_list');
            Route::get('/create', 'Admin\LangMasterController@create')->name('admin_langmaster_create');
            Route::post('/do_create', 'Admin\LangMasterController@do_create')->name('admin_langmaster_do_create');
            Route::get('/edit/{id}', 'Admin\LangMasterController@edit')->name('admin_langmaster_edit');
            Route::post('/do_edit/{id}', 'Admin\LangMasterController@do_edit')->name('admin_langmaster_do_edit');
        });
    });
    
});
