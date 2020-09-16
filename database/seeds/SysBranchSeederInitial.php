<?php

use Illuminate\Database\Seeder;

class SysBranchSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_branches.sql';
        DB::unprepared(file_get_contents($path));
    }
}
