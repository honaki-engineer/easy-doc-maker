<?php 

namespace App\Services;

use App\Models\BentoBrand;
use App\Models\BentoName;

class BentoService
{
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