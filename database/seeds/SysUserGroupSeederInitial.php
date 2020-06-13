<?php

use Illuminate\Database\Seeder;

class SysUserGroupSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_user_group.sql';
        DB::unprepared(file_get_contents($path));
    }
}
