<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class ControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        // $air = [886738701, 886738730];
        Factory('App\Models\Juanlian', 1)->create();
        Factory('App\Models\Guangai', 1)->create();
        Factory('App\Models\Shifei', 1)->create();
        Factory('App\Models\Tiaowen', 1)->create();
        Factory('App\Models\Tongfei', 1)->create();
        Factory('App\Models\Buguang', 1)->create();

        Model::reguard();
    }
}
