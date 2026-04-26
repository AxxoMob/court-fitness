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

// Coach sub-routes. Plan IDs in URLs are opaque (App\Support\IdObfuscator);
// the show/edit handlers decode and 404 on garbage.
$routes->group('coach', static function ($r): void {
    $r->get('players',             'Coach\Players::index');
    $r->get('plans',               'Coach\Plans::index');
    $r->get('plans/new',           'Coach\Plans::new');
    $r->post('plans',              'Coach\Plans::store');
    $r->get('plans/(:segment)',    'Coach\Plans::show/$1');
    $r->post('plans/(:segment)',   'Coach\Plans::update/$1');
});

// Player sub-routes.
$routes->group('player', static function ($r): void {
    $r->get('plans/(:segment)',    'Player\Plans::show/$1');
    $r->post('plans/(:segment)',   'Player\Plans::update/$1');
});

// Dev-only: stub SSO issuer. Short-circuits HitCourt for local testing.
// The controllers themselves also gate on ENVIRONMENT === 'development'.
if (ENVIRONMENT === 'development') {
    $routes->get('dev/sso-stub',      'DevSsoStub::index');
    $routes->get('dev',               'DevSsoStub::index_page');
}
