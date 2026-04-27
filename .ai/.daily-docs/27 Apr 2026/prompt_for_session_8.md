## Session Onboarding — court-fitness (Session 8, Sprint 01 Session 7)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at C:\xampp\htdocs\court-fitness on Windows 11 XAMPP. Production URL: https://fitness.hitcourt.com.

**Incoming AI Agent must not skim past any of the files. A thorough read is a must.** Sessions 5, 6, and 7 each went through multiple iterations because of skim-and-guess failures (HL-13). Don't be the next instance.

This is a long-horizon project under a strict AI Agent Framework. Before you do ANYTHING, read these docs **in order**:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (repo root) — project-specific conventions. §3 (reading), §5 (architecture), §6 (lifecycle).
3. `.ai/README.md` — folder map.
4. `.ai/core/BRIEFING.md` — 1-page project overview. **The "Who records actuals — both sides, equally" paragraph (line 7) is load-bearing — quote it verbatim in your Conformance Check.**
5. `.ai/core/WIP.md` — current state. **The "Sprint 02 reservations" section at the very TOP of the file is locked owner directive — do not touch the locked items in Sprint 01.**
6. `.ai/core/SESSION_LOG.md` — 7 rows now (plus a Session 6 addendum).
7. `.ai/core/HARD_LESSONS.md` — fourteen entries (HL-1..HL-14).
8. `.ai/core/SEALED_FILES.md` — **NINE sealed files now (was 6 at start of Session 7).** Read carefully. Modifying any sealed file without owner approval per AI_AGENT_FRAMEWORK.md §5.3 is a critical framework violation.
9. `.ai/core/KNOWN_ERRORS.md` — still template-only.
10. `.ai/core/ltat-fitness-findings.md`.
11. `.ai/core/plan_builder_ux.md` — SEALED.
12. `.ai/core/exercise_json_shapes.md` — JSON contract per format.
13. `.ai/sprints/sprint-01/sprint-plan.md`.
14. `.ai/sprints/sprint-02/sprint-plan.md` — **NEW. The Sprint 02 reservations locked at Session 7 close. Read in full.**
15. `.ai/research-notes/screenshots/` — screenshots + sibling notes.
16. `.ai/.daily-docs/26 Apr 2026/session_6_handover.md`.
17. `.ai/.daily-docs/26 Apr 2026/session_6_addendum_handover.md`.
18. `.ai/.daily-docs/27 Apr 2026/session_7_handover.md` — **Session 7 close, including all 11 commits + sealed-file modifications + Sprint 02 lock-in.**

After reading, DO NOT touch code yet. Run the **Framework Conformance Check** in chat AND commit it to `.ai/.daily-docs/{today}/session_8_conformance.md` per CLAUDE.md §3.1 + §6.1 before requesting "proceed." Quote BRIEFING.md's "Who records actuals" paragraph verbatim. List the **9** sealed files explicitly and confirm §5.3 understanding. Confirm awareness of the Sprint 02 reservations.

---

## Context — what just happened

Session 7 (2026-04-27) shipped a major polish round + plans-index rebuild + Sprint 02 lock-in across 12 commits:

1. Mobile-responsive pass on the sealed Plan Builder (rounds 1-4, four §5.3 unsealings):
   - Collapse threshold raised <992px → <1200px.
   - Block-head 4-col single-row at 480-1199px.
   - Cat + Sub side-by-side at 480-1199px (boundary lowered from 576 to 480 so 568px landscape phones get the compact layout).
   - Save button shrunk btn-lg → btn-sm.
   - Defensive `width: 100%` on form-controls in the block head.
   - Target/Actual placeholders capitalised; cell colours visibly differentiated (warm beige vs soft green); "+ Add Training Week / Date / Session" button label.
   - Redundant per-block "+ Add exercise" button removed (row-level [+] does the same job).

2. Plans-index page rebuild on `/coach/plans` and `/player`:
   - Filter strip at top (Year + Week Of From/To + Player on coach side / Coach on player side).
   - Fixed-size cards via `grid-template-rows: auto auto 1fr auto`.
   - Format chips per card (max 3): Cardio green / Weights amber / Agility blue.
   - Long Training Target ellipsis-truncates instead of breaking the layout.
   - "+ New Plan" button moved to top-right of `/coach/plans` header (was at bottom).

3. Three new seals registered (sealed file count 6 → 9):
   - `app/Views/coach/plans/index.php`
   - `app/Views/player/dashboard.php`
   - `public/assets/css/court-fitness.css` between SEALED-BEGIN: Plans-index page styles and SEALED-END markers.

4. Round 7/7b/7c late-day polish (three small §5.3 unsealings):
   - "← Back To My Plans" moved INTO the sticky save bar to the LEFT of "Save Changes" with brand-secondary outlined-orange colour (replacing the standalone bottom-of-page back-link).
   - Both Back and Save matched to `btn-sm` size.
   - "Save Changes" / "Save Plan" capitalised (capital C, capital P).

