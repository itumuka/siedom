<?php

return [
    'key' => env('JWT_SECRET'),
    'alg' => env('JWT_ALG', 'HS256'),  // Menambahkan algoritma default (HS256)
];
