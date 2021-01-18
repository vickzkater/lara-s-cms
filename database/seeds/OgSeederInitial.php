<?php

use Illuminate\Database\Seeder;

class OgSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/sys_config_og.sql';
        DB::unprepared(file_get_contents($path));
    }
}
