<?php

use Illuminate\Database\Seeder;

class App_moduleSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/app_module.sql';
        DB::unprepared(file_get_contents($path));
    }
}
