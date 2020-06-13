<?php

use Illuminate\Database\Seeder;

class SysLanguagesSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_languages.sql';
        DB::unprepared(file_get_contents($path));
    }
}
