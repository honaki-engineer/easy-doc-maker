<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            自社情報の編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">

                        <form action="{{ route('receipt_settings.update')}}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="container px-5 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        {{-- 郵便番号 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="postal_code"
                                                    class="leading-7 text-sm text-gray-600">
                                                    郵便番号(ハイフン有)
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <input type="text" id="postal_code" name="postal_code"
                                                    value="{{ $receipt_setting->postal_code }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 住所 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="address_line1"
                                                    class="leading-7 text-sm text-gray-600">
                                                    住所
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <input type="text" id="address_line1" name="address_line1"
                                                    value="{{ $receipt_setting->address_line1 }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('address_line1')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 建物名 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="address_line2"
                                                    class="leading-7 text-sm text-gray-600">建物名</label>
                                                <input type="text" id="address_line2" name="address_line2"
                                                    value="{{ $receipt_setting->address_line2 }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('address_line2')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 会社名 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="issuer_name"
                                                    class="leading-7 text-sm text-gray-600">
                                                    会社名
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <input type="text" id="issuer_name" name="issuer_name"
                                                    value="{{ $receipt_setting->issuer_name }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('issuer_name')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 登録番号 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="issuer_number"
                                                    class="leading-7 text-sm text-gray-600">登録番号</label>
                                                <input type="text" id="issuer_number" name="issuer_number"
                                                    value="{{ $receipt_setting->issuer_number }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('issuer_number')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 固定電話 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="tel_fixed"
                                                    class="leading-7 text-sm text-gray-600">固定電話(ハイフン有)</label>
                                                <input type="text" id="tel_fixed" name="tel_fixed"
                                                    value="{{ $receipt_setting->tel_fixed }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('tel_fixed')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 携帯電話 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="tel_mobile"
                                                    class="leading-7 text-sm text-gray-600">携帯電話(ハイフン有)</label>
                                                <input type="text" id="tel_mobile" name="tel_mobile"
                                                    value="{{ $receipt_setting->tel_mobile }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('tel_mobile')" class="mt-2" />
                                            </div>
                                        </div>
                                        {{-- 担当者 --}}
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="responsible_name"
                                                    class="leading-7 text-sm text-gray-600">
                                                    担当者
                                                    <span class="text-red-500 text-xs ml-1">※必須</span>
                                                </label>
                                                <input type="text" id="responsible_name" name="responsible_name"
                                                    value="{{ $receipt_setting->responsible_name }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <x-input-error :messages="$errors->get('responsible_name')" class="mt-2" />
                                            </div>
                                        </div>
                                        
    
                                        {{-- ボタンエリア --}}
                                        <div class="w-full mt-8">
                                            <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">更新</button>
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
</x-app-layout>
