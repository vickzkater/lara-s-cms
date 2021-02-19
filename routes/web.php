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
    /**
     * DISABLE ADMIN PANEL
     */
    if (env('ADMIN_CMS', false) == false) {
        Route::group(['prefix' => env('ADMIN_DIR')], function () {
            Route::get('/login', 'SiteController@redirect')->name('admin.login');
        });
    }

    if (env('APP_BACKEND', 'MODEL') == 'MODEL') {
        /**
         * USING MODEL AS BACK-END PROCESSOR
         */

        // HOME
        Route::get('/', 'SiteController@home')->name('web.home');

        // BLOG
        Route::group(['prefix' => 'blog'], function () {
            Route::get('/', 'SiteController@blog')->name('web.blog');
            Route::get('/{slug}', 'SiteController@blog_details')->name('web.blog.details');
        });
    } else {
        /**
         * USING API AS BACK-END PROCESSOR
         */

        // HOME
        Route::get('/', 'RemoteController@home')->name('web.home');

        // BLOG
        Route::group(['prefix' => 'blog'], function () {
            Route::get('/', 'RemoteController@blog')->name('web.blog');
            Route::get('/{slug}', 'RemoteController@blog_details')->name('web.blog.details');
        });
    }
});

// ADMIN
Route::group([
    'prefix' => env('ADMIN_DIR'),
    'namespace' => 'Admin'
], function () {
    /**
     * DISABLE ADMIN PANEL
     */
    if (env('ADMIN_CMS', false) == true) {
        // AUTH
        Route::get('/login', 'system\AuthController@login')->name('admin.login');
        Route::post('/do-login', 'system\AuthController@do_login')->name('admin.do_login');
        Route::get('/logout', 'system\AuthController@logout')->name('admin.logout');
        Route::get('/logout-all', 'system\AuthController@logout_all')->name('admin.logout.all');
        Route::get('/auth/{social}', 'system\AuthController@redirect_to_provider')->name('admin.auth.provider');
        Route::get('/auth/{social}/callback', 'system\AuthController@handle_provider_callback')->name('admin.auth.provider.callback');
    }

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
                // CONFIG
                Route::group(['prefix' => 'config'], function () {
                    Route::get('/', 'ConfigController@view')->name('admin.config');
                    Route::post('/update', 'ConfigController@update')->name('admin.config.update');
                });

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
                    Route::post('/sorting', 'DivisionController@sorting')->name('admin.division.sorting');
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
                    Route::post('/sorting', 'BranchController@sorting')->name('admin.branch.sorting');
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

        // BANNER
        Route::group(['prefix' => 'banner'], function () {
            Route::get('/', 'BannerController@list')->name('admin.banner.list');
            Route::get('/get-data', 'BannerController@get_data')->name('admin.banner.get_data');
            Route::get('/create', 'BannerController@create')->name('admin.banner.create');
            Route::post('/do-create', 'BannerController@do_create')->name('admin.banner.do_create');
            Route::get('/edit/{id}', 'BannerController@edit')->name('admin.banner.edit');
            Route::post('/do-edit/{id}', 'BannerController@do_edit')->name('admin.banner.do_edit');
            Route::post('/sorting', 'BannerController@sorting')->name('admin.banner.sorting');
            Route::post('/delete', 'BannerController@delete')->name('admin.banner.delete');
            Route::get('/deleted', 'BannerController@list_deleted')->name('admin.banner.deleted');
            Route::get('/get-data-deleted', 'BannerController@get_data_deleted')->name('admin.banner.get_data_deleted');
            Route::post('/restore', 'BannerController@restore')->name('admin.banner.restore');
        });

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

            // Excel
            Route::group(['prefix' => 'excel'], function () {
                Route::get('/import/get-template', 'ProductController@import_excel_template')->name('admin.product.import_excel_template');
                Route::post('/import', 'ProductController@import_excel')->name('admin.product.import_excel');
                Route::get('/export', 'ProductController@export_excel')->name('admin.product.export_excel');
            });
        });

        // TOPIC
        Route::group(['prefix' => 'topic'], function () {
            Route::get('/', 'TopicController@list')->name('admin.topic.list');
            Route::get('/get-data', 'TopicController@get_data')->name('admin.topic.get_data');
            Route::get('/create', 'TopicController@create')->name('admin.topic.create');
            Route::post('/do-create', 'TopicController@do_create')->name('admin.topic.do_create');
            Route::get('/edit/{id}', 'TopicController@edit')->name('admin.topic.edit');
            Route::post('/do-edit/{id}', 'TopicController@do_edit')->name('admin.topic.do_edit');
            Route::post('/delete', 'TopicController@delete')->name('admin.topic.delete');
        });

        // ARTICLE
        Route::group(['prefix' => 'article'], function () {
            Route::get('/', 'ArticleController@list')->name('admin.article.list');
            Route::get('/get-data', 'ArticleController@get_data')->name('admin.article.get_data');
            Route::get('/create', 'ArticleController@create')->name('admin.article.create');
            Route::post('/do-create', 'ArticleController@do_create')->name('admin.article.do_create');
            Route::get('/edit/{id}', 'ArticleController@edit')->name('admin.article.edit');
            Route::post('/do-edit/{id}', 'ArticleController@do_edit')->name('admin.article.do_edit');
            Route::post('/delete', 'ArticleController@delete')->name('admin.article.delete');
            Route::get('/enable/{id}', 'ArticleController@enable')->name('admin.article.enable');
            Route::get('/disable/{id}', 'ArticleController@disable')->name('admin.article.disable');
        });
    });
});

