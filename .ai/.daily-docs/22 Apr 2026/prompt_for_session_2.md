# court-fitness — Session 2 Kickoff Prompt

> Copy this entire file and paste it as the first message to the Session 2 agent. Customise only the **[bracketed]** lines if needed. Everything else is constant.

---

## Session Onboarding — court-fitness (Session 2, Sprint 01 Session 1)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both coach and player able to log actual session results. It lives at `C:\xampp\htdocs\court-fitness` on a Windows 11 XAMPP environment. Production URL is `https://fitness.hitcourt.com`.

This is a long-horizon project (3–5 years) operated under a strict AI Agent Framework. Before you do ANYTHING else, read these documents in order:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — the operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (at repo root) — project-specific conventions. Read Sections 3 and 4 carefully: 3 is the full mandatory reading list, 4 explains the Captain/Engineer collaboration model and plain-English requirement.
3. `.ai/README.md` — folder map of the `.ai/` tree.
4. `.ai/briefing/BRIEFING.md` — 1-page project overview.
5. `.ai/current-state/WIP.md` — current state. Read carefully: it lists blockers and follow-ups.
6. `.ai/institutional-memory/SESSION_LOG.md` — project diary.
7. `.ai/institutional-memory/HARD_LESSONS.md` — read ALL NINE entries (HL-1 through HL-9). These are traps inherited from `ltat-fitness-module` that you must avoid.
8. `.ai/domain-reference/SEALED_FILES.md` — full list.
9. `.ai/institutional-memory/KNOWN_ERRORS.md` — full list (currently empty with a template).
10. `.ai/domain-reference/ltat-fitness-findings.md` — distilled findings from the predecessor project.
11. `.ai/sprints/sprint-01/sprint-plan.md` — **THIS IS YOUR PLAYBOOK.** Read every word.
12. `.ai/.daily-docs/22 Apr 2026/session_1_handover.md` — what happened before you arrived.

After reading, DO NOT yet touch any code. Instead, complete the **Framework Conformance Check** (Appendix D of `AI_AGENT_FRAMEWORK.md`) — answer all 8 questions in chat to prove you read the docs. Wait for Rajat's "proceed."

Only after Rajat's "proceed" do you begin this session's work.

---

## Context

court-fitness is the successor to `ltat-fitness-module` (at `C:\xampp\htdocs\ltat-fitness-module`, live at `tourtest.ltat.org`). Sprint 00 (Session 1, 2026-04-22) produced all framework documentation and a detailed Sprint 01 plan. No code has been written yet. This session — Session 2, the first session of Sprint 01 — starts the actual build.

**Tech stack (summary):** PHP 8.1+ / CodeIgniter 4 latest stable / MySQL 8.0+ / Bootstrap 5 mobile-first / PWA (manifest + service worker from day 1) / HitCourt SSO via JWT HS256. Full detail in `CLAUDE.md` §7.

**Sprint 01's "done" definition:** a coach on a phone can create a weekly training plan for a player (picking exercises from a 3-level taxonomy), and a player on a phone can log actual results per exercise. Both sides see the latest data on refresh. Installable to the phone home screen as a PWA. Full detail in `.ai/sprints/sprint-01/sprint-plan.md`.

---

## What Was Done Last Session (Sprint 00 Session 1, 2026-04-22)

1. Read AI_AGENT_FRAMEWORK.md v1.0 (1368 lines) end-to-end and confirmed understanding with Rajat.
2. Read `ltat-fitness-module` deeply — 19 docs, live SQL dump (grep), assessment-feature code, exercise taxonomy tables, BPP workbooks, training-load workbook.
3. Had four rounds of scope clarification with Rajat. Final Sprint 01 scope landed on: full coach-plans-week + player-logs-actuals workflow in one sprint, on mobile-first PWA, using HitCourt SSO.
4. Made key technical decisions (schema, UI, auth, migration strategy). Each is in `HARD_LESSONS.md` by number.
5. Produced all framework-mandated documentation in `.ai/` sub-folders.
6. Initialised git and committed the Sprint 00 final state. No remote pushed yet.

---

## What Needs To Be Done Now (Session 2, Sprint 01 Session 1)

**Before any code:** resolve the three open questions (see next section). If Rajat has answered in the session-opening message, use his answers. If not, use the documented defaults (also listed there).

**Then, in order, per `.ai/sprints/sprint-01/sprint-plan.md`:**

