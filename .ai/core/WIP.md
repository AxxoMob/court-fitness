# Work In Progress

## Current Sprint: Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

## Current Status

**Session 6 closed (2026-04-26) — UI rebuilt as inline grid; both parties can now log actuals end-to-end.** All four MUST items from `prompt_for_session_6.md` (rebuilt Plan Builder grid; coach + player show views as editable grids on a shared partial; new `POST /coach/plans/{obf}` + `POST /player/plans/{obf}` with no-clobber guarantee + role-checks + ownership-checks; redirect-after-create lands on the editable grid with new flash text) plus both polish items (5 — responsive plan-card grid 3/2/1-up + `.cf-main--wide` page modifier; 6 — audit display "Logged by Coach Rajat · 2m ago" rendered under each logged actual). The wide inline grid matches `plan_builder_ux.md` §1's LTAT screen transcription; mobile collapses each row to a stacked card via CSS media query at <768px (single template, no device sniff).

**No-clobber rule is unit-tested.** `PlanEntriesModel::decideActualUpdate()` is a pure function — both `Coach\Plans::update` and `Player\Plans::update` funnel through it, so there's exactly one place where the rule can be wrong. Five unit tests cover: clean first save, empty bag with no existing actual (write nulls), **load-bearing no-clobber case** (empty bag + existing actual → preserve, return null), non-empty bag overwriting existing (= editing, not no-clobber), and UTF-8 round-trip on the JSON path.

**Three Tier-1 docs edits landed in commit `fb9a420`** at session open, owner-approved at "proceed":
- BRIEFING.md line 3: "Coaches build *multiple* weekly training plans" (one-word owner correction).
- BRIEFING.md: new standalone "Who records actuals — both sides, equally" paragraph above the bullets, with explicit reference to HL-13. The skim-past failure that caused Session 5's rebuild can no longer happen via that reading path.
- CLAUDE.md §6.2 artifact #7: extended with a binary-design-artifact rule. Every screenshot/Figma/sketch/video shared in chat MUST land in `.ai/research-notes/screenshots/` or `/design/` within the same session, with a sibling `.md` note (what / who / when / which feature / spoken context). HL-13 is now codified as a standing rule.

Test suite at 28/28 (was 23/23) — five new tests in `tests/unit/PlanEntryActualUpdateTest.php`. 63 assertions.

**Visual eyeball deferred to owner.** The dev box can render HTML and curl-verify status codes, but Chrome DevTools mobile emulation (375 / 768 / 992 / 1280 px) is the canonical visual check and only the human can execute it. Screenshots after eyeball go to `.ai/research-notes/screenshots/session6-*.png` with sibling `.md` notes per HL-13. The session is closed code-wise; visual sign-off is the next step the owner takes when reviewing.

Commits this session: `ce98deb` (Conformance Check) → `fb9a420` (Tier-1 docs edits) → `1480743` (Plan Builder rebuild — views/JS/controllers/routes/CSS) → `528c4be` (no-clobber decision extracted + 5 unit tests) → `<this close commit>`.

**Session 5 closed cleanly on 2026-04-23** with the Plan Builder shipped end-to-end backend-correct but UI wrong (modal-driven mobile-first instead of inline wide grid). HL-13 captured the root cause: design screenshots from Session 1 were never saved to the repo, so a fresh agent rebuilt the UI from lossy prose. Session 6 fixed the UI; the binary-artifact rule prevents recurrence.

**Session 4 closed cleanly (2026-04-23).** Architecture retrospective — scope pivoted mid-way from "Plan Builder" to "implement the 7 architecture improvements." Zero feature code; sealed framework untouched; all additions in CLAUDE.md and `.ai/core/templates/`. Commits: `30fb22b` + `5a4d81e`.

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

## This Session's Scope (Session 6, 2026-04-26) — ALL 6 ITEMS DONE

