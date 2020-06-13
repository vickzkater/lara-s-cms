<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Banner;
use App\Models\Article;

class SiteController extends Controller
{
    public function home()
    {
        // GET DATA - BLOGS
        $blogs = Article::select(
            'article.id',
            'article.title',
            'article.slug',
            'article.thumbnail',
            'article.keywords',
            'article.content',
            'article.status',
            'article.created_at',
            'article.updated_at',
            DB::raw('GROUP_CONCAT(topic.name) AS topic')
        )
            ->leftJoin('article_topics', 'article.id', 'article_topics.article')
            ->leftJoin('topic', 'article_topics.topic', 'topic.id')
            ->where('article.status', 1)
            ->groupBy(
                'article.id',
                'article.title',
                'article.slug',
                'article.thumbnail',
                'article.keywords',
                'article.content',
                'article.status',
                'article.created_at',
                'article.updated_at'
            )
            ->orderBy('article.id', 'desc')
            ->limit(4)
            ->get();

        return view('web.home', compact('blogs'));
    }

    public function contact()
    {
        return view('web.contact');
    }

    public function about()
    {
        return view('web.about');
    }
}
