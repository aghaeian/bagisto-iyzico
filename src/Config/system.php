<?php

return [
    [
        'key' => 'sales.payment_methods.iyzico',
        'name' => 'Iyzico',
        'info' => 'iyzico::app.admin.system.info',
        'sort' => 5,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'iyzico::app.admin.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'iyzico::app.admin.system.description',
                'type'          => 'textarea',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'merchant_id',
                'title'         => 'iyzico::app.admin.system.merchant-id',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'terminal_id',
                'title'         => 'iyzico::app.admin.system.terminal-id',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'store_key',
                'title'         => 'iyzico::app.admin.system.store-key',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox',
                'title'         => 'iyzico::app.admin.system.sandbox',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'status',
                'title'         => 'iyzico::app.admin.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'sort',
                'title'         => 'iyzico::app.admin.system.sort',
                'type'          => 'select',
                'channel_based' => false,
                'locale_based'  => false,
                'options'       => [
                    [
                        'title' => '1',
                        'value' => '1',
                    ], [
                        'title' => '2',
                        'value' => '2',
                    ], [
                        'title' => '3',
                        'value' => '3',
                    ], [
                        'title' => '4',
                        'value' => '4',
                    ], [
                        'title' => '5',
                        'value' => '5',
                    ],
                ],
            ],
        ],
    ],
];

