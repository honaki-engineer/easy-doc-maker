<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            領収書作成
        </h2>
    </x-slot>

    {{-- 戻るボタン --}}
    

    <!-- 領収書本体 -->
    <div class="print-area bg-[#f2f2f2] border border-gray-400 mx-auto p-28 w-[794px] h-[1123px] text-[10px]">
        <!-- タイトル -->
        <div class="text-[16px] font-bold border-b-[3px] border-gray-600 pb-1 w-full mb-8">領収書</div>

        <!-- 上部情報 -->
        <div class="flex justify-between mb-8">
            <div class="font-bold text-[12px] mt-20">〇〇〇〇〇株式会社 様</div>
            <div class="text-[10px] text-right leading-[1.6]">
                <p>2025/6/12</p>
                <p>〒333-3333</p>
                <p>埼玉県川口市川口1-1-1</p>
                <p>川口ビル101</p>
                <p>株式会社tone</p>
                <p>登録番号T0000000000000</p>
                <p>TEL：048-123-4567</p>
                <p>MOBILE：090-1111-0000</p>
                <p class="mt-1 font-semibold">担当：本多</p>
            </div>
        </div>

        <!-- 金額 -->
        <div class="inline-block bg-gray-600 text-white px-8 py-1 rounded text-2xl font-bold mb-2">
            ¥60,480
        </div>

        <!-- 但し書き -->
        <div class="text-[10px] mb-8 leading-[1.6]">
            但し、お弁当代 <span class="font-bold">￥2,160 × 28個</span> 分として、上記正に領収いたしました。<br>
            <span class="font-bold">クレジットカード払い</span>
        </div>

        <!-- 明細テーブル -->
        <div class="text-[10px] mb-8">
            <h2 class="mb-1">領収明細</h2>
            <table class="w-full border-collapse border-black text-left">
                <thead>
                    <tr>
                        <th class="w-[65%] border border-black px-1 py-[2px]">品目</th>
                        <th class="w-[10%] border border-black px-1 py-[2px]">単価</th>
                        <th class="w-[10%] border border-black px-1 py-[2px]">数量</th>
                        <th class="w-[15%] border border-black px-1 py-[2px]">金額</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black px-1">炭焼き和牛サーロイン＆ハンバーグ弁当</td>
                        <td class="border border-black text-right px-1">88888888</td>
                        <td class="border border-black text-right px-1"></td>
                        <td class="border border-black text-right px-1"></td>
                    </tr>
                    @for($i = 0; $i < 16; $i++)
                    <tr>
                        <td class="border border-black">&nbsp;</td>
                        <td class="border border-black">&nbsp;</td>
                        <td class="border border-black">&nbsp;</td>
                        <td class="border border-black">&nbsp;</td>
                    </tr>
                    @endfor

                    <!-- 小計・消費税・合計 -->
                    <tr>
                        <td class="px-1 border-l-0 border-b-0"></td>
                        <td colspan="2" class="border border-black">小計</td>
                        <td class="border border-black text-right px-1">56,000</td>
                    </tr>
                    <tr>
                        <td class="px-1 border-l-0 border-b-0"></td>
                        <td colspan="2" class="border border-black">消費税</td>
                        <td class="border border-black text-right px-1">4,480</td>
                    </tr>
                    <tr>
                        <td class="px-1 border-l-0 border-b-0"></td>
                        <td colspan="2" class="border border-black font-bold">合計</td>
                        <td class="border border-black font-bold text-right px-1">60,480</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 備考 -->
        <div class="text-[10px]">
            備考：軽減税率8%対象
        </div>
    </div>
</x-app-layout>
