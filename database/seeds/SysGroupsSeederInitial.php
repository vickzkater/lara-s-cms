<?php

use Illuminate\Database\Seeder;

class SysGroupsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_groups.sql';
        DB::unprepared(file_get_contents($path));
    }
}
