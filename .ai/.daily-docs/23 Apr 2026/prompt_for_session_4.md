# court-fitness — Session 4 Kickoff Prompt

> Paste this entire file as the first message to the Session 4 agent.

---

## Session Onboarding — court-fitness (Session 4, Sprint 01 Session 3)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at `C:\xampp\htdocs\court-fitness` on Windows 11 XAMPP. Production URL: `https://fitness.hitcourt.com`.

This is a long-horizon project under a strict AI Agent Framework. Before you do ANYTHING, read these docs **in order**:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (repo root) — project-specific conventions. Sections 3 + 4 especially.
3. `.ai/README.md` — folder map.
4. `.ai/.ai2/BRIEFING.md` — 1-page project overview.
5. `.ai/.ai2/WIP.md` — current state. Has "In scope — NOT DONE" for Session 4.
6. `.ai/.ai2/SESSION_LOG.md` — project diary (3 rows now).
7. `.ai/.ai2/HARD_LESSONS.md` — eleven entries (HL-1..HL-11). Read all.
8. `.ai/.ai2/SEALED_FILES.md`.
9. `.ai/.ai2/KNOWN_ERRORS.md` (still empty with template).
10. `.ai/.ai2/ltat-fitness-findings.md` — predecessor findings.
11. `.ai/sprints/sprint-01/sprint-plan.md` — **your playbook.** Especially §3 (schema, now implemented) and §5 (UI screens — the meat of Session 4+).
12. `.ai/.daily-docs/23 Apr 2026/session_3_handover.md` — what happened last session.

After reading, DO NOT touch code yet. Run the **Framework Conformance Check** (Appendix D of AI_AGENT_FRAMEWORK.md) in chat. Wait for Rajat's "proceed."

---

## Context

Sprint 01 Session 3 (last session) shipped a **working vertical slice**: DB migrated + seeded, SSO validates + upserts + role-redirects, Player Dashboard renders with orange branding and real demo plan data. Log in as any demo user via the dev stub SSO and see the dashboard on a phone-width layout.

**What Session 4 builds:** the Coach side. Specifically: `My Players`, `My Plans`, and the big hard one — the **Plan Builder** mobile screen. Plus a Plan Detail view for the Player side (so tapping a plan card actually goes somewhere).

**Tech stack (summary):** PHP 8.2.12 / CodeIgniter 4.7.2 / MySQL 8.0+ / Bootstrap 5 (not yet loaded — next session might pull it in, or we stay pure custom CSS — your call). Brand: `#F26522` orange. Full detail: `CLAUDE.md` §7.

---

## What Was Done Last Session (Session 3, 2026-04-23)

1. DB created, 3 migrations applied, 204-row exercise catalogue seeded from ltat_fitness SQL dump (commit `2140d87`).
2. Complete SSO flow — JWT validation → user upsert → session → role-based redirect (coach/player/admin-placeholder).
3. Dev stub SSO harness: `http://localhost:8080/dev` shows one-click login buttons for 4 demo users.
4. Mobile-first Player Dashboard renders Rohan's demo plan with orange header, training target chip, coach name, exercise count, progress bar.
5. Coach Dashboard stub + Admin "coming soon" placeholder.
6. Global CSS at `public/assets/css/court-fitness.css` with CSS custom properties (`--cf-primary` etc.), 44px tap targets, grid adapts to tablet+.

Demo-ready URLs:
- `/dev` — stub SSO landing
- `/dev/sso-stub?as=player` — log in as Rohan (player)
- `/dev/sso-stub?as=coach` — log in as Rajat (coach)
- `/dev/sso-stub?as=admin` — admin placeholder

---

## What Needs To Be Done Now (Session 4 — "Plan Builder, visible")

> **⚠️ Priority signal from owner at Session 3 close (2026-04-23):** "The layouts are looking decent for the moment, however the part which everybody would be really interested in, would be the part where the Coach adds a training session for the players — of which I had given screenshots. Both the Web and Mobile versions are eagerly awaited by the stakeholders."
>
> **Translation:** the Plan Builder is THE deliverable. Everything else in Session 4 is plumbing around it. If you have to cut something, cut AuthFilter or Player Plan Detail — never cut Plan Builder.

### Session 4 priority order (re-ranked per owner's signal)

1. **Pre-work: Bootstrap 5 + IdObfuscator.** These unlock the Plan Builder. ~1.5 hours combined.
   - Add Bootstrap 5 (CSS + JS bundle) via CDN in `app/Views/layouts/main.php`. Keep the existing `public/assets/css/court-fitness.css` — `--cf-primary` etc. still drive brand; Bootstrap handles structure.
   - Add `app/Support/IdObfuscator.php` with `encode(int): string` + `decode(string): ?int` using URL-safe base64 of `"cf:<id>"` (with `cf:` prefix so garbage strings return null). Write a unit test. All plan-ID-bearing routes use this helper.

