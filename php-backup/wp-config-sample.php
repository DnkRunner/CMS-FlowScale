<?php
// config-sample.php – przykładowy plik konfiguracyjny
// Skopiuj ten plik do config.php i wypełnij danymi

return [
    'db' => [
        'host' => 'localhost',
        'database' => 'nazwa_bazy_danych',
        'user' => 'użytkownik_bazy',
        'pass' => 'hasło_bazy',
        'prefix' => 'cms_',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'app' => [
        'base_url' => 'http://twoja-domena.pl',
        'base_path' => '',
        'app_key' => 'klucz_aplikacji_zostanie_wygenerowany',
        'env' => 'production'
    ]
];