1. **Project foundation.** NOTE — CI4 is ALREADY present (see HL-10 in HARD_LESSONS.md). It's a full framework clone, not `appstarter`. Composer dependencies are NOT yet installed (`vendor/` missing), `.env` does not exist yet (only the `env` template). So: run `composer install` to pull dev dependencies; copy `env` → `.env` and configure; add `HITCOURT_JWT_SECRET` and `HITCOURT_BASE_URL` env vars (via `Config/App.php` or direct `$_ENV` access); verify `php spark serve --port 8080` serves the default Welcome page. Confirm `app/Database/Migrations/` is clean (only `.gitkeep`).
2. **PWA scaffolding.** `public/manifest.json`, `public/sw.js` (minimal but functional), at least placeholder 192×192 and 512×512 app icons. Verify installability in Chrome DevTools → Application → Manifest.
3. **Authentication — SSO only.** Build `/sso` endpoint + `AuthFilter` per sprint plan §2. Write unit tests for signature validation, expiry, missing claims BEFORE building any feature controller (HL-8 preventive discipline). Build a stub SSO issuer locally if Rajat confirmed that path.
4. **Database schema migrations.** Create migrations for all 8 tables listed in sprint plan §3, in the dependency order given. Foreign keys enabled. Soft deletes via `deleted_at`. Run `php spark migrate` clean — any drift and you STOP (HL-1).
5. **Exercise taxonomy seed.** Seed 3 + 12 + 204 rows from `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql`. Schema-clean per sprint plan §3 (drop denormalised `exercise_type` on subcategories; normalise inverted `status` to `is_active`).
6. **Training targets seed.** Fixed list (Endurance, Strength, Power, Speed, Agility, Recovery, Mixed) — confirm list with Rajat at session open.

Session 2 probably gets through items 1–4 and starts item 5. Don't try to do everything in one session. Begin close procedures when you hit ~70% of your context window (framework Section 3.2 rule).

---

## Open Questions for Rajat (BLOCKERS — ask at session open)

These were raised in Session 1 and Rajat may already have answered in the same message that pastes this prompt. If not, ask plainly before touching code.

1. **Does the HitCourt SSO JWT include a `role` claim** (coach / player), or do we determine role some other way? Default if no answer: assume yes, handle the "missing role" case with a hardcoded error pending clarification.
2. **Dev SSO strategy** — stub SSO locally (recommended, self-contained) or credentials to HitCourt dev/staging (higher fidelity)? Default: stub locally.
3. **Git remote** — GitHub (which account, public/private) or local-only for now? Default: local-only; add remote when Rajat provides URL.

Ask these in plain English, present the defaults, and let Rajat decide.

---

## Key Conventions (quick reference — full detail in `CLAUDE.md`)

- **No local login screen.** Auth is SSO only. If you find yourself writing a login form, STOP.
- **Foreign keys ARE used.** Deviates from Master Rules Set §2; owner-approved engineering decision (HL-6).
- **ONE migrations folder.** Never two. If you feel tempted to apply raw SQL because migrations are broken, STOP and fix the folder (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder, not a coder. Define jargon on first use. No TLAs without expansion.
- **Captain (Rajat) / Engine Engineer (you) model.** Technical decisions within scope are yours by default — make them, explain them in plain English, move on. Product / scope / priority decisions are his.
- **Mobile-first UI, non-negotiable.** Every screen must be usable and pleasant on a 5-inch phone before desktop is considered.
- **Commit per logical unit.** Not one giant end-of-sprint commit.
- **No `--amend`, no `--force`, no `--no-verify`.** Framework Rule 5.
- **Sealed: `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`.** Read freely, never modify.
- **Six session-close artifacts** are mandatory (framework §3.3). Begin close procedures at 70% context.

---

## Verification Commands

Sprint 01 Session 1 should finish with these all passing:

```bash
cd C:/xampp/htdocs/court-fitness
composer install                              # CI4 + dev deps
php spark serve --port 8080                   # server starts; landing page redirects to HitCourt
php spark migrate                             # all migrations apply cleanly
php spark migrate:status                      # all green
php spark db:seed CourtFitnessCatalogSeeder   # exercise taxonomy + training targets seeded
curl -sI http://localhost:8080/               # 302 to HitCourt login (since no session)
vendor/bin/phpunit tests/unit/SsoTest.php     # JWT validation tests pass
```

If you can't pass these by session close, that's OK — write up what works, what doesn't, and why in the Session 2 handover. Do NOT mark the session "complete" without honest evidence.

---

## Known Risks (in descending order of likelihood)

1. **CI4 + PWA setup friction.** Service worker registration, manifest validation, icon sizing. Use Chrome DevTools → Application to verify each piece. Budget ~20% of Session 2 for PWA setup alone.
2. **JWT library choice.** CI4 doesn't ship one. `firebase/php-jwt` via Composer is the de-facto pick; vouch for it, install, use. If it doesn't work on PHP 8.1, fall back to another — document which and why.
3. **Exercise taxonomy seed size.** 204 rows via `insertBatch` is fine, but watch for character-encoding issues on the tennis-specific names with punctuation.
4. **"Am I a coach or player?" missing from JWT.** See Open Question 1.

---

## Things to remember from Session 1 that did NOT make it into the docs

None. Everything I learned is in the docs. If you find a gap, that is a bug in the Session 1 handover — flag it in the Session 2 handover under "Follow-Ups Noticed" and update the relevant doc.

---

## When in doubt

- Unclear requirement → ask Rajat (framework Rule 9).
- Unclear previous decision → read `.ai/institutional-memory/HARD_LESSONS.md` and the Session 1 handover.
- Tempted by a destructive git op → do not.
- Tempted by out-of-scope "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it.
- Tempted to apply raw SQL because migrations are complaining → STOP, you are repeating HL-1.

Good luck. Run the Framework Conformance Check, wait for "proceed," and begin.
