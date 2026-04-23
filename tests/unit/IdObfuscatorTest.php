<?php

declare(strict_types=1);

use App\Support\IdObfuscator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests for App\Support\IdObfuscator.
 *
 * The helper is a URL-opacity layer used wherever plan/assignment IDs appear
 * in user-visible routes. A bug here breaks every plan link globally, so the
 * tests cover:
 *   - Encode/decode round-trip for typical IDs
 *   - Garbage strings return null (not throw, not return 0)
 *   - Edge IDs (1, large) survive the round-trip
 *   - The PREFIX check — a base64 string that decodes to something else is
 *     rejected, even if the base64 itself is valid
 *
 * @internal
 */
final class IdObfuscatorTest extends CIUnitTestCase
{
    public function testRoundTripBasic(): void
    {
        foreach ([1, 42, 1337, 999_999] as $id) {
            $token = IdObfuscator::encode($id);
            $this->assertSame($id, IdObfuscator::decode($token), "round-trip failed for id={$id}");
        }
    }

    public function testEncodedStringIsUrlSafe(): void
    {
        // URL-safe alphabet: A-Z, a-z, 0-9, -, _  (no +, /, or =).
        $token = IdObfuscator::encode(42);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $token);
        $this->assertStringNotContainsString('+', $token);
        $this->assertStringNotContainsString('/', $token);
        $this->assertStringNotContainsString('=', $token);
    }

    public function testEncodedStringIsNotThePlainInteger(): void
    {
        // Sanity: the whole point is that the URL doesn't expose the ID.
        $token = IdObfuscator::encode(42);
        $this->assertNotSame('42', $token);
    }

    public function testDecodeEmptyStringReturnsNull(): void
    {
        $this->assertNull(IdObfuscator::decode(''));
    }

    public function testDecodeGarbageReturnsNull(): void
    {
        foreach (['garbage', '!!!not base64!!!', 'abc?def', '...'] as $junk) {
            $this->assertNull(IdObfuscator::decode($junk), "decode('{$junk}') should be null");
        }
    }

    public function testDecodeBase64WithoutPrefixReturnsNull(): void
    {
        // Valid base64 (URL-safe) but content is NOT `cf:<int>` — should reject.
        $bogus = rtrim(strtr(base64_encode('hello world'), '+/', '-_'), '=');
        $this->assertNull(IdObfuscator::decode($bogus));
    }

    public function testDecodeRejectsNonNumericIdAfterPrefix(): void
    {
        // Correct prefix, wrong payload shape.
        $bogus = rtrim(strtr(base64_encode('cf:not-a-number'), '+/', '-_'), '=');
        $this->assertNull(IdObfuscator::decode($bogus));
    }

    public function testDecodeRejectsZeroId(): void
    {
        // id=0 is not a valid plan row (AUTO_INCREMENT starts at 1); reject it
        // to avoid masking a programming error as a legitimate lookup.
        $zeroToken = rtrim(strtr(base64_encode('cf:0'), '+/', '-_'), '=');
        $this->assertNull(IdObfuscator::decode($zeroToken));
    }

    public function testDecodeRejectsPlainIntegerInput(): void
    {
        // Someone typing `/coach/plans/42` (plain int) should NOT silently work.
        // This enforces the "always route through the obfuscator" discipline.
        $this->assertNull(IdObfuscator::decode('42'));
    }
}
