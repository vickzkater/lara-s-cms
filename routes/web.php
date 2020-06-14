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

// WEBSITE
Route::group(['namespace' => 'Web'], function () {
    // HOME
    Route::get('/', 'SiteController@home')->name('web.home');

    // ABOUT
    Route::get('/about', 'SiteController@about')->name('web.about');

    // PRODUCTS
    Route::get('/products', 'SiteController@products')->name('web.products');

    // STORE
    Route::get('/store', 'SiteController@store')->name('web.store');
});

// ADMIN
Route::group([
    'prefix' => env('ADMIN_DIR'),
    'namespace' => 'Admin'
], function () {
    Route::get('/login', 'system\AuthController@login')->name('admin.login');
    Route::post('/do-login', 'system\AuthController@do_login')->name('admin.do_login');
    Route::get('/logout', 'system\AuthController@logout')->name('admin.logout');

    // NEED AUTH
    Route::group(['middleware' => 'check.admin'], function () {

        // LARA-S-CMS - SKELETON (PLEASE DO NOT MODIFY, UNLESS YOU UNDERSTAND WHAT YOU ARE DOING)
        Route::group(['namespace' => 'system'], function () {
            // CHANGE LANGUAGE
            Route::get('change-language/{alias}', 'UserController@change_language')->name('admin.change_language');

            // LOGS
            Route::get('system-logs', 'LogController@list')->name('admin.logs');

            // PROFILE
            Route::get('profile', 'UserController@profile')->name('admin.profile');
            Route::post('edit-profile', 'UserController@profile_edit')->name('admin.profile.edit');

            // SYSTEM
            Route::group(['prefix' => 'system'], function () {
                // DIVISION
                Route::group(['prefix' => 'division'], function () {
                    Route::get('/', 'DivisionController@list')->name('admin.division.list');
                    Route::get('/create', 'DivisionController@create')->name('admin.division.create');
                    Route::post('/do-create', 'DivisionController@do_create')->name('admin.division.do_create');
                    Route::get('/edit/{id}', 'DivisionController@edit')->name('admin.division.edit');
                    Route::post('/do-edit/{id}', 'DivisionController@do_edit')->name('admin.division.do_edit');
                    Route::post('/delete', 'DivisionController@delete')->name('admin.division.delete');
                    Route::get('/deleted', 'DivisionController@list_deleted')->name('admin.division.deleted');
                    Route::post('/restore', 'DivisionController@restore')->name('admin.division.restore');
                });

                // BRANCH
                Route::group(['prefix' => 'branch'], function () {
                    Route::get('/', 'BranchController@list')->name('admin.branch.list');
                    Route::get('/get-data', 'BranchController@get_data')->name('admin.branch.get_data');
                    Route::get('/create', 'BranchController@create')->name('admin.branch.create');
                    Route::post('/do-create', 'BranchController@do_create')->name('admin.branch.do_create');
                    Route::get('/edit/{id}', 'BranchController@edit')->name('admin.branch.edit');
                    Route::post('/do-edit/{id}', 'BranchController@do_edit')->name('admin.branch.do_edit');
                    Route::post('/delete', 'BranchController@delete')->name('admin.branch.delete');
                    Route::get('/deleted', 'BranchController@list_deleted')->name('admin.branch.deleted');
                    Route::get('/get-data-deleted', 'BranchController@get_data_deleted')->name('admin.branch.get_data_deleted');
                    Route::post('/restore', 'BranchController@restore')->name('admin.branch.restore');
                });

                // RULE
                Route::group(['prefix' => 'rule'], function () {
                    Route::get('/', 'RuleController@list')->name('admin.rule.list');
                    Route::get('/get-data', 'RuleController@get_data')->name('admin.rule.get_data');
                    Route::get('/create', 'RuleController@create')->name('admin.rule.create');
                    Route::post('/do-create', 'RuleController@do_create')->name('admin.rule.do_create');
                    Route::get('/edit/{id}', 'RuleController@edit')->name('admin.rule.edit');
                    Route::post('/do-edit/{id}', 'RuleController@do_edit')->name('admin.rule.do_edit');
                    Route::post('/delete', 'RuleController@delete')->name('admin.rule.delete');
                    Route::get('/deleted', 'RuleController@list_deleted')->name('admin.rule.deleted');
                    Route::get('/get-data-deleted', 'RuleController@get_data_deleted')->name('admin.rule.get_data_deleted');
                    Route::post('/restore', 'RuleController@restore')->name('admin.rule.restore');
                });

                // USERGROUP
                Route::group(['prefix' => 'usergroup'], function () {
                    Route::get('/', 'UsergroupController@list')->name('admin.usergroup.list');
                    Route::get('/get-data', 'UsergroupController@get_data')->name('admin.usergroup.get_data');
                    Route::get('/create', 'UsergroupController@create')->name('admin.usergroup.create');
                    Route::post('/do-create', 'UsergroupController@do_create')->name('admin.usergroup.do_create');
                    Route::get('/edit/{id}', 'UsergroupController@edit')->name('admin.usergroup.edit');
                    Route::post('/do-edit/{id}', 'UsergroupController@do_edit')->name('admin.usergroup.do_edit');
                    Route::post('/delete', 'UsergroupController@delete')->name('admin.usergroup.delete');
                    Route::get('/deleted', 'UsergroupController@list_deleted')->name('admin.usergroup.deleted');
                    Route::get('/get-data-deleted', 'UsergroupController@get_data_deleted')->name('admin.usergroup.get_data_deleted');
                    Route::post('/restore', 'UsergroupController@restore')->name('admin.usergroup.restore');
                });

                // USER
                Route::group(['prefix' => 'user'], function () {
                    Route::get('/', 'UserController@list')->name('admin.user.list');
                    Route::get('/get-data', 'UserController@get_data')->name('admin.user.get_data');
                    Route::get('/create', 'UserController@create')->name('admin.user.create');
                    Route::post('/do-create', 'UserController@do_create')->name('admin.user.do_create');
                    Route::get('/edit/{id}', 'UserController@edit')->name('admin.user.edit');
                    Route::post('/do-edit/{id}', 'UserController@do_edit')->name('admin.user.do_edit');
                    Route::post('/delete', 'UserController@delete')->name('admin.user.delete');
                    Route::get('/deleted', 'UserController@list_deleted')->name('admin.user.deleted');
                    Route::get('/get-deleted-data', 'UserController@get_data_deleted')->name('admin.user.get_data_deleted');
                    Route::post('/restore', 'UserController@restore')->name('admin.user.restore');
                    Route::get('/enable/{id}', 'UserController@enable')->name('admin.user.enable');
                    Route::get('/disable/{id}', 'UserController@disable')->name('admin.user.disable');
                });

                // LANGUAGE
                Route::group(['prefix' => 'language'], function () {
                    Route::get('/', 'LanguageController@list')->name('admin.language.list');
                    Route::get('/create', 'LanguageController@create')->name('admin.language.create');
                    Route::post('/do-create', 'LanguageController@do_create')->name('admin.language.do_create');
                    Route::get('/edit/{id}', 'LanguageController@edit')->name('admin.language.edit');
                    Route::post('/do-edit/{id}', 'LanguageController@do_edit')->name('admin.language.do_edit');
                });

                // DICTIONARY
                Route::group(['prefix' => 'dictionary'], function () {
                    Route::get('/', 'LangMasterController@list')->name('admin.langmaster.list');
                    Route::get('/get-data', 'LangMasterController@get_data')->name('admin.langmaster.get_data');
                    Route::get('/create', 'LangMasterController@create')->name('admin.langmaster.create');
                    Route::post('/do-create', 'LangMasterController@do_create')->name('admin.langmaster.do_create');
                    Route::get('/edit/{id}', 'LangMasterController@edit')->name('admin.langmaster.edit');
                    Route::post('/do-edit/{id}', 'LangMasterController@do_edit')->name('admin.langmaster.do_edit');
                });
            });
        });

        /**
         * ******************* ADD ANOTHER CUSTOM ROUTES BELOW *******************
         */

        // HOME
        Route::get('/', 'system\HomeController@index')->name('admin.home');

        // PRODUCT
        Route::group(['prefix' => 'product'], function () {
            Route::get('/', 'ProductController@list')->name('admin.product.list');
            Route::get('/get-data', 'ProductController@get_data')->name('admin.product.get_data');
            Route::get('/create', 'ProductController@create')->name('admin.product.create');
            Route::post('/do-create', 'ProductController@do_create')->name('admin.product.do_create');
            Route::get('/edit/{id}', 'ProductController@edit')->name('admin.product.edit');
            Route::post('/do-edit/{id}', 'ProductController@do_edit')->name('admin.product.do_edit');
            Route::post('/delete', 'ProductController@delete')->name('admin.product.delete');
            Route::get('/deleted', 'ProductController@list_deleted')->name('admin.product.deleted');
            Route::get('/get-data-deleted', 'ProductController@get_data_deleted')->name('admin.product.get_data_deleted');
            Route::post('/restore', 'ProductController@restore')->name('admin.product.restore');
        });
    });
});