2. **Coach Plan Builder — THE screen.** Full happy path for creating a fresh plan. Routes `GET /coach/plans/new` (renders the form) + `POST /coach/plans` (persists).
   - Top form fields (Bootstrap 5 form group): player (`<select>` from the coach's assigned players, empty state if none), week_of (date input that only accepts Mondays — client-side + server-side validation), training_target (combobox pattern: Bootstrap dropdown of 7 seeded suggestions + an "Add More" option that reveals a `<input maxlength="100">`), weight_unit (radio kg/lb).
   - Day-by-day Bootstrap accordion — 7 days of the selected week. Each day collapses to show its session cards.
   - Per-day, three session cards: morning / afternoon / evening. Each card has a list of exercises + "+ Add exercise" button.
   - Add-exercise opens a Bootstrap modal (on mobile it should feel like a bottom sheet — use `.modal-dialog-scrollable` + custom CSS for phone) with the 3-level drilldown: pick exercise_type (Cardio/Weights/Agility) → reveal fitness_category dropdown → reveal fitness_subcategory dropdown. After the subcategory is picked, the modal expands to show type-specific target fields (Cardio = max_hr_pct + duration_min; Weights = sets + reps + weight + rest_sec; Agility = reps + rest_sec). "Add" stores the entry in a hidden JSON buffer on the form.
   - Save button (sticky bottom on mobile) POSTs the whole plan + entries. CSRF token (CI4 built-in). Server-side: validate week_of is a Monday; create training_plans row + plan_entries rows in a transaction; redirect to the plan's obfuscated URL.
   - **Web and mobile layouts both must work.** Bootstrap's responsive grid does most of this; test at 375px and 1280px widths in DevTools.

3. **Coach My Plans.** List view the coach sees at `/coach/plans`. Cards with player name, week_of, training_target chip, entry count. FAB "New Plan" → Plan Builder. Filter chips (Weekof, Player) client-side.

4. **Coach My Players.** Simpler than I originally scoped: NO "Add Player" form (owner confirmed — see §Decisions below). Just a list of players currently assigned to this coach (`coach_player_assignments` JOIN `users`). Search field that filters the visible list by first-letters match. Future: Sprint 02+ adds an API-backed search to find HitCourt players who haven't yet SSO'd into court-fitness.

5. **Player Plan Detail (read-only).** Route `GET /player/plans/{obfuscated_id}`. Shows the plan's entries grouped by day + session. Same CSS card pattern as Plan Builder — reuse the template partial. Tap-to-log-actuals button on each entry (lands on a Session 5 placeholder until that session builds the Log Actuals modal).

6. **AuthFilter.** Bumped to the END because the dev stub SSO is the entry point — nothing actually needs AuthFilter to work for demo. Still should land in Session 4 if time. `app/Filters/AuthFilter.php` checks `session('is_authenticated')`; if missing, 302 to `${HITCOURT_BASE_URL}/login?return=<path>`. Register globally; exempt `/`, `/sso`, `/dev`, `/dev/sso-stub`. Add a unit test.

### Session 4 WILL NOT do (deferred to Session 5)

- POST actuals to `plan_entries.actual_json` (the "log actuals" modal contents).
- Coach-sees-player's-actuals refresh view.
- PWA manifest + service worker.
- Plan Builder EDIT mode (loading an existing plan for modification). Session 5 adds this once the Session 4 create flow is proven.
- Empty-state UX polish, icons, and micro-interactions. Developers team can refine.

### Realistic session shape

Session 4 is ambitious. Honest expectation: items 1-3 land complete; items 4-5 land functional-but-rough; item 6 likely slips to Session 5. That's still a demo-able Plan Builder + visible Coach-side UI for stakeholders, which is what the owner is asking for.

---

## Decisions the Owner Made at Session 3 Close (all 3 open questions resolved)

1. **New-player creation — SIMPLIFIED.** Per owner 2026-04-23: "The Coach can NOT add any player who is not already registered with HitCourt. That's the basis for everything. Anybody who wishes to do any kind of activity on any of the hitcourt modules must first be registered with HitCourt."
   - **Implication for Coach My Players:** no "Add Player" form that creates a new user record. Instead, the coach searches for a player who ALREADY exists in court-fitness's local `users` table (i.e. has SSO'd in at least once) and "assigns" them by creating a `coach_player_assignments` row.
   - **What if the player exists on HitCourt but has not yet SSO'd into court-fitness?** For Session 4 MVP the coach cannot assign them yet — they show up as soon as they log in to court-fitness for the first time. Future enhancement (Sprint 02+): an API call from court-fitness to HitCourt to pre-fetch users who've registered but not yet logged in, OR a HitCourt webhook on registration that pre-populates our `users` table. Flag this to Rajat in the Session 4 handover as a known limitation.
   - **What court-fitness never does:** create a new `users` row from scratch. Identity always originates on HitCourt.

