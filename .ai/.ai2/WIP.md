# Work In Progress

## Current Sprint: Sprint 01 — "Coach plans a week, player logs actuals, on a phone"

(Sprint 00 closed on 2026-04-22 with project bootstrap complete; Sprint 01 has NOT started — awaiting 3 answers from owner.)

## Current Status

**Sprint 00 closed, Session 1 of 2026-04-22 complete.** All framework scaffolding in place. No code has been written in `court-fitness` (that starts in Sprint 01 Session 1). Waiting on 3 answers from Rajat before Sprint 01 Session 1 can begin.

## This Session's Scope (Session 1, 2026-04-22)

**In scope (this was Sprint 00):**
- Read AI_AGENT_FRAMEWORK.md v1.0 and confirm understanding with owner
- Read `C:\xampp\htdocs\ltat-fitness-module` — docs, live SQL dump (via grep), assessment models/controllers, exercise taxonomy tables, helpful-project-documents (BPP PDF + 3 xlsx/xlsm)
- Clarify scope with owner (multiple rounds); settle on: coach-builds-weekly-plan + player-logs-actuals as Sprint 01 scope
- Produce all framework-mandated documents (CLAUDE.md at root, plus all `.ai/` tier 1-4 files)
- Organise `.ai/` into sub-folders by tier (per owner's explicit instruction, Session 1 mid-work)
- Draft Sprint 01 plan with enough detail that Session 2 can execute
- Init git with a clean first commit

**Out of scope (Sprint 00 did NOT):**
- Write any PHP, HTML, CSS, or JavaScript in court-fitness
- Install CodeIgniter 4
- Create any database or run any migration
- Push to a remote git repo (waiting on owner's answer re: repo hosting)
- Configure any `.env`
- Touch `ltat-fitness-module` in any way — read-only exploration only

## Latest: Session 1 (2026-04-22, Sprint 00)

**Goal:** Complete framework bootstrap and deliver a Sprint 01 plan detailed enough for Session 2 to execute without ambiguity.

**Outcome:** Achieved. All six framework-mandated session-close artifacts exist (see `.ai/.daily-docs/22 Apr 2026/session_1_handover.md` for details + evidence). No blockers to continuing other than the 3 open questions to the owner.

**Key technical decisions made** (full reasoning in session_1_handover.md §4 and HARD_LESSONS.md):
- Schema: flat `training_plans` + `plan_entries` (NOT ltat-fitness's 12-week macrocycle hierarchy). HL-4.
- Use foreign key constraints (contra Master Rules Set §2). HL-6.
- UI: Bootstrap 5 mobile-first + PWA from day one; no Falcon in Sprint 01; revisit Ionic in Sprint 02 if needed.
- Exercise targets/actuals stored as JSON blobs (reuse ltat-fitness Task 21 pattern).
- Authentication: HitCourt SSO only, no local login. `/sso?token=` endpoint validates HS256 JWT.
- Exercise taxonomy: seed from ltat-fitness's 3 + 12 + 204-row catalogue, schema-cleaned (see HL-5 quirks).
- One migrations folder, one lineage (never repeat HL-1's drift).

## Previous: N/A (Session 1 is the first session)

## Blockers — 3 open questions to owner before Sprint 01 Session 1 begins

1. **JWT role claim.** Does HitCourt's SSO JWT include a `role` claim identifying coach vs player, or do we infer role another way? See `.ai/sprints/sprint-01/sprint-plan.md` §"Open questions".
2. **Dev SSO strategy.** Stub SSO in court-fitness for local dev (recommended), or credentials to HitCourt dev/staging?
3. **Git remote.** GitHub repo (account + public/private) or local-only for now?

Defaults I will assume if no answer is received by Session 2 start:
1. Assume JWT carries a `role` claim. If it doesn't, Sprint 01 scope adjusts mid-session.
2. Build a stub SSO for local dev.
3. Local-only git; add remote when owner provides.

## Noticed this Session, for Future (NOT done here)

- Rajat may want to rename `trainers` terminology to `coaches` throughout court-fitness (ltat-fitness uses "trainer" interchangeably; screenshots show coach portal uses "Coach"). Confirm vocabulary in Session 2 pre-work.
- `fitness_subcategories.slug` column in ltat-fitness is often equal to `name` verbatim (spaces not slugified). Fix during seed.
- `exercise_type.status` semantics inverted (0=Active). Normalise to `is_active` during seed.
- The existing ltat-fitness `/coach-exercises-{base64id}.html` URL pattern obfuscates plan IDs with base64. Decide whether court-fitness follows the same pattern or uses CI4 default `/coach/training-plans/{id}`.
- Tennis-specific testing metrics are a researched-seed item for Sprint 03+ when the fitness-testing kernel lands.
- Multi-language (Thai/English) support — the ltat-fitness coach portal has a Tha/Eng toggle; court-fitness deferred. Revisit Sprint 02.
- PWA install prompt UX (iOS Add-to-Home behaves differently from Android Chrome) — plan for Sprint 02.
- `.ai/research-notes/xlsx-survey/` contains Python scripts used in Session 1 to inspect the BPP workbooks. Kept as evidence, not running code. Can be deleted once no longer useful.
- **Discovered at session close:** CodeIgniter 4 is ALREADY installed in `court-fitness/` (full framework clone, not appstarter; composer install NOT yet run; no `.env` yet; only default `Home::index` route). This reshapes Sprint 01 Session 1's opening task from "install CI4" to "verify + configure existing CI4." Documented as HL-10 and incorporated into Sprint 01 plan §1 + Session 2 prompt task 1. No blocker — just saves a step.

## Open Decisions (deferred to later sessions)

- Whether to use Ionic Framework (instead of Bootstrap 5) for the PWA. Sprint 01 uses Bootstrap; Sprint 02 re-evaluates.
- Whether to seal the SSO service once built. Candidate per `SEALED_FILES.md`.
- Whether to seal the exercise-taxonomy seed migrations once loaded. Candidate per `SEALED_FILES.md`.
- Whether the "Training Target" dropdown should be a fixed DB-seeded list or a per-coach customisable list. Sprint 01 goes with a fixed DB-seeded list; owner may override.

## Verification Commands

Sprint 00 has no runnable code, so no verification commands apply yet.

When Sprint 01 Session 1 installs CodeIgniter 4 and runs migrations, baseline verification will be:
```bash
cd C:/xampp/htdocs/court-fitness
php spark serve --port 8080
php spark migrate
php spark migrate:status
curl -I http://localhost:8080/
```
Expected: server starts, all migrations applied, HTTP 200 (or 302 to HitCourt login) from the root URL.
