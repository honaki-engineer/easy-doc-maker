<?php

namespace App\Http\Requests;

use App\Models\BentoBrand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReceiptRequest extends FormRequest
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
            'issued_at' => ['required', 'date'], // 日付
            'customer_name' => ['required', 'string', 'max:255'], // 顧客名
            'receipt_note' => ['required', 'string', 'max:500'], // 但し書き
            'payment_method' => ['required'], // 支払い方法
            'bento_brands' => ['array'], // ブランド
            'bento_names' => ['array'], // お弁当
            'bento_fees' => ['array'], // 税込価格
            'tax_rates' => ['array'], // 消費税
            'bento_quantities' => ['array'], // 個数
            'unit_prices' => ['array'], // 単価(税抜)
            'amounts' => ['array'], // 金額
            'subtotal' => ['required', 'integer', 'digits_between:1,10'], // 小計
            'tax_total' => ['required', 'integer', 'digits_between:1,10'], // 消費税の合計
            'total' => ['required', 'integer', 'digits_between:1,10'], // 合計
            'remarks' => ['nullable', 'string', 'max:500'], // 合計
        ];
    }

    // ⭐️ 個別バリデーションチェック
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // ✅ ----- 情報取得 -----
            // 🔹 ユーザー情報を取得
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 🔹 requestデータ取得
            $brands = $this->bento_brands ?? [];
            $bentos = $this->bento_names ?? [];
            $fees = $this->bento_fees ?? [];
            $taxRates = $this->tax_rates ?? [];
            $quantities = $this->bento_quantities ?? [];
            $unitPrices = $this->unit_prices ?? [];
            $amounts = $this->amounts ?? [];

            // 🔹 空欄を削除するフィルター
            $filteredBrands = collect($brands)->filter(fn($val) => $val !== null && $val !== '');
            $filteredBentos = collect($bentos)->filter(fn($val) => $val !== null && $val !== '');
            $filteredFees = collect($fees)->filter(fn($val) => $val !== null && $val !== '');
            $filteredTaxRates = collect($taxRates)->filter(fn($val) => $val !== null && $val !== '');
            $filteredQuantities = collect($quantities)->filter(fn($val) => $val !== null && $val !== '');
            $filteredUnitPrices = collect($unitPrices)->filter(fn($val) => $val !== null && $val !== '');
            $filteredAmounts = collect($amounts)->filter(fn($val) => $val !== null && $val !== '');

            // ✅ ----- `.*`を1度のみバリデーションエラーメッセージを表示する処理 -----
            // 🔹 ----- bento_brands ----- 
            // 空チェック（1つ以上必要）
            if($filteredBrands->isEmpty()) {
                $validator->errors()->add('bento_brands', 'ブランドは必ず指定してください。');
            } elseif ($filteredBrands->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('bento_brands', 'ブランドは必ず文字で指定してください(数字のみNG)。');
            } elseif ($filteredBrands->contains(fn($val) => mb_strlen($val) > 50)) {
                $validator->errors()->add('bento_brands', 'ブランドは50文字以内で指定してください。');
            }
            
            // 🔹 ----- bento_names ----- 
            if($filteredBentos->isEmpty()) {
                $validator->errors()->add('bento_names', '品目は必ず指定してください。');
            } elseif (collect($filteredBentos)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('bento_names', '品目は必ず文字で指定してください(数字のみNG)。');
            } elseif (collect($filteredBentos)->contains(fn($val) => mb_strlen($val) > 255)) {
                $validator->errors()->add('bento_names', '品目は255文字以内で指定してください。');
            }
            
            // 🔹 ----- bento_fees ----- 
            if($filteredFees->isEmpty()) {
                $validator->errors()->add('bento_fees', '税込は必ず指定してください。');
            } elseif (collect($filteredFees)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('bento_fees', '税込は必ず数字で指定してください。');
            } elseif (collect($filteredFees)->contains(fn($val) => strlen((string)$val) > 10)) {
                $validator->errors()->add('bento_fees', '税込は10文字以内で指定してください。');
            }

            // 🔹 ----- tax_rates ----- 
            if($filteredTaxRates->isEmpty()) {
                $validator->errors()->add('tax_rates', '消費税は必ず指定してください。');
            } elseif (collect($filteredTaxRates)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('tax_rates', '消費税は必ず数字で指定してください。');
            }

            // 🔹 ----- bento_quantities ----- 
            if($filteredQuantities->isEmpty()) {
                $validator->errors()->add('bento_quantities', '数量は必ず指定してください。');
            } elseif (collect($filteredQuantities)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('bento_quantities', '数量は必ず数字で指定してください。');
            } elseif (collect($filteredQuantities)->contains(fn($val) => strlen((string)$val) > 10)) {
                $validator->errors()->add('bento_quantities', '数量は10文字以内で指定してください。');
            }

            // 🔹 ----- unit_prices ----- 
            if($filteredUnitPrices->isEmpty()) {
                $validator->errors()->add('unit_prices', '単価は必ず指定してください。');
            } elseif (collect($filteredUnitPrices)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('unit_prices', '単価は必ず数字で指定してください。');
            } elseif (collect($filteredUnitPrices)->contains(fn($val) => strlen((string)$val) > 10)) {
                $validator->errors()->add('unit_prices', '単価は10文字以内で指定してください。');
            }

            // 🔹 ----- amounts ----- 
            if($filteredAmounts->isEmpty()) {
                $validator->errors()->add('amounts', '金額は必ず指定してください。');
            } elseif (collect($filteredAmounts)->contains(fn($val) => !is_string($val))) {
                $validator->errors()->add('amounts', '金額は必ず数字で指定してください。');
            } elseif (collect($filteredAmounts)->contains(fn($val) => strlen((string)$val) > 10)) {
                $validator->errors()->add('amounts', '金額は10文字以内で指定してください。');
            }


            // ✅ -----重複チェック -----
            if(!$brands || !$bentos || count($brands) !== count($bentos)) {
                return;
            }

            // 🔹 ----- bento_brands -----
            // 🔸 DBに存在するブランド名一覧を取得
            $existingBrands = $user
                ->bentoBrands()
                ->pluck('name')
                ->toArray();

            // 🔸 重複チェック(同じ単語は一度だけ)
            foreach(array_unique($brands) as $brand) {
                if(in_array($brand, $existingBrands)) {
                    $validator->errors()->add('bento_brands', "ブランド '{$brand}' は既に登録されています。");
                }
            }

            // 🔹 ----- bento_names -----
            $combinationSet = [];
            for($i = 0; $i < count($brands); $i++) {
                // 🔸 `$i`番目のブランド/お弁当を取得
                $brandName = trim($brands[$i] ?? '');
                $bentoName = trim($bentos[$i] ?? '');

                // 🔸 空チェック
                if($brandName === '' || $bentoName === '') continue;

                // 🔸 ブランドがDBに存在するかチェック
                $brand = $user->bentoBrands()->where('name', $brandName)->first();

                // ブランド内のお弁当重複チェック
                if($brand) {
                    // ブランドに紐づくお弁当に同名があるかチェック
                    $exists = $brand->bentos()->where('name', $bentoName)->exists();

                    if($exists) {
                        $validator->errors()->add("bento_names.{$i}", "'{$brandName}' ： '{$bentoName}' はすでに存在します。");
                    }
                }
            }
        });
    }

    // ⭐️ バリデーション「前」にリクエストの値を整える
    protected function prepareForValidation()
    {
        // ✅ 現在のリクエストデータに新しい値を追加・上書き
        $this->merge([
            // --- 🔹 string -> int型へ変換 ---
            // 🔸 税込
            'bento_fees' => array_map(function ($bento_fee) {
                return str_replace(',', '', $bento_fee);
            }, $this->bento_fees ?? []),

            // 🔸 消費税
            'tax_rates' => array_map(function ($tax_rate) {
                return str_replace(',', '', $tax_rate);
            }, $this->tax_rates ?? []),

            // 🔸 単価(税抜)
            'unit_prices' => array_map(function ($unit_price) {
                return str_replace(',', '', $unit_price);
            }, $this->unit_prices ?? []),

            // 🔸 金額
            'amounts' => array_map(function ($amount) {
                return str_replace(',', '', $amount);
            }, $this->amounts ?? []),

            // 🔸 小計
            'subtotal' => str_replace(',', '', $this->subtotal),

            // 🔸 消費税の合計
            'tax_total' => str_replace(',', '', $this->tax_total),

            // 🔸 合計
            'total' => str_replace(',', '', $this->total),
        ]);
    }

}
