<?php
return [
    'app' => [
        'name' => 'Arandu Go',
        'url' => 'https://tudominio.com',
        'timezone' => 'America/Asuncion',
        'debug' => false,
        'key' => 'CAMBIAR_DURANTE_LA_INSTALACION',
        'version' => '2.2.1',
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'cpanel_arandugo',
        'user' => 'cpanel_arandugo',
        'password' => '',
        'charset' => 'utf8mb4',
        'prefix' => 'ago_',
    ],
    'mail' => [
        'transport' => 'mail',
        'from_email' => 'formularios@tudominio.com',
        'from_name' => 'Arandu Go',
        'sendmail_path' => '/usr/sbin/sendmail -bs',
        'smtp' => [
            'host' => 'mail.tudominio.com',
            'port' => 465,
            'security' => 'ssl',
            'username' => 'formularios@tudominio.com',
            'password' => '',
        ],
    ],
    'recaptcha' => [
        'secret_key' => '',
    ],
];
