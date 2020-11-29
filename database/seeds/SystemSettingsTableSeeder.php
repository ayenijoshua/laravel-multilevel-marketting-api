<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SystemSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_settings')->insert(
            [
                'entry_payment'=>100,
                'pin_price'=>100,
                'referral_bonus'=>100
            ]
            );
    }
}
