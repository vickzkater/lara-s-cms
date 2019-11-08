<?php

use Illuminate\Database\Seeder;

class User_groupSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/user_group.sql';
        DB::unprepared(file_get_contents($path));
    }
}
