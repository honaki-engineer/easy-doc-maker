<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest;
use App\Models\PaymentMethod;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Spatie\Browsershot\Browsershot;
use ZipArchive;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 検索情報の取得
        $searches = [
            'search_issued_at' => $request->search_issued_at,
            'search_customer_name' => $request->search_customer_name,
        ];

        // ✅ 領収書の取得
        $receipts = $user
            ->receipts()
            ->search($searches) // 検索処理
            ->orderBy('issued_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

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

    // ⭐️ PDFダウンロード
    public function downloadPdf($id)
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

        // ✅ BladeテンプレートをHTML文字列に変換して、PDF生成に使うための処理
        $html = view('pdf.receipt', compact('receipt'))->render();

        // ✅ PDFファイルの保存先のフルパスを生成
        $customerName = preg_replace('/[^\w\-]/u', '_', $receipt->customer_name);
        $pdfPath = storage_path("app/public/receipt_{$customerName}_{$id}.pdf");

        // ✅ Tailwind対応のPDF（背景・影も含む）としてA4で保存
        Browsershot::html($html) // `$html`でPDFを作る準備
            ->setNodeBinary('/usr/local/bin/node') // MAMPなどNodeパス必要
            ->setIncludePath('/usr/local/bin') // Puppeteer(画面なしブラウザ)パス
            ->format('A4')
            ->showBackground() // Tailwindのbg色やshadowが表示されるように
            ->save($pdfPath);

        // ✅ ダウンロード後に削除
        return response()->download($pdfPath)->deleteFileAfterSend();
    }

    // ⭐️ PDF一括ダウンロード
    public function bulkDownload(Request $request)
    {
        // ✅ request情報の取得
        $ids = $request->input('receipt_ids', []);

        // ✅ エラー時のメッセージ
        if(empty($ids)) {
            return back()->with('error', 'PDFを出力する領収書を選択してください。');
        }

        // ✅ ユーザー情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ 複数の領収書をPDFに変換し、一時保存してパスを配列にまとめる
        $pdfPaths = [];
        foreach($ids as $id) {
            // 🔹 領収書情報の取得
            $receipt = $user->receipts()
                ->with(['paymentMethod', 'bentoDetails'])
                ->findOrFail($id);

            // 🔹 領収書のHTMLを生成し、そのPDFの保存先パスを設定
            $html = view('pdf.receipt', compact('receipt'))->render();
            $customerName = preg_replace('/[^\w\-]/u', '_', $receipt->customer_name);
            $pdfPath = storage_path("app/public/receipt_{$customerName}_{$id}.pdf");

            // 🔹 HTML文字列`$html`を「A4サイズ・背景付き」のPDFに変換し、`$pdfPath`の場所に保存
            Browsershot::html($html)
                ->setNodeBinary('/usr/local/bin/node')
                ->setIncludePath('/usr/local/bin')
                ->format('A4')
                ->showBackground()
                ->save($pdfPath);

            $pdfPaths[] = $pdfPath;
        }

        // ✅ ZIP作成
        $zipName = 'receipts_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path("app/public/{$zipName}");

        // ✅ PHPのZipArchiveクラスを使ってZIPファイルを操作するためのインスタンスを生成
        $zip = new ZipArchive;

        // ✅ PDFをまとめてZIPファイルに詰めて保存
        if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach($pdfPaths as $pdf) {
                $zip->addFile($pdf, basename($pdf));
            }
            $zip->close();
        }

        // ✅ 一時PDF削除
        foreach($pdfPaths as $pdf) {
            File::delete($pdf);
        }

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    // ⭐️ 印刷：PDF生成＆中継ビュー表示処理
    public function generateAndPrint($id)
    {
        // ✅ 情報の取得
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $receipt = $user
            ->receipts()
            ->with(['paymentMethod', 'bentoDetails'])
            ->findOrFail($id);

        // ✅ PDF生成
        $html = view('pdf.receipt', compact('receipt'))->render();
        $customerName = preg_replace('/[^\w\-]/u', '_', $receipt->customer_name);
        $filename = "receipt_{$customerName}_{$id}.pdf";
        $pdfPath = storage_path("app/public/tmp/{$filename}");

        Browsershot::html($html)
            ->setNodeBinary('/usr/local/bin/node')
            ->setIncludePath('/usr/local/bin')
            ->format('A4')
            ->showBackground()
            ->save($pdfPath);

        // ✅ PDF作成完了後、中継ビューへリダイレクト
        return redirect()->route('receipts.print.show', ['filename' => $filename]);
    }

    public function showPrintView($filename)
    {
        $pdfUrl = asset("storage/tmp/{$filename}");
        return view('pdf.print_redirect', compact('pdfUrl'));
    }
}
