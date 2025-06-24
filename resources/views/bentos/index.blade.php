<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            お弁当一覧
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

                        {{-- ボタンエリア --}}
                        <div class="w-full mb-4">
                            <a href="" class="text-indigo-600 underline hover:text-indigo-800 hover:underline cursor-pointer font-medium transition duration-200">ブランド一覧に切り替える</a>
                        </div>
                        
                        <table class="whitespace-nowrap table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        ブランド</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        お弁当</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bentos as $bento)
                                    <tr>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 break-words whitespace-normal">
                                            @if($bento->bentoBrand)
                                                {{ $bento->bentoBrand->name }}
                                            @endif
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3 break-words whitespace-normal">{{ $bento->name }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            {{-- 削除ボタン --}}
                                            <form
                                                action="{{ route('bentos.destroy', ['bento' => $bento->id]) }}"
                                                method="post" id="delete_{{ $bento->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="w-full">
                                                    <button type="button"
                                                        data-id="{{ $bento->id }}"
                                                        onclick="deletePost(this)"
                                                        class="text-white bg-pink-500 border-0 py-1 px-4 focus:outline-none hover:bg-pink-600 rounded text-lg">削除</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 確認メッセージ -->
    <script>
        function deletePost(e) {
            'use strict'
            if(confirm('本当に削除していいですか？')) {
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
