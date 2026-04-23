# court-fitness — AI Agent Instructions

> **Read this FIRST, before any code or doc edits.**
> Project-specific conventions for the court-fitness module under HitCourt.
> This file is Tier 1 (constitutional). Only the owner may modify.
> Version: 1.0 (2026-04-22)

---

## 1. Project Overview

**court-fitness** is a mobile-first Progressive Web App (PWA) for tennis coaches to create weekly training plans for their players, and for both sides to log actual session results. It is one module of **HitCourt** (the mothership), to be served at `https://fitness.hitcourt.com`. A sibling web property at `https://www.hitcourt.com` (temporarily at `https://www.org.hitcourt.com` during Google AdSense processing) owns all authentication — court-fitness has no login screen of its own.

It is the **successor** to `ltat-fitness-module` (located at `C:\xampp\htdocs\ltat-fitness-module`), which is currently live at `tourtest.ltat.org` and in use by coaches. court-fitness **takes its cue** from ltat-fitness but is a clean rebuild — mobile-first, better UI, cleaner schema, no mock authentication. The database structure from ltat-fitness is reused as-is; the front-end is reimagined.

Project horizon: 3–5 years. Multiple AI agents will work on this project over many sessions. **Agent continuity is non-negotiable.** The documentation system below is the defence against "AI amnesia."

---

## 2. Three Core Features (Sprint 1 scope)

1. **Coach builds a weekly training plan** — pick a Monday ("Weekof") and a player; add training dates, sessions (morning/afternoon/evening), and exercises from a 3-level taxonomy (Format → Category → Subcategory); save.
2. **Player views and logs actuals** — see assigned plans, tap a session, fill in actual values per exercise.
3. **Coach sees player's actuals** — refresh the plan view; the latest data is there (no push, no WebSockets in Sprint 1).

**Future features** (documented in [.ai/sprints/sprint-01/sprint-plan.md](.ai/sprints/sprint-01/sprint-plan.md) under "Deferred"): notifications, Capacitor native wrapping, rich player analytics, fitness directory (exercise encyclopaedia), fitness testing (assessments/metric_types kernel), tennis-specific testing catalogue, training load / ACWR monitoring, admin role, multi-language.

---

## 3. On Startup — Mandatory Reading Order

**Every agent, every session, before any tool call that modifies state:**

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — the operating constitution (read-only; sealed)
2. `CLAUDE.md` — this file
3. `.ai/README.md` — folder map of the `.ai/` tree
4. `.ai/briefing/BRIEFING.md` — 1-page project overview
5. `.ai/current-state/WIP.md` — current state
6. `.ai/institutional-memory/SESSION_LOG.md` — at minimum the last 5 sessions; preferably all
7. `.ai/institutional-memory/HARD_LESSONS.md` — every entry
8. `.ai/domain-reference/SEALED_FILES.md` — full list
9. `.ai/institutional-memory/KNOWN_ERRORS.md` — every open KE
10. `.ai/domain-reference/ltat-fitness-findings.md` — distilled lessons from the predecessor project
11. `.ai/sprints/sprint-NN/sprint-plan.md` — current sprint plan (check WIP.md for `NN`)
12. `.ai/.daily-docs/{DD Mon YYYY}/session_*_handover.md` — the prior session's handover
13. `.ai/.daily-docs/{DD Mon YYYY}/prompt_for_session_*.md` — YOUR kickoff brief

