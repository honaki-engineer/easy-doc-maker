<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceiptSettingRequest extends FormRequest
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
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'issuer_name' => ['required', 'string', 'max:255'],
            'issuer_number' => ['nullable', 'string', 'regex:/^T\d{13}$/'],
            'tel_fixed' => ['nullable', 'string', 'regex:/^\d{2,4}-\d{2,4}-\d{3,4}$/'],
            'tel_mobile' => ['nullable', 'string', 'regex:/^0[789]0-\d{4}-\d{4}$/'],
            'responsible_name' => ['required', 'string', 'max:255'],
        ];
    }

    // 個別エラーメッセージ
    public function messages()
    {
        return [
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'issuer_number.regex' => '登録番号は「T」+13桁の半角数字(例：T1234567890123)で入力してください。',
            'tel_fixed.regex' => '固定電話は「03-1234-5678」の形式で入力してください。',
            'tel_mobile.regex' => '携帯電話は「090-1234-5678」の形式で入力してください。',
        ];
    }
}
