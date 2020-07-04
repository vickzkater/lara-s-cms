<?php

use Illuminate\Database\Seeder;

class ArticleTopicSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/article_topic.sql';
        DB::unprepared(file_get_contents($path));
    }
}
