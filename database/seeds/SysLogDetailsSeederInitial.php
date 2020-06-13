<?php

use Illuminate\Database\Seeder;

class SysLogDetailsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_log_details.sql';
        DB::unprepared(file_get_contents($path));
    }
}
