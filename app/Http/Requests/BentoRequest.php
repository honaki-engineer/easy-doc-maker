<?php

namespace App\Http\Requests;

use App\Models\BentoName;
use App\Models\BentoBrand;
use Illuminate\Foundation\Http\FormRequest;

class BentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // ✅ ブランド
            'bento_brands.*' => [
                'required', 'string', 'max:50',

                // 🔹 自由入力時のみ、重複チェック
                function($attribute, $value, $fail) {
                    // 既存ブランドに含まれているか確認
                    $existsInMaster = BentoBrand::where('name', $value)
                        ->where('user_id', auth()->id())
                        ->exists();

                    if(!$existsInMaster) {
                        // → 自由入力なので、重複チェック（ユニーク制約）
                        $duplicate = BentoBrand::where('name', $value)
                            ->where('user_id', auth()->id())
                            ->exists();

                        if ($duplicate) {
                            $fail("すでに同じブランド名が存在します。");
                        }
                    }
                }
            ],

            /*  - `$attribute`: バリデーション対象の属性名(例：`bento_names.0`) = name属性、key
                - `$value`: 入力された値(例：「唐揚げ弁当」など)
                - `$fail`: バリデーション失敗時に呼び出す関数(`$fail("エラーメッセージ")`) */
            // ✅ お弁当
            'bento_names.*' => [
                'required', 'string', 'max:255',

                // 🔹 ユーザーごとの「同一ブランド内・同一弁当名」の重複をバリデーション
                function($attribute, $value, $fail) { // *
                    // 入力配列キー（例: bento_names.0）から`index`を取得
                    $index = (int)filter_var($attribute, FILTER_SANITIZE_NUMBER_INT);

                    // 同じ`index`の`bento_brands`配列からブランド名を取得
                    $brandName = $this->input("bento_brands.$index");

                    // ブランド名が未入力ならスキップ
                    if(!$brandName) return;

                    // 指定されたブランド・ユーザー内で、同じ弁当名が存在するかチェック
                    $exists = BentoName::where('name', $value)
                        ->whereHas('bentoBrand', function($query) use($brandName) { // use($brandName)はクロージャー(無名関数)内で`$brandName`を使うために必要。
                            $query->where('name', $brandName)
                                ->where('user_id', auth()->id());
                        })
                        ->exists();

                    if($exists) {
                        $fail("同じブランド内に既に同じお弁当名が存在します。");
                    }
                }
            ],
        ];
    }
}
