<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*', // Example: Exclude all routes under 'api' prefix
        '/add_form',
        'update_form/*',
        'get_names_json/',
        'get_namez_json/',
        'get_positions_json/',
        'get_divisions_json/',
        'get_employees_json/',
        'get_forms_json/',
        'add_account/',
        'update_account/*',
        'get_accounts_json/',
        'get_type_json/'
        //
    ];
}
