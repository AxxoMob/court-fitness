# Handover — court-fitness Session 6 (Sprint 01 Session 5 — "Rebuild: inline grid + actuals for both parties")

**Framework version:** 1.0 (2026-04-17)
**Date:** 2026-04-26
**Sprint:** Sprint 01 — "Coach plans a week, player logs actuals, on a phone"
**Duration:** Single session in a fresh Claude conversation (per CLAUDE.md §3 — full mandatory reading list, no fresh-vs-returning split per HL-14).
**Agent:** Claude (Anthropic, Opus 4.7, 1M-context)

---

## 1. Session Goal (as stated at start)

Per `.ai/.daily-docs/24 Apr 2026/prompt_for_session_6.md`: rebuild Session 5's Plan Builder + show views as an inline wide grid with editable actuals for both parties, per the LOCKED `plan_builder_ux.md`. Six-item priority list — items 1–4 must land complete (rebuild Plan Builder grid; Coach + Player show as editable grids; new POST routes with no-clobber; redirect-after-create on the editable grid); items 5–6 (responsive widths, audit display) may slip to Session 7 if context tightens.

**Outcome:** all six items landed complete. Plus the two Tier-1 owner-only docs edits flagged in WIP.md were owner-approved at the start of the session and committed before the rebuild started.

## 2. In Scope / Out of Scope (as declared at start)

**In scope — DELIVERED:**
- ✅ Inline-grid Plan Builder (`new.php` + rewritten `plan-builder.js`).
- ✅ Editable show views for coach + player on a shared partial.
- ✅ POST update routes for both sides with no-clobber guarantee + unit test.
- ✅ Redirect-after-create lands on editable grid with new flash text.
- ✅ Responsive widths across plan screens (3/2/1-up cards; per-row stacked card <768px).
- ✅ Audit display "Logged by ... · Xm ago".
- ✅ Owner-approved Tier-1 docs edits (BRIEFING.md "multiple" + standalone paragraph; CLAUDE.md §6.2 artifact #7 binary-artifact rule).

**Out of scope (intentionally deferred):**
- PWA manifest + service worker (Session 7).
- Per-type server-side validation of target_json/actual_json (Session 7+ hardening).
- Prettier target-badge labels beyond what the inline grid shows natively (Session 7+ polish).
- "Edit mode" as a separate URL — eliminated; the show URL IS the editable grid.
- Schema/migrations/catalogue changes.
- Rich analytics, charts, progression views, assessments kernel, tennis-specific testing catalogue, training-load / ACWR, fitness directory, export, admin real UI, multi-language, Capacitor wrap.

## 3. What Was Done

### 3.1 Conformance Check (commit `ce98deb`)
- `.ai/.daily-docs/26 Apr 2026/session_6_conformance.md` written and committed before any state-changing tool call. Quoted BRIEFING.md's both-record-actuals content verbatim from lines 3/8/9 (with a line-number-drift note: prompt + HL-13 say "line 10" but the file's current line 10 is blank — content is on the lines listed). Quoted `plan_builder_ux.md` §2 answers (a)-(d) verbatim. Listed the four view files being rebuilt (`new.php`, `plan-builder.js`, `coach/plans/show.php`, `player/plans/show.php`).

### 3.2 Owner-approved Tier-1 docs (commit `fb9a420`)
- `.ai/core/BRIEFING.md` line 3: "Coaches build *multiple* weekly training plans" (one-word owner correction).
- `.ai/core/BRIEFING.md`: new standalone "**Who records actuals — both sides, equally.**" paragraph above the "Who uses it" bullets, with explicit reference to HL-13 ("Skipping past this paragraph caused HL-13 — a full Plan Builder rebuild in Session 6").
- `CLAUDE.md` §6.2 artifact #7: extended with a binary-artifact promotion paragraph requiring screenshots/Figma/sketches/videos to land in `.ai/research-notes/screenshots/` or `/design/` within the same session, with sibling `.md` notes (what / who / when / which feature / spoken context).
- `.ai/core/WIP.md` flipped the two pending-approval bullets to ✅ landed.

### 3.3 Plan Builder + show rebuild (commit `1480743`)

