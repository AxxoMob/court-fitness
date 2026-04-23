<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// SSO handoff from HitCourt. Accepts ?token=<jwt>. No auth filter (unauthenticated by design).
// See app/Controllers/Sso.php + app/Services/JwtValidator.php.
$routes->get('sso', 'Sso::index');
