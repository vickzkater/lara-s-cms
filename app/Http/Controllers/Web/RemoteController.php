<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Generator;

class RemoteController extends Controller
{

    public function home()
    {
        $page_menu = 'home';

        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/banner';
        // Hit API - using method GET
        $response = $this->guzzle_get_public($url);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }
        $banners = $data;

        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/product';
        // Hit API - using method GET
        $response = $this->guzzle_get_public($url);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }
        $products = $data;

        return view('web.home', compact('page_menu', 'products', 'banners'));
    }

    public function blog(Request $request)
    {
        $page_menu = 'blog';

        // GET TOPIC
        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/topic';
        // Hit API - using method GET
        $response = $this->guzzle_get_public($url);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }
        $topics = $data;

        // PAGINATION
        $limit = 3;
        $page = 1;
        if ((int) $request->page) {
            $page = (int) $request->page;
        }
        if ($page < 1) {
            $page = 1;
        }

        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/blog';
        // Set parameters
        $params = $request->all();
        $params['limit'] = $limit;
        $params['page'] = $page;
        // Hit API - using method POST
        $response = $this->guzzle_post_public($url, $params);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }

        // GENERATE QR CODE
        $qrcode_gen = new Generator;
        $qrcode = $qrcode_gen->size(200)
            ->generate('https://github.com/vickzkater/lara-s-cms');

        return view('web.blog', compact('page_menu', 'data', 'topics', 'qrcode'));
    }

    public function blog_details($slug)
    {
        $page_menu = 'blog';

        // GET TOPIC
        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/topic';
        // Hit API - using method GET
        $response = $this->guzzle_get_public($url);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }
        $topics = $data;

        // Set API URL - retrieve the data
        $url = env('APP_URL_API') . '/blog/details';
        // Set parameters
        $params = [
            'slug' => $slug
        ];
        // Hit API - using method POST
        $response = $this->guzzle_post_public($url, $params);
        // validating the API response
        $data = null;
        if (!empty($response)) {
            if ($response->status == 'true') {
                // SUCCESS - convert array to object
                $data = json_decode(json_encode($response->data));
            }
        }

        // GENERATE QRCODE - https://www.simplesoftware.io/#/docs/simple-qrcode
        $qrcode_gen = new Generator;
        $qrcode = $qrcode_gen->size(200)
            ->generate(route('web.blog', $data->slug));

        $qrcode_gen = new Generator;
        $qrcode_main = $qrcode_gen->size(200)
            ->generate('https://github.com/vickzkater/lara-s-cms');

        return view('web.blog_details', compact('page_menu', 'data', 'topics', 'qrcode', 'qrcode_main'));
    }
}