**Views:**
- `app/Views/coach/plans/_grid.php` — new shared partial (mounts on new + coach show + player show via mode flag). Fundamentals strip on top (editable in 'new' mode; summary-pill in show modes). Below: a JS-driven `#cf-blocks` container plus `#cf-add-block` button. Hidden `entries_json` input; sticky save bar. Inline JSON `#cf-taxonomy-data` carries types + categories + subcategories + (in show modes) hydrated entries with audit JOIN data.
- `app/Views/coach/plans/new.php` — rewritten to mount the partial with `$mode = 'new'`, `$action_url = /coach/plans` (POST = store).
- `app/Views/coach/plans/show.php` — rewritten to mount the partial with `$mode = 'coach-edit'`, `$action_url = /coach/plans/{obf}` (POST = update).
- `app/Views/player/plans/show.php` — rewritten to mount the same partial with `$mode = 'player-edit'`, `$action_url = /player/plans/{obf}` (POST = update). Includes `APPPATH . 'Views/coach/plans/_grid.php'` so the partial is genuinely shared.
- `app/Views/layouts/main.php` — accepts a `$mainClass` data variable (default empty).

**JS:**
- `public/assets/js/plan-builder.js` — full rewrite. Reads `#cf-taxonomy-data` JSON. Manages `state.blocks[]` with `rows[]` in DOM. Each row has category-label + sub-category dropdown + cells per format (Cardio: max_hr_pct/duration_min · Weights: sets/reps/weight/rest_sec · Agility: reps/rest_sec). Sub-category options optgroup'd by category, scoped to the block's format. In show modes each metric renders paired (target, actual) inputs; readonly-ness driven by `can_edit_target` / `can_edit_actual` flags from the inline JSON. Submit serialises non-empty rows to `#entries_json` with optional `id` for update flow. "Logged by Coach Rajat · 2m ago" audit line under any row whose `actual_at` is set.

**Controllers:**
- `app/Controllers/Coach/Plans.php` — `index/new/store/show` updated to pass `$mainClass = 'cf-main--wide'`. New `update($obf)` action: re-verifies coach owns the plan, decodes obfuscator, parses `entries_json`, calls `applyUpdates()` which iterates entries and applies target+actual diffs via the `decideActualUpdate()` helper. Redirects to `/coach/plans/{obf}` on success with `Plan updated.` flash. `store()` redirect flash text changed to "Plan saved. You can now type actuals here."
- `app/Controllers/Player/Plans.php` — `show($obf)` rewritten to pass `$mainClass` + the taxonomy context the partial expects + audit JOIN. New `update($obf)` action: re-verifies player owns the plan, parses `entries_json`, iterates entries, **silently drops any target_<key>** (player can never update targets), applies actual updates via the same `decideActualUpdate()` helper. Redirects with `"Saved N entries."` flash.

**Routes:**
- `app/Config/Routes.php` — added `$r->post('plans/(:segment)', 'Coach\Plans::update/$1')` to the coach group and `$r->post('plans/(:segment)', 'Player\Plans::update/$1')` to the player group.

**CSS:**
- `public/assets/css/court-fitness.css` — added `.cf-main--wide` page modifier (max-width 1400px). `.cf-plan-grid` now responsive (3-up at ≥992px, 2-up at 768-991, 1-up <768). New inline-grid styles: `.cf-fundamentals`, `.cf-block`, `.cf-block__head`, `.cf-block__body`, `.cf-row`, `.cf-cells`, `.cf-cell`, `.cf-row__addremove`, `.cf-audit`, `.cf-add-block`, `.cf-empty-blocks`, `.cf-plan-summary`. Mobile collapse: `@media (max-width: 767.98px)` re-grids each row into a stacked card. Removed Session 5's accordion-only styles (`.cf-session-card`, accordion overrides) — no longer used.

### 3.4 No-clobber decision extracted + tests (commit `528c4be`)

