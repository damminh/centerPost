<?php

use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new \App\Models\Type();
        $type->name = 'WordPress';
        $type->description = null;
        $type->save();

        $type1 = new \App\Models\Type();
        $type1->name  = "Không phải wordpress";
        $type1->description = null;
        $type1->save();
    }
}
