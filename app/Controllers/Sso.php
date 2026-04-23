<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\JwtValidationException;
use App\Services\JwtValidator;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SSO handoff endpoint.
 *
 * Accepts GET /sso?token=<jwt> from HitCourt, validates the JWT, and (in a
 * future session) will upsert the user and mint a local session. Currently,
 * Session 2 implements JWT validation only; user upsert + session + role-based
 * redirect land in Session 3 once the `users` migration exists.
 *
 * Contract:
 *  - Missing or invalid token → 400 Bad Request.
 *  - Valid token → 200 OK with a diagnostic body (Session 2 placeholder).
 *  - Never returns 500 for a predictably-invalid token; that would leak info.
 *
 * See:
 *  - .ai/sprints/sprint-01/sprint-plan.md §2 (authentication design)
 *  - .ai/.ai2/HARD_LESSONS.md HL-8 (why real auth matters from day one)
 */
final class Sso extends BaseController
{
    public function index(): ResponseInterface
    {
        $token = (string) $this->request->getGet('token');

        try {
            $validator = new JwtValidator();
            $claims    = $validator->validate($token);
        } catch (JwtValidationException $e) {
            log_message('warning', 'SSO validation rejected a token: {msg}', ['msg' => $e->getMessage()]);

            return $this->response
                ->setStatusCode(400)
                ->setContentType('text/plain')
                ->setBody('SSO token invalid: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            // Configuration error (e.g. missing HITCOURT_JWT_SECRET) — 500 is correct here.
            log_message('error', 'SSO configuration error: {msg}', ['msg' => $e->getMessage()]);

            return $this->response
                ->setStatusCode(500)
                ->setContentType('text/plain')
                ->setBody('SSO is not configured on this server.');
        }

        // TODO (Session 3):
        //   1. Upsert user in `users` table by hitcourt_user_id.
        //   2. Mint local CI4 session cookie.
        //   3. Redirect: role=coach → /coach, role=player → /player,
        //      role=admin or unknown → a "Fitness administration coming in
        //      a later release" placeholder page.
        //
        // Session 2 placeholder response — proves validation works end-to-end.

        return $this->response
            ->setStatusCode(200)
            ->setContentType('text/plain')
            ->setBody(sprintf(
                "SSO validation OK [Session 2 placeholder].\nrole=%s\nhitcourt_user_id=%s\nemail=%s\n(Session 3 will replace this with a real session + role-based redirect.)",
                (string) $claims['role'],
                (string) $claims['hitcourt_user_id'],
                (string) $claims['email'],
            ));
    }
}
