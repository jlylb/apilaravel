<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Realdata extends Seeder
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
        Factory('App\RealAir', 1)->create(['pdi_index' => 886738701]);
        Factory('App\RealAir', 1)->create(['pdi_index' => 886738730]);
        
        // $arr = [865335183, 676214795];
        Factory('App\RealLight', 1)->create(['pdi_index' => 865335183]);
        Factory('App\RealLight', 1)->create(['pdi_index' => 676214795]);
        
        // $arr = [676214785];
        Factory('App\RealCo2', 1)->create(['pdi_index' => 676214785]);
        
        // $arr = [865335169];
        Factory('App\RealSoil', 1)->create(['pdi_index' => 865335169]);
        
        // $arr = [865335191];
        Factory('App\RealLiquid', 1)->create(['pdi_index' => 865335191]);
        Model::reguard();
    }
}
