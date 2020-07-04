<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTopic extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_topic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id', 'topic_id'
    ];
}
