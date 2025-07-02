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
            'bento_brands.*' => ['required', 'string', 'max:50'], // ブランド
            'bento_names.*' => ['required', 'string', 'max:255'], // お弁当
            'bento_fees.*' => ['required', 'integer', 'max:10'], // 税込価格
            'tax_rates.*' => ['required', 'integer'], // 消費税
            'bento_quantities.*' => ['required', 'integer', 'max:10'], // 個数
            'unit_prices.*' => ['required', 'integer', 'max:10'], // 単価(税抜)
            'amounts.*' => ['required', 'integer', 'max:10'], // 金額
            'subtotal' => ['required', 'integer', 'max:10'], // 小計
            'tax_total' => ['required', 'integer', 'max:10'], // 消費税の合計
            'total' => ['required', 'integer', 'max:10'], // 合計
            'remarks' => ['nullable', 'string', 'max:500'], // 合計
        ];
    }

    // ⭐️ 個別バリデーションチェック
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // ✅ ----- ユーザー情報 -----
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // ✅ ----- ブランド、お弁当 -----
            // 🔹 requestデータ取得
            $brands = $this->bento_brands;
            $bentos = $this->bento_names;
            if(!$brands || !$bentos || count($brands) !== count($bentos)) {
                return;
            }

            // 🔹 ----- ブランド -----
            // 🔸 DBに存在するブランド名一覧を取得
            $existingBrands = $user
                ->bentoBrands()
                ->pluck('name')
                ->toArray();

            // 🔸 重複チェック(同じ単語は一度だけ)
            foreach(array_unique($brands) as $brand) {
                if(in_array($brand, $existingBrands)) {
                    $validator->errors()->add('bento_brands', "ブランド名 '{$brand}' は既に登録されています。");
                }
            }


            // 🔹 ----- お弁当 -----
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

    // ⭐️ バリデーションのエラーメッセージを上書き
    public function messages()
    {
        return [
            'bento_brands.*.required' => 'ブランド名を入力してください。',
            'bento_brands.*.max' => 'ブランド名は50文字以内で入力してください。',
            'bento_names.*.required' => 'お弁当名を入力してください。',
            'bento_names.*.max' => 'お弁当名は255文字以内で入力してください。',
        ];
    }



}
