<?php

use Illuminate\Database\Seeder;

class OrderProductSeeder extends Seeder
{
    public function run()
    {
        factory(App\OrderProduct::class, 50)->create();
    }
}
