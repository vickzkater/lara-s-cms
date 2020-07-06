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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'API'], function () {
    Route::get('/', 'ApiController@index');

    Route::get('/banner', 'ApiController@get_banner');

    Route::get('/product', 'ApiController@get_product');

    Route::get('/topic', 'ApiController@get_topic');

    Route::group(['prefix' => 'blog'], function () {
        Route::post('/', 'ApiController@get_blog');

        Route::post('/details', 'ApiController@get_blog_details');
    });
});
