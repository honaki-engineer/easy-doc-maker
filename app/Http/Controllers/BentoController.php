<?php

namespace App\Http\Controllers;

use App\Http\Requests\BentoRequest;
use App\Services\BentoService;
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
            ->orderBy('bento_brands.id') // ブランドidでソート
                // ソートに「結合先のカラム（ブランド名）」を使っているため、上記でjoinを使う
            ->select('bento_names.*')
                // JOINすると、bento_brandsのカラム（nameなど）も一緒に取れてしまうので、それを防ぐために指定
                // bento_names のカラムだけを明示的に取得
            ->with('bentoBrand')
                // join はブランドでソートするために使う
                // with('bentoBrand') は $bento->bentoBrand を使えるようにする
            // ->get();
            ->paginate(10);

        return view('bentos.index', compact('bentos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $bento = $user->bentoNames()->find($id);
        $deletedName = $bento->name;
        $brand = $bento->bentoBrand;

        // お弁当を削除
        $bento->delete();

        // 該当ブランドにお弁当が存在しない場合ブランドも削除
        BentoService::destroyBrandIfEmpty($brand);

        return redirect()
            ->route('bentos.index')
            ->with('success', "{$deletedName}を削除しました。");
    }
}
