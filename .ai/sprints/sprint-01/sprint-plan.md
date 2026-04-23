# Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

**Status:** NOT STARTED. Awaiting owner answers to 3 open questions (see §"Open questions" below). Planned start: Session 2, 2026-04-22 or later.
**Estimated size:** 4–6 sessions.
**Primary goal:** Ship a working vertical slice of the coach-plans-week + player-logs-actuals workflow, on a mobile-first PWA, authenticated via HitCourt SSO.

## "Done" definition (in plain English)

At the end of Sprint 01:
- A coach on a phone visits `fitness.hitcourt.com` (local dev: `localhost:8080/court-fitness`), bounces to HitCourt to log in, comes back authenticated via our `/sso` endpoint.
- The coach adds a player into their own player list (either direct-add or pick from a shared directory).
- The coach opens "My Plans," taps "New Plan," picks Weekof (a Monday), picks the player, picks a Training Target from a fixed dropdown, picks kg or lb.
- For each training date in that week, they pick Morning/Afternoon/Evening, and add exercises by drilling down the 3-level taxonomy (Format → Category → Subcategory), and set numeric targets.
- They save.
- A player on a phone visits the same app, SSO's in, sees their plans list, taps one, sees each exercise per day/session, fills in the actual values, saves.
- The coach refreshes and sees the actuals.
- The whole thing is installable to the phone home screen as a PWA.

## Deliverables

### 1. Project foundation (Session 1)

**Correction discovered at Sprint 00 close (HL-10):** CodeIgniter 4 is ALREADY present in `C:\xampp\htdocs\court-fitness`. It is the full framework repo clone (not `codeigniter4/appstarter`) — `app/`, `system/`, `public/`, `composer.json` (requires PHP `^8.2`), `spark`, `env` template, default `Home::index` route. `vendor/` is NOT yet installed; `.env` does NOT yet exist. So Session 1's foundation step is:

- Verify the existing CI4 install: `php -v` (≥ 8.2), file sanity check on `composer.json` and `spark`.
- Run `composer install` to fetch dev dependencies (phpunit, fakerphp, etc.).
- Copy `env` → `.env` and configure DB credentials + new env vars (see below).
- Confirm migrations folder is empty at `app/Database/Migrations/` (only `.gitkeep` currently) — single clean folder, no dual-folder setup (HL-1).
- `.env.example` at repo root listing required vars (without real values):
  - `HITCOURT_JWT_SECRET` — shared secret for HS256 JWT validation
  - `HITCOURT_BASE_URL` — e.g. `https://www.hitcourt.com` or `https://www.org.hitcourt.com`
  - `database.default.*` — MySQL connection
  - `app.baseURL`, `app.indexPage`, `app.CSPEnabled`, etc.
- `.env` created locally, gitignored.
- Git commits per logical unit of work (not one giant end-of-sprint commit).
- PWA scaffolding:
  - `public/manifest.json` — app name, icons, theme-color, start_url, display: standalone
  - `public/sw.js` — minimal service worker that caches the app shell and allows offline navigation to "please connect" page
  - `public/assets/icons/` — at least 192×192 and 512×512 PNG app icons (placeholder is acceptable Sprint 01; real brand assets Sprint 02+)

### 2. Authentication — SSO only (Session 1)

- `/sso` endpoint that accepts `?token=<jwt>`:
  - Validates HS256 signature with `HITCOURT_JWT_SECRET`
  - Rejects if `exp` is elapsed (60-second max life)
  - Rejects if any required claim is missing (`email`, `first_name`, `family_name`, `hitcourt_user_id`)
  - Upserts a row in `users` by `hitcourt_user_id` (UNIQUE); writes/updates email + names
  - Mints a local session cookie
  - Redirects to `/coach` or `/player` based on role (see §"Open question 1")
- An `AuthFilter` on every route except `/sso` itself: if no valid session, 302 to `${HITCOURT_BASE_URL}/login?return=<requested_path>`.
- No local login form. No registration. No password reset. No captcha.

### 3. Database schema (Session 1-2)

One clean migration folder. Tables below. All tables have `id` INT UNSIGNED PK auto-increment, `created_at` DATETIME NULL, `updated_at` DATETIME NULL, `deleted_at` DATETIME NULL (soft-delete via CI4 convention). All relationships enforced by foreign keys.

