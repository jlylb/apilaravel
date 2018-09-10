<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Hisdata extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Factory('App\Air', 100)->create();
        Factory('App\Light', 100)->create();
        Model::reguard();
    }
}
