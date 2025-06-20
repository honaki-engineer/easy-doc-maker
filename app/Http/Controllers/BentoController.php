<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BentoBrand;
use App\Models\BentoName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 弁当情報を取得
        $bentos = $user->bentoNames()
            ->join('bento_brands', 'bento_names.bento_brand_id', '=', 'bento_brands.id')
                // join(くっつけるテーブル名, 自分の外部キー, '=', 相手の主キー)
            ->orderBy('bento_brands.name') // ブランド名でソート
                // ソートに「結合先のカラム（ブランド名）」を使っているため、上記でjoinを使う
            ->select('bento_names.*')
                // JOINすると、bento_brandsのカラム（nameなど）も一緒に取れてしまうので、それを防ぐために指定
                // bento_names のカラムだけを明示的に取得
            ->with('bentoBrand')
                // join はブランドでソートするために使う
                // with('bentoBrand') は $bento->bentoBrand を使えるようにする
            ->get();

        return view('bentos.index', compact('bentos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $brands = Auth::user()->bentoBrands;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $brands = $user->bentoBrands()->get();

        return view('bentos.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $brands = $request->bento_brands;
        $names = $request->bento_names;
    
        // 複数登録ループ
        foreach($brands as $index => $brand) {
            // ブランド名（またはID）が既存かチェックして取得・なければ作成
            $brand = BentoBrand::firstOrCreate(
                ['name' => $brand, 'user_id' => $user->id],
                ['name' => $brand, 'user_id' => $user->id]
            );
    
            // お弁当登録
            BentoName::create([
                'user_id' => $user->id,
                'bento_brand_id' => $brand->id,
                'name' => $names[$index],
            ]);
        }

        return redirect()
            ->route('bentos.index')
            ->with('success', 'お弁当を登録しました。');
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
