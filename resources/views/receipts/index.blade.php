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
                        
                        <table class="whitespace-nowrap table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl-lg"></th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        日付</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        取引先</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr-lg">
                                        </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipts as $receipt)
                                    <tr>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 break-words whitespace-normal">
                                            <a href="{{ route('receipts.show', ['receipt' => $receipt->id]) }}" class="text-blue-500 hover:text-blue-600">#</a>
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $receipt->issued_at }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $receipt->customer_name }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">☑️</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- 確認メッセージ -->
    <script>
        function deletePost(e) {
            'use strict'
            if(confirm('本当に削除していいですか？ブランドのお弁当も全て削除されます。')) {
                document.getElementById('delete_' + e.dataset.id).submit()
            }
        }
    </script>

    <!-- フラッシュメッセージの時間設定 -->
    <script>
        // フラッシュメッセージ
        setTimeout(() => {
            const flashMessage = document.getElementById('flash-message');
            if(flashMessage) {
                flashMessage.classList.add('opacity-0'); // フェードアウト
                setTimeout(() => flashMessage.remove(), 2000); // 2秒後に flashMessage というHTML要素を DOM(画面上)から完全に削除
            }
        }, 10000); // 10秒後にフェード開始
    </script>

</x-app-layout>