5. **Sprint 02 reservations locked in three places** (sprint-02 plan + WIP top + Session 7 handover):
   - Trainer ↔ Player association: Option 1 (Player Invite Code, expires after first use OR 24h, for standalone freelance trainers) PAIRED with Option 4 (Admin Assignment, for institutional 1(b) setup-managers).
   - `users.role` enum gains `admin` value.
   - New `trainer_invite_codes` table.
   - First-visit role-choice screen on SSO landing.

Test suite at Session 7 close: **28/28 passing, 63 assertions.**

---

## What Was Done at Session 7 Close (2026-04-27)

See `.ai/.daily-docs/27 Apr 2026/session_7_handover.md` §3 for the commit-by-commit walkthrough. Twelve commits. Sealed file count 6 → 9. Zero unauthorised sealed-file edits.

---

## What Needs To Be Done Now (Session 8)

> **Owner expectation (round 7 / 7b / 7c shipped without final screenshots — owner called off the day):** before any new work, hard-refresh + take a single screenshot of `/coach/plans/Y2Y6MQ` (or any open plan) to confirm the round-7 save-bar layout (Back + Save buttons, both btn-sm, Back outlined-orange, Save solid-orange) matches the `back-to-my-plans.png` annotation. If anything's off, Session 8's first item is the fix. Then proceed.

### Session 8 priority order

**Pre-work:**
1. Hard-refresh + visually confirm round-7 save-bar layout. If broken, fix first.
2. Hard-refresh + visually confirm `/coach/plans` and `/player` plans-index pages at the 5 breakpoints (375 / 568 / 768 / 992 / 1280 px). Save 5 screenshots into `.ai/research-notes/screenshots/session8-{viewport}px.png` with sibling `.md` notes per HL-13 + CLAUDE.md §6.2 artifact #7.
3. If everything's clean, proceed with the priority list below. If not, fixes first.

### Priority list (Session 8)

1. **PWA manifest + service worker.** Long-deferred from Sprint 01's plan §1.
   - `public/manifest.json` — app name, icons (192 + 512 px), theme `#F26522`, start `/`, display `standalone`.
   - `public/sw.js` — minimal: cache the app shell + offline fallback page. NO caching of `/coach/plans/*`, `/player/plans/*`, `/sso`, `/dev/*` (these stay network-first to avoid stale auth/data).
   - `public/assets/icons/icon-192.png`, `icon-512.png` — placeholder PNGs OK in Sprint 01; real brand icons in Sprint 02+.
   - `<link rel="manifest">` + `<meta name="theme-color">` + service-worker registration in `app/Views/layouts/main.php`.
   - Test: Chrome DevTools → Application tab → Manifest renders + Service Worker registers + "Add to home screen" CTA appears. Lighthouse PWA score noted (auditing only, not blocking).

2. **Per-type server-side validation of `target_json` and `actual_json`.** Currently UI-only; a malicious POST can store any JSON shape.
   - New `app/Validation/PlanEntryShapeValidator.php` (or static method on `PlanEntriesModel`). For each exercise_type, validate that the bag's keys match `exercise_json_shapes.md` and values are within bounds (sets 1-20, reps 1-100, etc.).
   - Wired into `Coach\Plans::store` / `::update` and `Player\Plans::update` AFTER the no-clobber check, BEFORE the DB write.
   - Tests in `tests/unit/PlanEntryShapeValidatorTest.php` covering each format's happy path + at least one rejection per format.
   - **Reject** unknown keys silently? Or 400? Default: silently strip unknown keys, log a warning. Reject only on out-of-bounds values. Owner can override.

3. **Prettier label prettifier in plan-builder.js** for tooltips/summaries. Currently the cell labels are "Max HR %", "Duration", etc.; the placeholder text and tooltips render literally as `max_hr_pct: 75`. Polish — small JS map from snake_case key → display label.

4. **No-clobber unit test for player-update silent-drop of target fields.** Currently behaviour is enforced (`Player\Plans::update` silently drops any `target_<key>` in POST), but not yet unit-tested. Add a test that builds a fake POST with both `target` and `actual` keys, runs through the player update path, and asserts `target_json` is NOT modified while `actual_json` IS.

5. **Empty-block edge case.** When a coach removes the last exercise row from a block via `[-]`, the block has no rows and no row-level `[+]` to add one back. Either disable `[-]` when only 1 row remains, or auto-delete the block when emptied. Owner to decide. Sprint 02 candidate; pick up if Session 8 has time.

### Session 8 WILL NOT do

- Modify any of the 9 sealed files without explicit §5.3 owner approval.
- Touch the Sprint 02 reservations.
- Schema/migrations/catalogue changes.
- Capacitor wrap.
- Assessments / metric_types kernel.
- Tennis-specific testing catalogue.
- Multi-language.

### Sprint-close candidate

If Session 8 lands PWA + per-type validation cleanly, **Sprint 01 is a close candidate**. Per CLAUDE.md §11 + §12:
- **Seal candidates review** at sprint close — the watchlist in SEALED_FILES.md: JwtValidator, IdObfuscator, the 3 catalogue seed migrations, Sso, PlanEntriesModel::decideActualUpdate, the two ::update controller actions.
- **Archival** of SESSION_LOG / KNOWN_ERRORS — too small still; nothing to archive yet.
- **HL_INDEX.md** kicks in at HL-20 — currently at HL-14, six entries away.

