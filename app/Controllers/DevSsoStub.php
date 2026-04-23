<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use Firebase\JWT\JWT;
use RuntimeException;

/**
 * Dev-only stub SSO issuer.
 *
 * Mints a short-lived HS256 JWT signed with HITCOURT_JWT_SECRET for one of the
 * seeded demo users, then redirects to /sso?token=... — exercising the real
 * SSO controller end-to-end without needing HitCourt to be reachable.
 *
 * ONLY available when CI_ENVIRONMENT = development. In production this route
 * 404s.
 *
 * Usage (dev):
 *   /dev/sso-stub                → logs in as Rohan (player)
 *   /dev/sso-stub?as=player      → same
 *   /dev/sso-stub?as=coach       → logs in as Rajat (coach)
 *   /dev/sso-stub?as=admin       → logs in as demo admin (hits admin placeholder)
 */
final class DevSsoStub extends BaseController
{
    /**
     * @var array<string,array{hitcourt_user_id:int,email:string,first_name:string,family_name:string,role:string}>
     */
    private const DEMO_USERS = [
        'coach' => [
            'hitcourt_user_id' => 1001,
            'email'            => 'rajat.coach@hitcourt.example',
            'first_name'       => 'Rajat',
            'family_name'      => 'Kapoor',
            'role'             => 'coach',
        ],
        'player' => [
            'hitcourt_user_id' => 2001,
            'email'            => 'rohan.player@hitcourt.example',
            'first_name'       => 'Rohan',
            'family_name'      => 'Mehta',
            'role'             => 'player',
        ],
        'player2' => [
            'hitcourt_user_id' => 2002,
            'email'            => 'priya.player@hitcourt.example',
            'first_name'       => 'Priya',
            'family_name'      => 'Sharma',
            'role'             => 'player',
        ],
        'admin' => [
            'hitcourt_user_id' => 9001,
            'email'            => 'admin.user@hitcourt.example',
            'first_name'       => 'Admin',
            'family_name'      => 'User',
            'role'             => 'admin',
        ],
    ];

    public function index(): RedirectResponse
    {
        if (ENVIRONMENT !== 'development') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Stub SSO is not available in this environment.');
        }

        $secret = getenv('HITCOURT_JWT_SECRET') ?: '';
        if ($secret === '') {
            throw new RuntimeException('HITCOURT_JWT_SECRET is not configured — cannot mint dev token.');
        }

        $as = strtolower((string) ($this->request->getGet('as') ?? 'player'));
        if (! array_key_exists($as, self::DEMO_USERS)) {
            // Default to player if unknown
            return redirect()->to('/dev/sso-stub?as=player');
        }

        $claims        = self::DEMO_USERS[$as];
        $claims['exp'] = time() + 30; // 30-second token life (within the 60s ceiling)

        $token = JWT::encode($claims, $secret, 'HS256');

        return redirect()->to('/sso?token=' . urlencode($token));
    }

    /**
     * Landing page with one-click links for each demo user.
     */
    public function index_page(): string
    {
        if (ENVIRONMENT !== 'development') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Stub SSO is not available in this environment.');
        }

        return view('dev_sso_stub');
    }
}
