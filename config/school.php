<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Lembaga & Yayasan
    |--------------------------------------------------------------------------
    */
    'name' => env('SCHOOL_NAME', "Yayasan Perguruan Islam Miftahul 'Ulum"),
    'institution' => env('SCHOOL_INSTITUTION', "MTs. Miftahul 'Ulum Cibitung"),
    'address' => env('SCHOOL_ADDRESS', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung'),
    'treasurer_name' => env('SCHOOL_TREASURER_NAME', 'Hj. Titi Nurhayati, S. Pd'),
    'treasurer_phone' => env('SCHOOL_TREASURER_PHONE', '081234567890'),

    /*
    |--------------------------------------------------------------------------
    | Rekening Resmi Pembayaran / Kas Bendahara
    |--------------------------------------------------------------------------
    */
    'bank_name' => env('SCHOOL_BANK_NAME', 'Bank BRI'),
    'bank_account' => env('SCHOOL_BANK_ACCOUNT', 'REKENING_BRI_ANDA_DISINI'),
    'bank_holder' => env('SCHOOL_BANK_HOLDER', 'Madrasah Tsanawiyah Miftahul Ulum'),
];
