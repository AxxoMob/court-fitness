# Work In Progress

## Current Sprint: Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

## Current Status

**Session 2 in progress (2026-04-23)** — Sprint 01 Session 1, housekeeping phase.

First work: fixing stale path references left over from the owner's folder reorganisation between sessions (tier-named sub-folders → flat `.ai/.ai2/`). No code written yet this session. All three Sprint 00 open questions are resolved; Sprint 01 is unblocked.

## This Session's Scope (Session 2, 2026-04-23)

**In scope:**
- **Housekeeping first:** fix `.ai/README.md`, `CLAUDE.md` §3/§6/§8/§10, and internal cross-refs in `.ai/.ai2/*`, `sprints/sprint-01/sprint-plan.md`, and `research-notes/xlsx-survey/README.md` to match the new `.ai/.ai2/` layout. Historical docs (`sprints/sprint-00/sprint-plan.md`, `.daily-docs/22 Apr 2026/session_1_handover.md`, `.daily-docs/22 Apr 2026/prompt_for_session_2.md`) get a header note rather than rewriting history.
- Commit housekeeping as one logical unit with a descriptive message.
- `composer install` — pull CI4 dev dependencies + the `firebase/php-jwt` library for SSO.
- `.env` configuration: copy `env` → `.env`, set DB creds, add `HITCOURT_JWT_SECRET` and `HITCOURT_BASE_URL`.
- Stub SSO harness for dev — a tiny route that mints test JWTs using the dev secret so I can exercise the real `/sso` endpoint locally.
- `/sso` endpoint + `AuthFilter` — JWT unit tests written FIRST (HL-8 discipline), then the implementation.
- Stretch if time: migrations for `users` and `coach_player_assignments`.

**Out of scope (deferred to later sessions):**
- Plan-builder UI (Coach) — Session 3+.
- Player actuals UI — Session 3+.
- Exercise taxonomy seed (the 3 + 12 + 204 rows) — Session 3.
- Any admin functionality — later sprint (Rajat clarified Admin is "Manager of the entire process of who does what").
- Assessments / metric_types / testing kernel — Sprint 03+.
- Tennis-specific testing catalogue — Sprint 03+.
- Falcon theme, multi-language, fitness directory, exports, rich analytics — per Sprint 01 plan §"OUT of scope."

## Latest: Session 2 (2026-04-23, Sprint 01 Session 1)

**Goal:** Resolve Sprint 00 blockers, fix stale path references, lay the SSO + PWA foundation, and leave Session 3 with a working `/sso` endpoint + clean migrations folder ready for the first feature tables.

**Answers received from owner at session opening (all three Sprint 00 blockers closed):**

1. **JWT role claim** — HitCourt DOES send role ("Admin, Coach, Player and so on"). court-fitness reads the claim and routes accordingly.
   - **Engineering adjustment:** `users.role` becomes `VARCHAR(20)` (not a locked ENUM), so we accept whatever HitCourt sends. Sprint 01 routes: `coach` → coach dashboard, `player` → player dashboard, `admin` (and any unknown role) → "Fitness administration is coming in a later release" placeholder. Admin features themselves stay deferred.
   - **Admin semantics note from owner:** "Admin would be the Manager of the entire process of who does what." Scope properly when we build it (later sprint).
2. **Dev SSO** — stub SSO locally for now; real shared secret goes in `.env` when HitCourt is ready. Both stub and real validate against the same `HITCOURT_JWT_SECRET` env var, so flipping to production means replacing a value, not code.
3. **Git remote** — Owner already created `https://github.com/AxxoMob/court-fitness.git` and added it as `origin`. Remote was present at session open; local `.git/config` shows it correctly.

## Previous: Session 1 (2026-04-22, Sprint 00)

Project bootstrap. Read AI_AGENT_FRAMEWORK.md v1.0 end-to-end + the ltat-fitness-module predecessor. Produced all framework-mandated documentation (CLAUDE.md + `.ai/*`). Drafted Sprint 01 plan with day-level specificity. Nine Hard Lessons recorded (HL-1 through HL-9) + a tenth at session close (HL-10, "always `ls` the repo root first"). Full detail in `.ai/.daily-docs/22 Apr 2026/session_1_handover.md`.

Between Session 1 and Session 2, owner reorganised `.ai/` — collapsed the four tier-named sub-folders into a single flat `.ai/.ai2/`.

## Blockers

**None.** Session 2 is unblocked and executing.

## Noticed this Session, for Future (NOT done here)

- The `.ai2` folder name is the owner's choice; if you later rename it, update `CLAUDE.md` §3 + `.ai/README.md` + internal cross-refs across the repo (grep for `\.ai2/` to find them).
- Admin role: Rajat described it as "Manager of the entire process of who does what." When we scope admin features in a later sprint, think orchestration (who assigns coaches to players, who sets permissions, who sees system-wide reporting), not just CRUD.
- Inherited carry-overs from Session 1 still valid: trainer→coach vocabulary, slug normalisation during seed, `exercise_type.status` inversion, base64-URL-obfuscation question, tennis-specific testing metrics for Sprint 03+, multi-language for Sprint 02+, PWA install prompt UX, `.ai/research-notes/xlsx-survey/` scripts (safe to delete if workbooks are gone).
- The PHP JWT library choice: plan to use `firebase/php-jwt` (de-facto standard on PHP 8.2+). If Composer resolution blocks, fall back to `lcobucci/jwt` and document in the handover.
- The `env` file at repo root is CI4's template; watch for encoding issues when copying to `.env` on Windows (CRLF vs LF).

## Open Decisions (deferred to later sessions)

- Ionic Framework vs Bootstrap 5 for the PWA — Sprint 01 stays on Bootstrap 5; Sprint 02 re-evaluates based on how phone-app-like the Bootstrap PWA feels.
- Seal the SSO service once it's stable + tested (candidate per `.ai/.ai2/SEALED_FILES.md`).
- Seal the exercise-taxonomy seed migration once loaded (candidate per `.ai/.ai2/SEALED_FILES.md`).
- Training Target dropdown — Sprint 01 goes with a fixed DB-seeded list (Endurance, Strength, Power, Speed, Agility, Recovery, Mixed); owner may override during Session 3 review.
- Whether to follow ltat-fitness's base64-URL-obfuscation pattern for plan IDs or use plain CI4 routing. Default: plain CI4.

## Verification Commands

End-of-Session-2 baseline (what we aim to have green by session close):

```bash
cd C:/xampp/htdocs/court-fitness
php -v                                              # ≥ 8.2
composer install                                    # exits 0, no deprecation warnings
ls -la .env                                         # exists locally; NOT committed
php spark serve --port 8080                         # server starts, serves default page
vendor/bin/phpunit tests/unit/SsoTest.php           # JWT validation tests pass (all green)
curl -sI http://localhost:8080/sso                  # returns 400 "missing token" (not 500)
curl -sI "http://localhost:8080/sso?token=BAD"      # returns 400 "invalid token" (not 500)
git status                                          # clean working tree at session close
```

If any command fails by session close, the Session 2 handover flags it honestly under "Open Issues / Unfinished Work."
