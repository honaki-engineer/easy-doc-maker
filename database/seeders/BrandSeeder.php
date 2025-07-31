<?php

namespace Database\Seeders;

use App\Models\User;
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
        // ゲスト情報
        $guest = User::where('email', config('app.guest_email'))->first();

        // ブランド情報
        $brands = ['ほっともっと', 'オリジン弁当', '玉子屋', '日本亭'];

        foreach($brands as $brand) {
            DB::table('bento_brands')->insert([
                [
                    'user_id' => 1,
                    'name' => $brand
                ],
                [
                    'user_id' => $guest->id,
                    'name' => $brand
                ],
            ]);
        }
    }
}
