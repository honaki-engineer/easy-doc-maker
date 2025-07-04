<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            領収書作成
        </h2>
    </x-slot>

    <!-- formタグ -->
    <form action="{{ route('receipts.store') }}" method="POST">
        @csrf

        <!-- 領収書の外側 -->
        <div class="bg-gray-200 py-8 print:bg-white print:py-8">

            {{-- フラッシュメッセージ --}}
            @if(session('success'))
                <div class="overflow-x-auto max-w-[794px] mx-auto overflow-auto">
                        <div id="flash-message"
                            class="inline-block bg-green-100 text-green-800 rounded px-4 py-2 mb-4 transition-opacity duration-1000">
                            {{ session('success') }}
                        </div>
                </div>
            @endif

            {{-- バリデーションエラーメッセージ --}}
            <div class="mx-auto max-w-[794px] w-full text-[10px] mb-2">
                <x-input-error :messages="$errors->get('issued_at')" class="mt-2" />
                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                <x-input-error :messages="$errors->get('receipt_note')" class="mt-2" />
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                <x-input-error :messages="$errors->get('bento_brands')" class="mt-2" />
                <x-input-error :messages="$errors->get('bento_names')" class="mt-2" />
                <x-input-error :messages="$errors->get('bento_fees')" class="mt-2" />
                <x-input-error :messages="$errors->get('tax_rates')" class="mt-2" />
                <x-input-error :messages="$errors->get('bento_quantities')" class="mt-2" />
                <x-input-error :messages="$errors->get('unit_prices')" class="mt-2" />
                <x-input-error :messages="$errors->get('amounts')" class="mt-2" />
                <x-input-error :messages="$errors->get('subtotal')" class="mt-2" />
                <x-input-error :messages="$errors->get('tax_total')" class="mt-2" />
                <x-input-error :messages="$errors->get('total')" class="mt-2" />
                <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
            </div>


            <!-- 領収書本体 -->
            <div class="print-area bg-[#f2f2f2] border border-gray-400 mx-auto p-20 max-w-[794px] w-full h-[1123px] text-[10px]">
                <!-- タイトル -->
                <div class="text-[16px] font-bold border-b-[3px] border-gray-600 pb-1 w-full mb-8">領収書</div>

                <!-- 上部情報 -->
                <div class="flex justify-between mb-8">
                    <div class="font-bold text-[12px] mt-20">
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="text-xs w-80 px-1 py-1 border border-gray-300 rounded"> 様
                    </div>
                    <div class="text-[10px] text-right leading-[1.6]">
                        {{-- 日付 --}}
                        <p><input type="date" name="issued_at" value="{{ old('issued_at') }}" id="date" class="text-xs w-[100px] px-1 py-[1px] border border-gray-300 rounded cursor-pointer"></p>
                        <p>{{ $receipt_setting->postal_code }}</p>
                        <p>{{ $receipt_setting->address_line1 }}</p>
                        <p>{{ $receipt_setting->address_line2 }}</p>
                        <p>{{ $receipt_setting->issuer_name }}</p>
                        <p>登録番号：{{ $receipt_setting->issuer_number }}</p>
                        <p>TEL：{{ $receipt_setting->tel_fixed }}</p>
                        <p>MOBILE：{{ $receipt_setting->tel_mobile }}</p>
                        <p class="mt-1 font-semibold">担当：{{ $receipt_setting->responsible_name }}</p>
                    </div>
                </div>

                <!-- 金額＆但し書き＋印紙欄 -->
                <div class="flex justify-between items-start mb-8">
                    <!-- 金額と但し書き -->
                    <div class="text-[10px] leading-[1.6]">
                        <!-- 金額 -->
                        <div id="total_display"
                            class="inline-block bg-gray-600 text-white px-8 py-1 rounded text-2xl font-bold mb-2">
                            {{-- 自動計算された合計金額が入る --}}
                        </div>

                        <!-- 但し書き -->
                        <div>
                            但し、お弁当代 <span id="receipt_note" class="font-bold"></span> 分として、上記正に領収いたしました。<br>
                            <input type="hidden" name="receipt_note" value="{{ old('receipt_note') }}" id="receipt_note_input">
                            <span class="font-bold">
                                <input list="payment_methods" name="payment_method" value="{{ old('payment_method') }}" class="text-xs w-48 px-1 py-[2px] border border-gray-300 rounded"> 支払い
                                <datalist id="payment_methods">
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ $payment_method->name }}">
                                    @endforeach
                                </datalist>
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
                            <tr>
                                <th class="w-[12%] border border-black px-1 py-[2px] text-orange-700 bg-orange-300">ブランド</th>
                                <th class="w-[35%] border border-black px-1 py-[2px] bg-gray-300">品目</th>
                                <th class="w-[11%] border border-black px-1 py-[2px] text-orange-700 bg-orange-300">税込</th>
                                <th class="w-[8%] border border-black px-1 py-[2px] text-orange-700 bg-orange-300">消費税</th>
                                <th class="w-[8%] border border-black px-1 py-[2px] bg-gray-300">数量</th>
                                <th class="w-[11%] border border-black px-1 py-[2px] bg-gray-300">単価</th>
                                <th class="w-[15%] border border-black px-1 py-[2px] bg-gray-300">金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- レコード入力 --}}
                            @for($i = 0; $i < 15; $i++)
                            <tr>
                                {{-- ブランド --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}">
                                    <input list="brand_list_{{ $i }}" 
                                        name="bento_brands[]" 
                                        value="{{ old('bento_brands.' . $i) }}"
                                        class="brand_input text-xs w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}" 
                                        data-index="{{ $i }}">
                                    <datalist id="brand_list_{{ $i }}">
                                        @foreach($bento_brands as $brand)
                                            <option value="{{ $brand->name }}">
                                        @endforeach
                                    </datalist>
                                </td>
                                {{-- 品目（選択肢はJSで切り替え） --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                    <input list="bento_list_{{ $i }}" 
                                        name="bento_names[]" 
                                        value="{{ old('bento_names.' . $i) }}"
                                        class="bento_input text-xs w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}" 
                                        data-index="{{ $i }}">
                                    <datalist id="bento_list_{{ $i }}">
                                        {{-- JavaScriptで動的に入れ替え --}}
                                    </datalist>
                                </td>
                                {{-- 税込 --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}">
                                    <input name="bento_fees[]" 
                                        value="{{ old('bento_fees.' . $i) }}"
                                        type="text"
                                        class="bento_fee_input text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}">
                                </td>
                                {{-- 消費税 --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}">
                                    <input name="tax_rates[]" 
                                        value="{{ old('tax_rates.' . $i) }}"
                                        type="text"
                                        class="tax_rate_input text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-orange-100' : 'bg-orange-200' }}">
                                </td>
                                {{-- 数量 --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                    <input name="bento_quantities[]" 
                                        value="{{ old('bento_quantities.' . $i) }}"
                                        type="number"
                                        class="bento_quantity_input text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                </td>
                                {{-- 単価(自動計算) --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                    <input type="text" 
                                        name="unit_prices[]"
                                        value="{{ old('unit_prices.' . $i) }}"
                                        class="unit_price_result text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}" 
                                        readonly>
                                </td>
                                {{-- 金額(自動計算) --}}
                                <td class="border border-black px-1 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                                    <input type="text" 
                                        name="amounts[]"
                                        value="{{ old('amounts.' . $i) }}"
                                        class="amount_result text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}" 
                                        readonly>
                                </td>
                            </tr>
                            @endfor
                            <!-- 小計・消費税・合計 -->
                            {{-- 小計(自動計算) --}}
                            <tr>
                                <td colspan="4" class="px-1 border-l-0 border-b-0 text-orange-500">
                                    ※オレンジ色の箇所は、印刷 / DLで表示されません。
                                </td>
                                <td colspan="2" class="border border-black font-bold px-1 py-[2px] bg-gray-600 text-white">小計</td>
                                <td class="border border-black text-right px-1">
                                    <input type="text"
                                        id="subtotal" 
                                        name="subtotal"
                                        value="{{ old('subtotal.') }}"
                                        class="text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded bg-transparent" 
                                        readonly>
                                </td>
                            </tr>
                            {{-- 消費税(自動計算) --}}
                            <tr>
                                <td colspan="4" class="px-1 border-l-0 border-b-0"></td>
                                <td colspan="2" class="border border-black font-bold px-1 py-[2px]">消費税</td>
                                <td class="border border-black text-right px-1">
                                    <input type="text"
                                        id="tax_total" 
                                        name="tax_total"
                                        value="{{ old('tax_total') }}"
                                        class="text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded bg-transparent" 
                                        readonly>
                                </td>
                            </tr>
                            {{-- 合計(自動計算) --}}
                            <tr>
                                <td colspan="4" class="px-1 border-l-0 border-b-0"></td>
                                <td colspan="2" class="border border-black font-bold px-1 py-[2px] bg-gray-600 text-white">合計</td>
                                <td class="border border-black font-bold text-right px-1">
                                    <input type="text"
                                        id="total"
                                        name="total"
                                        value="{{ old('total') }}"
                                        class="text-xs text-right w-full px-1 py-[2px] border border-gray-300 rounded bg-transparent font-bold"
                                        readonly>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 備考 -->
                <div class="text-[10px]">
                    <h2 class="mb-1 font-bold text-xs">備考：</h2>
                    <textarea name="remarks" 
                            class="w-full border text-xs rounded"
                            placeholder="例) 軽減税率8%対象">{{ old('remarks') }}</textarea>
                </div>
            </div>

            {{-- ボタンエリア --}}
            <div class="w-full mt-8 flex gap-4 justify-center">
                <button type="submit" name="action" value="store_and_index"
                    class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                    登録して「終了」
                </button>

                <button type="submit" name="action" value="store_and_create"
                    class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                    登録して「次の作成へ」
                </button>
            </div>

        </div>
    </form>

<style>
    /* 数字入力欄のスピンボタンを非表示にする */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>    

<script>
    document.addEventListener('DOMContentLoaded', () => {
    // ⭐️ ブランド、品目
        // ✅ Laravelから受け取ったブランドごとの品目リスト
        const brandBentoMap = @json($bento_brands->mapWithKeys(function($brand) {
            return [$brand->name => $brand->bentoNames->pluck('name')];
        }));

        // ✅ ブランド入力に応じて、対応する品目欄の有効化と候補リストの切り替えを行う処理
        document.querySelectorAll('.brand_input').forEach((brandInput) => {
            // 🔹 情報取得
            const index = brandInput.dataset.index;
            const bentoInput = document.querySelector(`.bento_input[data-index="${index}"]`);
            const datalist = document.getElementById(`bento_list_${index}`);

            const selectedBrand = brandInput.value.trim(); // `old()`で復元された値を取得
            const bentoNames = brandBentoMap[selectedBrand];

            // 🔹 old()対応：ブランドが入力or選択されていて登録済みなら`datalist`と品目入力を復元
            if(selectedBrand !== '') {
                bentoInput.disabled = false;
                datalist.innerHTML = '';

                if (Array.isArray(bentoNames)) {
                    bentoNames.forEach(name => {
                        const option = document.createElement('option');
                        option.value = name;
                        datalist.appendChild(option);
                    });
                }
            } else {
                bentoInput.disabled = true;
            }

            // 🔹 ブランドを入力したら、それに紐づくお弁当候補を表示する
            brandInput.addEventListener('input', function () {
                const selectedBrand = this.value.trim();
                const bentoNames = brandBentoMap[selectedBrand];

                // 🔸 品目欄を初期化
                bentoInput.value = '';
                datalist.innerHTML = '';

                // 🔸 ブランド未入力 ⇒ 品目入力不可
                if (selectedBrand === '') {
                    bentoInput.disabled = true;
                    return;
                }

                // 🔸 ブランド入力あり ⇒ 品目入力可能
                bentoInput.disabled = false;

                // 🔸 登録済みブランド ⇒ 候補を出す
                if (Array.isArray(bentoNames)) {
                    bentoNames.forEach(name => {
                        const option = document.createElement('option');
                        option.value = name;
                        datalist.appendChild(option);
                    });
                }
            });
        });



    // ⭐️ ブランド
        // ✅ 選択or新規入力後際クリック時に、全体の文章をポップアップ表示
        document.querySelectorAll('.brand_input').forEach(input => {
            // 🔹 フォーカス時に、入力済みテキストをポップアップで見やすく表示する
            input.addEventListener('focus', function () { //  input要素がフォーカス(=クリックされたりタブキーで選択された)時
                // 🔸 現在の入力欄に入力されている文字列から、前後の空白を取り除いたものを value に代入
                const value = this.value.trim();

                // 🔸 空欄でなければ、入力値をポップアップ表示し、フォーカスが外れたら自動で削除する
                if(value !== '') {
                    const popup = document.createElement('div');
                    popup.textContent = value;
                    popup.style.position = 'absolute';
                    popup.style.backgroundColor = 'white';
                    popup.style.border = '1px solid gray';
                    popup.style.padding = '4px 8px';
                    popup.style.fontSize = '12px';
                    popup.style.zIndex = 1000;

                    // 🔹🔹 入力欄の位置を取得して、ポップアップの表示位置を決める
                    const rect = this.getBoundingClientRect(); // getBoundingClientRect = 入力欄(this)の画面上での位置やサイズ(top, left, width, heightなど)を取得する
                    popup.style.top = `${rect.top - 30 + window.scrollY}px`;
                    popup.style.left = `${rect.left + window.scrollX}px`;

                    popup.classList.add('brand_popup');
                    document.body.appendChild(popup);

                    // 🔹🔹 入力欄からフォーカスが外れたときにポップアップを削除する
                    this.addEventListener('blur', () => { // blur = 「フォーカスが外れた時」(例：他の場所をクリックした時)に発生するイベント
                        popup.remove();
                    }, { once: true });
                }
            });
        });

        
    // ⭐️ 品目
        // ✅ 選択or新規入力後、クリック時に全文ポップアップ表示
        document.querySelectorAll('.bento_input').forEach(input => {
            input.addEventListener('focus', function () {
                const value = this.value.trim();
                if (value !== '') {
                    const popup = document.createElement('div');
                    popup.textContent = value;
                    popup.style.position = 'absolute';
                    popup.style.backgroundColor = 'white';
                    popup.style.border = '1px solid gray';
                    popup.style.padding = '4px 8px';
                    popup.style.fontSize = '12px';
                    popup.style.zIndex = 1000;

                    const rect = this.getBoundingClientRect();
                    popup.style.top = `${rect.top - 30 + window.scrollY}px`;
                    popup.style.left = `${rect.left + window.scrollX}px`;

                    popup.classList.add('bento_popup');
                    document.body.appendChild(popup);

                    this.addEventListener('blur', () => {
                        popup.remove();
                    }, { once: true });
                }
            });
        });


    // ⭐️ 税込
        // ✅ 数字にカンマをつける
        document.querySelectorAll('.bento_fee_input').forEach(input => {
            input.addEventListener('input', function () {
                const raw = this.value.replace(/,/g, ''); // カンマを削除

                if(raw === '') return; // 空欄ならスキップ

                if(!isNaN(raw)) {
                    this.value = Number(raw).toLocaleString(); // カンマ付きに変換
                }
            });
        });


    // ⭐️ 消費税
        document.querySelectorAll('.tax_rate_input').forEach(input => {
            input.addEventListener('blur', function () {
                let raw = this.value.replace(/%/g, '').trim();

                if (!isNaN(raw) && raw !== '') {
                    this.value = raw + '%';
                } else {
                    this.value = '';
                }
            });
        });


    // ⭐️ 単価(自動計算)
        // ✅ 税込価格と税率をもとに、税抜価格を自動計算して反映する
        document.querySelectorAll('.bento_fee_input, .tax_rate_input').forEach(input => {
            // 🔹 税込 or 消費税率の入力が終わったタイミングで、行単位の金額と全体の合計を再計算する
            input.addEventListener('blur', function () {
                const $row = this.closest('tr') || this.closest('td').parentElement;
                if(!$row) return;

                updateUnitPriceResult($row); // 税込価格と税率から税抜(単価)を計算し、税抜(単価)に反映する
                updateAmountResult($row); // 数量と税抜から金額を計算し、amount_result(金額)に表示する
                updateSubtotal(); // 小計を計算して表示する
                updateTaxTotal(); // 消費税の合計を計算して表示する
                updateTotal(); // 合計
                updateProgateeceipt_note(); // 但し書きの表示
            });
        });


        // ✅ 税込価格と税率から税抜(単価)を計算し、「税抜(単価)」に反映する
        function updateUnitPriceResult($row) {
            const bentoFeeInput = $row.querySelector('.bento_fee_input'); // 税込
            const taxRateInput = $row.querySelector('.tax_rate_input'); // 消費税
            const unitPriceResult = $row.querySelector('.unit_price_result'); // 税抜

            // 🔹 文字列→数値
            const price = parseFloat(bentoFeeInput?.value.replace(/,/g, '').trim());
            const taxRate = parseFloat(taxRateInput?.value.replace('%', '').trim());

            // 🔹 税込価格が数値なら、税率に応じて税抜価格を計算して表示し、数値でなければ空欄にする
            if(!isNaN(price)) { // NaN = Not a Number
                let untaxed = price;

                // 🔸 消費税未記入の場合
                if(!isNaN(taxRate) && taxRate !== 0) {
                    untaxed = price / (1 + taxRate / 100);
                }

                unitPriceResult.value = Math.round(untaxed).toLocaleString();
            } else {
                unitPriceResult.value = '';
            }
        }


    // ⭐️ 金額
        // ✅ 数量や単価が入力されたときに、金額を自動計算して反映する
        document.querySelectorAll('.bento_quantity_input, .unit_price_result').forEach(input => {
            input.addEventListener('input', function () {
                const $row = this.closest('tr') || this.closest('td')?.parentElement;
                if(!$row) return;

                updateAmountResult($row);   // 数量 × 税抜 単価 = 金額
                updateSubtotal();          // 金額を集計して小計へ
                updateTaxTotal();          // 税込×数量 - 税抜×数量 = 消費税合計
                updateTotal();             // 合計
                updateReceiptNote();        // 但し書きの表示
            });
        });


        // ✅ 数量と税抜から金額を計算し、amount_result(金額)に表示する
        function updateAmountResult($row) {
            const quantityInput = $row.querySelector('.bento_quantity_input'); // 数
            const unitPriceResult = $row.querySelector('.unit_price_result'); // 税抜
            const amountResult = $row.querySelector('.amount_result'); // 金額

            // 🔹 文字列→数値
            const quantity = parseFloat(quantityInput?.value);
            const price = parseFloat(unitPriceResult?.value.replace(/,/g, ''));

            // 🔹 数量と単価が数値なら金額を計算して表示し、どちらかが未入力なら空にする
            if(!isNaN(quantity) && !isNaN(price)) {
                const total = quantity * price;
                amountResult.value = Math.round(total).toLocaleString();
            } else {
                amountResult.value = '';
            }
        }


    // ⭐️ 小計
        // ✅ 小計を計算して表示する
        function updateSubtotal() {
            let subtotal = 0;

            // 🔹 金額(amount_result)の各金額をカンマ除去＆数値化して、合計(subtotal)に加える
            document.querySelectorAll('.amount_result').forEach(input => {
                const value = input.value.replace(/,/g, '').trim();
                const num = parseFloat(value);
                if(!isNaN(num)) {
                    subtotal += num;
                }
            });

            // 🔹 小計欄に反映
            const subtotalInput = document.getElementById('subtotal'); // 小計
            if(subtotalInput) {
                subtotalInput.value = subtotal.toLocaleString();
            }
        }


    // ⭐️ 消費税の合計
        // ✅ (税込金額×数量)-(税抜金額×数量)=消費税 → これの合計を計算・表示する
        function updateTaxTotal() {
            let taxTotal = 0;

            // 🔹 各行で「(税込金額×数量)-(税抜金額×数量)=消費税」計算を行い、合計に加算している
            document.querySelectorAll('tr').forEach(row => {
                const bentoFeeInput = row.querySelector('.bento_fee_input'); // 税込
                const quantityInput = row.querySelector('.bento_quantity_input'); // 数量
                const amountResult = row.querySelector('.amount_result'); // 金額(税抜×数量)

                if(!bentoFeeInput || !quantityInput || !amountResult) return;

                // 🔸 数値化
                const price = parseFloat(bentoFeeInput.value.replace(/,/g, '').trim());
                const quantity = parseFloat(quantityInput.value);
                const amount = parseFloat(amountResult.value.replace(/,/g, '').trim());// 金額(税抜×数量)

                // 🔸 (税込×数量)-(金額 = 税抜×数量)を引いて、消費税分を合計
                if(!isNaN(price) && !isNaN(quantity) && !isNaN(amount)) {
                    const tax = (price * quantity) - amount;
                    if(!isNaN(tax)) {
                        taxTotal += tax;
                    }
                }
            });

            // 🔹 `tax_total`に表示
            const taxInput = document.getElementById('tax_total');
            if(taxInput) {
                taxInput.value = Math.round(taxTotal).toLocaleString();
            }
        }


    // ⭐️ 合計
        // ✅ 合計の計算
        function updateTotal() {
            let total = 0;

            // 🔹 各行の「税込金額 × 数量」を合計して、合計金額を計算する
            document.querySelectorAll('tr').forEach(row => {
                const feeInput = row.querySelector('.bento_fee_input');
                const quantityInput = row.querySelector('.bento_quantity_input');

                if(!feeInput || !quantityInput) return;

                // 🔸 数値化
                const fee = parseFloat(feeInput.value.replace(/,/g, '').trim());
                const quantity = parseFloat(quantityInput.value);

                // 🔸 合計を計算
                if(!isNaN(fee) && !isNaN(quantity)) {
                    total += fee * quantity;
                }
            });

            // 🔹 表示形式を整える
            const totalValue = Math.round(total).toLocaleString();

            // 🔹 合計を表示
            const totalInput = document.getElementById('total');
            if(totalInput) {
                totalInput.value = totalValue;
            }

            // 🔹 「但し書き」上部にも反映
            const totalDiv = document.getElementById('total_display');
            if(totalDiv) {
                totalDiv.textContent = `¥${totalValue}`;
            }
        }


    // ⭐️ 但し書き
        // ✅ 但し書きの表示
        function updateReceiptNote() {
            const map = {};

            // 🔹 同じ税込金額ごとに数量を集計して、map に「金額: 合計数量」の形式でまとめる
            document.querySelectorAll('tr').forEach(row => {
                const feeInput = row.querySelector('.bento_fee_input'); // 税込
                const quantityInput = row.querySelector('.bento_quantity_input'); // 数量

                if(!feeInput || !quantityInput) return;

                // 🔸 数値に変換
                const fee = parseFloat(feeInput.value.replace(/,/g, '').trim());
                const quantity = parseInt(quantityInput.value);

                // 🔸税込価格(fee)ごとに、数量(quantity)を集計してmapに蓄積する
                if(!isNaN(fee) && !isNaN(quantity)) {
                    const key = fee.toFixed(0); // 小数点なし文字列
                    map[key] = (map[key] || 0) + quantity;
                }
            });

            // 🔹 出力用テキスト整形
            const resultText = Object.entries(map) // map オブジェクトを[税込金額, 数量]の配列に変換して扱いやすく
                .map(([fee, qty]) => `¥${Number(fee).toLocaleString()} × ${qty}個`)
                .join(', ');

            // 🔹 表示用の<span>と送信用の<input>に、同じ但し書きテキストを反映
            const target = document.getElementById('receipt_note');
            const hiddenInput = document.getElementById('receipt_note_input');
            if(target && hiddenInput) {
                target.textContent = resultText;
                hiddenInput.value = resultText;
            }
        }


    // ⭐️ 最後に計算処理を一度すべて実行して、バリデーション後でも反映されるようにする
        updateSubtotal();
        updateTaxTotal();
        updateTotal();
        updateReceiptNote();
    });


    // ⭐️ フラッシュメッセージ
        // ✅ フラッシュメッセージを10秒後にフェードアウトし、さらに2秒後に削除する
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if(flashMessage) {
                flashMessage.classList.add('opacity-0'); // フェードアウト
                setTimeout(() => flashMessage.remove(), 2000); // 2秒後に flashMessage というHTML要素を DOM(画面上)から完全に削除
            }
        }, 10000); // 10秒後にフェード開始
    
    // ⭐️ 日付クリック有効範囲を全域にする
        document.getElementById("date").addEventListener("click", function() {
            this.showPicker(); // Chrome でカレンダーを開く
        });
</script>
</x-app-layout>