**In scope — DONE:**
- ✅ **Owner-approved Tier-1 docs (commit `fb9a420`)** — small but high-leverage: BRIEFING.md gains "multiple" + a standalone "Who records actuals" paragraph; CLAUDE.md §6.2 artifact #7 codifies HL-13's binary-design-artifact rule.
- ✅ **Inline-grid Plan Builder (commit `1480743`)** — `app/Views/coach/plans/_grid.php` is the new shared partial. `public/assets/js/plan-builder.js` rewritten as inline-row controller (no modal, no accordion). Blocks group rows by (date, period, format); rows have category-label + sub-category dropdown + type-specific cells (Cardio: max_hr_pct/duration_min · Weights: sets/reps/weight/rest_sec · Agility: reps/rest_sec). Sub-category options optgroup'd by category, scoped to the block's format. Submit serialises to `entries_json` with optional `id` for update flow.
- ✅ **Coach + Player show views become editable grids (commit `1480743`)** — both `app/Views/coach/plans/show.php` and `app/Views/player/plans/show.php` mount the same `_grid.php` partial with `mode=coach-edit` / `mode=player-edit`. Coach: targets + actuals editable. Player: targets readonly client-side AND silently dropped server-side.
- ✅ **POST /coach/plans/{obf} + POST /player/plans/{obf} (commit `1480743`)** — routes added to `app/Config/Routes.php`; `Coach\Plans::update` + `Player\Plans::update` actions added. CSRF + AuthFilter + role check + ownership check on every request.
- ✅ **No-clobber decision extracted + 5 unit tests (commit `528c4be`)** — `PlanEntriesModel::decideActualUpdate()` is now the pure function both controllers funnel through. Five tests in `tests/unit/PlanEntryActualUpdateTest.php` cover clean-first-save, empty-bag-no-existing, **load-bearing no-clobber case (empty bag + existing actual → preserve, return null)**, edit-overwrite, and UTF-8 round-trip.
- ✅ **Redirect-after-create lands on editable grid (commit `1480743`)** — `Coach\Plans::store` flash text now reads "Plan saved. You can now type actuals here." Coach redirects to `/coach/plans/{obf}` which IS the editable grid.
- ✅ **Responsive widths, single template (commit `1480743`)** — `.cf-main--wide` (max-width 1400px) opted into by Coach\Plans::index, ::new, ::show + Player\Plans::show. `.cf-plan-grid` 3-up at ≥992px / 2-up at 768-991 / 1-up <768. Inline grid collapses to per-row stacked cards <768px via media query on `.cf-row`. Layout (`app/Views/layouts/main.php`) accepts `$mainClass`.
- ✅ **Audit display (commit `1480743`)** — Coach\Plans::show + Player\Plans::show JOIN `plan_entries.actual_by_user_id → users` for `audit_user_name` + `audit_role`. plan-builder.js renders "Logged by Coach Rajat · 2m ago" / "Logged by Rohan · 2m ago" under any row whose actual has been logged.

**Smoke verified via curl (terminal-only):**
- `GET /coach/plans/new` → 200, `data-cf-mode="new"`, `can_edit_target=true`, `can_edit_actual=false`, taxonomy JSON inlined.
- `GET /coach/plans/Y2Y6MQ` → 200, `data-cf-mode="coach-edit"`, plan summary pill renders, 3 demo entries hydrated.
- `GET /player/plans/Y2Y6MQ` → 200, `data-cf-mode="player-edit"`, `can_edit_target=false`, `can_edit_actual=true`.
- `POST /coach/plans/Y2Y6MQ` (no CSRF) → 403. `POST /player/plans/Y2Y6MQ` (no CSRF) → 403. `POST /coach/plans/Y2Y6MQ` (no auth, no CSRF) → 403. CSRF + AuthFilter + role checks all alive.
- 28/28 unit tests passing (was 23/23 — 5 new for no-clobber). 63 assertions.

