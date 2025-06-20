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
                                                        <input
                                                            type="text"
                                                            name="bento_names[]"
                                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base py-1 px-3 leading-8 outline-none text-gray-700"
                                                        >
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

    {{-- ⭐️ -------------------- 読み込み-------------------- --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    {{-- ⭐️ -------------------- CSS -------------------- --}}
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


    {{-- ⭐️ -------------------- JS -------------------- --}}
    <script>
        $(document).ready(function () { // ページのDOM（HTML）が完全に読み込まれたあとに処理を実行するお約束の書き方
            // ✅ セレクトボックスに Select2 を適用する関数
            function applySelect2() {
                $('.bento_brand').select2({
                    tags: true, // 新しいブランド名も入力（タグ）として許可
                    placeholder: 'ブランドを選択または入力',
                    allowClear: false,
                    width: '100%'
                }).each(function () { // each(function () { ... }) = 複数ある .bento_brand の <select> 要素を1つずつ順番に処理
                    // .bento_brandクラスの <select> に、select2() を適用して、そのあと「全てのセレクトボックスの選択状態を初期化（空に）」しています。
                    $(this).val(null).trigger('change'); // val(null) = <select> の「選択されている値」をnullにする | .trigger('change') = select2は .val(null) だけでは見た目が更新されないことがあるため、.trigger('change') を呼び出してUIもリセットさせる
                });
            }
        
            // ✅ フォームの最後にだけ「＋追加／✕削除」を付け直す関数
            function updateFormButtons() {
                $('.form-buttons').remove(); // 既存のボタン削除

                const $lastForm = $('.form-group').last();
                const index = $('.form-group').length;

                // 追加ボタンだけは常に表示
                let buttons = `
                    <div class="form-buttons mt-6 text-center">
                        <button type="button" class="add-form text-indigo-600 hover:underline">＋ 追加</button>`;

                // 2個目以降のみ「削除」ボタンを追加
                if(index > 1) {
                    buttons += `
                        <br class="sm:hidden">
                        <button type="button" class="remove-form text-red-600 hover:underline ml-4">✕ 削除</button>`;
                }

                // ボタンラッパー閉じタグを追加
                buttons += `
                    </div>`;

                // 追加
                $lastForm.append(buttons);
            }
        
            applySelect2(); // セレクトボックスに Select2 を適用する関数
            updateFormButtons(); // フォームの最後にだけ「＋追加／✕削除」を付け直す関数
        
            // ✅ 追加処理
            $(document).on('click', '.add-form', function () {
                // 1個目の .form-group 要素（1つ分の入力フォーム）を コピー。これを新しい入力エリアとして使う。
                const $newForm = $('.form-group').first().clone();
        
                // コピー内容をリセット
                $newForm.find('select').val('');
                $newForm.find('input').val('');
                $newForm.find('.select2-container').remove();
                $newForm.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').show();
        
                // 現在のフォーム数に1を足して、「〇個目」表示の番号を決定。
                const currentCount = $('.form-group').length + 1;
                $newForm.find('.form-count').text(`【${currentCount}個目】`);
                $newForm.addClass('mt-10');
        
                // 追加されたフォームを #button-group（ボタンが配置されている領域）の直前に挿入する。これにより、フォームとボタンの位置が常に正しくなる。
                $('#button-group').before($newForm);
                applySelect2();
                updateFormButtons();
            });
        
            // ✅ 削除処理
            $(document).on('click', '.remove-form', function () {
                $(this).closest('.form-group').remove();
        
                // 番号更新
                $('.form-group').each(function (index) {
                    // 残ったすべての .form-group に対して、その順番に応じて 「【1個目】」「【2個目】」... という番号を振り直す。
                    $(this).find('.form-count').text(`【${index + 1}個目】`);
                });
        
                updateFormButtons();
            });
        });
    </script>
</x-app-layout>
