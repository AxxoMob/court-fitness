# court-fitness — Session 5 Kickoff Prompt

> Paste this entire file as the first message to the Session 5 agent in a FRESH conversation. The re-entry path (§3.2) does NOT apply — this is a new Claude with a blank whiteboard.

---

## Session Onboarding — court-fitness (Session 5, Sprint 01 Session 4 — "Plan Builder, visible")

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at `C:\xampp\htdocs\court-fitness` on Windows 11 XAMPP. Production URL: `https://fitness.hitcourt.com`.

You are a **FRESH AGENT** in a new conversation. Follow the full reading list in `CLAUDE.md §3.1` (13 items). Then commit your Conformance Check to `.ai/.daily-docs/{today}/session_5_conformance.md` per CLAUDE.md §3.3 BEFORE requesting owner "proceed."

---

## What changed in Session 4 (architecture retrospective you should know about)

Session 4 was a scope-pivoted **architecture retrospective**, not a feature session. Owner asked the previous agent to evaluate the session-start/session-close protocol and implement improvements. Key changes you'll notice:

1. `.ai/.ai2/` is now `.ai/core/`. All path references updated. Historical docs' header notes updated too.
2. CLAUDE.md §3 split into §3.1 (fresh agent, 13-item list — that's YOU), §3.2 (returning agent, 4-item re-entry — NOT you), §3.3 (commit the Conformance Check).
3. CLAUDE.md §6 now requires **7 close artifacts** (not 6; added memory-to-repo promotion), **framework version stamp** at the top of every handover, and **project-wide session-N** naming (session_5_handover.md is correct regardless of calendar date).
4. New CLAUDE.md §11 (archival), §12 (seal cadence), §13 (session abort protocol).
5. Two new templates at `.ai/core/templates/` — copy them when needed.
6. `.ai/core/SEALED_FILES.md` has a live candidate watchlist now.

**Your mandatory reading list is at `CLAUDE.md §3.1`.** Read it. All 13 items. The full reading is deliberately long — the Conformance Check's 8 questions catch skimmers.

---

## Context — Sprint 01 state entering Session 5

Sessions 1-3 shipped: framework docs + Sprint 01 plan + DB migrated + seeded + SSO working + Player Dashboard on mobile with orange branding (`#F26522`). Session 4 evolved the architecture. **No feature code was written in Session 4.**

Session 4's ORIGINAL prompt (Plan Builder) is still your playbook — find it at `.ai/.daily-docs/23 Apr 2026/prompt_for_session_4.md`. It has the re-ranked task list, owner's decisions on new-player creation / base64 URLs / Bootstrap 5, and the stakeholder priority signal. **Use it as-is** — the task list is still correct; only Session 4's DATE and numbering shifted to Session 5.

Demo URLs (owner has seen these work):
- `http://localhost:8080/dev/sso-stub?as=player` → Rohan's orange Player Dashboard
- `http://localhost:8080/dev/sso-stub?as=coach` → Coach Rajat's dashboard
- `http://localhost:8080/dev/sso-stub?as=admin` → "Fitness administration features are coming soon."

---

## What Session 5 must do

Same as Session 4's re-ranked plan (priority signal from owner: Plan Builder is THE stakeholder deliverable):

1. **Pre-work — Bootstrap 5 (CDN) + `App\Support\IdObfuscator` helper + its unit test.** Add Bootstrap via `<link>` / `<script>` in `app/Views/layouts/main.php`. Obfuscator uses URL-safe base64 of `"cf:<id>"`. Unit test covers encode/decode round-trip + garbage-string returning null.

2. **Coach Plan Builder — THE stakeholder screen.** `GET /coach/plans/new` renders the form; `POST /coach/plans` persists. Bootstrap accordion (day-by-day) + modal drilldown (Type → Category → Subcategory → type-specific target fields). Mobile-first AND desktop. See `.ai/.daily-docs/23 Apr 2026/prompt_for_session_4.md` §"Session 4 priority order" item 2 for the full spec.

3. **Coach My Plans list** — `/coach/plans` — cards with FAB to Plan Builder.

4. **Coach My Players list** — `/coach/players` — list only, NO add-player form (owner ruled HitCourt is the only identity source).

5. **Player Plan Detail (read-only)** — `/player/plans/{obfuscated_id}` — so tapping a plan card actually goes somewhere.

6. **AuthFilter** — last, if time. Stub SSO remains the entry point for dev.

Session 5 will NOT fit all of this — be honest. Items 1-3 is a realistic target. Defer item 4-6 to Session 6 if needed.

---

## Key Conventions (quick reference — full in CLAUDE.md)

Same as before. New since you may be new: **commit the Conformance Check** (§3.3), **include framework version stamp** in handover (§6.2), **7 close artifacts** not 6 (§6.2), **project-wide session-N naming**.

---

## Verification Commands

See `CLAUDE.md §9`. Targeted for end-of-Session-5:

```bash
cd C:/xampp/htdocs/court-fitness
./vendor/bin/phpunit tests/unit/   # JwtValidator (10) + new IdObfuscator tests — all green
php spark serve --port 8080         # background
# Log in as Coach; Plan Builder loads; mobile layout works at 375px in DevTools
# Build a plan end-to-end: 2 days, 3 exercises, save, land on obfuscated plan URL
git status                          # clean at session close
```

---

## When In Doubt

Same as before. Plus: **if context budget feels tight, initiate session close EARLY** (70% rule) rather than risk needing the session abort protocol (§13). An honest partial close is preferable to a rushed full close.

Good luck. Run the Framework Conformance Check, commit it, wait for "proceed," build the Plan Builder.
