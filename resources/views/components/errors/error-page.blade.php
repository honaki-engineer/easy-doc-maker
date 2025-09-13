<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} | {{ $title }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen overflow-y-scroll">

    {{-- ヘッダー --}}
    <header class="bg-gray-200 border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
              <div class="flex">
                  <!-- Logo -->
                  <div class="shrink-0 flex items-center">
                      <a href="{{ route('receipts.index') }}">
                          <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                      </a>
                  </div>
              </div>
          </div>
      </div>
    </header>

    {{-- メイン --}}
    <main class="flex-grow flex flex-col justify-center items-center text-center px-4">
        <h2 class="text-xl font-bold mb-2">{{ $code }} | {{ $title }}</h2>
        <p class="mb-6">{!! $message !!}</p>
        <a href="{{ route('receipts.index') }}"
            class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 transition text-lg">
            トップページはこちら
        </a>
    </main>

    {{-- フッター --}}
    <footer class="shadow bg-gray-200 py-4 text-center text-sm text-gray-500">
        © {{ date('Y') }} {{ config('app.name') }} All rights reserved
    </footer>

</body>
</html>
