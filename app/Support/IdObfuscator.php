<?php

declare(strict_types=1);

namespace App\Support;

/**
 * URL opacity helper — NOT cryptography.
 *
 * Encodes an integer ID into a URL-safe string so plan URLs look like
 *   /coach/plans/Y2Y6NDI
 * instead of the raw integer (/coach/plans/42). Reason: mirrors the
 * ltat-fitness base64 URL pattern the owner asked us to follow, and stops
 * casual ID-enumeration by people sharing links.
 *
 * The `cf:` prefix is a sanity marker — if decode receives a string that
 * doesn't base64-round-trip to `cf:<positive int>`, decode returns null. A
 * bad URL therefore becomes a clean 404 instead of a database miss.
 *
 * This is URL opacity, not security. Anyone can still guess IDs. Authorisation
 * on every controller action is the real access control.
 */
final class IdObfuscator
{
    private const PREFIX = 'cf:';

    public static function encode(int $id): string
    {
        $payload = self::PREFIX . $id;

        // URL-safe base64 (RFC 4648 §5): +/= → -_  (we strip = padding entirely).
        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    public static function decode(string $token): ?int
    {
        if ($token === '') {
            return null;
        }

        // Reverse the URL-safe swap, re-pad, base64_decode with strict mode.
        $unswapped = strtr($token, '-_', '+/');
        $padded    = $unswapped . str_repeat('=', (4 - strlen($unswapped) % 4) % 4);
        $decoded   = base64_decode($padded, true);

        if ($decoded === false) {
            return null;
        }

        if (! str_starts_with($decoded, self::PREFIX)) {
            return null;
        }

        $idString = substr($decoded, strlen(self::PREFIX));

        // ctype_digit rejects negatives, leading zeros, empty, whitespace, non-digits.
        if (! ctype_digit($idString) || $idString === '0') {
            return null;
        }

        return (int) $idString;
    }
}
