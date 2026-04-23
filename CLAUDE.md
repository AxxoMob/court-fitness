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

The reading obligation depends on whether you are entering a **fresh conversation** (blank chat history — the default case) or **continuing the same conversation** after a session close.

### 3.1 Fresh agent (new conversation — default)

A fresh Claude conversation has no memory of prior work. Read these IN ORDER, every session, before any tool call that modifies state:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — the operating constitution (read-only; sealed)
2. `CLAUDE.md` — this file
3. `.ai/README.md` — folder map of the `.ai/` tree
4. `.ai/core/BRIEFING.md` — 1-page project overview
5. `.ai/core/WIP.md` — current state
6. `.ai/core/SESSION_LOG.md` — at minimum the last 5 sessions; preferably all
7. `.ai/core/HARD_LESSONS.md` — every entry
8. `.ai/core/SEALED_FILES.md` — full list
9. `.ai/core/KNOWN_ERRORS.md` — every open KE
10. `.ai/core/ltat-fitness-findings.md` — distilled lessons from the predecessor project
11. `.ai/sprints/sprint-NN/sprint-plan.md` — current sprint plan (check WIP.md for `NN`)
12. `.ai/.daily-docs/{DD Mon YYYY}/session_*_handover.md` — the prior session's handover
13. `.ai/.daily-docs/{DD Mon YYYY}/prompt_for_session_*.md` — YOUR kickoff brief

### 3.2 Returning agent (same conversation after a session close)

If the same continuous Claude conversation is starting a new session (a previous close already happened in-thread), the constitutional docs are already in working memory. A lighter **re-entry check** suffices — but you MUST still do the full filesystem pre-flight (HL-10) to catch any owner changes between sessions:

1. Run `git status` + `git log --oneline -5` + `ls -la .ai/` in bash — what changed since the last close?
2. Re-read `.ai/core/WIP.md` — the owner may have edited it between sessions.
3. Re-read the previous session's handover at `.ai/.daily-docs/{latest date}/session_*_handover.md` — owner may have appended a note.
4. SCAN `.ai/core/HARD_LESSONS.md` only for entries newer than your last read.

**Fresh-vs-returning test:** if this is a NEW Claude conversation (no prior chat history visible to you), you are a fresh agent → §3.1 applies. If the SAME conversation had a prior session close, you are a returning agent → §3.2 is acceptable.

**Temptation warning:** an agent in a long conversation may falsely claim "returning" status to skip the full read. Enforcement: the Conformance Check (§3.3) asks specific questions whose answers only come from actual reading. A skimmer who claims returning status will fail questions about HL content or sealed-file details.

### 3.3 Framework Conformance Check — commit it, don't just chat it

After reading, complete the Framework Conformance Check (Appendix D of AI_AGENT_FRAMEWORK.md) BEFORE any state-changing tool call. **Two steps, not one:**

1. Answer the 8 questions in chat.
2. Write the same answers (plus baseline-verification output) to `.ai/.daily-docs/{today}/session_N_conformance.md` using the template at `.ai/core/templates/SESSION_CONFORMANCE_TEMPLATE.md`. Commit this file before requesting owner "proceed."

Why commit it: chat history is transient. If the conversation ends, the Check answers are lost — no audit trail. The committed file creates a permanent record the owner can audit months or years later.

Only after the commit does the agent request owner "proceed."

### 3.4 Reference material (read when relevant)

