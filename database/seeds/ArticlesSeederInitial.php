<?php

use Illuminate\Database\Seeder;

class ArticlesSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/articles.sql';
        DB::unprepared(file_get_contents($path));
    }
}
