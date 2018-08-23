<?php

use Illuminate\Database\Seeder;

class SyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Factory('App\SyUser', 10)->create();
    }
}
