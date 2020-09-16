<?php

use Illuminate\Database\Seeder;

class SysLogsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_logs.sql';
        DB::unprepared(file_get_contents($path));
    }
}
