<?php

use Illuminate\Database\Seeder;

class Log_detailsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/log_details.sql';
        DB::unprepared(file_get_contents($path));
    }
}