---

## Key Conventions (quick reference)

- **9 sealed files now** (was 1 at start of Sprint 01). Read SEALED_FILES.md before any UI edit.
- **Capitalisation convention** (locked Session 7 round 6): "Headings/buttons/labels which are not a sentence start with Capital Letter." Apply opportunistically when you touch a label; don't sweep retroactively.
- **Foreign keys ARE used.** Engineering decision (HL-6).
- **ONE migrations folder.** Raw-SQL workaround is NEVER the answer (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder, not a coder. No jargon without definition.
- **Captain / Engine Engineer model.** Technical decisions within scope are yours by default; UX / scope / priority / product is his. Plus the "act if consequential, defer if not" judgment-call delegation.
- **Responsive, not mobile-first.** Desktop/tablet/iPad primary; mobile is adaptive. Sealed view is the desktop baseline; mobile-responsive collapse already lives in the sealed CSS.
- **Falcon theme cohesion** (CLAUDE.md §5.4). Font stack + 16.8px base.
- **CSRF + AuthFilter alive globally.**
- **Single-form Big Save model.** No per-row AJAX (HL-12 + CI4 token rotation).
- **Commit per logical unit.** Session 7 had 12 commits — that's the cadence.
- **No --amend, no --force, no --no-verify.**
- **Seven session-close artifacts mandatory** (CLAUDE.md §6.2).
- **Begin session close at 70% context.**
- **Browser cache surprises:** Chrome aggressively caches CSS + JS. After any change, hard-refresh (Ctrl+F5) before taking screenshots. Round-2 Session 7 lost time to this.

---

## Verification Commands

End-of-Session-8 targets:

```
cd C:/xampp/htdocs/court-fitness

# Tests — should be 28+ existing + at least 5 new (PWA tests + shape-validator tests + no-clobber-test)
./vendor/bin/phpunit tests/unit/                        # >= 33

# Migrations unchanged
php spark migrate:status                                # 3 migrations applied

# Dev server (note: .env app.baseURL = http://localhost:8080/, so use port 8080)
php spark serve --port 8080                             # background

# PWA verification
#   /manifest.json → 200, valid JSON
#   /sw.js → 200, text/javascript
#   Chrome DevTools → Application tab → manifest renders + SW registers
#   Lighthouse PWA audit baseline noted

# Shape validation
#   POST /coach/plans/{obf} with invalid target_json (e.g. sets=999) → 400 with field-level error
#   POST /coach/plans/{obf} with unknown keys (e.g. {"foo": "bar"}) → 200 (strips, logs warning)

# Regression
#   /coach/plans/new, /coach/plans/{obf}, /player/plans/{obf} all 200
#   /coach/plans, /player both render filter strip + format chips
#   no-clobber test still passing

git status                                              # clean at session close
git log --oneline -15                                   # >= 4 meaningful commits this session
```

---

## Known Risks (descending likelihood)

1. **Touching a sealed file accidentally.** 9 sealed files now. Always check SEALED_FILES.md before editing.
2. **PWA scope creep.** Workbox routing, push notifications, install prompts, and splash screens are interesting and out of scope for Sprint 01 PWA scaffolding. The bar is: manifest + minimal SW + offline shell. Anything more is Sprint 02+.
3. **Service worker caching wrong things.** Network-first or no-cache for `/coach/plans/*`, `/player/plans/*`, `/sso`, `/dev/*`. Cache only the static app shell.
4. **Shape validator that's too aggressive** rejects legitimate edge cases. The validator should silently strip unknown keys (forward-compat) but reject out-of-bounds values. Don't 400 on shape mismatches the UI can't even produce.
5. **Owner stakeholder meeting — Monday morning was 2026-04-27.** If a feature pivot emerged from that meeting, Session 8 may need to absorb it. Communicate clearly and treat the pivot as Rule 7 owner-direction.
6. **HL-13 relapse on screenshots.** Take screenshots BEFORE code changes, save into `.ai/research-notes/screenshots/`, write sibling `.md` notes immediately.

---

## When In Doubt

- Unclear UX detail → `.ai/core/plan_builder_ux.md` first. If not there, ASK Rajat (Rule 9).
- Unclear what's sealed → `.ai/core/SEALED_FILES.md`.
- Unclear past decision → `.ai/core/HARD_LESSONS.md` + Session 7 handover + Session 6 handover + Session 6 addendum.
- Tempted by destructive git → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL → STOP (HL-1).
- Tempted to mock auth → NEVER (HL-8).
- Tempted to modify a sealed file silently → STOP. Follow §5.3.
- Tempted to skim BRIEFING / SEALED_FILES.md / Sprint 02 plan → STOP (HL-13, HL-14).
- Owner shares a screenshot → save to `.ai/research-notes/screenshots/` with sibling `.md` note immediately.

Good luck. Run the Conformance Check, commit it, wait for "proceed," then PWA + validation + prettifier in that order. If Sprint 01 closes in this session, do the sprint-close chores per CLAUDE.md §11 + §12.
