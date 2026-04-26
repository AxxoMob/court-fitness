## Session Onboarding — court-fitness (Session 7, Sprint 01 Session 6)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at C:\xampp\htdocs\court-fitness on Windows 11 XAMPP. Production URL: https://fitness.hitcourt.com.

**Incoming AI Agent must not skim past any of the files. A thorough read is a must.** Session 5 skimmed and shipped the wrong UI; Session 6 had to rebuild it. HL-13 + HL-14 are the standing rules.

This is a long-horizon project under a strict AI Agent Framework. Before you do ANYTHING, read these docs **in order**:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (repo root) — project-specific conventions. §3 (reading protocol), §5 (architecture), §6 (session lifecycle) especially. **§6.2 artifact #7 was extended at Session 6 open with a binary-design-artifact rule** — every screenshot/Figma/sketch/video the owner shares lands in `.ai/research-notes/screenshots/` or `/design/` within the same session, not at session close.
3. `.ai/README.md` — folder map.
4. `.ai/core/BRIEFING.md` — 1-page project overview. **The "Who records actuals — both sides, equally" paragraph (added Session 6) is load-bearing — quote it verbatim in your Conformance Check.** This is the document that, when skimmed past, caused Session 5's full UI rebuild.
5. `.ai/core/WIP.md` — current state.
6. `.ai/core/SESSION_LOG.md` — six rows now (Sessions 1-6).
7. `.ai/core/HARD_LESSONS.md` — fourteen entries (HL-1..HL-14). HL-13 (design artifacts to repo immediately) and HL-14 (don't split the reading list) are the most active; read them carefully.
8. `.ai/core/SEALED_FILES.md` — one sealed file (the framework itself).
9. `.ai/core/KNOWN_ERRORS.md` — still template-only; no open KEs in court-fitness code.
10. `.ai/core/ltat-fitness-findings.md` — predecessor-project findings.
11. `.ai/core/plan_builder_ux.md` — LOCKED UX spec for the inline editable grid. Implemented Session 6; visual eyeball pending Session 7. Read before any UI edit.
12. `.ai/core/exercise_json_shapes.md` — canonical keys for `target_json` / `actual_json` per exercise type. Server-side validation of these shapes is Session 7 scope.
13. `.ai/sprints/sprint-01/sprint-plan.md` — sprint playbook.
14. `.ai/.daily-docs/26 Apr 2026/session_6_handover.md` — what Session 6 shipped + open issues + decisions made.

After reading, DO NOT touch code yet. Run the **Framework Conformance Check** (Appendix D of `AI_AGENT_FRAMEWORK.md`) in chat AND commit it to `.ai/.daily-docs/{today}/session_7_conformance.md` per CLAUDE.md §3.1 + §6.1 before requesting "proceed." Quote BRIEFING.md's "Who records actuals" paragraph verbatim. Cite plan_builder_ux.md §2 answers (a)-(d) verbatim if your work touches the inline grid.

---

## Context

Sprint 01 Session 6 (last session) **rebuilt the Plan Builder + show views** as a wide inline editable grid per plan_builder_ux.md (LOCKED). Backend persistence + SSO + AuthFilter + CSRF + IdObfuscator + DB schema are unchanged from Session 5. New:
- `app/Views/coach/plans/_grid.php` is the shared partial (mounts on new + coach show + player show via `mode` flag).
- `public/assets/js/plan-builder.js` is rewritten as inline-row controller. Blocks group rows by (date, period, format); each row has category-label + sub-category dropdown + type-specific cells (Cardio/Weights/Agility per `exercise_json_shapes.md`).
- `POST /coach/plans/{obf}` and `POST /player/plans/{obf}` are live. Coach can edit targets+actuals; player can only edit actuals (any target_<key> in player POST is silently dropped server-side).
- `PlanEntriesModel::decideActualUpdate()` is the pure-function home of the no-clobber rule, with 5 unit tests.
- `Coach\Plans::store` redirect-after-create lands on the editable grid with flash "Plan saved. You can now type actuals here."
- Responsive: `.cf-main--wide` page modifier (1400px); `.cf-plan-grid` 3/2/1-up at 992/768/375; per-row stacked card collapse <768px via CSS media query.
- Audit display "Logged by Coach Rajat · 2m ago" rendered under each logged actual.

Tier-1 owner-only docs were edited at Session 6 open (BRIEFING.md gains "multiple" + standalone "Who records actuals" paragraph; CLAUDE.md §6.2 artifact #7 codifies HL-13's binary-artifact rule).

**Tech stack:** PHP 8.2.12 / CodeIgniter 4.7.2 / MySQL 8.0+ / Bootstrap 5.3.3 via CDN / Poppins 16.8px Falcon stack. Brand: `#F26522`. CSRF + AuthFilter alive globally with except list.

Test suite at Session 6 close: **28/28 passing, 63 assertions** (10 JwtValidator + 9 IdObfuscator + 2 HealthTest + 2 AuthFilter + 5 PlanEntryActualUpdate).

Demo URLs (work end-to-end through SSO stub):
- /dev — stub SSO landing
- /dev/sso-stub?as=coach — Coach Rajat
- /dev/sso-stub?as=player — Rohan
- /coach, /coach/plans, /coach/players, /coach/plans/new, /coach/plans/{obf}
- /player, /player/plans/{obf}
POST /coach/plans/{obf} + POST /player/plans/{obf} are CSRF-gated.

---

## What Was Done Last Session (Session 6, 2026-04-26)

1. Owner-approved Tier-1 docs edits (commit `fb9a420`): BRIEFING.md "multiple" + standalone "Who records actuals" paragraph; CLAUDE.md §6.2 binary-artifact rule.
2. Plan Builder + show views rebuilt as inline editable grid (commit `1480743`). Shared `_grid.php` partial; rewritten `plan-builder.js`; `Coach\Plans::update` + `Player\Plans::update` actions; new POST routes; redirect-after-create on the editable grid; responsive widths; audit display.
3. No-clobber decision extracted into `PlanEntriesModel::decideActualUpdate()` + 5 unit tests (commit `528c4be`). Suite 23 → 28.
4. Smoke verified via curl: 200 on all three view modes; correct `data-cf-mode` + `can_edit_*` flags; 403 on tokenless POST to both new routes.

**Session 6 visual sign-off was deferred to owner.** Browser DevTools eyeball at 375 / 768 / 992 / 1280 px and screenshot capture cannot be done from a terminal; the human is the canonical visual checker per HL-13 / Session 6 prompt §"Known Risks #3."

---

## What Needs To Be Done Now (Session 7 — "Visual sign-off + PWA + per-type validation")

### Pre-work — visual eyeball at four breakpoints (the owner may have done this between Session 6 and Session 7; check chat)

If the owner has NOT yet eyeballed Session 6's grid:
1. Ask them to walk through the four flows in `prompt_for_session_6.md` §"Verification Commands" via Chrome DevTools (375 / 768 / 992 / 1280 px) — Coach create plan, Player log actuals on mobile, no-clobber, responsive resize, CSRF gate.
2. Save their screenshots to `.ai/research-notes/screenshots/session6-{viewport}px.png` with sibling `.md` notes (HL-13 / CLAUDE.md §6.2 artifact #7 — what / who / when / which feature / spoken context).
3. Treat any visual issues they raise as the **first thing** Session 7 fixes — those are higher-priority than the items below.

If the owner already eyeballed and signed off (or asked you to start Session 7 anyway), proceed.

### Session 7 priority order

1. **PWA manifest + service worker.** Long-deferred from Sprint 01's plan §1.
   - `public/manifest.json` — app name, icons (192 + 512 px), theme `#F26522`, start `/`, display `standalone`.
   - `public/sw.js` — minimal: cache the app shell + offline fallback page. No fancy stale-while-revalidate yet; an offline `Please connect` HTML is the floor.
   - `public/assets/icons/icon-192.png`, `icon-512.png` — placeholder PNGs OK in Sprint 01; real brand icons in Sprint 02+.
   - `<link rel="manifest">` + `<meta name="theme-color">` + service-worker registration in `app/Views/layouts/main.php`.
   - Test: Chrome DevTools → Application tab → Manifest renders + Service Worker registers + "Add to home screen" CTA appears. Lighthouse PWA score (auditing only).

2. **Per-type server-side validation of `target_json` and `actual_json`.** Currently UI-only (a malicious POST can store any JSON shape). Sprint 1 hardening.
   - New `app/Validation/PlanEntryShapeValidator.php` (or a static method on `PlanEntriesModel`). For each exercise_type, validate that the bag's keys match `exercise_json_shapes.md` and values are within bounds (sets 1-20, reps 1-100, etc.).
   - Wired into `Coach\Plans::store` / `::update` and `Player\Plans::update` AFTER the no-clobber check, BEFORE the DB write.
   - Tests in `tests/unit/PlanEntryShapeValidatorTest.php` covering each format's happy path + at least one rejection per format.
   - **Reject** unknown keys silently? Or 400? Default: silently strip unknown keys, log a warning. Reject only on out-of-bounds values. Owner can override.

3. **Prettier target-badge labels in the rendered cells.** Currently the cell labels are "Max HR %", "Duration min", "Sets", etc. — fine for the live grid, but the placeholder text + tooltips render literally as `max_hr_pct: 75`. Polish:
   - In plan-builder.js, add a label-prettifier (`max_hr_pct → HR %`, `duration_min → min`, `rest_sec → s`, `weight → kg|lb` based on plan's weight_unit) for tooltips and any future summary chip view.
   - Plan summary pill could show "3 exercises · 45 min" instead of just the target word.

4. **Server-side `entry.id` echo on update success.** Currently `Coach\Plans::update` redirects with a generic flash. Echo the count of updated entries ("Saved 3 actuals.") for parity with `Player\Plans::update`. Small consistency fix.

### Session 7 WILL NOT do (deferred to Session 8+)

- Plan archival (older weeks moving off the dashboard).
- Per-sub-category cell variance (plan_builder_ux.md §2.4 hint) — depends on owner walkthrough of the live LTAT system to map cells.
- Autosave-on-blur per row (HL-12 CSRF token-rotation work).
- Capacitor native wrap.
- Assessments / metric_types kernel; tennis-specific testing catalogue; training-load / ACWR; rich analytics.
- Multi-language.

### Sprint-close candidate

If Session 7 lands all four items above clean and the owner's visual sign-off is positive, Session 7 is a Sprint 01 close candidate. Sprint-close chores per CLAUDE.md §11 + §12:
- **Seal candidates review.** Watching: `app/Services/JwtValidator.php`, `app/Support/IdObfuscator.php`, the three exercise-catalogue seed migrations, `app/Controllers/Sso.php`. Possibly add: `app/Models/PlanEntriesModel.php::decideActualUpdate` and `app/Views/coach/plans/_grid.php` (load-bearing).
- **Archival.** SESSION_LOG.md is small (6 rows); nothing to archive yet (the rule kicks in only when there are 3 sprints' worth of rows). HL_INDEX.md should be created at HL-20 — we're at HL-14, so still 6 entries away. KNOWN_ERRORS.md is empty.

---

## Key Conventions (quick reference)

- **Foreign keys ARE used.** Engineering decision (HL-6).
- **ONE migrations folder.** Raw-SQL workaround is NEVER the answer (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder, not a coder. No jargon without definition.
- **Captain / Engine Engineer model.** Technical decisions within scope are yours by default; UX / scope / priority / product is his.
- **Responsive, not mobile-first.** Desktop/tablet/iPad primary; mobile is adaptive (plan_builder_ux.md §2.2).
- **Falcon theme cohesion** (CLAUDE.md §5.4). Font stack + 16.8px base.
- **CSRF + AuthFilter alive globally.** Every POST form needs `csrf_field()`; every new route is role-checked + ownership-checked.
- **Single-form Big Save model.** No per-row AJAX in Session 7 either; HL-12 + CI4 token rotation make AJAX expensive.
- **Commit per logical unit.** Typical Session 7 commits: pwa-manifest → pwa-service-worker → shape-validator → label-prettifier → flash-consistency → session-close.
- **No --amend, no --force, no --no-verify.**
- **Sealed:** `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`. Never modify.
- **Seven session-close artifacts** mandatory at close (CLAUDE.md §6.2), including memory-to-repo promotion + binary-artifact promotion (artifact #7's binary half).
- **Begin session close at 70% context.** Honest partial close > rushed full close > abort protocol (§13).

---

## Verification Commands

End-of-Session-7 targets:

```
cd C:/xampp/htdocs/court-fitness

# Tests: 28 existing + at least 3 new (PlanEntryShapeValidator coverage)
./vendor/bin/phpunit tests/unit/                        # all pass; count >= 31

# Migrations unchanged
php spark migrate:status                                # 3 migrations applied

# Dev server
php spark serve --port 8080                             # background

# PWA verification
#   /manifest.json should serve 200 with valid JSON
#   /sw.js should serve 200 with text/javascript
#   Chrome DevTools → Application tab → Manifest renders + Service Worker registers
#   Lighthouse PWA audit score (note baseline; not blocking unless < 50)

# Shape validation
#   POST /coach/plans/{obf} with invalid target_json (e.g. sets=999) → 400 with field-level error
#   POST /coach/plans/{obf} with unknown keys (e.g. {"foo": "bar"}) → 200 (strips, logs warning)

# Regression — Session 6's flows still work
#   /dev/sso-stub?as=coach → /coach/plans/new → fill grid → save → land on editable grid
#   /dev/sso-stub?as=player → /player → tap plan → log actuals → save → audit line shows player

git status                                              # clean at session close
git log --oneline -10                                   # >= 4 meaningful commits this session
```

At close, paste in the handover: a screenshot of Chrome DevTools Application tab showing the manifest + service worker registered, saved under `.ai/research-notes/screenshots/session7-pwa.png` with sibling `.md` note.

---

## Known Risks (descending likelihood)

1. **PWA scope creep.** A "real" PWA wants offline support, push notifications, background sync, install prompts, splash screens. Session 7's bar is low: manifest + a minimal service worker + an offline shell. Deferrals are explicit; if you find yourself implementing Workbox routing or push notifications, STOP — that's Sprint 02+.
2. **Shape validation that's too aggressive rejects legitimate edge cases.** The validator should silently strip unknown keys (forward-compatibility for future fields) but reject out-of-bounds values. Don't 400 on shape mismatches that the UI can't even produce — focus on what a malicious or buggy client could do.
3. **Service worker caching the wrong things.** A bad SW cache strategy can serve stale auth pages or stale plan grids. For Session 7 keep it dumb: cache the app shell (CSS, JS, layout HTML) but NEVER cache `/coach/plans/*` or `/player/plans/*` or `/sso` or `/dev/*`. Network-first or no-cache for all dynamic routes.
4. **Lighthouse PWA score is a vanity metric in dev.** Don't chase it past "registered + installable." Production hardening (HTTPS, manifest icon optimisation, pre-caching strategies) is a Sprint 02 concern.
5. **Owner's visual eyeball might surface UX issues that bump Session 6 polish back into Session 7's queue.** That's expected — handle those FIRST, before PWA. Communicate clearly which item slipped.

---

## When In Doubt

- Unclear UX detail → `.ai/core/plan_builder_ux.md` first. If not there, ASK Rajat (Rule 9).
- Unclear past decision → `.ai/core/HARD_LESSONS.md` + `.ai/.daily-docs/26 Apr 2026/session_6_handover.md`.
- Tempted by a destructive git op → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL → STOP (HL-1).
- Tempted to mock auth → NEVER (HL-8).
- Tempted to skim BRIEFING / plan_builder_ux.md → STOP (HL-13). The Conformance Check exists exactly to catch this.
- Owner shares a screenshot / design artifact → save to `.ai/research-notes/screenshots/` with sibling `.md` note the MOMENT it arrives, not at session close (HL-13 + CLAUDE.md §6.2 artifact #7).

Good luck. Run the Framework Conformance Check, commit it, wait for "proceed," handle owner's Session 6 visual feedback first if any, then the four Session 7 items.
