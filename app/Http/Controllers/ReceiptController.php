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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 自社情報
        $receipt_setting = Auth::user()->receiptSettings;


        // ✅ 支払い方法情報
        // 入力された`payment_method`を受け取る
        $request_payment_method = $request->payment_method;
        // 新規入力の場合は保存 | 既存の場合は取得
        $payment_method = PaymentMethod::firstOrCreate([
            'user_id' => $user->id,
            'name' => $request_payment_method,
        ]);

        // dd((int) str_replace(',', '', $request->input('subtotal')));

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

        // 領収書_弁当テーブルへの保存
        $bentoBrands = $request->bento_brands;
        $bentoNames = $request->bento_names;
        $bentoFees = $request->bento_fees;
        $taxRates = $request->tax_rates;
        $bentoQuantities = $request->bento_quantities;
        $unitPrices = $request->unit_prices; // 税抜
        $amounts = $request->amounts; // 金額

        foreach($bentoBrands as $index => $bentoBrand) {
            if(empty($bentoBrand) && empty($bentoNames[$index])) {
                continue; // 空行はスキップ
            }


            // 🔸ブランドをfirstOrCreate（ユーザーごとに保存）
            $brand = BentoBrand::firstOrCreate([
                'user_id' => $user->id,
                'name' => $bentoBrand,
            ]);

            // 🔸ブランドに紐づけてお弁当名を保存（存在しなければ）
            BentoName::firstOrCreate([
                'user_id' => $user->id,
                'bento_brand_id' => $brand->id,
                'name' => $bentoNames[$index],
            ]);
        




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


        return redirect()->route('receipts.index')->with('success', '登録完了しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