**Reference material (read when relevant):**
- `C:\xampp\htdocs\ltat-fitness-module\docs\*.md` — predecessor project's docs
- `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql` — authoritative source schema (13,398 lines; don't read whole; grep)
- `C:\xampp\htdocs\ltat-fitness-module\docs\warp-project-instructions.md` — Rajat's Master Rules Set v1.0 (precursor to the AI_AGENT_FRAMEWORK; still informative)

**After reading, complete the Framework Conformance Check** (Appendix D of AI_AGENT_FRAMEWORK.md) in chat BEFORE any state-changing tool call. Wait for owner "proceed."

---

## 4. Working with Rajat — Collaboration Model

Rajat (the owner) has defined our relationship as **Captain and Engine Engineer**. He steers the ship (product direction, scope, priorities, domain decisions, timeline). I am the engine engineer (technical decisions, schema, framework choices, code patterns, file structure, migration strategy, performance/security trade-offs within the scope he sets).

**Two consequences for every agent:**

1. **Technical decisions are yours by default.** If a decision is pure engineering (e.g. "should we use ENUMs or lookup tables?"), make it, state it, state why in plain English, move on. Rajat can override — but you don't wait.
2. **Plain English, always.** Rajat is a Vibe-Coder, not a coder. Jargon is permitted only when defined on first use. No unexplained acronyms. When asking a question, frame it in terms of the *decision* (risk, cost, user impact) not the *mechanism*. Do not patronise — he is smart; he just doesn't read as a compiler.

**When a technical decision has product implications** (e.g. "this column limits what the player dashboard can show later"), surface the trade-off in plain English and let Rajat decide.

**When in doubt whether something is your lane**, Framework Rule 9 applies: ask.

---

## 5. Architecture Rules

### 5.1 Framework & structure

- **CodeIgniter 4** (latest stable, PHP 8.1+) — same as ltat-fitness, matches Rajat's portfolio.
- **HMVC modular structure**: modules live in `app/Modules/{ModuleName}/`. Planned modules for court-fitness: `Coach`, `Player`, `Shared` (for SSO, exercise catalogue, common views). `Admin` is future scope.
- **PSR-4 autoloading**; camelCase for methods/variables; PascalCase for classes.

### 5.2 Authentication — SSO only

- **NO local login screen, NO registration, NO password reset, NO captcha** in court-fitness. All of that is HitCourt's responsibility.
- Single SSO endpoint: `/sso?token={jwt}`. Validates HS256 JWT signed with shared secret (`HITCOURT_JWT_SECRET` env var), max 60-second expiry, extracts identity, upserts local `users` row by `hitcourt_user_id`, mints session cookie, redirects to role-appropriate landing.
- Every other route requires a valid local session. Unauthenticated hits redirect to `${HITCOURT_BASE_URL}/login?return=<requested_path>`.
- JWT role claim status: **to be confirmed with Rajat** (session 2 blocker) — see [.ai/sprints/sprint-01/sprint-plan.md](.ai/sprints/sprint-01/sprint-plan.md) "Open questions."

### 5.3 Database conventions

- MySQL 8.0+. Database name: `court_fitness`.
- Every table has: `id` INT UNSIGNED PK auto-increment, `created_at` DATETIME NULL, `updated_at` DATETIME NULL, `deleted_at` DATETIME NULL (soft delete via timestamp, NOT the `is_deleted INT` pattern from Master Rules Set §2; this aligns with CI4 conventions and with ltat-fitness's actual practice).
- **Foreign keys ARE used.** This deviates from Master Rules Set §2 ("no MySQL foreign keys"). Justification: engineering decision on 2026-04-22, recorded in session 1 handover — FKs catch data-integrity bugs that code-level enforcement can miss. Rajat can override.
- **One migrations folder, one clean lineage.** ltat-fitness's dual-migrations-folder drift (see HL-1 in HARD_LESSONS.md) must not repeat.
- Migrations only; NO direct SQL in production (Master Rules Set §9 stands).
- Identifiers: snake_case table and column names.
- Soft deletes universally applied; queries filter `WHERE deleted_at IS NULL` unless explicitly fetching deleted records.

### 5.4 UI conventions

- **Mobile-first, non-negotiable.** Every screen must be usable and pleasant on a 5-inch phone before desktop is considered. Players travel internationally and use phones; this is Rajat's explicit requirement.
- **Bootstrap 5** with a single custom stylesheet (`public/assets/css/overrides.css`) for brand tokens and mobile-specific tweaks. Do NOT modify Bootstrap core CSS (Master Rules Set §8).
- **PWA** — manifest + service worker from day one. Installable to home screen. Capacitor native wrap is Sprint 2+.
- **No Falcon theme in Sprint 1.** ltat-fitness used Falcon; we re-evaluate in Sprint 2 if Bootstrap 5 feels inadequate.
- Fonts: Google Fonts Poppins (same as ltat-fitness) unless a concrete reason to change.

### 5.5 Exercise taxonomy (seeded from ltat-fitness, see HL-5 for quirks)

Three levels, loaded from database at startup:
- `exercise_types` — 3 rows (Cardio, Weights, Agility).
- `fitness_categories` — 12 rows, each linked to one exercise_type.
- `fitness_subcategories` — 204 rows, each linked to one fitness_category; leaf-level exercises.

Seeded from `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql`. Schema cleaned up during seed (drop unreliable denormalised columns — see HL-5).

### 5.6 Exercise targets and actuals: JSON blobs

- `plan_entries.target_json` (what the coach prescribes) and `plan_entries.actual_json` (what the player or coach records) are JSON columns.
- Justification: exercise input fields vary by type (Cardio = Max HR + Duration; Weights = Sets + Reps + Weight + Rest; Agility = mixed). Rigid columns would be half-null across most rows. JSON blobs keep the table narrow. This reuses ltat-fitness's Task 21 pattern.
- Validation happens at the model layer per exercise type, not at the database level.

---

## 6. Session Protocol

See `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` Section 3.

**Project-specific overrides to the framework's default paths** (applied portfolio-wide from court-fitness onwards):

- Framework file location: `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` (not `_project-docs/`).
- All project documentation under `.ai/` (not `_project-docs/`, not at repo root).
- Sprint documents: `.ai/sprints/sprint-NN/sprint-plan.md`.
- Daily docs: `.ai/.daily-docs/{DD Mon YYYY}/`.
- Per-session artifacts, renamed:
  - `HANDOVER.md` → `session_N_handover.md` (N = session number of the day)
  - `NEXT_SESSION_PROMPT.md` → `prompt_for_session_N.md`
- If more than one session runs in a single day, the filename's `N` disambiguates.

**Six session-close artifacts** (Framework Section 3.3) still mandatory:
1. `.ai/current-state/WIP.md` updated
2. `.ai/institutional-memory/SESSION_LOG.md` appended
3. `.ai/.daily-docs/{today}/session_N_handover.md` created
4. `.ai/.daily-docs/{next session's date}/prompt_for_session_{N+1}.md` created
5. `.ai/institutional-memory/HARD_LESSONS.md` updated (if anything non-obvious surfaced)
6. Meaningful git commit(s)

---

## 7. Tech Stack Reference

| Layer | Technology | Version | Notes |
|---|---|---|---|
| Language | PHP | 8.2+ | The pre-installed CI4 `composer.json` requires `^8.2`. ltat-fitness was 8.1; court-fitness has moved up. Consider 8.3+ for longevity. |
| Backend framework | CodeIgniter | 4.x latest stable | HMVC modules |
| Database | MySQL | 8.0+ | utf8mb4_general_ci (matches ltat-fitness) |
| Frontend CSS | Bootstrap | 5.x | Mobile-first |
| Frontend JS | Vanilla + minimal jQuery | — | Keep PWA bundle small |
| Auth | HitCourt SSO (JWT HS256) | — | No local login |
| PWA | Workbox or hand-rolled service worker | — | Manifest + SW from day 1 |
| Native wrapping | Capacitor | 6.x | Sprint 2+ |
| Charts | Chart.js | latest | Same as ltat-fitness |
| Dev env | XAMPP on Windows 11 | — | Rajat's primary machine |
| Repo path | `C:\xampp\htdocs\court-fitness` | — | absolute |

---

## 8. Sealed Files

See `.ai/domain-reference/SEALED_FILES.md`. As of Sprint 0, only `AI_AGENT_FRAMEWORK.md` is sealed. When the codebase matures and specific files become load-bearing, seal them there.

---

## 9. Verification Commands (placeholder until Sprint 1 produces code)

```bash
# When CI4 is installed in Sprint 1:
cd C:/xampp/htdocs/court-fitness
php spark serve --port 8080
php spark migrate
php spark db:seed CourtFitnessSeeder
```

Sprint 0 has no code, so no verification commands apply yet.

---

## 10. What this file is NOT

- Not a changelog. That's `.ai/institutional-memory/SESSION_LOG.md`.
- Not a to-do list. That's `.ai/current-state/WIP.md`.
- Not a bug tracker. That's `.ai/institutional-memory/KNOWN_ERRORS.md`.
- Not session narrative. That's the per-session handover in `.ai/.daily-docs/...`.
- Not architecture-decision-records. Architectural decisions go in this file (Section 5) when locked, or in the sprint plan when being decided.

Keep CLAUDE.md focused on **what is always true** for this project. Move time-bound content elsewhere.
