<?php

use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        Bouncer::allow('superadmin')->everything();
//
//        Bouncer::allow('admin')->everything();
//        Bouncer::forbid('admin')->toManage(\App\User::class);
//
//        Bouncer::allow('editor')->to('create', \App\Post::class);
//        
//        Bouncer::allow('editor')->toOwn(\App\Post::class);
        
       // Bouncer::allow('editor')->to('update');
        
        Bouncer::allow('editor')->toOwn(\App\Post::class)->to(['view', 'update']);
    }
}
