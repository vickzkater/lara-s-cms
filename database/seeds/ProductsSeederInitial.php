<?php

use Illuminate\Database\Seeder;

class ProductsSeederInitial extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/raw/products.sql';
        DB::unprepared(file_get_contents($path));
    }
}