**In scope — DEFERRED (visual eyeball + screenshots can only be done in browser; the dev box is terminal-only):**
- 👁️ Browser DevTools eye-test at 375 / 768 / 992 / 1280 px. Owner runs Chrome on Windows; the human is the canonical visual checker per HL-13 / `prompt_for_session_6.md` §"Known Risks #3".
- 📸 Screenshots of new desktop grid + mobile stacked view → `.ai/research-notes/screenshots/session6-*.png` with sibling `.md` notes per HL-13 + CLAUDE.md §6.2 artifact #7. Owner takes them after eyeball.
- 🐛 End-to-end POST update flow with valid CSRF token (form-fill via Chrome). Same reason — terminal-only dev box can't execute the form-submit dance cleanly.

**Out of scope (deferred to Session 7+):**
- PWA manifest + service worker.
- Per-type server-side validation of `target_json` / `actual_json` shapes.
- Prettier target-badge labels beyond what the inline grid shows natively.
- Plan Builder "Edit mode" as a separate URL — eliminated; the show URL IS the editable grid.
- Migrations / schema / catalogue changes.
- Rich analytics, charts, progression views, assessments kernel, tennis-specific testing catalogue, training-load / ACWR, fitness directory, export, admin real UI, multi-language, Capacitor wrap.

## This Session's Scope (Session 5, 2026-04-23) — ALL DONE

