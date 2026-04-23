# Work In Progress

## Current Sprint: Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

## Current Status

**Session 2 closed cleanly (2026-04-23).** Sprint 01 Session 1 delivered the SSO foundation: `composer install` completed, `firebase/php-jwt` added, `App\Services\JwtValidator` + `App\Controllers\Sso` skeleton built, 10 unit tests (23 assertions) all passing. `.env` configured locally. Three commits on `main`, none pushed to GitHub (owner pushes manually). No blockers. Session 3 is ready to start.

## This Session's Scope (Session 2, 2026-04-23) — COMPLETED

**In scope — DONE:**
- ✅ Housekeeping: fixed stale path references in `CLAUDE.md`, `.ai/README.md` (rewrote), 8 other living docs; added historical-notice headers on 3 archival docs (commit `54a12b0`).
- ✅ `composer install` completed cleanly (58 packages, no security advisories).
- ✅ `firebase/php-jwt ^7.0` added as dep (commit `42d4300`).
- ✅ `.env` copied from template and configured (HITCOURT_JWT_SECRET, HITCOURT_BASE_URL, XAMPP MySQL defaults). Gitignored.
- ✅ `App\Services\JwtValidator` — validates signature + expiry + required claims + 60-second lifetime ceiling.
- ✅ `App\Services\JwtValidationException` — single exception type callers handle.
- ✅ 10 unit tests written and passing (`tests/unit/JwtValidatorTest.php`).
- ✅ `App\Controllers\Sso` — thin `/sso` skeleton with the validator wired in.
- ✅ Route `GET /sso` registered.
- ✅ HL-11 added (firebase/php-jwt `DomainException` path).

**In scope — NOT DONE (carry to Session 3):**
- ❌ AuthFilter on all non-`/sso` routes — design included in Sprint 01 plan but implementation deferred.
- ❌ `users` and `coach_player_assignments` migrations — required before SSO can upsert users.
- ❌ Stub SSO issuer for dev — will be needed when testing the `/sso` → user-upsert → redirect flow end-to-end.
- ❌ PWA scaffolding (manifest + service worker) — deferred.

**Out of scope (deferred per plan):** plan-builder UI, player-actuals UI, exercise taxonomy seed, admin features, assessments kernel, Falcon theme, multi-language, fitness directory, analytics.

## Latest: Session 2 (2026-04-23, Sprint 01 Session 1)

Goal achieved: SSO boundary tested first, per HL-8 discipline. `JwtValidator` is now a dependable unit that Session 3 can compose with database access and session management.

**Answers received from owner at session opening (all three Sprint 00 blockers closed):**

1. **JWT role claim** — HitCourt sends `role` ("Admin, Coach, Player, and so on"). court-fitness reads it. **Engineering decision:** `users.role` will be `VARCHAR(20)` (not ENUM) to accept any role. Admin + unknown roles will get a "Fitness administration coming in a later release" placeholder.
2. **Dev SSO** — stub locally for now; real shared secret in `.env` when HitCourt is ready.
3. **Git remote** — live at `https://github.com/AxxoMob/court-fitness.git`.

**Additional owner clarification mid-session:** Admin is "the Manager of the entire process of who does what" — i.e. orchestration role, not just another CRUD permission. Relevant when Admin features are scoped in a later sprint.

## Previous: Session 1 (2026-04-22, Sprint 00)

Bootstrap. AI_AGENT_FRAMEWORK.md read. ltat-fitness-module predecessor analysed. All framework docs produced. Sprint 01 plan drafted. HL-1..HL-10 recorded. Full detail in `.ai/.daily-docs/22 Apr 2026/session_1_handover.md`.

## Blockers

**None.** Session 3 is unblocked.

## Noticed this Session, for Future (NOT done here)

- The Sso controller has a `TODO` block for Session 3 covering user upsert, session cookie, and role-based redirect. When Session 3 implements this, write an integration test covering the full SSO → redirect path (controller test, not just unit test of the validator).
- The `env` template has `database.default.hostname = localhost` (commented). I uncommented and set `root` / empty password in `.env` (XAMPP default). If Rajat's XAMPP uses a different MySQL password, `.env` needs updating.
- `database.default.database = court_fitness` — the DB does NOT exist yet. Session 3 must run `php spark db:create court_fitness` (or CREATE manually in phpMyAdmin) before `php spark migrate` works.
- `database.tests.database = court_fitness_test` — needed for CI4's test-run DB isolation. Create when tests start touching the DB.
- When AuthFilter is built (Session 3), remember `/sso` must bypass it — otherwise unauthenticated users arriving from HitCourt get redirected away from `/sso` and the handoff breaks.
- Inherited carry-overs from Session 1 (still valid): trainer→coach vocabulary, slug normalisation during seed, `exercise_type.status` inversion, base64-URL-obfuscation question, tennis testing for Sprint 03+, multi-language for Sprint 02+, PWA install prompt UX, xlsx-survey scripts deletion question, `.ai2` rename decision.
- PHPUnit warning "No code coverage driver available" is benign (no Xdebug/PCOV on dev machine). If we want coverage reports later, install Xdebug in XAMPP's php.ini.
- The CI4 framework clone includes `system/` in the repo (unusual — most CI4 apps get `system/` from Composer/vendor). Left as-is; if we ever want to convert to the `appstarter` pattern, that's a separate refactor.

## Open Decisions (deferred to later sessions)

- Ionic Framework vs Bootstrap 5 for the PWA — Sprint 02 re-evaluates.
- Seal `JwtValidator.php` once stable? Strong candidate — it's the identity boundary. Revisit after Session 3 wires the full flow.
- Seal `app/Services/JwtValidationException.php` alongside.
- Training Target dropdown list (Endurance, Strength, etc.) — confirm with owner in Session 3.
- Whether to rename `.ai/.ai2/` to something more descriptive (owner's call).
- System/vendor Composer layout — the project has `system/` in-repo rather than via Composer. Probably fine; revisit if we ever swap to `codeigniter4/appstarter`.

## Verification Commands — results at end of Session 2

```
$ php -v                                          → PHP 8.2.12 ✓
$ composer install                                → 58 packages, clean ✓
$ ls -la .env                                     → exists, 1897 bytes ✓
$ ls -la vendor                                   → 19 package directories ✓
$ ./vendor/bin/phpunit tests/unit/JwtValidatorTest.php
  PHPUnit 11.5.55 — 10 tests, 23 assertions, OK ✓
$ git log --oneline -5
  <session-close commit>  session-2 close artifacts
  <sso commit>            sprint-1: SSO JwtValidator + /sso endpoint skeleton
  42d4300                 sprint-1: composer install + firebase/php-jwt
  54a12b0                 sprint-1: housekeeping — update paths to .ai/.ai2/
  be8b3a0                 First Push
  9c4ffbe                 Initial commit
$ git status                                      → clean (after close commit)
```

The target "end-of-Session-2 baseline" from the opening WIP is met, with one caveat: `php spark serve` was not started in the session (not needed for the test-first work we did; Session 3 will exercise it when SsoController gains real routing behaviour).
