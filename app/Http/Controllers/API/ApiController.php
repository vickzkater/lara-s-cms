<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\Product;
use App\Models\Article;
use App\Models\Topic;
use App\Models\Banner;

class ApiController extends Controller
{
    public function index()
    {
        return '[API] Lara-S-CMS';
    }

    public function get_banner()
    {
        $data = Banner::where('status', 1)->orderBy('ordinal')->get();

        return response()->json([
            'status' => 'true',
            'message' => 'Successfully get banner data',
            'data' => $data
        ]);
    }

    public function get_product()
    {
        $data = Product::whereNull('replaced_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'true',
            'message' => 'Successfully get product data',
            'data' => $data
        ]);
    }

    public function get_topic()
    {
        $data = Topic::where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'true',
            'message' => 'Successfully get topic data',
            'data' => $data
        ]);
    }

    public function get_blog(Request $request)
    {
        // GET THE DATA
        $data = Article::select(
            'articles.slug',
            'articles.title',
            'articles.thumbnail',
            'articles.summary',
            'articles.posted_at',
            'articles.author'
        )
            ->leftJoin('article_topic', 'articles.id', '=', 'article_topic.article_id')
            ->leftJoin('topics', 'article_topic.topic_id', '=', 'topics.id')
            ->where('articles.status', 1)
            ->orderBy('articles.posted_at', 'desc')
            ->groupBy(
                'articles.slug',
                'articles.title',
                'articles.thumbnail',
                'articles.summary',
                'articles.posted_at',
                'articles.author'
            );

        // FILTER BY TOPIC
        if ($request->topic) {
            $topic = Helper::validate_input_text($request->topic);
            $data->where('topics.name', $topic);
        }

        // FILTER BY KEYWORD
        if ($request->keyword) {
            $keyword = Helper::validate_input_text($request->keyword);
            $data->where(function ($query_where) use ($keyword) {
                $query_where->where('articles.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('articles.keywords', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('articles.summary', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('articles.content', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('topics.name', 'LIKE', '%' . $keyword . '%');
            });
        }

        // FILTER BY AUTHOR
        if ($request->author) {
            $author = Helper::validate_input_text($request->author);
            $data->where('articles.author', $author);
        }

        // GET TOTAL DATA
        $query = $data;
        $total = $query->get()->count();

        // PAGINATION
        $limit = 3;
        $page = 1;
        if ((int) $request->page) {
            $page = (int) $request->page;
        }
        if ($page < 1) {
            $page = 1;
        }
        $skip = ($page - 1) * $limit;

        $data = $data
            ->take($limit)
            ->skip($skip)
            ->get();

        return response()->json([
            'status' => 'true',
            'message' => 'Successfully get blog data',
            'data' => $data,
            'total' => $total
        ]);
    }

    public function get_blog_details(Request $request)
    {
        // GET PARAMATERS DATA
        $slug = $request->slug;

        // GET THE DATA
        $data = Article::select(
            'articles.slug',
            'articles.title',
            'articles.thumbnail',
            'articles.posted_at',
            'articles.author',
            'articles.summary',
            'articles.content',
            DB::raw('GROUP_CONCAT(topics.name SEPARATOR " | ") AS topics')
        )
            ->leftJoin('article_topic', 'articles.id', '=', 'article_topic.article_id')
            ->leftJoin('topics', 'article_topic.topic_id', '=', 'topics.id')
            ->where('articles.status', 1)
            ->where('articles.slug', $slug)
            ->orderBy('articles.posted_at', 'desc')
            ->groupBy(
                'articles.slug',
                'articles.title',
                'articles.thumbnail',
                'articles.posted_at',
                'articles.author',
                'articles.summary',
                'articles.content'
            )
            ->first();

        return response()->json([
            'status' => 'true',
            'message' => 'Successfully get blog details data',
            'data' => $data
        ]);
    }
}
