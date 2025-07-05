<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReceiptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'issued_at' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'receipt_note' => ['required', 'string', 'max:500'],
            'payment_method' => ['required'],
            'bento_brands' => ['array'],
            'bento_names' => ['array'],
            'bento_fees' => ['array'],
            'tax_rates' => ['array'],
            'bento_quantities' => ['array'],
            'unit_prices' => ['array'],
            'amounts' => ['array'],
            'subtotal' => ['required', 'integer', 'digits_between:1,10'],
            'tax_total' => ['required', 'integer', 'digits_between:1,10'],
            'total' => ['required', 'integer', 'digits_between:1,10'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ];
    }

    // ⭐️ オリジナルバリデーション
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // ✅ リクエストデータを取得
            $brands     = $this->bento_brands ?? [];
            $bentos     = $this->bento_names ?? [];
            $fees       = $this->bento_fees ?? [];
            $taxRates   = $this->tax_rates ?? [];
            $quantities = $this->bento_quantities ?? [];
            $unitPrices = $this->unit_prices ?? [];
            $amounts    = $this->amounts ?? [];

            // ✅ 行数の最大値を取得
            $rowCount = max(
                count($brands), count($bentos), count($fees),
                count($taxRates), count($quantities), count($unitPrices), count($amounts)
            );

            // ✅ 入力行の確認用(あったらfor文でtrue、なかったら最後にバリデーションエラーメッセージ)
            $isAnyRowFilled = false;

            // ✅ `$rowCount`内でバリデーション
            for($i = 0; $i < $rowCount; $i++) {
                $brand     = trim($brands[$i] ?? '');
                $bento     = trim($bentos[$i] ?? '');
                $fee       = trim($fees[$i] ?? '');
                $taxRate   = trim($taxRates[$i] ?? '');
                $quantity  = trim($quantities[$i] ?? '');
                $unitPrice = trim($unitPrices[$i] ?? '');
                $amount    = trim($amounts[$i] ?? '');

                // 🔹 その行のいずれかに入力があればバリデーション対象
                $isFilledRow = $brand !== '' || $bento !== '' || $fee !== '' || $taxRate !== ''
                    || $quantity !== '' || $unitPrice !== '' || $amount !== '';

                // 🔹 行はスキップ
                if(!$isFilledRow) {
                    continue;
                }

                // 🔹✅ 入力行の確認用(あったらfor文でtrue、なかったら最後にバリデーションエラーメッセージ)
                $isAnyRowFilled = true;

                // 🔹 バリデーション
                // 🔸 ブランド
                if($brand === '') {
                    $validator->errors()->add("bento_brands", "ブランドは必ず指定してください。");
                } elseif(mb_strlen($brand) > 50) {
                    $validator->errors()->add("bento_brands", "ブランドは50文字以内で指定してください。");
                }

                // 🔸 品目
                if($bento === '') {
                    $validator->errors()->add("bento_names", "品目は必ず指定してください。");
                } elseif(mb_strlen($bento) > 255) {
                    $validator->errors()->add("bento_names", "品目は255文字以内で指定してください。");
                }

                // 🔸 税込
                if($fee === '') {
                    $validator->errors()->add("bento_fees", "税込は必ず指定してください。");
                } elseif(!ctype_digit($fee)) {
                    $validator->errors()->add("bento_fees", "税込は数字で指定してください。");
                } elseif(strlen($fee) > 10) {
                    $validator->errors()->add("bento_fees", "税込は10桁以内で指定してください。");
                }

                // 🔸 消費税
                if($taxRate === '') {
                    $validator->errors()->add("tax_rates", "消費税は必ず指定してください。");
                } elseif(!ctype_digit($taxRate)) {
                    $validator->errors()->add("tax_rates", "消費税は数字で指定してください。");
                }

                // 🔸 数量
                if($quantity === '') {
                    $validator->errors()->add("bento_quantities", "数量は必ず指定してください。");
                } elseif(!ctype_digit($quantity)) {
                    $validator->errors()->add("bento_quantities", "数量は数字で指定してください。");
                } elseif(strlen($quantity) > 10) {
                    $validator->errors()->add("bento_quantities", "数量は10桁以内で指定してください。");
                }

                // 🔸 単価
                if($unitPrice === '') {
                    $validator->errors()->add("unit_prices", "単価は必ず指定してください。");
                } elseif(!ctype_digit($unitPrice)) {
                    $validator->errors()->add("unit_prices", "単価は数字で指定してください。");
                } elseif(strlen($unitPrice) > 10) {
                    $validator->errors()->add("unit_prices", "単価は10桁以内で指定してください。");
                }

                // 🔸 金額
                if($amount === '') {
                    $validator->errors()->add("amounts", "金額は必ず指定してください。");
                } elseif(!ctype_digit($amount)) {
                    $validator->errors()->add("amounts", "金額は数字で指定してください。");
                } elseif(strlen($amount) > 10) {
                    $validator->errors()->add("amounts", "金額は10桁以内で指定してください。");
                }
            }

            // ✅ 全行が空欄だった場合、まとめてエラー表示
            if(!$isAnyRowFilled) {
                $validator->errors()->add("bento_brands", "ブランドは必ず指定してください。");
                $validator->errors()->add("bento_names", "品目は必ず指定してください。");
                $validator->errors()->add("bento_fees", "税込は必ず指定してください。");
                $validator->errors()->add("tax_rates", "消費税は必ず指定してください。");
                $validator->errors()->add("bento_quantities", "数量は必ず指定してください。");
                $validator->errors()->add("unit_prices", "単価は必ず指定してください。");
                $validator->errors()->add("amounts", "金額は必ず指定してください。");
            }
        });
    }

    // ⭐️ バリデーション前
    protected function prepareForValidation()
    {
        // ✅ カンマ,%削除
        $this->merge([
            'bento_fees' => array_map(fn($v) => str_replace(',', '', $v), $this->bento_fees ?? []),
            'tax_rates' => array_map(fn($v) => str_replace(['%', ','], '', $v), $this->tax_rates ?? []),
            'unit_prices' => array_map(fn($v) => str_replace(',', '', $v), $this->unit_prices ?? []),
            'amounts' => array_map(fn($v) => str_replace(',', '', $v), $this->amounts ?? []),
            'subtotal' => str_replace(',', '', $this->subtotal),
            'tax_total' => str_replace(',', '', $this->tax_total),
            'total' => str_replace(',', '', $this->total),
        ]);
    }
}
