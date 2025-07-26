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
            1 => ['野菜炒め弁当', '唐揚げ弁当', 'チキン南蛮弁当'],
            2 => ['タルタルのり弁', 'カツ丼', '牛焼肉弁当'],
            3 => ['日替わり弁当'],
            4 => ['満腹生姜焼き弁当', 'ロースカツ丼', '酢豚弁当'],
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
