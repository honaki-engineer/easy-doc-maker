<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptSettingController extends Controller
{
    public function show()
    {
        $receipt_setting = Auth::user()->receiptSettings;

        return view('receipt_settings.show', compact('receipt_setting'));
    }

    public function edit()
    {
        $receipt_setting = Auth::user()->receiptSettings;

        return view('receipt_settings.edit', compact('receipt_setting'));
    }

    public function update(Request $request)
    {
        $receipt_setting = Auth::user()->receiptSettings;

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

        return redirect()
            ->route('receipt_settings.show', compact('receipt_setting'))
            ->with('success', '自社情報を更新しました。');
    }
}