// DEVELOPMENT TESTER
Route::group(['prefix' => 'dev'], function () {
    // GOSMS
    Route::group(['prefix' => 'gosms'], function () {
        // Send SMS - sample: "{URL}/dev/gosms/send?mobile_phone=62812345xxx&message=Hello"
        Route::get('/send', 'DevController@gosms_send');
    });

    // GOSMS - support for Indonesian telecommunications operators
    Route::group(['prefix' => 'gosms'], function () {
        // Send SMS - sample: "{URL}/dev/gosms/send?mobile_phone=62812345xxx&message=Hello"
        Route::get('/send', 'DevController@gosms_send');
    });

    // ONEWAYSMS - support for Malaysian telecommunications operators
    Route::group(['prefix' => 'onewaysms'], function () {
        // Send SMS - sample: "{URL}/dev/onewaysms/send?mobile_phone=601234xxx&message=Hello"
        Route::get('/send', 'DevController@onewaysms_send');
    });

    // MAILCHIMP
    Route::group(['prefix' => 'mailchimp'], function () {
        // Get List - sample: "{URL}/dev/mailchimp/"
        Route::get('/', 'DevController@mailchimp_list');

        // Check the status of a contact - sample: "{URL}/dev/mailchimp/status?email=vicky@domain.com"
        Route::get('/status', 'DevController@mailchimp_status');

        // Subscribe a user to your list with merge fields and double-opt-in confirmation disabled - sample: "{URL}/dev/mailchimp/subscribe?email=vicky@domain.com"
        Route::get('/subscribe', 'DevController@mailchimp_subscribe');

        // View Tags - sample: "{URL}/dev/mailchimp/tags"
        Route::get('/tags', 'DevController@mailchimp_tags');

        // Add New Tag - sample: "{URL}/dev/mailchimp/add-tag?name=Newsletter"
        Route::get('/add-tag', 'DevController@mailchimp_add_tag');

        // Add a tag to a contact - sample: "{URL}/dev/mailchimp/add-tag-to?email=vicky@domain.com&tag_id=123456"
        Route::get('/add-tag-to', 'DevController@mailchimp_add_tag_to_contact');

        // View tags in a contact - sample: "{URL}/dev/mailchimp/tags-in-contact?email=vicky@domain.com"
        Route::get('/tags-in-contact', 'DevController@mailchimp_view_tags_in_contact');
    });

    // EMAIL
    Route::group(['prefix' => 'email'], function () {
        // Send Email using SMTP - sample: "{URL}/dev/email?send=true&email=username@domain.com"
        // Preview Email - sample: "{URL}/dev/email"
        Route::get('/', 'DevController@email_send');
    });
});
