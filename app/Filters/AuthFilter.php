<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Global authentication gate.
 *
 * Any route that is NOT in Config\Filters' 'except' list for this filter must
 * have a valid session (set by App\Controllers\Sso on handoff from HitCourt).
 * Unauthenticated requests 302 to HitCourt's login, carrying the originally
 * requested path as ?return=... so HitCourt can send them back after login.
 *
 * There is NO local login screen, by design. court-fitness has no way to
 * authenticate anyone — identity always originates on HitCourt.
 */
final class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('is_authenticated') === true) {
            return null;
        }

        $hitcourtBase = rtrim((string) env('HITCOURT_BASE_URL', 'https://www.hitcourt.com'), '/');
        $returnPath   = (string) $request->getUri()->getPath();
        $query        = (string) $request->getUri()->getQuery();
        if ($query !== '') {
            $returnPath .= '?' . $query;
        }

        $loginUrl = $hitcourtBase . '/login?return=' . rawurlencode($returnPath);

        return redirect()->to($loginUrl);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
