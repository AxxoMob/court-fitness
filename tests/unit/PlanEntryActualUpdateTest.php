<?php

declare(strict_types=1);

use App\Models\PlanEntriesModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Pure-function tests for the no-clobber rule on plan_entries.actual_json.
 *
 * Per .ai/.daily-docs/24 Apr 2026/prompt_for_session_6.md §"Known Risks #2":
 *   "If `plan_entries.actual_json IS NOT NULL` for an entry and the coach
 *    re-saves the plan, the actuals MUST NOT be clobbered. Test this
 *    explicitly. Write a unit test for it."
 *
 * The rule extends symmetrically to the player save path (Player\Plans::update).
 * Both controllers funnel their decision through PlanEntriesModel::decideActualUpdate
 * — this test exercises that pure function across the four meaningful cases.
 *
 * @internal
 */
final class PlanEntryActualUpdateTest extends CIUnitTestCase
{
    private const NOW         = '2026-04-26 14:00:00';
    private const SAVER_ID    = 42;
    private const COACH_BAG   = ['sets' => 4, 'reps' => 8, 'weight' => 60.0, 'rest_sec' => 120];
    private const PLAYER_BAG  = ['sets' => 4, 'reps' => 7, 'weight' => 55.0, 'rest_sec' => 120];

    public function testCleanFirstSaveWritesActualBagAndStampsSaver(): void
    {
        // Existing actual = NULL, submitted bag has values → write the bag + stamp saver.
        $diff = PlanEntriesModel::decideActualUpdate(
            existingActualJson: null,
            submittedBag:       self::COACH_BAG,
            savedByUserId:      self::SAVER_ID,
            nowDatetime:        self::NOW,
        );

        $this->assertIsArray($diff);
        $this->assertSame(json_encode(self::COACH_BAG, JSON_UNESCAPED_UNICODE), $diff['actual_json']);
        $this->assertSame(self::SAVER_ID, $diff['actual_by_user_id']);
        $this->assertSame(self::NOW, $diff['actual_at']);
    }

    public function testEmptyBagWithNoExistingActualWritesExplicitNulls(): void
    {
        // Existing actual = NULL, submitted bag is empty → write NULLs (idempotent).
        $diff = PlanEntriesModel::decideActualUpdate(
            existingActualJson: null,
            submittedBag:       [],
            savedByUserId:      self::SAVER_ID,
            nowDatetime:        self::NOW,
        );

        $this->assertIsArray($diff);
        $this->assertNull($diff['actual_json']);
        $this->assertNull($diff['actual_by_user_id']);
        $this->assertNull($diff['actual_at']);
    }

    /**
     * THE LOAD-BEARING NO-CLOBBER TEST.
     *
     * If a player has already logged actuals on an entry and the coach re-saves
     * the plan WITHOUT submitting actuals (e.g. they're editing only targets),
     * the player's actuals MUST be preserved — not nuked to NULL.
     *
     * `decideActualUpdate` returning NULL is the contract: "do not write to the
     * actual columns." The caller skips the diff merge and the row's existing
     * actual_json + actual_by_user_id + actual_at remain intact.
     */
    public function testNoClobberPreservesExistingActualsWhenSubmittedBagIsEmpty(): void
    {
        $existing = json_encode(self::PLAYER_BAG, JSON_UNESCAPED_UNICODE);

        $diff = PlanEntriesModel::decideActualUpdate(
            existingActualJson: $existing,
            submittedBag:       [],            // saver did not include actuals
            savedByUserId:      self::SAVER_ID,
            nowDatetime:        self::NOW,
        );

        $this->assertNull(
            $diff,
            'No-clobber violation: empty submitted bag with non-null existing actual_json must return null (preserve existing)'
        );
    }

    public function testNonEmptyBagOverwritesExistingAndStampsNewSaver(): void
    {
        // Coach corrects an actual the player had already logged.
        $existing = json_encode(self::PLAYER_BAG, JSON_UNESCAPED_UNICODE);

        $diff = PlanEntriesModel::decideActualUpdate(
            existingActualJson: $existing,
            submittedBag:       self::COACH_BAG,
            savedByUserId:      self::SAVER_ID,
            nowDatetime:        self::NOW,
        );

        $this->assertIsArray($diff);
        $this->assertSame(
            json_encode(self::COACH_BAG, JSON_UNESCAPED_UNICODE),
            $diff['actual_json'],
            'Non-empty submitted bag must overwrite existing actual_json (this is editing, not no-clobber)'
        );
        $this->assertSame(self::SAVER_ID, $diff['actual_by_user_id']);
        $this->assertSame(self::NOW, $diff['actual_at']);
    }

    public function testJsonOutputIsValidUtf8AndRoundTrips(): void
    {
        // Sanity: the JSON we write is parseable, and special characters survive.
        $bag = ['note' => 'felt 70 % effort', 'reps' => 6];

        $diff = PlanEntriesModel::decideActualUpdate(null, $bag, self::SAVER_ID, self::NOW);

        $this->assertNotNull($diff);
        $decoded = json_decode($diff['actual_json'], true);
        $this->assertSame($bag, $decoded);
    }
}
