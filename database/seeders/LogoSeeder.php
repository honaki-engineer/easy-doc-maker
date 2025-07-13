<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ✅ ロゴ画像を public/storage/images にコピー
        $source = storage_path('app/public/images/easyDocMaker.png'); // storage
        $destination = public_path('storage/images/easyDocMaker.png'); // public

        if(!File::exists($destination)) {
            File::copy($source, $destination);
        }
    }
}
