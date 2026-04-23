<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UsersModel;
use App\Services\JwtValidationException;
use App\Services\JwtValidator;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SSO handoff endpoint. Accepts GET /sso?token=<jwt> from HitCourt.
 *
 * Flow:
 *   1. Validate HS256 JWT (JwtValidator).
 *   2. Upsert local users row keyed on hitcourt_user_id.
 *   3. Mint a CI4 session with user_id + role + display name.
 *   4. Redirect to the role-appropriate landing:
 *        role=coach  → /coach
 *        role=player → /player
 *        anything else (admin, unknown) → /admin-placeholder
 *
 * See HL-8 — the SSO boundary is the single entry point; JwtValidator unit
 * tests guard every invalid-token path so this controller can stay small.
 */
final class Sso extends BaseController
{
    public function index(): RedirectResponse|ResponseInterface
    {
        $token = (string) $this->request->getGet('token');

        try {
            $validator = new JwtValidator();
            $claims    = $validator->validate($token);
        } catch (JwtValidationException $e) {
            log_message('warning', 'SSO rejected a token: {msg}', ['msg' => $e->getMessage()]);

            return $this->response
                ->setStatusCode(400)
                ->setContentType('text/plain')
                ->setBody('SSO token invalid: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            log_message('error', 'SSO configuration error: {msg}', ['msg' => $e->getMessage()]);

            return $this->response
                ->setStatusCode(500)
                ->setContentType('text/plain')
                ->setBody('SSO is not configured on this server.');
        }

        $users = new UsersModel();
        $user  = $users->upsertFromJwt($claims);

        if (empty($user)) {
            log_message('error', 'SSO upsert failed for hitcourt_user_id={id}', ['id' => $claims['hitcourt_user_id']]);

            return $this->response
                ->setStatusCode(500)
                ->setContentType('text/plain')
                ->setBody('Could not establish your court-fitness account. Please try again.');
        }

        // Mint session
        $session = session();
        $session->regenerate(true);
        $session->set([
            'is_authenticated' => true,
            'user_id'          => (int) $user['id'],
            'hitcourt_user_id' => (int) $user['hitcourt_user_id'],
            'role'             => (string) $user['role'],
            'first_name'       => (string) $user['first_name'],
            'family_name'      => (string) $user['family_name'],
            'email'            => (string) $user['email'],
        ]);

        return match (strtolower((string) $user['role'])) {
            'coach'  => redirect()->to('/coach'),
            'player' => redirect()->to('/player'),
            default  => redirect()->to('/admin-placeholder'),
        };
    }
}
