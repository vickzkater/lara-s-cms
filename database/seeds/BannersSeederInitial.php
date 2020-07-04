<?php

use Illuminate\Database\Seeder;

class BannersSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/banners.sql';
        DB::unprepared(file_get_contents($path));
    }
}