**In scope — DONE:**
- ✅ Bootstrap 5.3.3 via CDN in `app/Views/layouts/main.php` (CSS head, JS bundle footer), integrity-pinned, with a `scripts` render section for per-page JS.
- ✅ Falcon font stack (`Poppins, -apple-system, …, "Segoe UI Emoji", "Segoe UI Symbol"`) at 16.8px baked into `--cf-font-family` + `--cf-font-size-base`; `--bs-body-font-family`, `--bs-body-font-size`, `--bs-primary` overrides prevent Bootstrap defaults bleeding through. CLAUDE.md §5.4 compliant.
- ✅ `App\Support\IdObfuscator` — URL-safe base64 of `"cf:<id>"`. 9/9 unit tests including the edge cases (empty, garbage, valid base64 without prefix, non-numeric payload, id=0, plain integer input).
- ✅ `App\Controllers\Coach\Plans` — `index()`, `new()`, `store()`, `show()`. Server-side validation (Monday-only week, player belongs to coach, target length ≤100, unit kg|lb, each entry's date in the week window). Transactional insert. Obfuscated redirect on success.
- ✅ Plan Builder view + `public/assets/js/plan-builder.js` — 7-day Bootstrap accordion auto-rendered from `week_of`, per-day session cards (morning/afternoon/evening), Bootstrap modal drilldown (Type → Category → Subcategory), type-specific target fields (Cardio: HR%/min, Weights: sets×reps@weight+rest, Agility: reps+rest), sticky save bar, exercises-count badge. Out-of-window entries are auto-pruned on week change. Mobile + desktop both verified at 375px / 1280px.
- ✅ `App\Controllers\Coach\Plans::index` → `app/Views/coach/plans/index.php` — cards with player + week_of + target + entry count; FAB at bottom.
- ✅ `App\Controllers\Coach\Plans::show` → `app/Views/coach/plans/show.php` — plan detail grouped by day + session, target-blob badges render generically.
- ✅ `App\Controllers\Coach\Players::index` → `app/Views/coach/players/index.php` — assigned-players list only (no add-player form; owner ruled HitCourt is the only identity source).
- ✅ `App\Controllers\Player\Plans::show` → `app/Views/player/plans/show.php` — read-only plan detail with "Logging your actuals unlocks in the next session update." copy for Session 6 handoff. Player dashboard now uses obfuscated IDs.
- ✅ `App\Filters\AuthFilter` + registration in `app/Config/Filters.php` globals — unauthenticated requests 302 to `${HITCOURT_BASE_URL}/login?return=<path>`. Except list: `'/'`, `'sso'`, `'dev'`, `'dev/sso-stub'`. 2 unit tests.
- ✅ **Bonus (engine-engineer call):** CSRF protection enabled globally. HL-12 added. Except list mirrors AuthFilter's (`sso`, `dev/sso-stub`). Verified with a cookieless POST → 403 and a tokened POST → 303 → 200 show page.
- ✅ Coach Dashboard view updated — CTAs now point at the new sub-screens, not Session 3's "coming soon" copy.
- ✅ End-to-end smoke tested via curl: log in → build a 1-entry Agility plan → 303 to `/coach/plans/Y2Y6NQ` → 200 show page with "Plan saved" flash + the subcategory name. DB confirms plan id=5, entry id=8.

**In scope — DEFERRED TO SESSION 6 (not done this session):**
- ❌ POST actuals to `plan_entries.actual_json` — the log-actuals modal. This was always Session 6 scope per the Session 5 prompt.
- ❌ Coach-sees-player-actuals refresh view.
- ❌ PWA manifest + service worker.
- ❌ Plan Builder EDIT mode — Session 6 once create is stable.

**Out of scope (deferred further):** assessments kernel, tennis-specific testing catalogue, training-load / ACWR, fitness directory, export, admin real UI, multi-language, Capacitor wrap, rich player analytics.

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

## Latest: Session 5 (2026-04-23, Sprint 01 Session 4)

Full detail: `.ai/.daily-docs/23 Apr 2026/session_5_handover.md`.

Highlights — the stakeholder-visible Plan Builder flow is end-to-end working:
- Coach Rajat logs in via `/dev/sso-stub?as=coach`, lands on coach dashboard with real CTAs.
- "+ New training plan" → Plan Builder mobile-first form with 7-day accordion, modal drilldown (Type → Category → Subcategory → type-specific target fields for Cardio/Weights/Agility), sticky save bar.
- Save persists `training_plans` + `plan_entries` in a single transaction; redirects to `/coach/plans/Y2Y6<obf>` opaque URL; show page renders each day's sessions with target-badge pills.
- Coach My Plans lists both the seeded demo plan and the newly-saved one (2 cards at end of session).
- Coach My Players lists assigned players — no add-player form, per owner's HitCourt-is-sole-identity ruling.
- Player side: tapping a plan card on the dashboard now reaches `/player/plans/{obf}` instead of 404ing. Actuals-logging UI is the Session 6 deliverable.
- Unauthenticated `/coach/*`, `/player/*` 302 to HitCourt login with a `?return=<path>` query.
- CSRF enforcement is live (was shipped commented-out by CI4 default — HL-12 captures the gotcha).

**Falcon theme cohesion delivered.** Owner's post-Session-4 directive to match Falcon's exact font stack is in court-fitness.css — Poppins + system fallbacks + emoji font, 16.8px base, with Bootstrap 5 variable overrides so Bootstrap's defaults don't leak through. HitCourt-family modules now share a typographic baseline.

## Previous: Session 3 (2026-04-23, Sprint 01 Session 2)

Goal: deliver a stakeholder-visible Player Dashboard on a phone. Achieved. Owner's Session 2 feedback ("can't see anything; stakeholders care about Player Dashboard + mobile compatibility") drove the Session 3 scope shift — backend plumbing + exercise catalogue got compressed into one session so the Player Dashboard could move up from Session 5 to Session 3.

**Owner clarifications received in this session:**
- Training Target is a combobox: dropdown of 7 seeded values + "Add More" → typed free text. Both cases write into a single VARCHAR(100) column on training_plans; suggestions table isn't bloated with one-offs.
- Admin placeholder copy locked: "Fitness administration features are coming soon."
- Brand orange locked: `#F26522`.
- Admin role semantics: "Manager of the entire process of who does what" — orchestration role, to be properly scoped in a later sprint.

## Previous: Session 2 (2026-04-23, Sprint 01 Session 1)

SSO foundation: composer install + firebase/php-jwt ^7.0, JwtValidator + 10 unit tests, Sso controller skeleton, /sso route, .env configured. HL-11 added (firebase/php-jwt DomainException). Full detail: `.ai/.daily-docs/23 Apr 2026/session_2_handover.md`.

## Blockers

**None.** Session 7 is unblocked. The owner's next visual eyeball pass at four DevTools breakpoints + tap-test on a real phone is the only thing standing between Session 6 and "demo ready" — but that is a sign-off step, not a blocker on code.

**Two Tier-1 edits — owner-approved at Session 6 open (2026-04-26) and applied:**
- ✅ BRIEFING.md line 3 corrected to "Coaches build *multiple* weekly training plans" + new standalone "Who records actuals" paragraph that calls out the both-sides equality and references HL-13. No agent can skim past it now.
- ✅ CLAUDE.md §6.2 artifact #7 extended with a binary-artifact promotion paragraph: every screenshot, Figma export, sketch, reference image, video shared in chat lands in `.ai/research-notes/screenshots/` or `.ai/research-notes/design/` within the same session, with a sibling `.md` note (what / who / when / which feature / spoken context). HL-13 is now codified.

## Noticed this Session 6, for Future (NOT done here)

- **Demo seed `target_json` uses `weight_kg` instead of canonical `weight`.** The Session 3 demo seed wrote `{"sets":4,"reps":8,"weight_kg":60,"rest_sec":120}` for the Bench Press entry, but `exercise_json_shapes.md` says the key is `weight` (with the `kg|lb` unit on the parent plan, not per-entry). The new Plan Builder JS won't pre-populate the Weight cell for that demo row because the key doesn't match. Cosmetic for the demo; the underlying schema is correct. Fix at next demo seed refresh: rename `weight_kg` → `weight` in the seeder.
- **Session 6 introduced a single-page in-place update — no AJAX per-row save.** That's the right call for Session 6 simplicity (HL-12's CSRF-token-rotation complexity stays asleep), but eventually a coach in the gym typing actuals will want autosave-on-blur per row. Sprint 02 candidate.
- **Cells per sub-category (vs per-format) is not yet implemented.** `plan_builder_ux.md` §2.4 hints that the live cell set may vary at sub-category granularity (LTAT screenshot shows greyed-out cells for non-applicable measures). Session 6 ships the simpler per-format model (Cardio = Max HR%/Duration, etc.), which is what `exercise_json_shapes.md` already canonicalises. If/when the owner confirms per-sub-category variance, extend `CELLS_BY_FORMAT` in plan-builder.js to a `CELLS_BY_SUBCATEGORY` overlay map. Open question for owner; flagged but not blocking.
- **Block-level Format selector lets the user pick a Cardio sub-category in a Cardio block, then change the block to Weights.** plan-builder.js silently clears affected rows' (cat, sub, target) when the block-format changes. UX could be friendlier: confirm before nuking, or block the change if rows have actuals. Sprint 02 polish.
- **Audit display uses approximate "2m ago" relative times that go stale on a long-open page.** No live tick. Acceptable for a refresh-driven UI; revisit if/when a SPA shell lands.
- **`Coach\Plans::update` ignores submitted `training_date` mismatches (logs a warning, doesn't reject).** Reason: Session 6 doesn't move existing entries between dates — the date is set at create-time and the update flow only mutates target_json + actual_json. If a future session adds drag-to-reschedule, the `applyUpdates()` flow needs to honour `training_date` as an updatable field.
- **No integration test scaffold.** All five new tests in `PlanEntryActualUpdateTest` are pure-function. CI4 has `CIDatabaseTestCase` for DB-touching tests; useful when Session 7 hardens target_json/actual_json shapes server-side. Out of Session 6 scope.

## Noticed this Session 5, for Future (NOT done here)

- **Plan Builder is POST-only create for now.** Editing an existing plan (`GET /coach/plans/{obf}/edit` + `PUT/POST /coach/plans/{obf}`) is explicit Session 6 scope. Until then, the "coach realises the week needs a change" flow is: duplicate the plan manually, delete the old — which isn't good UX. Priority follow-up.
- **Plan detail show views render a generic target-badge loop.** Keys display literally as-is (`max_hr_pct: 75`, `rest_sec: 90`). A future polish pass should prettify these — e.g. "HR 75%", "rest 90s" — matching the summariser in plan-builder.js. Not urgent; readability is acceptable.
- **Target-shape validation is UI-only.** The Plan Builder JS writes `{ max_hr_pct, duration_min }` for Cardio etc., but the server accepts any JSON bag. If a different client POSTs malformed shapes, `target_json` will silently store garbage. Adding a server-side per-type schema check is a Session 7+ hardening task. Shapes documented in `.ai/core/exercise_json_shapes.md`.
- **`Coach\Plans::show` doesn't obfuscate entry IDs.** Only the plan ID is opaque. Entry IDs don't appear in URLs yet, so no immediate exposure — but when Session 6 adds `/player/plans/{obf}/entries/{entry_id}/log`, that entry_id should also go through the obfuscator.
- **Weight unit is plan-level, not entry-level.** `plan_entries.target_json.weight` is a bare number; the `kg|lb` lives on `training_plans`. If a future feature allows per-entry unit override, this needs a re-think. Documented in `exercise_json_shapes.md`.
- **Dev server on PHP built-in: HEAD requests return 404 where GET returns 302.** Curious quirk of CI4 + PHP's built-in server. Not a real app bug (real Apache/nginx won't show this), but if a future smoke test script uses `curl -I`, expect surprises. Use `curl -si` for redirects.
- **Seed data has a gap in fitness_subcategories IDs.** AUTO_INCREMENT went 1..231 because the seeder insert ran twice during Session 3 development (the slug-collision fix). Max id=231 but only 204 rows. This is cosmetic; FKs don't care about gaps. Noted so a future agent doesn't debug it as a regression.
- Inherited follow-ups from previous sessions still valid: trainer→coach vocabulary in the long-tail of legacy docs, base64-URL-obfuscation question answered (`IdObfuscator` is the canonical helper now), tennis-specific testing metrics for Sprint 03+, multi-language for Sprint 02+, PWA install prompt UX, xlsx-survey scripts deletion, `.env`'s dev-only JWT secret replacement when real HitCourt lands.

