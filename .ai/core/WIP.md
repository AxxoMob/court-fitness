# Work In Progress

## Current Sprint: Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

## Current Status

**Session 4 closed cleanly (2026-04-23).** This session was an architecture retrospective — scope pivoted mid-way at owner's request from "Plan Builder" to "evaluate and implement the 7 architecture improvements." Zero feature code written this session; sealed framework file untouched; all additions in CLAUDE.md and new project-specific templates under `.ai/core/templates/`. Commits: `30fb22b` (architecture) + `5a4d81e` (close).

**Session 3 (previous) remains the most recent feature session:** DB migrated + seeded (3+12+204+7 taxonomy + 1 coach + 2 players + 1 plan + 3 entries), SSO working end-to-end, Player Dashboard renders on mobile with orange branding. Commit `2140d87`.

**Plan Builder** (Session 4's original scope) **moved to Session 5** in a fresh conversation. See `.ai/.daily-docs/24 Apr 2026/prompt_for_session_5.md`.

**Architectural changes in Session 4:**
- `.ai/.ai2/` renamed → `.ai/core/` (self-describing).
- CLAUDE.md §3 split: §3.1 fresh agent, §3.2 returning agent re-entry check, §3.3 Conformance Check must be committed.
- CLAUDE.md §6: 7 close artifacts (was 6), framework version stamp in handovers, project-wide session-N naming.
- CLAUDE.md §§11-13: archival policy, seal-candidates review cadence, session abort protocol.
- `core/templates/SESSION_CONFORMANCE_TEMPLATE.md` + `SESSION_ABORT_TEMPLATE.md`.
- SEALED_FILES.md updated with live watchlist.

**Owner directive passed to Session 5 (post-close, 2026-04-23):** HitCourt-family visual cohesion. All Rajat's projects run Falcon theme — court-fitness's CSS should match Falcon's font stack exactly (see `prompt_for_session_5.md` for the full `font-family` string + 16.8px base size). Falcon reference: https://prium.github.io/falcon/v2.8.2/default/index.html.

## This Session's Scope (Session 3, 2026-04-23) — COMPLETED

**In scope — DONE:**
- ✅ `court_fitness` database created via `php spark db:create`.
- ✅ 3 migrations: users + assignments, exercise catalogue, plan tables. Single clean `app/Database/Migrations/` folder (HL-1 discipline).
- ✅ Catalogue seed: 3 exercise_types + 12 fitness_categories + 204 fitness_subcategories (parsed from ltat_fitness SQL dump) + 7 training_targets.
- ✅ Demo seed: 1 coach (Rajat), 2 players (Rohan, Priya), 1 plan for Rohan's next-Monday with 3 entries across 2 days + 2 sessions.
- ✅ UsersModel with `upsertFromJwt()`.
- ✅ Sso controller completed: validate → upsert → session → role-based redirect (coach → `/coach`, player → `/player`, admin/other → `/admin-placeholder`).
- ✅ DevSsoStub dev-only controller: mints JWT for a demo user and hands off to `/sso`. Gated on `ENVIRONMENT === 'development'` at both route and controller level.
- ✅ Coach/Player/Admin-placeholder controllers + views.
- ✅ Global orange-branded CSS at `public/assets/css/court-fitness.css` — `--cf-primary: #F26522`, mobile-first, 44px min tap targets, grid adapts to 2-column at 720px+.
- ✅ Layout at `app/Views/layouts/main.php` with Poppins font + sticky orange header.
- ✅ Routes updated: `/`, `/sso`, `/coach`, `/player`, `/admin-placeholder`; dev-only `/dev` + `/dev/sso-stub`.
- ✅ Mid-session fix: subcategory slug UNIQUE was too strict (ltat_fitness has a cross-category slug collision on `pro-agility-5-10-5`). Changed to composite UNIQUE on (fitness_category_id, slug) via migration edit + `migrate:refresh`.
- ✅ End-to-end curl verification for all three role flows returned correct HTML (Rohan's dashboard, Coach Rajat's stats, Admin "coming soon").
- ✅ 10/10 JwtValidator unit tests still passing (23 assertions) after adding DB-touching Sso logic.

**In scope — NOT DONE (carry to Session 4):**
- ❌ AuthFilter — no global filter yet; unauthenticated hits to `/coach` or `/player` redirect to `/` (the CI4 welcome page) rather than to HitCourt's login. Works for dev via the stub; Session 4 adds the real filter.
- ❌ Coach `My Players` screen — Coach dashboard currently shows stats only.
- ❌ Plan Builder — the hard mobile screen for building a weekly plan.
- ❌ Player Plan Detail / Log Actuals — the player tapping a plan card currently goes to a 404 (route `/player/plans/{id}` not yet mapped).
- ❌ PWA manifest + service worker.

**Out of scope (deferred further):** assessments kernel, tennis-specific testing catalogue, training-load / ACWR, fitness directory, export, admin real UI, multi-language, Capacitor wrap.

## Latest: Session 3 (2026-04-23, Sprint 01 Session 2)

Goal: deliver a stakeholder-visible Player Dashboard on a phone. Achieved. Owner's Session 2 feedback ("can't see anything; stakeholders care about Player Dashboard + mobile compatibility") drove the Session 3 scope shift — backend plumbing + exercise catalogue got compressed into one session so the Player Dashboard could move up from Session 5 to Session 3.

**Owner clarifications received in this session:**
- Training Target is a combobox: dropdown of 7 seeded values + "Add More" → typed free text. Both cases write into a single VARCHAR(100) column on training_plans; suggestions table isn't bloated with one-offs.
- Admin placeholder copy locked: "Fitness administration features are coming soon."
- Brand orange locked: `#F26522`.
- Admin role semantics: "Manager of the entire process of who does what" — orchestration role, to be properly scoped in a later sprint.

## Previous: Session 2 (2026-04-23, Sprint 01 Session 1)

SSO foundation: composer install + firebase/php-jwt ^7.0, JwtValidator + 10 unit tests, Sso controller skeleton, /sso route, .env configured. HL-11 added (firebase/php-jwt DomainException). Full detail: `.ai/.daily-docs/23 Apr 2026/session_2_handover.md`.

## Blockers

**None.** Session 4 is unblocked.

## Noticed this Session, for Future (NOT done here)

- **File-naming convention:** per `CLAUDE.md` §6 the handover filename should use "N = session number of the day." Session 2 was named `session_2_handover.md` using PROJECT session number (since Session 1 was a different day, it happened to also be Session 1 of its day). Session 3 is the second session of day 2026-04-23, so under the convention it should be `session_2_handover.md` — but that collides with Session 2's handover in the same folder. I'm using PROJECT session number (`session_3_handover.md`) throughout to avoid collision. Propose CLAUDE.md §6 clarification: use project session number as the canonical rule.
- Player Dashboard card links currently point at `/player/plans/{id}` which doesn't route yet. Session 4 adds that route + view; until then tapping the card 404s.
- When HitCourt's real SSO comes online, replace the dev-only `DevSsoStub` routes with an explicit `throw new PageNotFoundException` in production. Currently gated on `ENVIRONMENT === 'development'` at the routes level — production will 404 naturally.
- Session cookie works via CI4's default FileHandler. `writable/session/` already exists (CI4 default). For production behind a load balancer we'd want Redis or DB session handler — not Sprint 01 concern.
- `.env` has a dev-only `HITCOURT_JWT_SECRET` with a clearly-marked placeholder. When real HitCourt is ready, replace the value in dev AND production `.env` files simultaneously (owner's responsibility).
- Inherited from Sessions 1-2 (still valid): trainer→coach vocabulary, slug normalisation during seed (done via ltat_fitness source as-is), base64-URL-obfuscation question for plan IDs, tennis-specific testing metrics for Sprint 03+, multi-language for Sprint 02+, PWA install prompt UX, xlsx-survey scripts deletion.

## Open Decisions (deferred)

- AuthFilter design — filter globally with `/sso`, `/dev/sso-stub`, `/dev` exempted, OR apply per-route group. Session 4 picks one.
- Ionic vs Bootstrap 5 for Plan Builder — Sprint 02 re-evaluates once we've seen the Bootstrap-only version in Session 4.
- Sealing `JwtValidator.php` + `UsersModel.php` + the Sso controller once the full SSO flow has run in production for a week. Revisit Sprint 02.
- URL shape for plan IDs — plain CI4 (`/player/plans/42`) vs ltat_fitness-style base64 (`/player/plans/MCMjlzA=`). Session 4 default: plain CI4 unless owner says otherwise.
- Training Target dropdown list — 7 default items in place; owner will review in Session 5 once the Plan Builder form renders.
- Whether to rename `.ai2/` to something self-describing (owner's call).

## Verification Commands — results at end of Session 3

```
$ php spark migrate:status
  3 migrations applied (users+assignments, exercise catalogue, plan tables)

$ php -r "foreach ([users,assignments,types,categories,subcategories,targets,plans,entries] as t) echo COUNT"
  users: 3, coach_player_assignments: 2, exercise_types: 3,
  fitness_categories: 12, fitness_subcategories: 204, training_targets: 7,
  training_plans: 1, plan_entries: 3  ✓

$ ./vendor/bin/phpunit tests/unit/JwtValidatorTest.php
  10 tests, 23 assertions, OK  ✓

$ php spark serve --port 8080  (background)
$ curl -sL -c cookies -b cookies http://localhost:8080/dev/sso-stub?as=player
  32KB HTML containing "Rohan" (2x), "Week of", "Endurance"  ✓
$ curl -sL -c cookies -b cookies http://localhost:8080/dev/sso-stub?as=coach
  HTML containing "Coach Rajat", "Assigned players"  ✓
$ curl -sL -c cookies -b cookies http://localhost:8080/dev/sso-stub?as=admin
  HTML containing "coming soon"  ✓

$ git log --oneline -3
  <session close commit>   sprint-1: session 3 close artifacts
  2140d87                  sprint-1: DB schema + SSO wiring + Player Dashboard
  9e9779b                  sprint-1: session 2 close (six artifacts, handover, next prompt)

$ git status     → clean
```

All demo-relevant URLs — for the owner to open in Chrome DevTools mobile emulation:
  `http://localhost:8080/dev`                      → stub SSO landing
  `http://localhost:8080/dev/sso-stub?as=player`   → Rohan's orange dashboard with 1 plan card
  `http://localhost:8080/dev/sso-stub?as=coach`    → Coach stats
  `http://localhost:8080/dev/sso-stub?as=admin`    → "Fitness administration features are coming soon." placeholder
