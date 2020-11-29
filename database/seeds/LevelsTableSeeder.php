<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Level;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Level::create([
            'id'=> 0,
            'name'=>'feeder',
            'description'=>'feeder level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 1,
            'name'=>'Stage1',
            'description'=>'stage1 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 2,
            'name'=>'Stage2',
            'description'=>'stage2 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 3,
            'name'=>'Stage3',
            'description'=>'stage3 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 4,
            'name'=>'Stage4',
            'description'=>'stage1 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 5,
            'name'=>'Stage5',
            'description'=>'stage5 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 6,
            'name'=>'Stage6',
            'description'=>'stage6 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
        Level::create([
            'id'=> 7,
            'name'=>'Stage7',
            'description'=>'stage7 level',
            'downline_bonus'=>100,
            'completion_bonus'=>100,
            'food_stuff_bonus'=>100
        ]);
    }
}
