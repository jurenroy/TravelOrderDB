<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie','/add_form', 'update_form/*', 'get_names_json/', 'get_namez_json/', 'get_positions_json/', 'get_divisions_json/', 'get_employees_json/', 'get_forms_json/', 'add_account/', 'update_account/*', 'get_accounts_json', 'get_type_json', '/send-otp/*','/get_otp_json','/update_employee/*','/add_employees', 'edit_employee','/storage/images/*','*','/addleave_form','updateleave_form/*','acclogin'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*', 'http://202.137.117.84:8010', 'http://172.31.10.35:8010'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
