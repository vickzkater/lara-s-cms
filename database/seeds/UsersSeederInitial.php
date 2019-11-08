<?php

use Illuminate\Database\Seeder;

class UsersSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/users.sql';
        DB::unprepared(file_get_contents($path));
    }
}
