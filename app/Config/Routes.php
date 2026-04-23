<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// SSO handoff from HitCourt. Accepts ?token=<jwt>. No auth filter (unauthenticated by design).
// See app/Controllers/Sso.php + app/Services/JwtValidator.php.
$routes->get('sso', 'Sso::index');

// Role-based landings (all require authenticated session; controllers check role).
$routes->get('coach',              'Coach\Dashboard::index');
$routes->get('player',             'Player\Dashboard::index');
$routes->get('admin-placeholder',  'AdminPlaceholder::index');

// Dev-only: stub SSO issuer. Short-circuits HitCourt for local testing.
// The controllers themselves also gate on ENVIRONMENT === 'development'.
if (ENVIRONMENT === 'development') {
    $routes->get('dev/sso-stub',      'DevSsoStub::index');
    $routes->get('dev',               'DevSsoStub::index_page');
}
