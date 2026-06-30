<?php

return [

    'token' => env('FONNTE_TOKEN'),

    'url' => env('FONNTE_URL', 'https://api.fonnte.com/send'),

    'timeout' => env('FONNTE_TIMEOUT', 30),

    'retry' => env('FONNTE_RETRY', 3),

    'retry_delay' => env('FONNTE_RETRY_DELAY', 1000),

];
