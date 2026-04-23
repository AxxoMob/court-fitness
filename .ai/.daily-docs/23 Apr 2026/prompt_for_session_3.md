# court-fitness — Session 3 Kickoff Prompt

> Copy this entire file and paste it as the first message to the Session 3 agent. Customise only the **[bracketed]** lines if needed. Everything else is constant.

---

## Session Onboarding — court-fitness (Session 3, Sprint 01 Session 2)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both coach and player able to log actual session results. It lives at `C:\xampp\htdocs\court-fitness` on Windows 11 XAMPP. Production URL will be `https://fitness.hitcourt.com`.

This is a long-horizon project operated under a strict AI Agent Framework. Before you do ANYTHING else, read these documents **in order**:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (repo root) — project-specific conventions. Sections 3 + 4 especially (mandatory reading list + Captain/Engineer collaboration model).
3. `.ai/README.md` — folder map of the `.ai/` tree. Current layout: flat files in `.ai/.ai2/`, sprint plans in `.ai/sprints/`, daily docs in `.ai/.daily-docs/`.
4. `.ai/.ai2/BRIEFING.md` — 1-page project overview.
5. `.ai/.ai2/WIP.md` — current state. Has blockers and follow-ups. Read carefully.
6. `.ai/.ai2/SESSION_LOG.md` — project diary.
7. `.ai/.ai2/HARD_LESSONS.md` — ELEVEN entries (HL-1..HL-11). Read all. These are traps to avoid.
8. `.ai/.ai2/SEALED_FILES.md` — sealed file list. Just the framework file.
9. `.ai/.ai2/KNOWN_ERRORS.md` — currently empty with template.
10. `.ai/.ai2/ltat-fitness-findings.md` — distilled findings from the predecessor project.
11. `.ai/sprints/sprint-01/sprint-plan.md` — **your playbook.** Read all of §3 (schema) and §4 (backend logic) especially — Session 3 starts implementing them.
12. `.ai/.daily-docs/23 Apr 2026/session_2_handover.md` — what happened before you arrived.

After reading, DO NOT touch code yet. Complete the **Framework Conformance Check** (Appendix D of AI_AGENT_FRAMEWORK.md) — 8 questions in chat to prove you read. Wait for Rajat's "proceed."

---

## Context

Sprint 01 Session 1 (Session 2, 2026-04-23) delivered the SSO foundation: `composer install` + `firebase/php-jwt`, `App\Services\JwtValidator` + unit tests (10/10 passing), `App\Controllers\Sso` skeleton, `/sso` route. `.env` configured locally (gitignored). No blockers carried forward.

**You're now continuing Sprint 01 from the middle.** Specifically: the `/sso` endpoint validates JWTs but does NOT yet upsert users or mint a session. That's Session 3's job — plus AuthFilter, the first two migrations, and the role-based redirect.

**Tech stack (summary):** PHP 8.2.12 / CodeIgniter 4.7.2 / MySQL 8.0+ / Bootstrap 5 (Session 4+) / PWA (Session 4+) / HitCourt SSO via JWT HS256. Full detail in `CLAUDE.md` §7.

---

## What Was Done Last Session (Session 2, 2026-04-23)

1. Housekeeping: 18 stale path references fixed, `.ai/README.md` rewritten, 3 historical docs given a header note (commit `54a12b0`).
2. `composer install` + `firebase/php-jwt ^7.0` (commit `42d4300`).
3. `JwtValidator` service + `JwtValidationException` + 10 unit tests (23 assertions, all passing).
4. `Sso` controller skeleton + `/sso` route.
5. `.env` configured (gitignored).
6. HL-11 added (firebase/php-jwt `DomainException` path discovered mid-test-run).

All three Sprint 00 blockers resolved at Session 2 opening (owner confirmed: JWT carries `role`; stub SSO for dev; GitHub remote already live at `AxxoMob/court-fitness`).

---

