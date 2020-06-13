<?php

use Illuminate\Database\Seeder;

class SysUsersSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_users.sql';
        DB::unprepared(file_get_contents($path));
    }
}
