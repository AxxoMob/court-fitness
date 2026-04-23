<?php

declare(strict_types=1);

use App\Services\JwtValidationException;
use App\Services\JwtValidator;
use CodeIgniter\Test\CIUnitTestCase;
use Firebase\JWT\JWT;

/**
 * Unit tests for App\Services\JwtValidator.
 *
 * Covers the SSO boundary discipline demanded by HL-8 — these tests MUST pass
 * before any feature controller is written. An auth bug at this boundary is a
 * silent security failure.
 *
 * @internal
 */
final class JwtValidatorTest extends CIUnitTestCase
{
    // A realistic-length HS256 secret for tests. Must match what validTokens() signs with.
    private const TEST_SECRET = 'test-secret-long-enough-to-be-safe-32-bytes-min-for-hs256';

    /**
     * Produce a valid JWT with optional claim overrides.
     *
     * @param array<string,mixed> $overrides
     */
    private function makeToken(array $overrides = [], string $secret = self::TEST_SECRET): string
    {
        $claims = array_merge([
            'email'            => 'test@example.com',
            'first_name'       => 'Test',
            'family_name'      => 'User',
            'hitcourt_user_id' => 42,
            'role'             => 'coach',
            'exp'              => time() + 30, // 30 seconds from now
        ], $overrides);

        return JWT::encode($claims, $secret, 'HS256');
    }

    public function testValidTokenReturnsClaims(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $claims    = $validator->validate($this->makeToken());

        $this->assertSame('test@example.com', $claims['email']);
        $this->assertSame('Test', $claims['first_name']);
        $this->assertSame('User', $claims['family_name']);
        $this->assertSame(42, $claims['hitcourt_user_id']);
        $this->assertSame('coach', $claims['role']);
    }

    public function testAcceptsAllKnownRolesFromHitCourt(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        foreach (['admin', 'coach', 'player'] as $role) {
            $claims = $validator->validate($this->makeToken(['role' => $role]));
            $this->assertSame($role, $claims['role'], "role={$role} should pass");
        }
    }

    public function testEmptyTokenRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Empty token');
        $validator->validate('');
    }

    public function testMalformedTokenRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $this->expectException(JwtValidationException::class);
        $validator->validate('this.is.not-a-real-jwt');
    }

    public function testExpiredTokenRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $token     = $this->makeToken(['exp' => time() - 1]);
        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Token expired');
        $validator->validate($token);
    }

    public function testInvalidSignatureRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $token     = $this->makeToken([], 'some-completely-different-secret-value-xyz');
        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Invalid signature');
        $validator->validate($token);
    }

    public function testMissingRoleClaimRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        // Build a JWT without `role` claim manually
        $claims = [
            'email'            => 'test@example.com',
            'first_name'       => 'Test',
            'family_name'      => 'User',
            'hitcourt_user_id' => 42,
            'exp'              => time() + 30,
        ];
        $token = JWT::encode($claims, self::TEST_SECRET, 'HS256');

        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Missing claim: role');
        $validator->validate($token);
    }

    public function testMissingHitCourtUserIdClaimRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        $claims    = [
            'email'       => 'test@example.com',
            'first_name'  => 'Test',
            'family_name' => 'User',
            'role'        => 'coach',
            'exp'         => time() + 30,
        ];
        $token = JWT::encode($claims, self::TEST_SECRET, 'HS256');

        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Missing claim: hitcourt_user_id');
        $validator->validate($token);
    }

    public function testExcessiveLifetimeRejected(): void
    {
        $validator = new JwtValidator(self::TEST_SECRET);
        // 2 hours — far beyond the 60-second ceiling
        $token = $this->makeToken(['exp' => time() + 7200]);

        $this->expectException(JwtValidationException::class);
        $this->expectExceptionMessage('Token life exceeds maximum');
        $validator->validate($token);
    }

    public function testConstructorRequiresSecret(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HITCOURT_JWT_SECRET is not configured');
        new JwtValidator('');
    }
}
