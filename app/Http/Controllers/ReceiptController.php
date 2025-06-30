<?php

namespace App\Http\Controllers;

use App\Models\BentoBrand;
use App\Models\BentoName;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use App\Models\ReceiptBentoDetail;
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

        // 自社情報
        $receipt_setting = Auth::user()->receiptSettings;
        // 支払い方法
        $payment_methods = Auth::user()->paymentMethods;
        // ブランド&お弁当
        $bento_brands = $user->bentoBrands()->with('bentoNames')->get();

        return view('receipts.create', compact('receipt_setting', 'payment_methods', 'bento_brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 自社情報の取得
        $receipt_setting = Auth::user()->receiptSettings;


        // ✅ 支払い方法情報
        // 🔹 入力された`payment_method`を受け取る
        $request_payment_method = $request->payment_method;
        // 🔹 新規入力の場合は保存 | 既存の場合は取得
        $payment_method = PaymentMethod::firstOrCreate([
            'user_id' => $user->id,
            'name' => $request_payment_method,
        ]);


        // ✅ receiptsテーブルへの保存
        $receipt = Receipt::create([
            'user_id' => Auth::id(),
            'payment_method_id' => $payment_method->id,
            'customer_name' => $request->customer_name,
            'issued_at' => $request->issued_at,
            'postal_code' => $receipt_setting->postal_code,
            'address_line1' => $receipt_setting->address_line1,
            'address_line2' => $receipt_setting->address_line2,
            'issuer_name' => $receipt_setting->issuer_name,
            'issuer_number' => $receipt_setting->issuer_number,
            'tel_fixed' => $receipt_setting->tel_fixed,
            'tel_mobile' => $receipt_setting->tel_mobile,
            'responsible_name' => $receipt_setting->responsible_name,
            'receipt_note' => $request->receipt_note,
            'subtotal' => (int) str_replace(',', '', $request->input('subtotal')),
            'tax_total' => (int) str_replace(',', '', $request->input('tax_total')),
            'total' => (int) str_replace(',', '', $request->input('total')),
            'remarks' => $request->remarks,
        ]);


        // ✅ 領収書_弁当テーブルへの保存
        // 🔹 $request情報を変数へ入れる
        $bentoBrands = $request->bento_brands;
        $bentoNames = $request->bento_names;
        $bentoFees = $request->bento_fees;
        $taxRates = $request->tax_rates;
        $bentoQuantities = $request->bento_quantities;
        $unitPrices = $request->unit_prices; // 税抜
        $amounts = $request->amounts; // 金額

        // 🔹 receipt_bento_detailsテーブルへの保存
        foreach($bentoBrands as $index => $bentoBrand) {
            if(empty($bentoBrand) && empty($bentoNames[$index])) {
                continue; // 空行はスキップ
            }

            // 🔸 ブランドをfirstOrCreate(新規入力のみ保存)
            $brand = BentoBrand::firstOrCreate([
                'user_id' => $user->id,
                'name' => $bentoBrand,
            ]);

            // 🔸 ブランドに紐づけてお弁当名をfirstOrCreate(新規入力のみ保存)
            BentoName::firstOrCreate([
                'user_id' => $user->id,
                'bento_brand_id' => $brand->id,
                'name' => $bentoNames[$index],
            ]);
        
            // 🔸 領収書_弁当テーブルへの保存
            ReceiptBentoDetail::create([
                'receipt_id' => $receipt->id,
                'bento_brand_name' => $bentoBrand,
                'bento_name' => $bentoNames[$index],
                'bento_fee' =>  (int) str_replace(',', '', $bentoFees[$index]),
                'tax_rate' =>  (int) str_replace('%', '', $taxRates[$index]),
                'bento_quantity' => $bentoQuantities[$index],
                'unit_price' =>  (int) str_replace(',', '', $unitPrices[$index] ?? 0),
                'amount' =>  (int) str_replace(',', '', $amounts[$index] ?? 0),
            ]);
        }


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
        //
    }
}
