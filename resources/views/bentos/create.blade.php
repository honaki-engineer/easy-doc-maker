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

                                {{-- バリデーションエラーなどで戻ってきたときに、フォームの入力を復元するための準備 --}}
                                @php
                                    $oldBrands = old('bento_brands', ['']);
                                    $oldNames = old('bento_names', ['']);
                                    // old() で復元された入力の 最大数を数える処理。(例：ブランドは3個あるけど、お弁当は2個 ⇒ max(3, 2) で 3個分の行を出力)
                                    $count = max(count($oldBrands), count($oldNames));
                                @endphp

                                @for($i = 0; $i < $count; $i++)
                                <div class="form-group {{ $i > 0 ? 'mt-10' : '' }}">
                                    <div class="container px-5 mx-auto">
                                        <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                            <p class="form-count font-semibold text-sm text-gray-700 pt-2 pb-4">【{{ $i + 1 }}個目】</p>
                                            {{-- <p class="form-count font-semibold text-sm text-gray-700 pt-2 pb-4">【1個目】</p> --}}
                                            <div class="flex flex-wrap -m-2">
                                                {{-- ブランド --}}
                                                <div class="p-2 w-full">
                                                    <label class="leading-7 text-sm text-gray-600">ブランド <span class="text-red-500 text-xs ml-1">※必須</span></label>
                                                    <select name="bento_brands[{{ $i }}]" class="bento_brand w-full rounded border border-gray-300 text-base py-1 px-3 leading-8 outline-none text-gray-700">
                                                        <option value=""></option>

                                                        {{-- 通常のブランド一覧 --}}
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->name }}" {{ $oldBrands[$i] == $brand->name ? 'selected' : '' }}>
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach

                                                        {{-- Select2などで自由入力された値を、old関数で復元 --}}
                                                        @php
                                                            $oldBrand = $oldBrands[$i];
                                                            // $oldBrand というブランド名が $brands コレクションの中に存在するかどうか
                                                            $brandExists = $brands->contains('name', $oldBrand);
                                                        @endphp
                                                        @if($oldBrand && !$brandExists)
                                                            <option value="{{ $oldBrand }}" selected>{{ $oldBrand }}</option>
                                                        @endif
                                                    </select>

                                                    <x-input-error :messages="$errors->get('bento_brands.' . $i)" class="mt-2" />{{-- エラー時は`bento_brands.0`の形で保存されている --}}
                                                </div>

                                                {{-- お弁当 --}}
                                                <div class="p-2 w-full">
                                                    <label class="leading-7 text-sm text-gray-600">お弁当 <span class="text-red-500 text-xs ml-1">※必須</span></label>
                                                    <select name="bento_names[{{ $i }}]" class="bento_name w-full rounded border border-gray-300 text-base py-1 px-3 leading-8 outline-none text-gray-700">
                                                        <option value="{{ $oldNames[$i] ?? '' }}" selected>{{ $oldNames[$i] ?? '' }}</option>
                                                    </select>
                                                    <x-input-error :messages="$errors->get('bento_names.' . $i)" class="mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endfor

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


    {{-- ----- ⭐️ Select2 CSS -------------------- --}}
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
    $(document).ready(function () {
        // ✅ Select2 を適用
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


        // ✅ `Select2`を破棄して再初期化
        function reInitBentoSelect($bentoSelect, selectedValue = '') {
            $bentoSelect.select2('destroy'); // UI破棄
            $bentoSelect.find('option:not([value=""])').remove(); // `value`以外の`<option>`要素を削除

            $bentoSelect.select2({
                tags: true,
                placeholder: 'お弁当を選択または入力',
                allowClear: false,
                width: '100%'
            });

            $bentoSelect.val(selectedValue).trigger('change');
        }


        // ✅ バリデーションエラーでフォームが戻ってきたときに、セレクトボックスを正しく復元するための処理
        function loadBentoOptionsFromOld() {
            $('.form-group').each(function () {
                const $formGroup = $(this);
                const selectedBrand = $formGroup.find('.bento_brand').val(); // `selected`を取得
                const $bentoSelect = $formGroup.find('.bento_name');
                const oldValue = $bentoSelect.val();

                if(selectedBrand) {
                    $.ajax({
                        url: '/api/bentos',
                        type: 'GET',
                        data: { brand: selectedBrand },
                        success: function(response) {
                            reInitBentoSelect($bentoSelect); // 先に初期化

                            // 🔸 Ajaxで取得したお弁当一覧を、セレクトボックスに重複なく追加する
                            response.forEach(bento => {
                                // すでに`option[value="${bento.name}"`が存在するかチェック
                                if($bentoSelect.find(`option[value="${bento.name}"]`).length === 0) {
                                    const option = new Option(bento.name, bento.name, false, false); // Option(表示名, value, selected, defaultSelected)
                                    $bentoSelect.append(option);
                                }
                            });

                            $bentoSelect.val(oldValue).trigger('change');
                        }
                    });
                }
            });
        }


        // ✅ ボタン表示切替
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


        // ✅ 初期表示時に実行
        applySelect2();
        loadBentoOptionsFromOld();
        updateFormButtons();


        // ✅ フォーム追加
        $(document).on('click', '.add-form', function () {
            const $lastForm = $('.form-group').last();
            const $newForm = $lastForm.clone();
            $newForm.addClass('mt-10'); // 

            // 🔹 クローンしたフォームの中身を空っぽにして初期状態に戻す
            $newForm.find('input').val('');
            $newForm.find('select').val('');
            $newForm.find('.select2').remove();

            // 🔹 元の純粋な<select>タグに戻す
            $newForm.find('select')
                .removeAttr('data-select2-id')
                .removeClass('select2-hidden-accessible')
                .removeAttr('aria-hidden')
                .show();

            // 🔹 クローンされた「お弁当セレクトボックス」の選択肢を一旦すべて削除し、空の初期状態に戻す
            $newForm.find('select.bento_name').empty().append('<option value=""></option>');

            const currentCount = $('.form-group').length;
            $newForm.find('.form-count').text(`【${currentCount + 1}個目】`);
            $newForm.find('select.bento_brand').attr('name', `bento_brands[${currentCount}]`);
            $newForm.find('select.bento_name').attr('name', `bento_names[${currentCount}]`);
            $newForm.find('.mt-2').remove(); // `mt-2`が付いている`DOM要素`(エラーメッセージ)を削除

            $('#button-group').before($newForm); // A.before(x) = Aの「直前」xを追加する
            applySelect2();
            updateFormButtons();
        });


        // ✅ 削除
        $(document).on('click', '.remove-form', function () {
            $(this).closest('.form-group').remove(); // `remove-form`が含まれている`form-group`をまるごと削除

            $('.form-group').each(function(index) {
                $(this).find('.form-count').text(`【${index + 1}個目】`);
            });

            updateFormButtons();
        });


        // ✅ ブランド選択時に弁当取得(重複排除)
        $(document).on('change', '.bento_brand', function () {
            const selectedBrand = $(this).val();
            const $bentoSelect = $(this).closest('.form-group').find('.bento_name');
            const currentValue = $bentoSelect.val();

            if(!selectedBrand) {
                reInitBentoSelect($bentoSelect, ''); // 初期化
                return;
            }

            $.ajax({
                url: '/api/bentos',
                type: 'GET',
                data: { brand: selectedBrand },
                success: function (response) {
                    reInitBentoSelect($bentoSelect, currentValue);

                    // ブランドに紐づくお弁当のリストを取得して、セレクトボックスに追加する
                    response.forEach(bento => {
                        if($bentoSelect.find(`option[value="${bento.name}"]`).length === 0) {
                            const option = new Option(bento.name, bento.name, false, false);
                            $bentoSelect.append(option);
                        }
                    });

                    $bentoSelect.val(currentValue).trigger('change');
                }
            });
        });
    });
</script>

</x-app-layout>
