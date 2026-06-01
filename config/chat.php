<?php

return [

    'default_specialist' => env('CHAT_DEFAULT_SPECIALIST', 'marco'),

    'firm_name' => env('CHAT_FIRM_NAME', 'David assistant of Bankruptcy Custom Solution'),

    'specialists' => [
        'marco' => [
            'display_name' => 'Marco',
            'title' => 'David assistant',
        ],
        'ruby' => [
            'display_name' => 'Ruby',
            'title' => 'David assistant',
        ],
        'alina' => [
            'display_name' => 'Alina',
            'title' => 'David assistant',
        ],
    ],

    'situation_options' => [
        'en' => [
            'Credit Card Debt',
            'Personal Loans',
            'Lawsuits',
            'Foreclosure',
            'Business Debt',
            'Student Loans',
            'Other',
        ],
        'es' => [
            'Deuda de tarjetas de crédito',
            'Préstamos personales',
            'Demandas',
            'Ejecución hipotecaria',
            'Deuda comercial',
            'Préstamos estudiantiles',
            'Otra',
        ],
    ],

];
