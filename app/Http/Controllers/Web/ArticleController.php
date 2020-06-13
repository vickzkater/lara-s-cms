<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Article;

class ArticleController extends Controller
{
    public function list()
    {
        return view('web.article.list');
    }

    public function details($slug)
    {
        // get the data
        $data = Article::select(
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
            ->where('article.slug', $slug)
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
            ->first();

        if (!$data) {
            // 404 PAGE NOT FOUND
            return abort(404);
        }

        return view('web.article.details', compact('data'));
    }
}
