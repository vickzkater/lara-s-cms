<?php

use Illuminate\Database\Seeder;

class SysDivisionSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_division.sql';
        DB::unprepared(file_get_contents($path));
    }
}
