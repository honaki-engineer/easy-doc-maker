<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            領収書一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto lg:w-2/3 w-full mx-auto overflow-auto">
                        {{-- フラッシュメッセージ --}}
                        @if(session('success'))
                            <div id="flash-message"
                                class="bg-green-100 text-green-800 rounded px-4 py-2 mb-4 transition-opacity duration-1000 w-fit">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- 検索フォーム --}}
                        <form action="{{ route('receipts.index') }}" method="GET" class="mb-4">
                            <input type="date" name="search_issued_at" value="{{ request('search_issued_at') }}" id="date" class="border border-gray-300 rounded cursor-pointer">
                            <input type="text" name="search_customer_name" value="{{ request('search_customer_name') }}" placeholder="検索" class="border border-gray-300 rounded cursor-pointer">
                            <button class="mx-auto text-white bg-indigo-500 border-0 py-2 px-8 ml-4 focus:outline-none hover:bg-indigo-600 rounded text-lg">検索</button>
                        </form>

                        <form id="receipt-form" method="POST">
                            @csrf

                            <div class="flex gap-2 mb-4">
                                <button 
                                    type="button" {{-- `submitForm`で`submit`をするため --}}
                                    onclick="submitForm('{{ route('receipts.bulkDownload') }}', false)"
                                    class="text-white bg-gray-500 px-4 py-2 rounded hover:bg-gray-600">
                                    ✅ 選択したPDFを一括DL
                                </button>

                                <button 
                                    type="button" {{-- `submitForm`で`submit`をするため --}}
                                    onclick="submitForm('{{ route('receipts.generate_and_print_multiple') }}', true)"
                                    class="text-white bg-green-500 px-4 py-2 rounded hover:bg-green-600">
                                    🖨️ 選択したPDFを一括印刷
                                </button>
                            </div>

                            {{-- ダウンロードチェックボックスエラーメッセージ --}}
                            @if(session('error'))
                                <div id="flash-message-error" class="text-red-500 mb-2">{{ session('error') }}</div>
                            @endif

                            {{-- 全て選択ボタン --}}
                            <div class="text-right">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="select-all" class="form-checkbox text-indigo-600 cursor-pointer">
                                    <span class="ml-1">すべて選択 / 解除</span>
                                </label>
                            </div>

                            {{-- テーブル --}}
                            <table class="table-fixed w-full text-left">
                                {{-- テーブルの列の指定 --}}
                                <colgroup>
                                    <col class="w-[4ch]" />  <!-- '#' -->
                                    <col class="w-[13ch]" /> <!-- 日付（YYYY-MM-DD） -->
                                    <col />                  <!-- 取引先（残り幅ぜんぶ） -->
                                    <col class="w-[5ch]" />  <!-- チェックボックス -->
                                </colgroup>
                                {{-- テーブルタイトル --}}
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl-lg"></th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">日付</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">取引先</th>
                                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr-lg"></th>
                                    </tr>
                                </thead>
                                {{-- テーブル要素 --}}
                                <tbody>
                                    @foreach($receipts as $receipt)
                                    <tr>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            <a href="{{ route('receipts.show', ['receipt' => $receipt->id]) }}" class="text-blue-500 hover:text-blue-600">#</a>
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 whitespace-nowrap tabular-nums">
                                            {{ $receipt->issued_at }}
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 max-w-0 overflow-hidden">
                                            <div class="block max-w-full overflow-x-auto whitespace-nowrap"
                                                style="-webkit-overflow-scrolling:touch;">
                                                {{ $receipt->customerName->name }}
                                            </div>
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 text-center">
                                            <input type="checkbox" name="receipt_ids[]" value="{{ $receipt->id }}" class="cursor-pointer">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>


    <script>
        // ⭐️ 確認メッセージ
        function deletePost(e) {
            'use strict'
            if(confirm('本当に削除していいですか？ブランドのお弁当も全て削除されます。')) {
                document.getElementById('delete_' + e.dataset.id).submit()
            }
        }


        // ⭐️ フラッシュメッセージ
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message') || document.getElementById('flash-message-error');
            if(flashMessage) {
                flashMessage.classList.add('opacity-0'); // フェードアウト
                setTimeout(() => flashMessage.remove(), 2000); // 2秒後に flashMessage というHTML要素を DOM(画面上)から完全に削除
            }
        }, 10000); // 10秒後にフェード開始

        
        // ⭐️ 日付クリック有効範囲を全域にする
        document.getElementById("date").addEventListener("click", function() {
            this.showPicker(); // Chrome でカレンダーを開く
        });

        
        // ⭐️ PDFダウンロード、印刷ボタンクリック時
        function submitForm(action, openInNewTab = false) {
            const form = document.getElementById('receipt-form');
            form.action = action;
            form.target = openInNewTab ? '_blank' : '_self';
            form.submit();
        }

        // ⭐️ 全てのチェックボックスを一括で選択または解除
        document.getElementById('select-all').addEventListener('click', function () {
            const checkboxes = document.querySelectorAll('input[name="receipt_ids[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
</script>

</x-app-layout>
