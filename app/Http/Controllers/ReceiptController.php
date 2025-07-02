<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest;
use App\Models\PaymentMethod;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 領収書の取得
        $receipts = $user
            ->receipts()
            ->orderBy('issued_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 自社情報の取得
        $receipt_setting = ReceiptService::getReceiptSetting();

        // ✅ 支払い方法の取得
        $payment_methods = Auth::user()->paymentMethods;

        // ✅ ブランド&お弁当の取得
        $bento_brands = $user->bentoBrands()->with('bentoNames')->get();

        return view('receipts.create', compact('receipt_setting', 'payment_methods', 'bento_brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceiptRequest $request)
    {
        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ ----- receiptsテーブルへの保存 -----
        // 🔹 自社情報の取得
        $receipt_setting = ReceiptService::getReceiptSetting();

        // 🔹 支払い方法情報の$request & 保存or取得
        // 🔸 入力された`payment_method`を受け取る
        $request_payment_method = $request->payment_method;
        // 🔸 新規入力の場合は保存 | 既存の場合は取得
        $payment_method = PaymentMethod::firstOrCreate([
            'user_id' => $user->id,
            'name' => $request_payment_method,
        ]);

        // 🔹 receiptsテーブルへの保存
        $receipt = ReceiptService::storeReceipt($payment_method, $request, $receipt_setting);
        
        // ✅ ----- 領収書_弁当テーブルへの保存 -----
        // 🔹 $request情報を変数へ入れる
        $bentoBrands = $request->bento_brands;
        $bentoNames = $request->bento_names;
        $bentoFees = $request->bento_fees;
        $taxRates = $request->tax_rates;
        $bentoQuantities = $request->bento_quantities;
        $unitPrices = $request->unit_prices; // 税抜
        $amounts = $request->amounts; // 金額
        
        // 🔹 receipt_bento_detailsテーブルへの保存
        ReceiptService::storeReceiptBentoDetails($bentoBrands, $user, $bentoNames, $receipt, $bentoFees, $taxRates, $bentoQuantities, $unitPrices, $amounts);

        // ✅ リダイレクトの分岐
        if($request->action === 'store_and_create') {
            return redirect()->route('receipts.create')->with('success', '領収書の登録完了しました。続けて作成可能です。');
        } elseif($request->action === 'store_and_index') {
            return redirect()->route('receipts.index')->with('success', '領収書の登録完了しました。');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 領収書の取得
        $receipt = $user
            ->receipts()
            ->with('paymentMethod') // リレーション
            ->with('bentoDetails') // リレーション
            ->findOrFail($id);

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $receipt = $user->receipts()->findOrFail($id);
        $receipt->delete();

        return redirect()
            ->route('receipts.index')
            ->with('success', "領収書を削除しました。");
    }
}
