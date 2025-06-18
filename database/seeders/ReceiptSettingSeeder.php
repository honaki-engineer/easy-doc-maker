<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('receipt_settings')->insert([
            [
                'user_id' => 1,
                'postal_code' => '333-4444',
                'address_line1' => '埼玉県川口市1-1-1',
                'address_line2' => '再頭ビル203',
                'issuer_name' => '株式会社one',
                'issuer_number' => 'T1010001111110',
                'tel_fixed' => '048-111-1111',
                'tel_mobile' => '090-2222-3333',
                'responsible_name' => '本多',
            ],
        ]);
    }
}
