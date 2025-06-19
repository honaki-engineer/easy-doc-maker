<?php

namespace App\Http\Controllers;

use App\Services\ReceiptSettingService;
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

        // 更新処理
        ReceiptSettingService::updateReceiptSetting($receipt_setting, $request);

        return redirect()
            ->route('receipt_settings.show', compact('receipt_setting'))
            ->with('success', '自社情報を更新しました。');
    }
}