## What Needs To Be Done Now (Session 3, Sprint 01 Session 2)

**Confirmed prerequisites before first migration (do these in order):**
1. `php spark db:create court_fitness` — creates the database (needed before any migration runs).
2. Verify `.env` DB credentials work: `php spark db:table` or similar sanity check.

**Then, in order, per `.ai/sprints/sprint-01/sprint-plan.md` §3 and §4:**

1. **First migration: `users` table.** Columns per the plan: `id`, `hitcourt_user_id` UNSIGNED UNIQUE NOT NULL, `email` VARCHAR(255), `first_name`, `family_name`, `role` VARCHAR(20) [not ENUM — owner confirmed HitCourt sends varied roles], timestamps, `deleted_at`. Write a `UsersModel` with soft-delete enabled. Write a migration test that round-trips a create + find-by-hitcourt-user-id.
2. **Second migration: `coach_player_assignments`.** Columns per plan §3. UNIQUE(coach_user_id, player_user_id). FK on both user fields.
3. **Wire up the SsoController.** Replace the Session 2 placeholder body with: (a) upsert by `hitcourt_user_id`, (b) mint CI4 session cookie, (c) role-based redirect — `coach` → `/coach`, `player` → `/player`, anything else (including `admin`) → a simple "Fitness administration coming in a later release" placeholder view. Write an **integration test** for the full SSO → session → redirect path.
4. **AuthFilter.** New class at `app/Filters/AuthFilter.php` that checks for a valid session; if none, 302 to `${HITCOURT_BASE_URL}/login?return=<requested_path>`. Register in `Config/Filters.php` with `$globals` aliases AND exempt `/sso` (the SSO endpoint MUST bypass AuthFilter — otherwise the handoff breaks).
5. **Stub SSO dev harness.** A controller at `app/Controllers/DevSsoStub.php` (active only when `CI_ENVIRONMENT = development`) that mints a test JWT using `HITCOURT_JWT_SECRET` and redirects to `/sso?token=...`. Useful for local end-to-end testing without HitCourt being reachable.
6. **Coach and Player landing pages (stubs).** `app/Controllers/Coach/Dashboard.php` and `app/Controllers/Player/Dashboard.php` — each returns a trivial "welcome <user>" view. Session 4 replaces these with real dashboards.
7. **Stretch — if time:** begin the exercise-taxonomy seed migration + seeder (3 rows `exercise_types` + 12 rows `fitness_categories` + 204 rows `fitness_subcategories`). Source: `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql` — grep `INSERT INTO \`exercise_type\`` / `fitness_categories` / `fitness_subcategories`.

Don't try to do all 7 in one session. Session 3 probably gets through items 1-5 cleanly. Begin session-close procedures at ~70% of your context window (framework §3.2 rule).

---

## Open Questions for Rajat (ASK at session open — all are product calls)

1. **Training Target dropdown.** Sprint 01 plan tentatively lists: Endurance, Strength, Power, Speed, Agility, Recovery, Mixed. Before I seed, please confirm or adjust the list.
2. **Admin placeholder page — what should it say?** I propose a simple friendly message: "Fitness administration features are coming in a later release. For now, please ask a coach or player to invite you into their plan." But you may have specific wording.
3. **Database name.** Sprint 01 plan uses `court_fitness`. XAMPP MySQL default root/empty-password works for dev. Any production DB naming convention I should know about before migrations land? Default: stay with `court_fitness`.

