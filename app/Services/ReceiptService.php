<?php 

namespace App\Services;

use App\Models\BentoBrand;
use App\Models\BentoName;
use App\Models\Receipt;
use App\Models\ReceiptBentoDetail;
use Illuminate\Support\Facades\Auth;

class ReceiptService
{
    // ⭐️ common (create & store) --------------------------------------------------
        // ✅ 自社情報の取得
        public static function getReceiptSetting() {
          $receipt_setting = Auth::user()->receiptSettings;
          return $receipt_setting;
        }
      
    // ⭐️ store --------------------------------------------------
        // ✅ receiptsテーブルへの保存
        public static function storeReceipt($payment_method, $request, $receipt_setting) {
          $receipt = Receipt::create([
            'user_id' => Auth::id(),
            'payment_method_id' => $payment_method->id,
            'customer_name' => $request->customer_name,
            'issued_at' => $request->issued_at,
            'postal_code' => $receipt_setting->postal_code,
            'address_line1' => $receipt_setting->address_line1,
            'address_line2' => $receipt_setting->address_line2,
            'issuer_name' => $receipt_setting->issuer_name,
            'issuer_number' => $receipt_setting->issuer_number,
            'tel_fixed' => $receipt_setting->tel_fixed,
            'tel_mobile' => $receipt_setting->tel_mobile,
            'responsible_name' => $receipt_setting->responsible_name,
            'receipt_note' => $request->receipt_note,
            'subtotal' => (int) $request->subtotal,
            'tax_total' => (int) $request->tax_total,
            'total' => (int) $request->total,
            'remarks' => $request->remarks,
          ]);
          
          return $receipt;
        }
        
        // ✅ receipt_bento_detailsテーブルへの保存
        public static function storeReceiptBentoDetails($bentoBrands, $user, $bentoNames, $receipt, $bentoFees, $taxRates, $bentoQuantities, $unitPrices, $amounts) {
            foreach($bentoBrands as $index => $bentoBrand) {
                $brandName = trim($rawBrand ?? '');
                $bentoName = trim($bentoNames[$index] ?? '');

                // 🔹 両方とも空ならスキップ(完全に空の行)
                if ($brandName === '' && $bentoName === '') {
                    continue;
                }

                // 🔹 ブランドをfirstOrCreate(新規入力のみ保存)
                $brand = BentoBrand::firstOrCreate([
                    'user_id' => $user->id,
                    'name' => $bentoBrand,
                ]);

                // 🔹 ブランドに紐づけてお弁当名をfirstOrCreate(新規入力のみ保存)
                BentoName::firstOrCreate([
                    'user_id' => $user->id,
                    'bento_brand_id' => $brand->id,
                    'name' => $bentoNames[$index],
                ]);
            
                // 🔹 領収書_弁当テーブルへの保存
                ReceiptBentoDetail::create([
                    'receipt_id' => $receipt->id,
                    'bento_brand_name' => $bentoBrand,
                    'bento_name' => $bentoNames[$index],
                    'bento_fee' =>  (int) $bentoFees[$index],
                    'tax_rate' =>  (int) $taxRates[$index],
                    'bento_quantity' => $bentoQuantities[$index],
                    'unit_price' =>  (int) $unitPrices[$index],
                    'amount' =>  (int) $amounts[$index],
                ]);
            }
        }
}

?>