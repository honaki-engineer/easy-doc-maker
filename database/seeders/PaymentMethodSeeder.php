<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
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

        // 支払い方法情報
        $payment_methods = ['クレジットカード', '請求書', '代引き'];

        foreach($payment_methods as $payment_method) {
            DB::table('payment_methods')->insert([
                [
                    'user_id' => 1,
                    'name' => $payment_method,
                ],[
                    'user_id' => $guest->id,
                    'name' => $payment_method,
                ]
            ]);
        }
    }
}