Each has a sensible default I can run with if you're not immediately available.

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **No local login screen.** Auth is SSO only. If you find yourself writing a login form, STOP.
- **Foreign keys ARE used.** Engineering decision per HL-6. Owner knows.
- **ONE migrations folder** at `app/Database/Migrations/`. If the migrations folder feels "broken" and you're tempted to apply raw SQL, STOP — that's HL-1 repeating.
- **Plain English with Rajat.** He is a Vibe-Coder. Define jargon on first use. No unexplained TLAs.
- **Captain (Rajat) / Engine Engineer (you).** Technical decisions within scope are yours by default — make them, explain in plain English, move on. Product / scope / priority are his.
- **Mobile-first UI** — not a Session 3 concern yet, but when you build the landing pages in item 6, keep phones in mind.
- **Commit per logical unit** — not one giant end-of-sprint commit. Typical Session 3 commit sequence: migrations → SsoController wiring → AuthFilter → stub SSO → landing pages.
- **No `--amend`, no `--force`, no `--no-verify`, no `-A` / `.` staging of unrelated files.** Framework §7.
- **Sealed file:** `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`. Read freely, never modify.
- **Six session-close artifacts** are mandatory. Begin close at 70% context.
- **Session 3's file naming:** `.ai/.daily-docs/{today}/session_N_handover.md` where N is the session number OF THE DAY (Rajat's convention; see `CLAUDE.md` §6). If Session 3 runs on 2026-04-23 like Session 2, it is `session_3_handover.md` (session 3 of the project but 2nd of the day). If it runs on 2026-04-24, it becomes `session_1_handover.md` of that day. CHECK THE CALENDAR BEFORE NAMING.

---

## Verification Commands

End-of-Session-3 baseline (targets for session close):

```bash
cd C:/xampp/htdocs/court-fitness

# DB is up, migrations apply
php spark db:create court_fitness       # idempotent; OK if already exists
php spark migrate                        # all migrations applied; exits 0
php spark migrate:status                 # all green

# Tests pass
./vendor/bin/phpunit tests/unit/          # all unit tests pass (JwtValidator + any new model/service tests)
./vendor/bin/phpunit tests/database/      # or wherever integration tests live

# Server + SSO round-trip
php spark serve --port 8080               # in background; Ctrl+C when done
# open browser to http://localhost:8080/dev/sso-stub  (stub issues a test JWT, hits /sso, lands on /coach)
# verify cookie set, verify redirect to /coach, verify /coach shows "welcome Test User"

# Git
git status                                # clean working tree
git log --oneline -10                     # each commit has a meaningful message
```

If any don't pass by session close, write them up honestly in the Session 3 handover under "Open Issues / Unfinished Work." Do NOT declare a session "complete" without evidence.

---

## Known Risks (in descending order of likelihood)

1. **XAMPP MySQL credential mismatch.** `.env` has XAMPP defaults (`root` / empty password). If Rajat's XAMPP has a password, `php spark migrate` fails with access denied. Fix: update `.env` locally.
2. **CI4 session cookie + Windows XAMPP quirks.** CI4's default session config uses files; confirm `writable/session/` exists and is writable. If not, sessions silently fail and the SSO flow never sticks.
3. **AuthFilter filter ordering.** Make sure `/sso` is in the filter's `$except` list, or AuthFilter will redirect SSO traffic to HitCourt — infinite loop.
4. **Migration ordering.** `coach_player_assignments` has FKs pointing at `users`. If the migration file for `users` is timestamped AFTER `coach_player_assignments`, the FK creation fails. CI4 runs migrations in filename order — name them accordingly (use ISO timestamps: `2026-04-23-XXXXXX_CreateUsersTable.php` before `2026-04-23-XXXXXY_CreateCoachPlayerAssignmentsTable.php`).
5. **Integration test with session + redirect.** CI4's `FeatureTestCase` is the right base class; make sure to enable the relevant database groups.

---

## When In Doubt

- Unclear requirement → ask Rajat (framework Rule 9).
- Unclear previous decision → read `.ai/.ai2/HARD_LESSONS.md` and the Session 2 handover.
- Tempted by a destructive git op → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL because a migration is complaining → STOP, that's HL-1.
- Tempted to mock-auth a feature controller "just to unblock" → NEVER, that's HL-8.

Good luck. Run the Framework Conformance Check, wait for "proceed," and begin.
