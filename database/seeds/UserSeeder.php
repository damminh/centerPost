<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Models\User();
        $user->name = 'user';
        $user->username = 'user';
        $user->password = \Illuminate\Support\Facades\Hash::make('123456a@');
        $user->description = null;
        $user->api_token = str_random(128);
        $user->save();
    }
}
