<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ReceiptSettingController extends Controller
{
    public function show()
    {
        $receipt_setting = Auth::user()->receiptSettings;

        return view('receipt_settings.show', compact('receipt_setting'));
    }
}
