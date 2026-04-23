# court-fitness — Session 6 Kickoff Prompt

> Paste this entire file as the first message to the Session 6 agent in a **fresh** Claude conversation. The §3.2 re-entry path does NOT apply — you are a new Claude with a blank whiteboard.

---

## Session Onboarding — court-fitness (Session 6, Sprint 01 Session 5 — "Close the loop: log actuals, edit plans")

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at `C:\xampp\htdocs\court-fitness` on Windows 11 XAMPP. Production URL: `https://fitness.hitcourt.com`.

You are a **FRESH AGENT** in a new conversation. Follow the full reading list in `CLAUDE.md §3.1` (13 items). Then commit your Conformance Check to `.ai/.daily-docs/{today}/session_6_conformance.md` per CLAUDE.md §3.3 BEFORE requesting owner "proceed."

**The full reading is deliberately long.** The Conformance Check's 8 questions catch skimmers. Don't rush; a fresh Claude that skips the read is why this framework exists.

---

## What Session 5 shipped (read this before picking a task)

Session 5 overshot its honest "1-3 items complete" target and delivered all six items in the re-ranked Plan Builder task list, plus one bonus:

1. **Bootstrap 5 + Falcon font cohesion** — Bootstrap 5.3.3 via CDN in `app/Views/layouts/main.php`. Falcon-exact font stack (`Poppins, -apple-system, …, "Segoe UI Emoji", "Segoe UI Symbol"`) at 16.8px baked into `--cf-font-family` / `--cf-font-size-base`, with `--bs-body-font-family` / `--bs-body-font-size` / `--bs-primary` overrides preventing Bootstrap defaults bleeding through. HitCourt-family typographic cohesion per CLAUDE.md §5.4.
2. **`App\Support\IdObfuscator`** — URL-safe base64 of `"cf:<id>"`. All user-visible plan URLs go through this. Rejects empty, garbage, valid-base64-without-prefix, non-numeric, id=0, and plain-integer input. 9 unit tests.
3. **Coach Plan Builder** — `GET /coach/plans/new` + `POST /coach/plans`. Mobile-first Bootstrap accordion (7 days) + modal drilldown (Type → Category → Subcategory → type-specific target fields: Cardio HR%/min, Weights sets×reps@weight+rest, Agility reps+rest). Sticky save bar. Transactional insert. Monday-only enforcement client-side + server-side. Redirects to obfuscated plan URL with "Plan saved" flash.
4. **Coach My Plans** — `/coach/plans` card list + FAB.
5. **Coach My Players** — `/coach/players` list only (NO add-player form; HitCourt is the sole identity source, per owner's 2026-04-23 ruling).
6. **Player Plan Detail (read-only)** — `/player/plans/{obfuscated_id}`. The Player Dashboard cards now route here instead of 404-ing.
7. **AuthFilter** — global `before` filter. Unauthenticated requests 302 to `${HITCOURT_BASE_URL}/login?return=<path>`. Except list: `/`, `sso`, `dev`, `dev/sso-stub`.

**Bonus engine-engineer call:** **CSRF protection enabled globally**. CI4 ships `'csrf'` commented out in `Config/Filters` by default — HL-12 captures the trap. All future POST forms must include `csrf_field()`; the Plan Builder already does.

**Test count:** 12 → 23 (23/23 green, 48 assertions). 10 JwtValidator + 9 IdObfuscator + 2 HealthTest + 2 AuthFilter.

**Commits from Session 5:** `e86a658` (conformance) → `ea3f40a` (pre-work) → `0303225` (Plan Builder) → `d153c9a` (AuthFilter) → session-close commit.

Session 5 handover (detailed): [.ai/.daily-docs/23 Apr 2026/session_5_handover.md](../23%20Apr%202026/session_5_handover.md).

---

## What Session 6 must do — "Close the loop"

The feature built in Session 5 is a one-way street: coach creates a plan; player sees it; neither side can modify or log anything. Session 6 closes the loop so coach and player can actually USE the app.

### Session 6 priority order

1. **Player Log-Actuals modal** — THE Session 6 stakeholder deliverable. Mirrors the Plan Builder's add-exercise modal but for logging a SINGLE entry's actual values after-the-fact. Routes:
   - `POST /player/plans/{obfuscated_plan_id}/entries/{entry_id}/log` — persists to `plan_entries.actual_json` + stamps `actual_by_user_id` + `actual_at`. CSRF-protected.
   - View change: `app/Views/player/plans/show.php` adds a "Log actuals" button per entry that opens a Bootstrap modal with the same type-specific fields as the Plan Builder modal (the shapes are pinned in [.ai/core/exercise_json_shapes.md](../../core/exercise_json_shapes.md)). Pre-fills with target values as hints.
   - Server validates: entry belongs to a plan owned by the logged-in player; `actual_json` shape matches the entry's `exercise_type_id`.
   - On save: redirect back to `/player/plans/{obf}` with a success flash; the green "Logged" badge renders (the show view already has the hook — see `app/Views/player/plans/show.php:60-70`).
   - **Owner signal carried from Session 5:** make this cockpit-simple on a phone. Big number pads, no accidental double-tap, save button visually dominant. Stakeholder judgement happens here alongside the Plan Builder.

2. **Coach-logs-actuals on behalf of a player** — when the coach is training the player in person they log for them. Same modal, mounted on `app/Views/coach/plans/show.php`, with:
   - `POST /coach/plans/{obf}/entries/{entry_id}/log` — same shape, different route, writes `actual_by_user_id = coach's id` for audit.
   - Show view: both coach and player see `actual_by: "Coach Rajat"` vs `actual_by: "Rohan"` so there's no ambiguity.
   - This unlocks the "coach refreshes, sees actuals" story that Sprint 01's "done" definition requires.

3. **Plan Builder EDIT mode** — the coach should be able to fix a plan they built. Routes:
   - `GET /coach/plans/{obf}/edit` — renders the Plan Builder form pre-filled with the existing plan + entries.
   - `POST /coach/plans/{obf}` — updates. CSRF-protected. Transactional: update plan header + `DELETE FROM plan_entries WHERE training_plan_id = ?` + re-insert entries. (Simpler than diffing; acceptable because actuals are on separate columns — but verify with a test that editing a plan does NOT clobber already-logged actuals.)
   - **Watch out:** if an entry has `actual_json IS NOT NULL`, do NOT delete it on edit. Either skip it from the delete, or do a three-way merge. Safer option for Session 6: refuse to edit entries that already have actuals — show a warning. Owner can decide.
   - The Plan Builder JS already has most of the machinery; it just needs a hydration path that pre-populates `entries[]` from a JSON blob rendered into the page.

4. **Seal proposals at sprint close** — if Session 6 is the Sprint 01 close, per CLAUDE.md §12 the closing agent proposes seal candidates in the sprint handover. Current watchlist in `.ai/core/SEALED_FILES.md`: `App\Services\JwtValidator`, `App\Support\IdObfuscator`, the three seed migrations, `App\Controllers\Sso`. Propose them formally; owner decides.

5. **PWA manifest + service worker** — if time. This unblocks the phone-installable demo. `public/manifest.json` (name, short_name, icons 192/512, theme-color `#F26522`, start_url `/`, display `standalone`) + a minimal `public/sw.js` that caches the app shell. `<link rel="manifest" href="/manifest.json">` + `<script>if('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');</script>` in `app/Views/layouts/main.php`.

6. **Prettier target-badge rendering on show views (polish)** — currently keys show as-is (`max_hr_pct: 75`). The `summariseTarget()` function in `public/assets/js/plan-builder.js` already has the pretty formatter; extract it to a PHP helper or mirror it in the view and the badges become `HR 75%` / `30 min`. Nice-to-have.

### Session 6 WILL NOT do (deferred to Session 7+)

- Assessments kernel (`assessments` + `metric_types` tables) — Sprint 03+.
- Tennis-specific testing catalogue — Sprint 03+.
- Training load / ACWR monitoring — Sprint 04+.
- Rich player analytics charts — Sprint 02+.
- Fitness exercise encyclopaedia (descriptions/videos for 204 exercises) — Sprint 02+.
- Capacitor native wrap (App Store / Play Store) — Sprint 02+.
- Admin real UI — deferred indefinitely.
- Multi-language Thai/English — Sprint 02+.
- Server-side per-type `target_json` schema validation — Session 7+ hardening; Session 6 trusts the UI.

### Realistic session shape

Honest target: items 1-2 complete. Item 3 might land functional-but-rough; item 4 only if we're sprint-closing; items 5-6 likely slip. **The stakeholder-visible deliverable is the player's ability to log actuals from a phone.** If context gets tight, that is the one thing that MUST land complete.

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **Framework reading is mandatory.** 13-item list for fresh agents (CLAUDE.md §3.1). Commit `session_6_conformance.md` BEFORE owner says "proceed" (§3.3).
- **Seven close artifacts** (§6.2): WIP · SESSION_LOG · handover (with framework version stamp at top) · next prompt · HARD_LESSONS (if non-obvious things surfaced) · git commits · memory-to-repo promotion.
- **Project-wide session-N naming** — `session_6_handover.md` regardless of calendar date.
- **Captain / Engine Engineer.** Technical decisions inside the plan are yours; product/scope/priority is Rajat's. Ask plain-English questions when a decision has product implications.
- **Mobile-first UI, non-negotiable.** Every screen passes the 5-inch phone test before desktop. Over-invest on the Log Actuals modal specifically — thumbs matter.
- **Falcon theme cohesion** (CLAUDE.md §5.4). If in doubt about a UI choice, open https://prium.github.io/falcon/v2.8.2/default/index.html in one tab, court-fitness in another, and match.
- **CSRF is live** (HL-12). Every form needs `csrf_field()`. Every test of a POST handler needs to include the token.
- **Sealed files** (§5): only `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`. Never modify. If owner approves a seal in chat, add to `SEALED_FILES.md` per the schema.
- **Commit cadence:** one logical unit per commit. Typical Session 6 commits might be: player-log-actuals modal + route → coach-log-actuals → Plan Builder edit mode → session-close commit.
- **No `--amend` on pushed commits. No `--force`. No `--no-verify`.** Hook failures mean something is wrong; investigate, don't bypass.
- **Scope discipline** (Rule 7). The temptation mid-session is always to "also fix this one thing I noticed." Log it in WIP.md follow-ups; do NOT do it. Session 5's CSRF enable is the one defensible exception (security + trivial + form already supported) — your Session 6 has no equivalent free shots.
- **Evidence over assertion** (Rule 11). Every "works" claim in the handover comes with curl output or phpunit output.

---

## Verification Commands

End-of-Session-6 targets (from the owner's demo perspective):

```bash
cd C:/xampp/htdocs/court-fitness

# Tests: 23 existing + at least 2 new (ActualsController tests; edit-mode test)
./vendor/bin/phpunit tests/unit/                     # all pass; count ≥ 25

# Migration + seed state unchanged (no new migrations expected in Session 6)
php spark migrate:status                             # 3 migrations applied

# Dev server
php spark serve --port 8080                          # background

# Flow 1 — Player logs actuals
#   /dev/sso-stub?as=player → /player → tap a plan → see "Log actuals" button
#   → modal opens with type-specific fields → fill in → save
#   → redirect back to plan → green "Logged" badge rendered
curl -sL -c c.txt -b c.txt "http://localhost:8080/dev/sso-stub?as=player"
# (Manual browser verification at 375px is the real test.)

# Flow 2 — Coach logs on behalf of a player
#   /dev/sso-stub?as=coach → /coach/plans → open a plan → same modal → save
#   → refresh as player → see the coach's log with actual_by = "Coach Rajat"

# Flow 3 — Plan Builder edit
#   /coach/plans → Open a plan → "Edit plan" button → Plan Builder form loads populated
#   → change a value, save, redirect back to show view with updated content
#   → any entries that had actual_json are preserved (test explicitly)

# Flow 4 — AuthFilter still works on new routes
curl -sI "http://localhost:8080/player/plans/Y2Y6MQ/entries/1/log"
# → 302 to https://www.org.hitcourt.com/login?return=…

git status                                           # clean at session close
git log --oneline -8                                 # ≥ 3 meaningful commits
```

Also at close, paste in the handover:

```
$ /c/xampp/mysql/bin/mysql.exe -uroot court_fitness -e \
    "SELECT COUNT(*) AS plans, (SELECT COUNT(*) FROM plan_entries) AS entries, \
            (SELECT COUNT(*) FROM plan_entries WHERE actual_json IS NOT NULL) AS logged \
     FROM training_plans;"
```

---

## Known Risks (descending likelihood)

1. **Plan Builder edit + already-logged entries** — the naive "DELETE all entries, re-insert" approach clobbers actuals. Either refuse to edit entries that have `actual_json IS NOT NULL` (simpler, safer for Session 6) OR implement a proper diff (more work; more correctness). Pick the first; surface it plainly in the handover.
2. **CSRF token regeneration on successful POST** — CI4's `Security::$regenerate = true` means after a verified POST, the token rotates. For a page with multiple POST actions (the Plan Builder show view has multiple "Log actuals" buttons, one per entry), each submission needs to re-read the CURRENT cookie value. Bootstrap modals that submit via `<form>` post-and-redirect handle this naturally; any AJAX path does not. For Session 6 scope, keep it to plain `<form>` posts and you sidestep this.
3. **Per-type target validation drift** — the log-actuals modal re-uses the JS shape logic. Make sure the actual-shape matches the target-shape per type (same keys). Documented in `.ai/core/exercise_json_shapes.md`; any drift is a silent data-quality bug.
4. **Modal state on a page with 20+ entries** — a week with 3 days × 3 sessions × 4 exercises = 36 modals. Don't render 36 separate `<div class="modal">` blocks; render ONE modal and swap its contents when a button is tapped, passing the entry_id via `data-entry-id`. Bootstrap's `show.bs.modal` event hook makes this clean.
5. **Session 6 is Sprint 01 close territory.** Be alert to the §12 seal-candidate pass and the §11 archival pass. If this is the last session of Sprint 01, do both. If Sprint 01 needs one more session (7) to close, save archival for Session 7.

---

## Sprint 01 state entering Session 6

Scope of Sprint 01 per `.ai/sprints/sprint-01/sprint-plan.md` was: "Coach plans a week, player logs actuals, on a phone."

**Done:**
- Auth (SSO + AuthFilter)
- DB schema (all 3 migrations)
- Exercise taxonomy seeded (3 + 12 + 204)
- Player Dashboard
- Coach Dashboard
- Plan Builder (create)
- My Plans + My Players
- Plan Detail (coach + player, read-only)

**Remaining for Sprint 01 "done":**
- Player logs actuals (item 1 above)
- Coach sees player's actuals (flows naturally from item 1 + existing show views)
- PWA manifest + service worker (nice-to-have; can slip to Sprint 02)
- Plan edit (item 3 above; probably Sprint 01 must-have)

Session 6 likely closes Sprint 01. Session 7 is the sprint-close + first session of Sprint 02 if not.

---

## Demo URLs (owner has seen these work in Session 5)

- `http://localhost:8080/` — unauthenticated welcome (200)
- `http://localhost:8080/dev/sso-stub?as=coach` → `/coach` (Coach Dashboard with 3 CTAs)
- `http://localhost:8080/dev/sso-stub?as=player` → `/player` (orange Player Dashboard)
- `http://localhost:8080/dev/sso-stub?as=admin` → `/admin-placeholder` ("coming soon")
- `http://localhost:8080/coach/plans` → My Plans list with cards
- `http://localhost:8080/coach/plans/new` → Plan Builder form
- `http://localhost:8080/coach/plans/Y2Y6MQ` → show plan 1 (the Session 3 seeded demo)
- `http://localhost:8080/coach/players` → My Players list
- `http://localhost:8080/player/plans/Y2Y6MQ` → read-only plan detail

Unauth'd access to any `/coach/*` or `/player/*` URL 302s to `https://www.org.hitcourt.com/login?return=…`.

---

## When In Doubt

- **Unclear requirement?** Ask Rajat in chat (Rule 9). The cost of asking is 30 seconds.
- **Tempted by "while I'm here…"?** Don't. Log it in WIP.md follow-ups (Rule 7).
- **A test feels flaky?** Investigate; do NOT delete (Rule 10 + Test-count invariant §8.2).
- **Tempted by `--amend` / `--force` / `--no-verify`?** Stop and ask.
- **Tempted to apply raw SQL because a migration fights you?** STOP (HL-1).
- **Tempted to bypass SSO with a query-string user ID?** NEVER (HL-8).
- **Tempted to handle auth differently for the log-actuals endpoint?** NO — use the same session + role + ownership check pattern as `Player\Plans::show`.
- **Context feels tight?** Initiate session close EARLY (70% rule). Seven artifacts matter more than one more feature. An honest partial close is preferable to the §13 abort protocol. A Session 7 picks up whatever slipped.
- **Unsure whether Session 6 is Sprint 01 close?** Read `.ai/sprints/sprint-01/sprint-plan.md` § "Done definition" before writing the close artifacts; check your actual delivery against the bulleted list there; ask Rajat in chat if ambiguous. If it IS the close, add the §12 seal-candidate review to your handover AND do the §11 archival (both sprint-close-only chores).

---

## Fresh-Agent Checklist (do these in order, nothing skipped)

1. Read the full 13-item list per CLAUDE.md §3.1.
2. Run baseline verification commands (`git status`, `git log --oneline -5`, `ls -la .ai/`, `php spark migrate:status`, `./vendor/bin/phpunit tests/unit/`).
3. Write `.ai/.daily-docs/{today}/session_6_conformance.md` using the template at `.ai/core/templates/SESSION_CONFORMANCE_TEMPLATE.md`. Answer all 8 questions from actual reading; paste baseline output.
4. Commit the conformance file: `git commit -m "sprint-1: session 6 open — Framework Conformance Check committed"`.
5. Tell Rajat in chat: "Conformance Check committed as `<sha>`. Plan for Session 6: items 1-2 target complete (player log actuals + coach log actuals); items 3-5 as time allows. Awaiting 'proceed'."
6. Wait. Do NOT start coding.
7. On "proceed," begin work.

Good luck. Session 5 closed the form; Session 6 closes the loop.
