<?php 

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class ReceiptSettingService
{
    // - ⭐️ update --------------------------------------------------
    // 更新処理
    public static function updateReceiptSetting($receipt_setting, $request) {
        $receipt_setting->user_id = Auth::id();
        $receipt_setting->postal_code = $request->postal_code;
        $receipt_setting->address_line1 = $request->address_line1;
        $receipt_setting->address_line2 = $request->address_line2;
        $receipt_setting->issuer_name = $request->issuer_name;
        $receipt_setting->issuer_number = $request->issuer_number;
        $receipt_setting->tel_fixed = $request->tel_fixed;
        $receipt_setting->tel_mobile = $request->tel_mobile;
        $receipt_setting->responsible_name = $request->responsible_name;

        $receipt_setting->save();
    }
}

?>