- **`users`** — `hitcourt_user_id` INT UNSIGNED UNIQUE NOT NULL, `email` VARCHAR(255) NOT NULL, `first_name` VARCHAR(100), `family_name` VARCHAR(100), `role` ENUM('coach','player') NOT NULL, standard timestamps + soft-delete.
- **`coach_player_assignments`** — links coaches to players. `coach_user_id` FK → users.id, `player_user_id` FK → users.id, `assigned_date` DATE NOT NULL, `is_active` BOOLEAN DEFAULT 1, timestamps + soft-delete. UNIQUE index on (coach_user_id, player_user_id) to prevent duplicate active links.
- **`exercise_types`** — seeded 3 rows. `name` VARCHAR(50) NOT NULL, `sort_order` INT, `is_active` BOOLEAN. (Normalise ltat-fitness's inverted `status` to `is_active`; drop `is_deleted` — use `deleted_at` instead.)
- **`fitness_categories`** — seeded 12 rows. `exercise_type_id` FK → exercise_types.id, `name` VARCHAR(100) NOT NULL, `slug` VARCHAR(191), `is_active` BOOLEAN. (Drop redundant `code` column from ltat-fitness.)
- **`fitness_subcategories`** — seeded 204 rows. `fitness_category_id` FK → fitness_categories.id, `name` VARCHAR(150) NOT NULL, `slug` VARCHAR(191), `is_active` BOOLEAN, `description` TEXT NULL (reserved for future fitness-directory feature). (Drop denormalised `exercise_type` column — always JOIN through category.)
- **`training_targets`** — seeded with a fixed list (Endurance, Strength, Power, Speed, Agility, Recovery, Mixed — confirm with owner in Session 2 pre-work if list is correct). `name` VARCHAR(50), `sort_order` INT, `is_active` BOOLEAN.
- **`training_plans`** — `coach_user_id` FK → users.id, `player_user_id` FK → users.id, `week_of` DATE NOT NULL (must be a Monday — enforce at model layer), `training_target_id` FK → training_targets.id, `weight_unit` ENUM('kg','lb') NOT NULL, timestamps + soft-delete. Composite INDEX on (player_user_id, week_of) for fast player-dashboard queries.
- **`plan_entries`** — `training_plan_id` FK → training_plans.id, `training_date` DATE NOT NULL, `session_period` ENUM('morning','afternoon','evening') NOT NULL, `exercise_type_id` FK → exercise_types.id, `fitness_category_id` FK → fitness_categories.id, `fitness_subcategory_id` FK → fitness_subcategories.id, `sort_order` INT NOT NULL, `target_json` JSON NULL, `actual_json` JSON NULL, `actual_by_user_id` FK → users.id NULL, `actual_at` DATETIME NULL, timestamps + soft-delete. INDEX on (training_plan_id, training_date, session_period, sort_order).

All FKs use `ON DELETE CASCADE` or `ON DELETE SET NULL` as appropriate (cascade for plan_entries → training_plans; set-null for optional references like actual_by_user_id).

### 4. Backend logic (Session 2-3)

Models: `UsersModel`, `CoachPlayerAssignmentsModel`, `ExerciseTypesModel`, `FitnessCategoriesModel`, `FitnessSubcategoriesModel`, `TrainingTargetsModel`, `TrainingPlansModel`, `PlanEntriesModel`. CI4-style, using soft deletes.

Controllers:
- `SsoController` — `/sso`
- `Coach\DashboardController` — `/coach` (landing after SSO, if role=coach)
- `Coach\PlayersController` — `/coach/players` (list/add/search)
- `Coach\PlansController` — `/coach/plans` (list), `/coach/plans/new`, `/coach/plans/{id}`, `/coach/plans/{id}/edit`
- `Coach\PlanEntriesController` — `/coach/plans/{id}/entries` (add/edit exercises within a plan)
- `Player\DashboardController` — `/player` (landing after SSO, if role=player)
- `Player\PlansController` — `/player/plans/{id}` (view a plan)
- `Player\PlanEntriesController` — `/player/plans/{id}/entries/{entry_id}/log` (log actuals for one entry)
- `Shared\CatalogController` — `/api/catalog/types`, `/api/catalog/categories?type={id}`, `/api/catalog/subcategories?category={id}` (for dropdown population)

Role-based filter: `CoachOnlyFilter` rejects non-coach sessions; `PlayerOnlyFilter` rejects non-player sessions.

### 5. UI screens (Session 3-5)

Six screens, mobile-first Bootstrap 5. Each screen must be pleasant on a 5-inch phone before desktop is considered.

1. **Landing / unauthenticated** — one "Sign in on HitCourt" CTA button; 302 to HitCourt on click.
2. **Coach — My Players** — list with search-by-first-3-letters; "Add Player" modal (direct-add form + pre-registered search); empty-state card when no players yet.
3. **Coach — My Plans** — filter chips (Weekof, Player, Target); list of plan cards; FAB (floating action button) for "New Plan."
4. **Coach — Plan Builder** — the hard screen. Mobile pattern: single-column form with a day-by-day accordion, each day expanding to session-by-session cards, each session-card holding its exercises. Add-exercise opens a bottom-sheet drilldown: pick Type → pick Category → pick Subcategory → fill in numeric targets. Commit with a big touch-friendly save button.
5. **Player — My Dashboard** — list of my plans ordered by upcoming-first; "N new plans since your last visit" badge at the top; tap a plan to enter it.
6. **Player — Log Actuals** — for one plan, one day, one session: card per exercise with target displayed and tap-to-log. Log-actuals opens a bottom-sheet input focused for thumbs (big number pads, no accidental taps). Save per-exercise or save-all-at-once (both are valid UX — pick one after pilot test).

### 6. Seed data for Sprint 01 demo (Session 5-6)

- Exercise taxonomy: all 3 + 12 + 204 rows from ltat-fitness SQL dump.
- Training targets: seeded fixed list.
- One coach user, two player users (one local, one "travelling" — use descriptive names for demo).
- Coach-player assignments linking the coach to both players.
- One sample plan: Weekof = next Monday from session date, 3 training days × 2 sessions × 3-5 exercises per session, targets filled in realistically (Cardio + Weights + Agility examples).

### 7. Verification for sprint close (Session 6)

- Test evidence: `php spark test` or equivalent — all tests pass (unit tests for models + at least one integration test that exercises the SSO → dashboard path).
- Manual test evidence: a screen recording or narrated screenshot sequence showing the full happy path on a phone emulator (coach creates plan → player logs actual → coach sees actual).
- Build evidence: `composer install` clean, no errors.
- File-modification summary: list of new files + line counts.
- All in the session handover.

## Explicitly OUT of scope for Sprint 01 (each has a home for later)

| Feature | Why out | Where it lives |
|---|---|---|
| Fitness testing kernel (`assessments` + `metric_types`) | Narrower slice; deferred until plan-and-log workflow is stable. | Sprint 03+ |
| Tennis-specific testing catalogue | Research task; depends on Fitness testing kernel existing first. | Sprint 03+ |
| Training load / ACWR monitoring | Depends on enough session data existing to compute moving averages. | Sprint 04+ |
| Rich player analytics (progression charts, PR tracking, volume heatmaps) | Requires multiple weeks of data + design work. | Sprint 02+ |
| Fitness directory / exercise encyclopaedia | Content task (descriptions + videos for 204 exercises). | Sprint 02+ |
| In-app notifications (push to phone) | Requires Capacitor plugins + permissions UX. | Sprint 03+ |
| Simple "new plans since last visit" badge | NOT deferred — this IS in Sprint 01 (cheap). | Sprint 01 |
| Capacitor native wrap (App Store / Play Store) | Requires signing, store listings, review — a project in itself. | Sprint 02+ |
| Admin role / admin dashboard | Unclear ownership (HitCourt admin vs fitness admin). | TBD |
| Multi-language (Thai / English) | ltat-fitness has a Tha/Eng toggle. Noted but out of Sprint 01 to keep scope focused. | Sprint 02+ |
| Coach training calendar | Sibling feature in ltat-fitness sidebar. | Sprint 02+ |
| Export to CSV / Excel | Nice-to-have; not blocking. | Sprint 02+ |
| BPP 12-week macrocycle authoring | Coaches don't use it. | Never, unless requested. |
| Movement screening (BPP Assessment tab) | Different concept of testing; not needed for plan-and-log. | TBD; maybe never. |
| 1-Rep-Max calculator | Nice-to-have. | Sprint 04+ |
| Cropper.js profile images, math captcha, raise-a-ticket | Starter Kit globals from Master Rules Set. HitCourt should own most; captcha is on HitCourt's login, not ours. | HitCourt's concern. |

## Open questions for owner (BLOCK Sprint 01 Session 1 START)

### Q1. Does the HitCourt SSO JWT include a `role` claim?

**Plain English:** when HitCourt signs a token and sends the user to us, does the token say whether the person is a coach or a player? If yes, we read the claim and route accordingly. If no, we need another mechanism — possibilities: (a) an API call back to HitCourt to fetch role, (b) infer from `register-coach` vs `register-player` URL the user originally signed up at and store locally, (c) ask the user to pick a role on first visit.

**My default if no answer:** assume the JWT carries `role` (value = "coach" or "player"). If it turns out not to, Session 2 handover will flag it and scope adjusts.

### Q2. Dev SSO strategy — stub or real HitCourt staging?

**Plain English:** to test SSO locally I need some way to generate tokens. Two options:
- **(a) Stub SSO** — a tiny dev-only page in court-fitness that mints a test JWT using a dev-only secret, so I can work without HitCourt being reachable. Production still uses real HitCourt. I recommend this.
- **(b) HitCourt dev/staging credentials** — you give me a dev URL + shared secret that works against a real HitCourt instance. Higher fidelity, more coordination needed.

**My default if no answer:** build stub SSO (option a). Clean, self-contained, no cross-system dependency.

### Q3. Git remote — where does court-fitness live?

**Plain English:** should I push court-fitness to a GitHub repo? If yes, which account (AxxoMob, your personal, a new HitCourt account?), public or private? If no, local git is fine and we add remote later.

**My default if no answer:** init git locally (done Sprint 00); add remote in the session after owner provides URL.

## Sprint 01 risks — ranked by size

1. **Mobile plan-builder UX.** The hardest screen. The ltat-fitness desktop form has ~10 columns side-by-side — that absolutely cannot translate to a phone. Rajat's explicit ask: make this one screen friction-free for players. Over-invest here.
2. **JWT/SSO boundary correctness.** An auth bug is a silent vulnerability. Evidence-based testing at the SSO boundary is non-negotiable. Write unit tests for signature validation, expiry, missing claims before building any feature controller.
3. **PWA discipline from day one.** If we bolt on manifest+service-worker at the end of Sprint 01, we'll find something incompatible and have to redesign. Scaffold PWA in Session 1.
4. **Schema drift repeating.** ltat-fitness's migration mess (HL-1) is the canonical bad pattern. One folder, one lineage, no raw SQL workarounds — if we feel the urge, we stop and fix the folder.

## Reading list for the Session 2 agent (condensed — see `CLAUDE.md` §3 for full)

Must read before any code:
1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` (framework)
2. `CLAUDE.md` (project constitution)
3. `.ai/README.md` (folder map)
4. `.ai/briefing/BRIEFING.md`
5. `.ai/current-state/WIP.md`
6. `.ai/institutional-memory/HARD_LESSONS.md` (all 9 entries)
7. `.ai/institutional-memory/SESSION_LOG.md`
8. `.ai/domain-reference/SEALED_FILES.md`
9. `.ai/domain-reference/ltat-fitness-findings.md`
10. **THIS FILE** (`.ai/sprints/sprint-01/sprint-plan.md`)
11. `.ai/.daily-docs/22 Apr 2026/session_1_handover.md`
12. `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md`

Recommended contextual reads (not mandatory but will save time):
- `C:\xampp\htdocs\ltat-fitness-module\app\Modules\Fitness\Controllers\Trainer\Programs.php` + `Trainer\Sessions.php` + `Player\Sessions.php` — the predecessor's workflow code that Sprint 01 recreates (cleaner).
- `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql` lines 126-600 (grep for CREATE TABLE of `exercise_type`, `fitness_categories`, `fitness_subcategories` + their INSERT blocks) — the seed data source.
- `C:\xampp\htdocs\ltat-fitness-module\docs\TASK_21_IMPLEMENTATION_SUMMARY.md` — justification for the JSON-blob pattern we're reusing.