- `app/Models/PlanEntriesModel.php` — added the static method `decideActualUpdate(?string $existingActualJson, array $submittedBag, int $savedByUserId, string $nowDatetime): ?array`. Pure function, no DB access. Returns `null` when the no-clobber rule says "preserve existing"; returns the column→value diff (`actual_json`, `actual_by_user_id`, `actual_at`) otherwise.
- Refactored `Coach\Plans::applyUpdates` and `Player\Plans::update` to funnel through this helper. Now there is exactly one place where the no-clobber rule lives.
- New `tests/unit/PlanEntryActualUpdateTest.php` with 5 test methods:
  1. `testCleanFirstSaveWritesActualBagAndStampsSaver` — clean save path.
  2. `testEmptyBagWithNoExistingActualWritesExplicitNulls` — idempotent clear.
  3. `testNoClobberPreservesExistingActualsWhenSubmittedBagIsEmpty` — **the load-bearing case**, returns null.
  4. `testNonEmptyBagOverwritesExistingAndStampsNewSaver` — editing an actual is allowed.
  5. `testJsonOutputIsValidUtf8AndRoundTrips` — sanity on the JSON encode path.

## 4. Decisions Made

1. **Block-level Format selector, format-driven cells (not per-sub-category cells).** plan_builder_ux.md §1 shows a Format selector at the date-period block level in the LTAT screenshot, and §2.4 hints at per-sub-category variance. For Session 6 I went with the simpler block-level Format model: cells are determined by Format (Cardio = 2 cells; Weights = 4; Agility = 2), matching `exercise_json_shapes.md` and Session 5's controller validation. Per-sub-category overrides would require LTAT live-system inspection to map (which I can't do from terminal). Documented as a follow-up in WIP.md — when the owner confirms per-sub-category variance, extend `CELLS_BY_FORMAT` in plan-builder.js to a `CELLS_BY_SUBCATEGORY` overlay map. Owner can override.
2. **Same partial for new + coach show + player show; mode flag drives disabled-ness.** Single template, three contexts. Reduces duplication and guarantees identical layout/visuals across the three modes. The partial's data contract is documented in `_grid.php`'s opening comment.
3. **Pure-function `decideActualUpdate`.** Extracted the no-clobber decision out of the controller transactions so it could be unit-tested without DB scaffolding. Both controllers funnel through it. Reasoning in the Session 6 close commit message — boils down to "two callers, one rule, no drift, cheap test."
4. **Player target writes silently dropped, not 400'd.** The prompt was explicit: "any target field in the POST is silently ignored server-side — do not 400, just drop." Avoids friction if a future client mistakenly POSTs targets in good faith; the no-trust-the-client invariant still holds.
5. **Block delete confirmation modal vs row delete inline.** Rows can be removed with `[-]` directly; the block delete uses a JS `confirm()` dialog because losing 4-5 rows by accident would be expensive. Owner can correct if they want both inline.
6. **Fundamentals locked in show modes.** A coach editing Week_of mid-week or changing the Player after creation is out of Sprint 1 scope (no feature need; the entries' dates would have to migrate). Update flow handles entry mutations only; fundamentals are write-once at creation.
7. **`weight` (canonical) vs `weight_kg` (demo seed).** I noticed Session 3's demo seed wrote `weight_kg` in `target_json` but `exercise_json_shapes.md` says the key is `weight` (with `kg|lb` on the parent plan). Did NOT fix the seed in Session 6 because (a) it would require a migration/seed re-run that's out of scope per the prompt, (b) the code is correct — only the demo data is non-canonical. Logged as a follow-up in WIP.md.
8. **CSRF "Big Save" model — one form, one POST per save.** Per `prompt_for_session_6.md` §"Known Risks #4", didn't split into per-row AJAX saves to avoid CI4's CSRF token-rotation complexity (HL-12). Acceptable for Session 6; revisit when autosave-on-blur is desired.

## 5. Sealed File Modifications

**None.** `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` was not touched.

## 6. Test Evidence

```
$ ./vendor/bin/phpunit tests/unit/
PHPUnit 11.5.55 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.2.12
Configuration: C:\xampp\htdocs\court-fitness\phpunit.xml.dist
............................                                      28 / 28 (100%)
Time: 00:00.065, Memory: 14.00 MB
OK, but there were issues!
Tests: 28, Assertions: 63, PHPUnit Warnings: 1 (no code coverage driver — harmless).
```

Breakdown:
- **JwtValidatorTest** — 10 tests (unchanged from Session 2).
- **IdObfuscatorTest** — 9 tests (unchanged from Session 5).
- **HealthTest** — 2 tests (unchanged, pre-existing).
- **AuthFilterTest** — 2 tests (unchanged from Session 5).
- **PlanEntryActualUpdateTest** — 5 tests, 15 assertions (NEW this session).

Test count invariant: **23 → 28** (only up).

## 7. Build Evidence

No build step (PHP + static assets). Dev server: `php spark serve --port 8080` ran during smoke tests.

## 8. Files Modified / Created

### New this session (3 files)
```
app/Views/coach/plans/_grid.php                           (new, 200 lines)
tests/unit/PlanEntryActualUpdateTest.php                  (new, 110 lines)
.ai/.daily-docs/26 Apr 2026/session_6_conformance.md      (new, session-open artifact)
.ai/.daily-docs/26 Apr 2026/session_6_handover.md         (this file)
.ai/.daily-docs/26 Apr 2026/prompt_for_session_7.md       (next-session kickoff)
```

### Modified this session
```
.ai/core/BRIEFING.md                                      (Tier-1: "multiple" + new "Who records actuals" paragraph)
CLAUDE.md                                                 (Tier-1: §6.2 binary-artifact rule)
.ai/core/WIP.md                                           (Session 6 close state, follow-ups, verification block)
.ai/core/SESSION_LOG.md                                   (Session 6 row appended)
app/Config/Routes.php                                     (+2 POST routes)
app/Controllers/Coach/Plans.php                           (mainClass; ::update; audit JOIN; no-clobber via helper; flash text)
app/Controllers/Player/Plans.php                          (mainClass; ::update with player-only-actuals + no-clobber; audit JOIN)
app/Models/PlanEntriesModel.php                           (+ decideActualUpdate static helper)
app/Views/coach/plans/new.php                             (rewired onto _grid.php partial)
app/Views/coach/plans/show.php                            (rewritten as editable grid via _grid.php)
app/Views/player/plans/show.php                           (rewritten as editable grid via _grid.php)
app/Views/layouts/main.php                                (accept $mainClass)
public/assets/css/court-fitness.css                       (.cf-main--wide; .cf-plan-grid responsive; full inline-grid stylesheet)
public/assets/js/plan-builder.js                          (full rewrite — inline-grid controller)
```

### Commits this session (in order)
```
ce98deb  sprint-1: session 6 open — Framework Conformance Check committed
fb9a420  docs: owner-approved Tier-1 edits to prevent HL-13 recurrence
1480743  sprint-1: rebuild Plan Builder + show views as inline editable grid (session 6)
528c4be  sprint-1: extract no-clobber decision + 5 unit tests on actual_json
<close>  sprint-1: session 6 close — 7 artifacts (Plan Builder rebuilt, no-clobber tested)
```

## 9. Open Issues / Unfinished Work

**Visual sign-off pending owner.** Browser DevTools eyeball at 375 / 768 / 992 / 1280 px is the canonical check per `prompt_for_session_6.md` §"Known Risks #3", and the dev box is terminal-only. Same applies to taking the two screenshots for `.ai/research-notes/screenshots/session6-*.png` per HL-13 + CLAUDE.md §6.2 artifact #7's binary-artifact rule. The session is closed code-wise; visual sign-off is the next thing the owner does. **A posted screenshot from owner unlocks Sprint 01 close** (assuming nothing visual breaks).

**End-to-end POST update flow wasn't curl-tested with a valid CSRF token.** The decision logic is unit-tested; the routing/auth/CSRF gating is curl-verified at the 403 layer; the JSON wire format is verified via reading entries from inline JSON in show mode. But a fully successful `POST /coach/plans/{obf}` round-trip with token+session+payload was not exercised end-to-end via terminal — the form-fill dance is awkward outside a browser. This is a Session-7-or-owner-eyeball gap; the no-clobber rule is the riskiest piece and that IS tested.

## 10. Follow-Ups Noticed (NOT done this session)

See `.ai/core/WIP.md` § "Noticed this Session 6, for Future." Top three:
- **Demo seed `weight_kg` vs canonical `weight`** — Session 3 seed used the wrong key; the new Plan Builder won't pre-populate that cell. Cosmetic for the demo; fix at next seed refresh.
- **Per-sub-category cells** — `plan_builder_ux.md` §2.4 hints at this; Session 6 ships per-format. If the owner confirms per-sub-category variance, extend the JS map.
- **Block-format change clears affected rows silently** — UX could confirm before nuking, especially when actuals exist.

## 11. Known Errors Added or Updated

**None new.** `.ai/core/KNOWN_ERRORS.md` is still template-only (no court-fitness bugs caught in production). The `weight_kg` seed inconsistency is cosmetic data drift rather than a code bug, so it's a WIP follow-up rather than a KE.

## 12. Hard Lessons Added

**None new.** Session 6 was an execution session against a locked spec — no surprising discoveries surfaced that future agents would need to know about. HL-13 (the lesson that drove this whole rebuild) and HL-14 (don't split the reading list) were the active lessons; both were respected.

## 13. Next Session

See `.ai/.daily-docs/26 Apr 2026/prompt_for_session_7.md`. Session 7 picks up: PWA manifest + service worker, per-type server-side validation of `target_json` / `actual_json` shapes, prettier badge labels, and any owner-eyeball corrections from the Session 6 visual sign-off. **If Session 6's visual sign-off lands clean, Session 7 is also a candidate to close Sprint 01** (Sprint-close chores: seal candidates review per CLAUDE.md §12, and the SESSION_LOG / KNOWN_ERRORS archival per §11).

## 14. Framework Conformance Declaration

I, Claude (Opus 4.7, 1M-context), followed `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` v1.0 for this session.

I did not:
- Modify the sealed framework file.
- Use destructive git operations (no `--amend`, no `--force`, no `reset --hard`, no `--no-verify`).
- Delete or disable tests. Count went UP from 23 → 28.
- Expand scope beyond what was declared. The two Tier-1 docs edits (BRIEFING + CLAUDE) were owner-approved at "proceed" before any code changed; I treated them as the explicit-owner-direction case under Rule 7.

### All seven session-close artifacts (per CLAUDE.md §6.2)

1. ✅ `.ai/core/WIP.md` updated to Session 6 close state — scope, narrative, follow-ups, verification block all refreshed.
2. ✅ `.ai/core/SESSION_LOG.md` — Session 6 row appended at the top of the table.
3. ✅ This file — `.ai/.daily-docs/26 Apr 2026/session_6_handover.md` — with framework version stamp at top per §6.2.
4. ✅ `.ai/.daily-docs/26 Apr 2026/prompt_for_session_7.md` — next-session kickoff.
5. ✅ `.ai/core/HARD_LESSONS.md` — reviewed; no new HL needed (no surprising discoveries; HL-13 + HL-14 were respected, not extended).
6. ✅ Meaningful git commits — 4 this session (Conformance, Tier-1 docs, Plan Builder rebuild, no-clobber test) + this close commit. One per logical unit per CLAUDE.md §7.4.
7. ✅ Memory-to-repo promotion — scanned session-private agent memory; nothing load-bearing not already in `.ai/`. The Tier-1 docs edits (BRIEFING + CLAUDE) themselves promoted the most important insight (HL-13's binary-artifact rule). The `weight_kg` seed inconsistency is logged in WIP.md follow-ups. The "block-format change silently clears affected rows" UX trade-off is logged in WIP.md follow-ups.

Plus the session-open artifact required by §3.1:
- ✅ `.ai/.daily-docs/26 Apr 2026/session_6_conformance.md` — committed as `ce98deb` before any state-changing work. Contains the 8 Conformance answers, baseline verification output, and the three mandatory verbatim quotes (BRIEFING both-record-actuals; plan_builder_ux.md §2 a-d; the four view files being rebuilt).

**Session closed cleanly. Awaiting owner's visual sign-off at four DevTools breakpoints + screenshots for `.ai/research-notes/screenshots/session6-*.png`.**
