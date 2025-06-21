<?php 

namespace App\Services;

use App\Models\BentoBrand;
use App\Models\BentoName;

class BentoService
{
    // - ⭐️ store --------------------------------------------------
    // お弁当 + ブランド登録
    public static function storeBentosWithBrands($brands, $user, $names) {
        // 複数登録ループ
        foreach($brands as $index => $brand) {
            // ブランド名（またはID）が既存かチェックして取得・なければ作成
            $brand = BentoBrand::firstOrCreate(
                ['name' => $brand, 'user_id' => $user->id],
                ['name' => $brand, 'user_id' => $user->id]
            );

            // お弁当登録
            BentoName::create([
                'user_id' => $user->id,
                'bento_brand_id' => $brand->id,
                'name' => $names[$index],
            ]);
        }
    }

    // - ⭐️ destroy --------------------------------------------------
    public static function destroyBrandIfEmpty($brand) {
        // 該当ブランドに他のお弁当が存在しないかチェック
        $remaining = $brand->bentoNames()->exists();
        // もし1件もなければブランドも削除
        if(!$remaining) {
            $brand->delete();
        }
    }
}

?>