2. **URL pattern — base64 obfuscation.** Per owner: follow ltat-fitness pattern. URLs look like `/coach/plans/MCMjlzA=` instead of `/coach/plans/42`.
   - **Implementation:** a small helper class (say `App\Support\IdObfuscator`) with `encode(int): string` and `decode(string): ?int`. Use URL-safe base64 of `"cf:<id>"`; `decode` validates the `cf:` prefix so a garbage string returns null. This is URL opacity (not cryptographic security) — matches ltat-fitness's approach.
   - All routes that accept a plan/assignment/etc. ID go through the helper: `/coach/plans/(:segment)` → controller decodes, loads by int ID, 404s if decode fails.
   - For Session 4 scope: apply to plan IDs in coach + player URLs. Other IDs can remain plain integers until they hit user-visible URLs.

3. **Bootstrap 5 — yes, pull it in.** Per owner: "Please build in BootStrap 5, small css customizations can be handled by the Developers team."
   - **Implementation:** add Bootstrap 5 CSS + JS bundle via CDN links in `app/Views/layouts/main.php`. Keep the existing `public/assets/css/court-fitness.css` for brand overrides — CSS custom properties (`--cf-primary`, etc.) continue to drive orange branding on top of Bootstrap's defaults.
   - Use Bootstrap components where they save work: accordion (Plan Builder day-by-day), modals (bottom-sheet drilldown on mobile, dialog on desktop), forms, navbar.
   - Don't rip out the cf-prefixed CSS — it has the orange branding dialled in; just let Bootstrap handle structure.
   - Consider pulling via Composer (`composer require twbs/bootstrap`) for offline reliability in Sprint 02; CDN is fine for Session 4.

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **Foreign keys ARE used.** Engineering decision (HL-6).
- **ONE migrations folder.** If tempted to apply raw SQL because a migration fights you, STOP (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder. No jargon without definition.
- **Captain / Engine Engineer model.** Technical decisions within scope are yours by default — make them, explain, move on. Product / scope / priority is his.
- **Mobile-first UI, non-negotiable.** Every screen passes the 5-inch phone test before desktop is considered.
- **Commit per logical unit.** Typical Session 4 commits: AuthFilter + tests → Player Plan Detail → Coach My Players → Coach My Plans → Plan Builder skeleton.
- **No `--amend`, no `--force`, no `--no-verify`.**
- **Sealed:** `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`. Never modify.
- **Six session-close artifacts** mandatory at close. Begin close at 70% context.

---

## Verification Commands

End-of-Session-4 targets:

```bash
cd C:/xampp/htdocs/court-fitness

# Tests: AuthFilter test added on top of existing 10
./vendor/bin/phpunit tests/unit/                        # all pass

# Server + end-to-end demo flow
php spark serve --port 8080                              # background
# As player — tap plan card reaches Plan Detail (not 404 anymore)
curl -sL -c c.txt -b c.txt "http://localhost:8080/dev/sso-stub?as=player"       # lands on dashboard
# Then: curl the plan detail URL with cookies → renders
# As coach — My Players + My Plans + Plan Builder all render without 500s
curl -sL -c c.txt -b c.txt "http://localhost:8080/dev/sso-stub?as=coach"        # lands on coach dashboard
# Manually: browse /coach/players, /coach/plans, /coach/plans/new and verify each mobile layout

# Unauthenticated → AuthFilter → 302 to HitCourt
curl -sI "http://localhost:8080/coach"                   # 302 to hitcourt login URL

git status                                               # clean at session close
git log --oneline -10                                    # ≥ 3 meaningful commits this session
```

---

## Known Risks (descending likelihood)

1. **Plan Builder mobile UX.** The hardest screen. ltat-fitness's desktop form has ~10 columns side-by-side — cannot translate to a phone. Design the accordion → bottom-sheet drilldown carefully. Over-invest here; this is what stakeholders judge the product on.
2. **AuthFilter + dev stub SSO interaction.** Filter must exempt `/sso`, `/dev`, `/dev/sso-stub`, and `/` (the CI4 welcome page). Getting this wrong creates an infinite redirect loop OR blocks SSO login. Test carefully.
3. **JSON shape for `target_json` per exercise type.** Cardio, Weights, Agility each want different fields. Either branch in the Plan Builder UI or use a flexible dynamic form. Document the JSON shapes in a new doc (`.ai/.ai2/exercise_json_shapes.md` is a reasonable place).
4. **Session cookie persistence through a POST form submit.** The Plan Builder will POST — make sure CSRF tokens are configured (CI4 has built-in support; check `Config/Security.php`).

---

## When In Doubt

- Unclear requirement → ask Rajat (Rule 9).
- Unclear past decision → `.ai/.ai2/HARD_LESSONS.md` + the Session 3 handover.
- Tempted by destructive git op → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL because a migration fights you → STOP (HL-1).
- Tempted to mock a role check "just to unblock" → NEVER (HL-8).

Good luck. Run the Framework Conformance Check, wait for "proceed," build the Coach side.
