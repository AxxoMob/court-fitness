<?php

declare(strict_types=1);

use App\Filters\AuthFilter;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * AuthFilter tests exercise the only thing the filter can decide:
 *   - If the session says authenticated, pass through (return null).
 *   - Otherwise, redirect to ${HITCOURT_BASE_URL}/login?return=<path>.
 *
 * The per-route except list lives in Config\Filters, not this class, so we
 * don't test that here — we test the filter's own decision function.
 *
 * @internal
 */
final class AuthFilterTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure each test starts without an authenticated session.
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    public function testAuthenticatedSessionPassesThrough(): void
    {
        session()->set('is_authenticated', true);

        $filter   = new AuthFilter();
        $request  = Services::request(null, false);
        $response = $filter->before($request);

        $this->assertNull($response, 'authenticated session must pass through the filter');
    }

    public function testUnauthenticatedRedirectsToHitCourtLogin(): void
    {
        // Explicitly mark not-authenticated.
        session()->set('is_authenticated', false);

        $filter   = new AuthFilter();
        $request  = Services::request(null, false);
        $response = $filter->before($request);

        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
        $location = $response->getHeaderLine('Location');

        $base = rtrim((string) env('HITCOURT_BASE_URL', 'https://www.hitcourt.com'), '/');
        $this->assertStringStartsWith($base . '/login', $location);
        $this->assertStringContainsString('return=', $location);
    }
}
