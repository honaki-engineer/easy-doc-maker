<?php

putenv('HOME=' . env('BROWSERSHOT_HOME', '/var/www/.browsershot')); // 本番環境のみ

return [
    'node_binary' => env('NODE_BINARY_PATH'),
    'include_path' => env('NODE_INCLUDE_PATH'),
    'chrome_path' => env('CHROME_PATH'),
];