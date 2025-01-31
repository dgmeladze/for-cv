<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        factory(App\Customer::class, 10)->create();
    }
}
