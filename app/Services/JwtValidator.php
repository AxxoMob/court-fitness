<?php

declare(strict_types=1);

namespace App\Services;

use DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Validates HS256 JWTs issued by HitCourt and returns the claims.
 *
 * Responsibilities (and ONLY these):
 *  - Verify the HS256 signature against HITCOURT_JWT_SECRET.
 *  - Reject expired tokens.
 *  - Reject tokens whose `exp` claim is more than MAX_TOKEN_LIFE_SECONDS in the future
 *    (defence against overly-permissive signing by a compromised issuer).
 *  - Reject tokens missing any required claim.
 *  - Return the decoded claims as an associative array on success.
 *
 * NOT this class's job: database writes, session creation, routing, logging.
 *
 * Discipline reference: `.ai/.ai2/HARD_LESSONS.md` HL-8 — ltat-fitness shipped
 * with mock auth; court-fitness gates every feature behind a tested /sso.
 */
final class JwtValidator
{
    /**
     * Hard ceiling on token life regardless of what `exp` claim says.
     * HitCourt's own spec (per owner 2026-04-22) is 60 seconds, but we enforce
     * it here too as belt-and-braces.
     */
    public const MAX_TOKEN_LIFE_SECONDS = 60;

    /**
     * Claims HitCourt must include in every SSO JWT. If any are missing, reject.
     */
    public const REQUIRED_CLAIMS = [
        'email',
        'first_name',
        'family_name',
        'hitcourt_user_id',
        'role',
        'exp',
    ];

    private readonly string $secret;

    /**
     * @param string|null $secret Shared HS256 secret. If null, reads from HITCOURT_JWT_SECRET env var.
     * @throws RuntimeException if no secret is available.
     */
    public function __construct(?string $secret = null)
    {
        $this->secret = $secret ?? (getenv('HITCOURT_JWT_SECRET') ?: '');
        if ($this->secret === '') {
            throw new RuntimeException('HITCOURT_JWT_SECRET is not configured');
        }
    }

    /**
     * Validates a JWT and returns its claims.
     *
     * @param string $token The raw JWT string from the `?token=` query parameter.
     *
     * @throws JwtValidationException on any validation failure.
     *
     * @return array{email:string,first_name:string,family_name:string,hitcourt_user_id:int|string,role:string,exp:int} Decoded claims.
     */
    public function validate(string $token): array
    {
        if ($token === '') {
            throw new JwtValidationException('Empty token');
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (ExpiredException $e) {
            throw new JwtValidationException('Token expired', 0, $e);
        } catch (SignatureInvalidException $e) {
            throw new JwtValidationException('Invalid signature', 0, $e);
        } catch (BeforeValidException $e) {
            throw new JwtValidationException('Token not yet valid', 0, $e);
        } catch (UnexpectedValueException | DomainException $e) {
            throw new JwtValidationException('Malformed token: ' . $e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            // Defence-in-depth: any other exception from firebase/php-jwt
            // (e.g. internal JSON decode errors, base64 failures) becomes a
            // JwtValidationException so callers have one type to handle.
            throw new JwtValidationException('Malformed token: ' . $e->getMessage(), 0, $e);
        }

        $claims = (array) $decoded;

        foreach (self::REQUIRED_CLAIMS as $claim) {
            if (! array_key_exists($claim, $claims)) {
                throw new JwtValidationException("Missing claim: {$claim}");
            }
        }

        // Enforce maximum token life beyond what firebase/php-jwt checks,
        // in case HitCourt ever signs a token with an inappropriately long exp.
        $remaining = (int) $claims['exp'] - time();
        if ($remaining > self::MAX_TOKEN_LIFE_SECONDS) {
            throw new JwtValidationException(
                "Token life exceeds maximum ({$remaining}s > " . self::MAX_TOKEN_LIFE_SECONDS . 's)'
            );
        }

        return $claims;
    }
}
