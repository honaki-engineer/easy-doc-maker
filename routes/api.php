<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BentoBrand;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ お弁当登録で「ブランド」選択時、「お弁当名」を表示
Route::get('/bentos', function(Request $request) {
    $brandName = $request->input('brand');

    // ブランド名から BentoBrand モデルを取得
    $brand = BentoBrand::where('name', $brandName)->first();

    if(!$brand) {
        return response()->json([]);
    }

    // リレーションを使って bento_names を取得
    $bentoNames = $brand->bentoNames()->select('name')->distinct()->get();
        // select('name') = データベースから取り出すカラムを name だけに限定
        // distinct() = 重複する name を排除

    return response()->json($bentoNames);
});