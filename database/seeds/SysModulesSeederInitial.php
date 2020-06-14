<?php

use Illuminate\Database\Seeder;

class SysModulesSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_modules.sql';
        DB::unprepared(file_get_contents($path));
    }
}
