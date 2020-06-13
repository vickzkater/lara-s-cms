<?php

use Illuminate\Database\Seeder;

class SysLanguageMasterSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_language_master.sql';
        DB::unprepared(file_get_contents($path));
    }
}
