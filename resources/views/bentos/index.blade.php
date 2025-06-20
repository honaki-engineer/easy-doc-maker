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
                            <a href="{{ route('bentos.create') }}" class="inline-block text-white bg-indigo-500 border-0 py-1 px-4 focus:outline-none hover:bg-indigo-600 rounded text-lg">お弁当登録</a>
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
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            @if($bento->bentoBrand)
                                                {{ $bento->bentoBrand->name }}
                                            @endif
                                        </td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $bento->name }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            <a href="{{ route('bentos.create') }}" class="inline-block text-white bg-green-500 border-0 py-1 px-4 focus:outline-none hover:bg-green-600 rounded text-lg">編集</a>
                                            <a href="{{ route('bentos.create') }}" class="inline-block text-white bg-pink-500 border-0 py-1 px-4 focus:outline-none hover:bg-pink-600 rounded text-lg">削除</a>
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
</x-app-layout>
