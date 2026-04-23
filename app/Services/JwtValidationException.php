<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Thrown by JwtValidator when a token fails any validation check.
 * Wraps any underlying firebase/php-jwt exception so callers can catch one type.
 */
final class JwtValidationException extends RuntimeException
{
}
