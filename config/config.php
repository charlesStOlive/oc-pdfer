<?php return [
    'packages' => [
        'barryvdh' => [
            'providers' => [
                '\Barryvdh\Snappy\ServiceProvider',
            ],

            'aliases' => [
                'SnappyPDF' => '\Barryvdh\Snappy\Facades\SnappyPdf',
                'SnappyImage' => '\Barryvdh\Snappy\Facades\SnappyImage',
            ],

            'config_namespace' => 'snappy',

            'config' => [
                'pdf' => [
                    'enabled' => true,
                    'binary' => env("WKHTML_PDF_BINARY", '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
                    'timeout' => false,
                    'options' => [],
                    'env' => [],
                ],

                'image' => [
                    'enabled' => true,
                    'binary' => env("WKHTML_IMG_BINARY", '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"'),
                    'timeout' => false,
                    'options' => [],
                    'env' => [],
                ],
            ],
        ],
    ],
    'btns' => [
        'pdf' => [
            'label' => 'PDF',
            'class' => 'btn-secondary',
            'ajaxCaller' => 'onLoadPdfBehaviorPopupForm',
            'ajaxInlineCaller' => 'onLoadPdfBehaviorContentForm',
            'icon' => 'oc-icon-file-pdf-o',
        ],
    ],
];