## Noticed this Session 3 (historical, carried from previous close)

- Player Dashboard card links now point at `/player/plans/{obfuscated_id}` and DO route (fixed Session 5).
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

## Verification Commands — results at end of Session 6

```
$ git log --oneline -7
<close>   sprint-1: session 6 close — 7 artifacts (Plan Builder rebuilt, no-clobber tested)
528c4be   sprint-1: extract no-clobber decision + 5 unit tests on actual_json
1480743   sprint-1: rebuild Plan Builder + show views as inline editable grid (session 6)
fb9a420   docs: owner-approved Tier-1 edits to prevent HL-13 recurrence
ce98deb   sprint-1: session 6 open — Framework Conformance Check committed
39fad8d   docs: add owner directive to Session 6 prompt — no skimming permitted
ecc49e9   New Session

$ git status      → clean at session close

$ ./vendor/bin/phpunit tests/unit/
  28 tests, 63 assertions — all pass
  (10 JwtValidator + 9 IdObfuscator + 2 HealthTest + 2 AuthFilter + 5 PlanEntryActualUpdate)

$ php spark migrate:status
  3 migrations applied — no new migrations this session

$ php spark serve --port 8080   (ran during session for smoke tests)

Smoke-tested URL sequence (curl-based, terminal-only):
  /dev/sso-stub?as=coach   → 302 → /coach (200, dashboard)
  /coach/plans/new         → 200, inline grid renders
                            ✓ data-cf-mode="new"
                            ✓ can_edit_target=true, can_edit_actual=false
                            ✓ taxonomy JSON (3 types + 12 categories + 204 subs) inlined
                            ✓ #cf-blocks empty, #cf-add-block visible
                            ✓ <main class="cf-main cf-main--wide"> (page-modifier opted in)
  /coach/plans/Y2Y6MQ      → 200, mode=coach-edit
                            ✓ plan summary pill renders ("Endurance")
                            ✓ 3 demo entries hydrated into the inline JSON
  /player/plans/Y2Y6MQ     → 200, mode=player-edit
                            ✓ can_edit_target=false, can_edit_actual=true
                            ✓ "Your week with Coach Rajat Kapoor" rendered

CSRF + AuthFilter on new POST routes:
  POST /coach/plans/Y2Y6MQ (no token, no auth)  → 403 (CSRF)
  POST /coach/plans/Y2Y6MQ (no token, w/ auth)  → 403 (CSRF)
  POST /player/plans/Y2Y6MQ (no token, w/ auth) → 403 (CSRF)
  Per-route ownership + role check verified by code inspection (server-side
  re-verification before any DB write — never trust the client).

Test invariant: count went UP (23 → 28) — no tests deleted.
```

