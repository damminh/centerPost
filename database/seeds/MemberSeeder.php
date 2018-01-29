<?php

use Illuminate\Database\Seeder;

class MembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $member = new \App\Models\Member();
        $member->name = 'member';
        $member->username = 'member';
        $member->password = \Illuminate\Support\Facades\Hash::make('123456a@');
        $member->phone = '01674139029';
        $member->user_id = 1;
        $member->description = null;
        $member->token = str_random(128);
        $member->save();
    }
}
