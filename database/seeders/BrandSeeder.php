<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = ['炭治郎', '牛亭', '俺のフレンチ', 'かまつ田'];

        foreach($brands as $brand) {
            DB::table('bento_brands')->insert([
                'user_id' => 1,
                'name' => $brand,
            ]);
        }
    }
}