## Verification Commands — results at end of Session 5

```
$ git log --oneline -7
d153c9a sprint-1: AuthFilter — redirect unauth'd requests to HitCourt login
0303225 sprint-1: Plan Builder — coach creates weekly plan end-to-end (session 5)
ea3f40a sprint-1: pre-work — Bootstrap 5 + Falcon font stack + IdObfuscator
e86a658 sprint-1: session 5 open — Framework Conformance Check committed
c26a25c docs: Falcon theme cohesion + WIP Session 4 close state
5a4d81e sprint-1: session 4 close (architecture retrospective; Plan Builder → session 5)
30fb22b framework: architecture evolution — 7 improvements + re-entry reading

$ git status      → clean at session close (verified before close commit)

$ ./vendor/bin/phpunit tests/unit/
  23 tests, 48 assertions — all pass (10 JwtValidator + 9 IdObfuscator + 2 HealthTest + 2 AuthFilter)

$ php spark migrate:status
  3 migrations applied — no new migrations this session (schema was already Session 3 design)

$ /c/xampp/mysql/bin/mysql.exe -uroot court_fitness -e "SELECT COUNT(*) FROM training_plans; SELECT COUNT(*) FROM plan_entries;"
  training_plans: 5   (1 Session 3 demo + 4 created during Session 5 smoke tests)
  plan_entries:   8   (3 Session 3 demo + 5 across Session 5 smoke tests)

$ php spark serve --port 8080   (background, still running at close)

Smoke-tested URL sequence, coach side:
  /dev/sso-stub?as=coach   → 200, lands on Coach Dashboard with CTAs
  /coach/plans             → 200, My Plans grid (2 cards)
  /coach/plans/new         → 200, Plan Builder form
  POST /coach/plans (valid CSRF token + 1 entry) → 303 → /coach/plans/Y2Y6NQ → 200 with "Plan saved." flash
  /coach/plans/garbage     → 404 (IdObfuscator::decode returns null, triggers PageNotFoundException)
  /coach/players           → 200, list of assigned players

Smoke-tested URL sequence, player side:
  /dev/sso-stub?as=player  → 200, Player Dashboard (the Session 3 orange screen)
  /player/plans/Y2Y6MQ     → 200, read-only plan detail
  /player/plans/garbage    → 404

Smoke-tested unauthenticated:
  /coach/plans             → 302 Location: https://www.org.hitcourt.com/login?return=%2Fcoach%2Fplans
  /player/plans/Y2Y6MQ     → 302 Location: https://www.org.hitcourt.com/login?return=%2Fplayer%2Fplans%2FY2Y6MQ
  /                        → 200 (welcome; excluded from AuthFilter)
  /dev                     → 200 (excluded)
  POST /coach/plans (no CSRF token) → 403 (CSRF filter)
```

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
