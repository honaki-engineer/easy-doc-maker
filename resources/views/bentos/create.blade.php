<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            お弁当の登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">
                        <form action="{{ route('bentos.store') }}" method="POST">
                            @csrf
                            <div id="form-wrapper">

                                {{-- 入力フォーム1 --}}
                                <div class="form-group">
                                    <div class="container px-5 mx-auto">
                                        <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                            <p class="form-count font-semibold text-sm text-gray-700 pt-2 pb-4">【1個目】</p>
                                            <div class="flex flex-wrap -m-2">
                                                {{-- ブランド --}}
                                                <div class="p-2 w-full">
                                                    <div class="relative">
                                                        <label class="leading-7 text-sm text-gray-600">
                                                            ブランド
                                                            <span class="text-red-500 text-xs ml-1">※必須</span>
                                                        </label>
                                                        <select
                                                            name="bento_brands[]"
                                                            class="bento_brand w-full rounded border border-gray-300 text-base py-1 px-3 leading-8 outline-none text-gray-700">
                                                            <option value=""></option>
                                                            @foreach($brands as $brand)
                                                                <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- お弁当 --}}
                                                <div class="p-2 w-full">
                                                    <div class="relative">
                                                        <label class="leading-7 text-sm text-gray-600">
                                                            お弁当
                                                            <span class="text-red-500 text-xs ml-1">※必須</span>
                                                        </label>
                                                        <select name="bento_names[]" class="bento_name w-full rounded border border-gray-300 text-base py-1 px-3 leading-8 outline-none text-gray-700">
                                                            <option value=""></option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ボタンエリア --}}
                                <div id="button-group" class="text-center mt-4">
                                    <button class="mt-10 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                        登録
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    {{-- ----- ⭐️ 読み込み-------------------- --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <style>
        /* セレクトボックス本体（枠・高さ・内側の文字）を調整 */
        .select2-container--default .select2-selection--single {
            height: 2.625rem;
            padding: 0.25rem 0.75rem;
            display: flex;
            align-items: center;
            font-size: 1rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
        }

        /* セレクトボックスの右側の▼アイコンを調整 */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 0.75rem;
        }

        /* セレクトボックスの検索欄（タグ入力可など）のデザインを調整 */
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 0.375rem;
            padding: 0.25rem 0.75rem;
            border: 1px solid #d1d5db;
            font-size: 1rem;
        }

        /* 選択された文字列の表示部分のpaddingを調整 */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
        }
    </style>


    <script>
        // ----- ⭐️ Select2付きのお弁当入力フォームを複数追加・削除できるUIを実現する処理 --------------------
        $(document).ready(function () {
            // ✅ Select2 を適用する関数
            function applySelect2() {
                $('.bento_brand').select2({
                    tags: true,
                    placeholder: 'ブランドを選択または入力',
                    allowClear: false,
                    width: '100%'
                });

                $('.bento_name').select2({
                    tags: true,
                    placeholder: 'お弁当を選択または入力',
                    allowClear: false,
                    width: '100%'
                });
            }

            // ✅ 「フォームの最後のブロックにだけ “＋追加” ボタンと、2個目以降の場合は “✕削除” ボタンを表示する」関数
            function updateFormButtons() {
                $('.form-buttons').remove();

                const $lastForm = $('.form-group').last();
                const index = $('.form-group').length;

                let buttons = `
                    <div class="form-buttons mt-6 text-center">
                        <button type="button" class="add-form text-indigo-600 hover:underline">＋ 追加</button>`;

                if(index > 1) {
                    buttons += `
                        <br class="sm:hidden">
                        <button type="button" class="remove-form text-red-600 hover:underline ml-4">✕ 削除</button>`;
                }

                buttons += `</div>`;
                $lastForm.append(buttons);
            }

            applySelect2();
            updateFormButtons();

            // ✅ フォーム追加処理
            $(document).on('click', '.add-form', function () {
                const $firstForm = $('.form-group').first();
                const $newForm = $firstForm.clone();

                // コピー先のみ値をリセット
                $newForm.find('input').val('');
                $newForm.find('select').val('');

                // clone に付いた select2 UI を除去（元フォームには触れない）
                $newForm.find('.select2').remove();
                $newForm.find('select')
                    .removeAttr('data-select2-id')
                    .removeClass('select2-hidden-accessible')
                    .removeAttr('aria-hidden')
                    .show();

                // 番号振り直し
                const currentCount = $('.form-group').length + 1;
                $newForm.find('.form-count').text(`【${currentCount}個目】`);
                $newForm.addClass('mt-10');

                $('#button-group').before($newForm);
                applySelect2(); // 新しい select に再適用
                updateFormButtons();
            });

            // ✅ 削除処理
            $(document).on('click', '.remove-form', function () {
                $(this).closest('.form-group').remove();

                $('.form-group').each(function (index) {
                    $(this).find('.form-count').text(`【${index + 1}個目】`);
                });

                updateFormButtons();
            });
        });


        // ⭐️ ----- ブランド選択 → お弁当候補をAjaxで取得 --------------------
        $(document).on('change', '.bento_brand', function () {
            const selectedBrand = $(this).val();
            const $bentoSelect = $(this).closest('.form-group') // 自分と同じフォーム行にある親を特定し
                                        .find('.bento_name'); // その中の .bento_name（セレクト）だけを探す

            // ✅ 既存オプション削除（自由入力除く）
            $bentoSelect.find('option:not([value=""])').remove(); // 'option:not([value=""])' = value 属性が空でないものだけを指定(ん自由ニュ力はこの時点では空)

            if(!selectedBrand) return;

            // ✅ ajax通信
            $.ajax({
                url: '/api/bentos',
                type: 'GET',
                data: { brand: selectedBrand }, // 送信するデータ
                success: function(response) { // 通信が成功したときに呼ばれる関数 | response にサーバーからの返却データ（JSON）が入る。
                    response.forEach(bento => { // responseを小分けにしたものがbento
                        const option = new Option(bento.name, bento.name, false, false); // <option> 要素を作成 | new Option(表示名, 値, selected?, defaultSelected?) の形式。
                        $bentoSelect.append(option);
                    });
                    $bentoSelect.trigger('change'); // select2を更新
                }
            });
        });
    </script>
</x-app-layout>
