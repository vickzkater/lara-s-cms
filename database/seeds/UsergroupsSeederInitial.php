<?php

use Illuminate\Database\Seeder;

class UsergroupsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/usergroups.sql';
        DB::unprepared(file_get_contents($path));
    }
}
