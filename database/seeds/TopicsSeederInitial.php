<?php

use Illuminate\Database\Seeder;

class TopicsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/topics.sql';
        DB::unprepared(file_get_contents($path));
    }
}
