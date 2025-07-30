<x-receipt-plain>

  <!-- 領収書本体 -->
  {{-- <div class="print-area bg-[#f2f2f2] mx-auto p-28 max-w-[794px] h-[1123px] w-full text-[10px]"> --}}
  <div class="print-area bg-white mx-auto p-28 max-w-[794px] h-[1123px] w-full text-[10px]">
      <!-- タイトル -->
      <div class="text-[16px] font-bold border-b-[3px] border-gray-600 pb-1 w-full mb-8">領収書</div>

      <!-- 上部情報 -->
      <div class="flex justify-between mb-8">
          <div class="font-bold text-[12px] mt-20">{{ $receipt->customerName->name }} 様</div>
          <div class="text-[10px] text-right leading-[1.6]">
              <p>{{ $receipt->issued_at }}</p>
              <p>{{ $receipt->postal_code }}</p>
              <p>{{ $receipt->address_line1 }}</p>
              <p>{{ $receipt->address_line2 }}</p>
              <p>{{ $receipt->issuer_name }}</p>
              <p>登録番号：{{ $receipt->issuer_number }}</p>
              <p>TEL：{{ $receipt->tel_fixed }}</p>
              <p>MOBILE：{{ $receipt->tel_mobile }}</p>
              <p class="mt-1">担当：{{ $receipt->responsible_name }}</p>
          </div>
      </div>

      <!-- 金額＆但し書き＆印紙欄 -->
      <div class="flex justify-between items-start mb-8">
          <!-- 金額と但し書き -->
          <div class="text-[10px] leading-[1.6]">
              <!-- 金額 -->
              <div id="total_display"
                  class="inline-block bg-gray-600 text-white px-8 py-1 rounded text-2xl font-bold mb-2">
                  ¥{{ number_format($receipt->total) }}
              </div>

              <!-- 但し書き -->
              <div class="font-bold">
                  但し、お弁当代<span id="receipt_note">{{ $receipt->receipt_note }}</span>分として、上記正に領収いたしました。<br>
                  <span class="text-xs">
                      {{ $receipt->paymentMethod->name }}支払い
                  </span>
              </div>
          </div>

          <!-- 印紙欄 -->
          <div class="border border-dashed border-gray-600 w-40 h-20 text-center flex items-center justify-center ml-4 shrink-0">
              印紙
          </div>
      </div>


      <!-- 明細テーブル -->
      <div class="text-[10px] mb-8">
          <h2 class="mb-1 font-bold text-xs">領収明細</h2>
          <table class="w-full border-collapse border-black text-left">
              <thead>
                  <tr class="bg-gray-300">
                      <th class="w-[70%] border border-black px-1 py-[2px]">品目</th>
                      <th class="w-[7%] border border-black px-1 py-[2px]">数量</th>
                      <th class="w-[9%] border border-black px-1 py-[2px]">単価</th>
                      <th class="w-[14%] border border-black px-1 py-[2px]">金額</th>
                  </tr>
              </thead>
              <tbody>
                  {{-- レコード入力 --}}
                  @for($i = 0; $i < 15; $i++)
                  <tr>
                      {{-- 品目（選択肢はJSで切り替え） --}}
                      <td class="border border-black px-1 py-[2px] {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                          {!! $receipt->bentoDetails[$i]->bento_name ?? '&nbsp;' !!}
                      </td>
                      {{-- 数量 --}}
                      <td class="text-right border border-black px-1 py-[2px] {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                          {{-- `number_format`がnullだとPHPエラーになるため条件分岐で表示 --}}
                          {!! isset($receipt->bentoDetails[$i]) && $receipt->bentoDetails[$i]->bento_quantity !== null
                              ? number_format($receipt->bentoDetails[$i]->bento_quantity)
                              : '&nbsp;' !!}
                      </td>
                      {{-- 単価(自動計算) --}}
                      <td class="text-right border border-black px-1 py-[2px] {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                          {!! isset($receipt->bentoDetails[$i]) && $receipt->bentoDetails[$i]->unit_price !== null
                              ? number_format($receipt->bentoDetails[$i]->unit_price)
                              : '&nbsp;' !!}
                      </td>
                      {{-- 金額(自動計算) --}}
                      <td class="text-right border border-black px-1 py-[2px] {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                          {!! isset($receipt->bentoDetails[$i]) && $receipt->bentoDetails[$i]->amount !== null
                              ? number_format($receipt->bentoDetails[$i]->amount)
                              : '&nbsp;' !!}
                      </td>
                  </tr>
                  @endfor
                  <!-- 小計・消費税・合計 -->
                  {{-- 小計(自動計算) --}}
                  <tr>
                      <td class=""></td>
                      <td colspan="2" class="border border-black font-bold px-1 py-[2px] bg-gray-600 text-white">小計</td>
                      <td class="border border-black text-right px-1 py-[2px]">
                          {{ number_format($receipt->subtotal) }}
                      </td>
                  </tr>
                  {{-- 消費税(自動計算) --}}
                  <tr>
                      <td class="px-1 border-l-0 border-b-0"></td>
                      <td colspan="2" class="border border-black font-bold px-1 py-[2px]">消費税</td>
                      <td class="border border-black text-right px-1">
                          {{ number_format($receipt->tax_total) }}
                      </td>
                  </tr>
                  {{-- 合計(自動計算) --}}
                  <tr>
                      <td class="px-1 border-l-0 border-b-0"></td>
                      <td colspan="2" class="border border-black font-bold px-1 py-[2px] bg-gray-600 text-white">合計</td>
                      <td class="border border-black text-right px-1">
                          {{ number_format($receipt->total) }}
                      </td>
                  </tr>
              </tbody>
          </table>
      </div>

      <!-- 備考 -->
      <div class="text-[10px]">
          @if($receipt->remarks)
              <h2 class="mb-1 font-bold text-xs">備考：</h2>
              {{ $receipt->remarks }}
          @endif
      </div>
  </div>

</x-receipt-plain>