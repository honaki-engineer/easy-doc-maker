<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            自社情報
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 mx-auto">
                            <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">
                                    {{-- フラッシュメッセージ --}}
                                    @if(session('success'))
                                        <div id="flash-message"
                                            class="bg-green-100 text-green-800 rounded px-4 py-2 mb-2 transition-opacity duration-1000">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    {{-- 郵便番号 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="postal_code" class="leading-7 text-sm text-gray-600">郵便番号</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $receipt_setting->postal_code }}</div>
                                        </div>
                                    </div>
                                    {{-- 住所 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="address_line1" class="leading-7 text-sm text-gray-600">住所</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $receipt_setting->address_line1 }}</div>
                                        </div>
                                    </div>
                                    {{-- 建物名 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="address_line2" class="leading-7 text-sm text-gray-600">建物名</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out min-h-10">
                                            {{ $receipt_setting->address_line2 }}</div>
                                        </div>
                                    </div>
                                    {{-- 会社名 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="issuer_name" class="leading-7 text-sm text-gray-600">会社名</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $receipt_setting->issuer_name }}</div>
                                        </div>
                                    </div>
                                    {{-- 登録番号 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="issuer_number" class="leading-7 text-sm text-gray-600">登録番号</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out min-h-10">
                                            {{ $receipt_setting->issuer_number }}</div>
                                        </div>
                                    </div>
                                    {{-- 固定電話 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="tel_fixed" class="leading-7 text-sm text-gray-600">固定電話</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out min-h-10">
                                            {{ $receipt_setting->tel_fixed }}</div>
                                        </div>
                                    </div>
                                    {{-- 携帯電話 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="tel_mobile" class="leading-7 text-sm text-gray-600">携帯電話</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out min-h-10">
                                            {{ $receipt_setting->tel_mobile }}</div>
                                        </div>
                                    </div>
                                    {{-- 担当者 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="responsible_name" class="leading-7 text-sm text-gray-600">担当者</label>
                                            <div
                                                class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $receipt_setting->responsible_name }}</div>
                                        </div>
                                    </div>

                                    {{-- ボタンエリア --}}
                                    <div class="w-full mt-8">
                                        <form
                                            action="{{ route('receipt_settings.edit') }}"
                                            method="get">
                                                <button
                                                    class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">編集</button>
                                        </form>
                                    </div>
                    
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

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