- `C:\xampp\htdocs\ltat-fitness-module\docs\*.md` — predecessor project's docs
- `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql` — authoritative source schema (13,398 lines; don't read whole; grep)
- `C:\xampp\htdocs\ltat-fitness-module\docs\warp-project-instructions.md` — Rajat's Master Rules Set v1.0 (precursor to the AI_AGENT_FRAMEWORK; still informative)

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

See `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` Section 3. court-fitness extends that protocol in the ways documented in this file (§3.3 Conformance commit, §6 artifacts, §§11-13 policies).

**Project-specific overrides to the framework's default paths** (applied portfolio-wide from court-fitness onwards):

- Framework file location: `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` (not `_project-docs/`).
- All project documentation under `.ai/` (not `_project-docs/`, not at repo root).
- Sprint documents: `.ai/sprints/sprint-NN/sprint-plan.md`.
- Daily docs: `.ai/.daily-docs/{DD Mon YYYY}/`.
- Per-session artifacts, renamed:
  - `HANDOVER.md` → `session_N_handover.md`
  - `NEXT_SESSION_PROMPT.md` → `prompt_for_session_N.md`
- **Naming convention for `N`:** use the **project-wide** session counter (Session 1 of the project = `session_1_handover.md`; Session 7 = `session_7_handover.md`), NOT the session-of-the-day. This avoids collisions when multiple sessions land on the same calendar date. The framework's default is "N of the day"; court-fitness deviates because (a) a fresh agent can't reliably tell what "the day" means without reading SESSION_LOG, (b) same-day collision happened on 2026-04-23 between Sessions 2 and 3.

### 6.1 Session open artifact (before owner "proceed")

- `.ai/.daily-docs/{today}/session_N_conformance.md` — the Framework Conformance Check answers, committed. See §3.3. Without this committed, do NOT request "proceed."

### 6.2 Seven session-close artifacts (court-fitness extension of framework §3.3)

All MUST exist before the session is declared closed:

1. `.ai/core/WIP.md` updated to reflect the close state.
2. `.ai/core/SESSION_LOG.md` appended with a 1-3 line row (project session number, date, sprint, summary).
3. `.ai/.daily-docs/{today}/session_N_handover.md` created. **Must include a line near the top recording the framework version applied** — e.g. "Framework version: 1.0 (2026-04-17)." When the framework later publishes v1.1, this stamp tells any auditor which rules governed this session.
4. `.ai/.daily-docs/{next session's date OR today}/prompt_for_session_{N+1}.md` created.
5. `.ai/core/HARD_LESSONS.md` updated if anything non-obvious surfaced.
6. Meaningful git commit(s) — one per logical unit of work + one session-close commit.
7. **Memory-to-repo promotion.** Before the close commit, scan your session-private agent memory (the owner cannot see this; it dies when the conversation ends) for any load-bearing fact — owner preferences, architectural decisions, newly-discovered domain constraints, unresolved questions — NOT already in `.ai/` repo docs. Promote each into the appropriate `.ai/core/*.md` file (or a new reference doc under `.ai/core/`). Repo docs are amnesia-proof; agent memory is not.

### 6.3 Session abort protocol (summary; full rules in §13)

If a session cannot close cleanly — context exhaustion mid-step, tool-server crash, owner interrupt that leaves work incomplete — do NOT pretend it closed. Create `.ai/.daily-docs/{today}/session_N_abort.md` from `.ai/core/templates/SESSION_ABORT_TEMPLATE.md` and commit it. The next agent reads the abort file first and decides: resume, revert, or escalate to owner.

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

See `.ai/core/SEALED_FILES.md`. As of Sprint 0, only `AI_AGENT_FRAMEWORK.md` is sealed. When the codebase matures and specific files become load-bearing, seal them there.

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

## 11. Archival Policy (institutional memory hygiene)

The project horizon is 3–5 years. Left unchecked, `SESSION_LOG.md` and `HARD_LESSONS.md` grow to unreadable sizes, burdening every new agent with hundreds of lines to scan. Archival rules:

- **`SESSION_LOG.md`:** at **sprint close** (not session close), rows older than the previous 3 sprints move to `.ai/core/archive/session-log-{oldest_date}-to-{newest_date}.md`. The main SESSION_LOG keeps the last 3 sprints in full.
- **`HARD_LESSONS.md`:** lessons do NOT expire — they stay in the main file forever. BUT at every 10th HL added, create or update `.ai/core/HL_INDEX.md` with a one-liner per HL (number + title + category tag: schema / auth / UI / process / tooling / domain). Agents scan the INDEX first, then jump to relevant full entries.
- **`KNOWN_ERRORS.md`:** resolved KEs (status = `fixed` or `won't fix`) older than 6 months move to `.ai/core/archive/known-errors-{year}.md`. Open KEs stay in the main file regardless of age.

Archival is a **sprint-close checklist item**, not a session-close one. The closing agent of the last session of a sprint performs it. If the agent forgets, the owner flags it at sprint-audit time.

---

## 12. Seal Candidates Review Cadence

Framework §5 defines criteria for sealing a file. court-fitness adds a cadence:

At every **sprint close**, the closing agent reviews the code written during that sprint and proposes any file that meets sealing criteria (load-bearing + under-tested OR source of recurring regressions OR embedded domain rules). Candidates go into the sprint handover under a section titled "Seal Candidates for Owner Review." Owner confirms which, if any, to add to `.ai/core/SEALED_FILES.md`.

Opportunistic sealing between sprints (agent notices a file became load-bearing mid-sprint) is welcome via the same mechanism: draft the entry matching the SEALED_FILES schema, propose to owner in chat, wait for explicit approval, add to the registry.

Current seal count: 1 (`.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`). Ongoing candidates to watch: `app/Services/JwtValidator.php`, `app/Support/IdObfuscator.php` (once built), the three exercise-catalogue seed migrations, `app/Controllers/Sso.php`.

---

## 13. Session Abort Protocol

A session that cannot close cleanly (context exhaustion, tool crash, owner interrupt mid-step) MUST leave a machine-readable abort record, NOT a pretend-closed one. A half-done migration silently left uncatalogued is how projects die; an honest abort is how projects survive.

**Trigger criteria (any one of these):**
- Context budget hit its limit mid-tool-call, AND you cannot fit all 7 close artifacts.
- A tool call errored in a way that leaves repo state uncertain (migration ran partially? file half-written?).
- Owner signalled an interrupt ("we have to stop") before close artifacts could be assembled.

**Procedure (in order):**

1. Do NOT pretend the session closed normally. Do NOT commit close artifacts if you cannot assemble all 7.
2. Stop issuing state-changing tool calls immediately.
3. Create `.ai/.daily-docs/{today}/session_N_abort.md` from the template at `.ai/core/templates/SESSION_ABORT_TEMPLATE.md`. Fill in: what was in-flight, current repo state (run `git status`; run `php spark migrate:status` if mid-migration; note any new tables unseeded; note any file half-written), what needs un-doing OR completing before the next agent can safely resume.
4. Commit the abort file only. Do NOT commit the in-flight work unless it can be brought to a coherent state first.
5. Notify the owner in chat.

**The next agent's first action** (on reading the abort file) is to decide, in consultation with owner:
- **Resume** — finish what was started, if safe.
- **Revert** — roll back partial changes (e.g. `migrate:refresh` if migrations half-ran) and restart from the last clean commit.
- **Escalate** — if the repo state is ambiguous, do not touch anything; ask owner.

Aborts are not failures of the framework — they are the framework working. They become failures only when they are suppressed.

---

## 10. What this file is NOT

- Not a changelog. That's `.ai/core/SESSION_LOG.md`.
- Not a to-do list. That's `.ai/core/WIP.md`.
- Not a bug tracker. That's `.ai/core/KNOWN_ERRORS.md`.
- Not session narrative. That's the per-session handover in `.ai/.daily-docs/...`.
- Not architecture-decision-records. Architectural decisions go in this file (Section 5) when locked, or in the sprint plan when being decided.

Keep CLAUDE.md focused on **what is always true** for this project. Move time-bound content elsewhere.
