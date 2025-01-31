<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CustomerSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(OrderProductSeeder::class);
    }
}
