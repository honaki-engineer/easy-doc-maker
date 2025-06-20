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
        //
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

    
        // return redirect()->route('bentos.index')->with('success', 'お弁当を複数登録しました');
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
