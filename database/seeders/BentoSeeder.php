<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bentos = [
            1 => ['牛タン', 'カルビ', 'サーロイン', 'モモ'],
            2 => ['牛タン', 'カルビ', 'サーロイン', 'みすじ'],
            3 => ['特上', '通常', '肉なし'],
            4 => ['特上', '通常'],
        ];

        foreach($bentos as $brandId => $names) {
            foreach($names as $name) {
                DB::table('bento_names')->insert([
                    'user_id' => 1,
                    'bento_brand_id' => $brandId,
                    'name' => $name,
                ]);
            }
        }
    }
}
