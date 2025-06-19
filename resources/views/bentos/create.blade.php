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
                        <form action="" method="POST">
                            @csrf
                            <div class="container px-5 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">

                                        {{-- ブランド --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="bento_brand" class="leading-7 text-sm text-gray-600">
                                                    ブランド
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <select
                                                    id="bento_brand"
                                                    name="bento_brand"
                                                    class="w-full rounded border border-gray-300 text-base py-1 px-3 leading-8 outline-none text-gray-700"
                                                >
                                                    <option value=""></option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-input-error :messages="$errors->get('bento_brand')" class="mt-2" />
                                            </div>
                                        </div>

                                        {{-- お弁当 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="bento_name" class="leading-7 text-sm text-gray-600">
                                                    お弁当
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    id="bento_name"
                                                    name="bento_name"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base py-1 px-3 leading-8 outline-none text-gray-700"
                                                >
                                                <x-input-error :messages="$errors->get('bento_name')" class="mt-2" />
                                            </div>
                                        </div>

                                        {{-- 登録ボタン --}}
                                        <div class="w-full mt-8">
                                            <button
                                                class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                                            >
                                                登録
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS：4.0.13 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
<!-- Select2 JS：4.0.13 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


<style>
    /* セレクトボックスの高さをボタンと揃える */
    .select2-container--default .select2-selection--single {
        height: 2.625rem;
        padding: 0.5rem 0.75rem;
        display: flex;
        align-items: center;
        font-size: 1rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }

    /* プルダウンの▼ボタン位置を調整 */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        top: 0;
        right: 0.75rem;
    }

    /* 検索欄を丸くする */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        font-size: 1rem;
    }

    /* ×ボタン削除 */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }
</style>
    

    {{-- 初期化スクリプト --}}
    <script>
      $(document).ready(function () {
        $('#bento_brand').select2({
          tags: true,
          placeholder: 'ブランドを選択または入力',
          allowClear: true,
          width: '100%'
        });
        // placeholder を確実に表示
        $('#bento_brand').val(null).trigger('change');
      });
    </script>
</x-app-layout>
