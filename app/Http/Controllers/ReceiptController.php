<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiptRequest;
use App\Models\PaymentMethod;
use App\Models\CustomerName;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Normalizer;
use setasign\Fpdi\Fpdi;
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

        // ✅ 顧客名の取得
        $customer_names = Auth::user()->customerNames;

        // ✅ ブランド&お弁当の取得
        $bento_brands = $user->bentoBrands()->with('bentoNames')->orderBy('id')->get();

        return view('receipts.create', compact('receipt_setting', 'payment_methods', 'customer_names', 'bento_brands'));
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

        // 🔹 顧客情報の$request & 保存or取得
        // 🔸 入力された`payment_method`を受け取る
        $request_customer_name = $request->customer_name;
        // 🔸 新規入力の場合は保存 | 既存の場合は取得
        $customer_name = CustomerName::firstOrCreate([
            'user_id' => $user->id,
            'name' => $request_customer_name,
        ]);

        // 🔹 receiptsテーブルへの保存
        $receipt = ReceiptService::storeReceipt($payment_method, $customer_name, $request, $receipt_setting);
        
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
            ->with(['paymentMethod', 'bentoDetails'])
            ->findOrFail($id);

        // ✅ BladeテンプレートをHTML文字列に変換して、PDF生成に使うための処理
        $html = view('pdf.receipt', compact('receipt'))->render();
    
        // ✅ `$receipt->customerName->name`のサニタイズ
        if(class_exists('Normalizer')) { // 正規化して“が/ぱ などの結合文字問題”を解消
            $normalizeCustomerName = Normalizer::normalize($receipt->customerName->name, Normalizer::FORM_C);
        }
        $customerName = preg_replace('/[^\p{L}\p{N}\-_.]+/u', '_', $normalizeCustomerName);
        
        // ✅ Browsershot保存のフルパス / DL時のファイル名
        $timestamp = now()->format('YmdHis'); // ユニークのため
        $savePdfPath = storage_path("app/public/tmp/receipt_{$id}_{$timestamp}.pdf");
        $downloadPdfName = "{$receipt->issued_at}_receipt_{$id}_{$customerName}.pdf";

        // ✅ Tailwind対応のPDF（背景・影も含む）としてA4で保存
        Browsershot::html($html) // `$html`でPDFを作る準備
            ->setNodeBinary(config('browsershot.node_binary')) // MAMPなどNodeパス必要
            ->setIncludePath(config('browsershot.include_path')) // Puppeteer(画面なしブラウザ)パス
            ->setChromePath(config('browsershot.chrome_path'))
            ->noSandbox() // 本番環境のみ
            ->format('A4')
            ->showBackground() // Tailwindのbg色やshadowが表示されるように
            ->save($savePdfPath);

        // ✅ ダウンロード後に削除
        return response()->download($savePdfPath, $downloadPdfName)->deleteFileAfterSend();
    }

    // ⭐️ PDF一括ダウンロード
    public function bulkDownload(Request $request)
    {
        // ✅ 情報の取得
        $ids = $request->input('receipt_ids', []);
        if(empty($ids)) {
            return back()->with('error', 'PDFを出力する領収書を選択してください。');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $receipts = $user->receipts()
            ->with(['paymentMethod', 'bentoDetails'])
            ->whereIn('id', $ids) // 選択されたID配列 $ids に含まれるレコードだけ
            ->orderBy('issued_at', 'desc') // 発行日が新しい順
            ->orderBy('id', 'desc') // 同じ発行日の行の並びをIDの大きい順
            ->get();

        // ✅ 複数の領収書をPDFに変換し、一時保存してパス&DLファイル名を配列にまとめる
        $pdfPaths = [];

        foreach($receipts as $receipt) {
            // 🔹 BladeテンプレートをHTML文字列に変換して、PDF生成に使うための処理
            $html = view('pdf.receipt', compact('receipt'))->render();

            // 🔹 `$receipt->customerName->name`のサニタイズ
            if(class_exists('Normalizer')) { // 正規化して“が/ぱ などの結合文字問題”を解消
                $normalizeCustomerName = Normalizer::normalize($receipt->customerName->name, Normalizer::FORM_C);
            }
            $customerName = preg_replace('/[^\p{L}\p{N}\-_.]+/u', '_', $normalizeCustomerName); // ファイル名

            // 🔹 Browsershot保存のフルパス / DL時のファイル名
            $timestamp = now()->format('YmdHis'); // ユニークのため
            $savePdfPath = storage_path("app/public/tmp/receipt_{$receipt->id}_{$timestamp}.pdf");
            $downloadPdfName = "{$receipt->issued_at}_receipt_{$receipt->id}_{$customerName}.pdf";

            // 🔹 HTML文字列`$html`を「A4サイズ・背景付き」のPDFに変換し、`$savePdfPath`の場所に一時保存
            Browsershot::html($html)
                ->setNodeBinary(config('browsershot.node_binary'))
                ->setIncludePath(config('browsershot.include_path'))
                ->setChromePath(config('browsershot.chrome_path'))
                ->noSandbox() // 本番環境のみ
                ->format('A4')
                ->showBackground()
                ->save($savePdfPath);

            // 🔹 foreach で回すための.  $savePdfPat と downloadPdfName をセット
            $pdfPaths[] = ['path' => $savePdfPath, 'fileName' => $downloadPdfName];
        }

        // ✅ ZIP作成
        $zipName = 'receipts_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path("app/public/tmp/{$zipName}");

        // ✅ PHPのZipArchiveクラスを使ってZIPファイルを操作するためのインスタンスを生成
        $zip = new ZipArchive;

        // ✅ PDFをまとめてZIPファイルに詰めて保存
        if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach($pdfPaths as $pdf) {
                $zip->addFile($pdf['path'], $pdf['fileName']);
            }
            $zip->close();
        }

        // ✅ 一時PDF削除
        foreach($pdfPaths as $pdf) {
            File::delete($pdf['path']);
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

        // ✅ BladeテンプレートをHTML文字列に変換して、PDF生成に使うための処理
        $html = view('pdf.receipt', compact('receipt'))->render();

        // ✅ ファイル名 / PDFファイルの保存先のフルパス
        $timestamp = now()->format('YmdHis'); // ユニークのため
        $fileName = "receipt_{$id}_{$timestamp}.pdf";
        $savePdfPath = storage_path("app/public/tmp/{$fileName}");

        // ✅ HTML文字列`$html`を「A4サイズ・背景付き」のPDFに変換し、`$savePdfPath`の場所に一時保存
        Browsershot::html($html)
            ->setNodeBinary(config('browsershot.node_binary'))
            ->setIncludePath(config('browsershot.include_path'))
            ->setChromePath(config('browsershot.chrome_path'))
            ->noSandbox() // 本番環境のみ
            ->format('A4')
            ->showBackground()
            ->save($savePdfPath);

        // ✅ PDF作成完了後、中継ビューへリダイレクト
        return redirect()->route('receipts.print.show', ['filename' => $fileName]);
    }

    // ⭐️ 中間ビュー
    public function showPrintView($fileName)
    {
        $pdfUrl = asset("storage/tmp/{$fileName}");
        return view('pdf.print_redirect', compact('pdfUrl'));
    }

    // ⭐️ 選択された複数の領収書をPDF化して1つに結合し、印刷用の中継画面にリダイレクトする
    public function generateAndPrintMultiple(Request $request)
    {
        // ✅ 情報を取得
        $ids = $request->input('receipt_ids', []);
        if(empty($ids)) {
            return back()->with('error', '印刷する領収書を選択してください。');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $receipts = $user->receipts()
            ->with(['paymentMethod', 'bentoDetails'])
            ->whereIn('id', $ids) // 選択されたID配列 $ids に含まれるレコードだけ
            ->orderBy('issued_at', 'desc') // 発行日が新しい順
            ->orderBy('id', 'desc') // 同じ発行日の行の並びをIDの大きい順
            ->get();

        if($receipts->count() !== count($ids)) {
            abort(404, '一部の領収書が見つかりませんでした。');
        }

        // ✅ 選択された各領収書を`HTML`から`PDF`に変換して一時保存し、ファイル名を配列にまとめている
        $fileNames = [];

        foreach($receipts as $receipt) {
            // 🔹 BladeテンプレートをHTML文字列に変換して、PDF生成に使うための処理
            $html = view('pdf.receipt', compact('receipt'))->render();

            // 🔹 ファイル名 / PDFファイルの保存先のフルパス
            $timestamp = now()->format('YmdHis'); // ユニークのため
            $fileName = "receipt_{$receipt->id}_{$timestamp}.pdf";
            $savePdfPath = storage_path("app/public/tmp/{$fileName}");

            // 🔹 HTML文字列`$html`を「A4サイズ・背景付き」のPDFに変換し、`$savePdfPath`の場所に一時保存
            Browsershot::html($html)
                ->setNodeBinary(config('browsershot.node_binary'))
                ->setIncludePath(config('browsershot.include_path'))
                ->setChromePath(config('browsershot.chrome_path'))
                ->noSandbox() // 本番環境のみ
                ->format('A4')
                ->showBackground()
                ->save($savePdfPath);

            $fileNames[] = $fileName;
        }

        // ✅ PDFファイルの絶対パス(結合用に必要)
        $pdfPaths = array_map(function ($fileName) {
            return storage_path("app/public/tmp/{$fileName}");
        }, $fileNames);

        // ✅ 結合後のPDF保存先
        $mergedFilename = 'merged_receipt_' . now()->format('YmdHis'). '.pdf';
        $mergedPath = storage_path("app/public/tmp/{$mergedFilename}");

        // ✅ 結合処理
        $this->mergePdfs($pdfPaths, $mergedPath); // $this = `generateAndPrintMultiple()メソッド`が定義されているクラス
        foreach($pdfPaths as $pdfPath) {
            @unlink($pdfPath); // 個別PDF削除
        }

        // ✅ 中継ビューへリダイレクト（iframe + 印刷）
        return redirect()->route('receipts.print.show', ['filename' => $mergedFilename]);
    }

    // ⭐️ 複数のPDFファイルを1つに結合して指定パスに保存する
    public function mergePdfs(array $pdfPaths, string $mergedPath)
    {
        // ✅ Fpdfを継承したFpdiインスタンスを作成
        $pdf = new class extends Fpdi {
            // 何も追加しなくてOK（匿名クラス）
        };

        foreach($pdfPaths as $file) {
            // 🔹 読み込むPDFファイルを指定して、ページ数などの情報を取得
            $pageCount = $pdf->setSourceFile($file);

            // 🔹 `$pdfPaths`の中にある`PDF($file)`の各ページを読み込んで、新しいPDFに1ページずつ同じサイズで追加
            for($pageNo = 1; $pageNo <= $pageCount; $pageNo++) { // 例)A領収書:1ページ、B:1,B:2、C:1
                $tplIdx = $pdf->importPage($pageNo); // 「$pageNo ページ目をコピー機に乗せる準備をする」
                $size = $pdf->getTemplateSize($tplIdx);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }
        }

        // ✅ 作ったPDFを保存
        $pdf->Output('F', $mergedPath); //（ファイルとして保存, ファイルパス）
    }
}
