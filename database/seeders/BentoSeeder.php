<?php

namespace Database\Seeders;

use App\Models\User;
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
        // ゲスト情報
        $guest = User::where('email', config('app.guest_email'))->first();

        // お弁当情報
        $bentos = [
            'ほっともっと' => ['野菜炒め弁当', '唐揚げ弁当', 'チキン南蛮弁当'],
            'オリジン弁当' => ['タルタルのり弁', 'カツ丼', '牛焼肉弁当'],
            '玉子屋' => ['日替わり弁当'],
            '日本亭' => ['満腹生姜焼き弁当', 'ロースカツ丼', '酢豚弁当'],
        ];

        foreach($bentos as $brandName => $names) {
            // オーナー（user_id: 1）のブランドIDを取得
            $user_brand = DB::table('bento_brands')
                ->where('name', $brandName)
                ->where('user_id', 1)
                ->first();

            // ゲストのブランドIDを取得
            $guest_brand = DB::table('bento_brands')
                ->where('name', $brandName)
                ->where('user_id', $guest->id)
                ->first();

            foreach($names as $name) {
                DB::table('bento_names')->insert([
                    [
                        'user_id' => 1,
                        'bento_brand_id' => $user_brand->id,
                        'name' => $name,
                    ],
                    [
                        'user_id' => $guest->id,
                        'bento_brand_id' => $guest_brand->id,
                        'name' => $name,
                    ],
                ]);
            }
        }
    }
}
