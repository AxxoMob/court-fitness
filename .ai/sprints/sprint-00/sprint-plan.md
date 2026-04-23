# Sprint 00 — Bootstrap & Plan

> ⚠️ **Historical document — paths updated post-Sprint.** This plan was written during Sprint 00 Session 1 (2026-04-22). Rajat reorganised the `.ai/` folder on 2026-04-23 between sessions, moving every Tier 1-3 file out of `briefing/`, `current-state/`, `domain-reference/`, and `institutional-memory/` sub-folders into a flat `.ai/.ai2/` folder. Path references below (like `.ai/briefing/BRIEFING.md`) now resolve to `.ai/.ai2/BRIEFING.md` — same file, new location. See the current `.ai/README.md` for the live folder map. The body of this retrospective is left as-written for historical accuracy.

---

**Status:** CLOSED as of 2026-04-22 (end of Session 1).
**Duration:** Single session.
**Goal:** Set up the project's documentation skeleton, understand the predecessor project, align with the owner on Sprint 01 scope, and produce a Sprint 01 plan detailed enough for a new agent to execute without ambiguity.

## What Sprint 00 Delivered

1. **Read the constitution:** AI_AGENT_FRAMEWORK.md v1.0 (1368 lines) in full, and confirmed understanding with the owner.
2. **Read the predecessor project:** `ltat-fitness-module` — all 19 `docs/*.md` files, live SQL dump (via grep), assessment-feature models/controllers/views, exercise-taxonomy tables, integration patches, the 4 helpful-project-documents (1 PDF + 2 BPP xlsx + 1 training-load xlsm).
3. **Multi-round scope alignment with owner:** Four distinct rounds of clarification that each shifted my understanding materially:
   - Round 1: "fitness testing" was taken too narrowly (assessments kernel only); owner clarified it meant the full coach-plans + player-logs-actuals workflow.
   - Round 2: owner shared 5 screenshots of the live LTAT coach portal at `tourtest.ltat.org`, clarifying the 3-level taxonomy (Format → Category → Subcategory), Weekof/Monday anchoring, morning/afternoon/evening sessions, dual-stakeholder logging.
   - Round 3: owner decided Sprint 01 = both halves (A coach-plans + B player-logs) in one sprint, not split.
   - Round 4: owner specified SSO via HitCourt JWT (not local login), fixed-dropdown taxonomy (not free text), mobile-first PWA + Capacitor stack.
4. **Documentation scaffolding, per the framework:**
   - `CLAUDE.md` at repo root (project-specific constitution)
   - `.ai/README.md` (folder map)
   - `.ai/briefing/BRIEFING.md` (1-page overview)
   - `.ai/domain-reference/SEALED_FILES.md` (1 entry: the framework itself)
   - `.ai/domain-reference/ltat-fitness-findings.md` (everything learned from the predecessor)
   - `.ai/institutional-memory/SESSION_LOG.md` (Session 1 row)
   - `.ai/institutional-memory/HARD_LESSONS.md` (9 entries, HL-1 through HL-9, all inherited from ltat-fitness analysis)
   - `.ai/institutional-memory/KNOWN_ERRORS.md` (none yet; template + inherited-in-spirit patterns)
   - `.ai/current-state/WIP.md`
   - `.ai/sprints/sprint-00/sprint-plan.md` (this file)
   - `.ai/sprints/sprint-01/sprint-plan.md` (the upcoming build)
   - `.ai/.daily-docs/22 Apr 2026/session_1_handover.md`
   - `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md`
   - `.ai/research-notes/xlsx-survey/` (Python scripts used to inspect the BPP workbooks + their README)
5. **Folder reorganisation** (owner direction mid-session): `.ai/` is now organised into tier-named sub-folders (briefing, domain-reference, institutional-memory, current-state, sprints, .daily-docs, research-notes) rather than flat files.
6. **Git repository initialised** with a clean first commit capturing the Sprint 00 final state. No remote push yet (awaiting owner direction).

## What Sprint 00 Explicitly Did NOT Deliver

- No PHP, HTML, CSS, or JavaScript code in `court-fitness`.
- No CodeIgniter 4 installation.
- No database or migration.
- No `.env` or secrets configuration.
- No push to a remote git repo.
- No changes to `ltat-fitness-module` (read-only exploration only).

## Key Technical Decisions Made in Sprint 00

Each decision is recorded with a Hard Lesson reference and full rationale where applicable. See `.ai/institutional-memory/HARD_LESSONS.md`.

| # | Decision | Rationale | Cross-ref |
|---|---|---|---|
| 1 | Use a flat `training_plans` + `plan_entries` schema, NOT ltat-fitness's `fitness_programs` + `program_phases` + `program_exercises` + `program_assignments` hierarchy. | Coaches work at week scale, not 12-week macrocycle scale. Porting the hierarchy would give us 5 empty tables and the wrong mental model. | HL-4 |
| 2 | Use MySQL foreign key constraints. | Database-level enforcement catches impossible records (plans pointing at non-existent players, etc.) that code-level checks can miss. Deviates from Master Rules Set §2. Owner can override. | HL-6 |
| 3 | Store exercise targets/actuals as JSON blobs (`target_json`, `actual_json`). | Exercise input fields vary by type (Cardio = Max HR + Duration; Weights = Sets/Reps/Weight/Rest; Agility = mixed). Rigid columns would be half-null. Reuses ltat-fitness Task 21 pattern. | — |
| 4 | UI: Bootstrap 5 mobile-first + PWA, no Falcon in Sprint 01. | Falcon is desktop-oriented; existing ltat-fitness Falcon UI is what players complain about on mobile. Bootstrap 5 delivers mobile-first faster than Ionic's learning curve. Revisit Ionic in Sprint 02 if needed. | — |
| 5 | Authentication: HitCourt SSO only, no local login/registration/captcha in court-fitness. | HitCourt is the mothership identity provider. JWT HS256 handoff with 60-second expiry and shared `.env` secret. court-fitness uses its own session cookie post-handoff. | HL-8 |
| 6 | Seed the 3 + 12 + 204-row exercise taxonomy verbatim from ltat-fitness's live SQL dump, with schema quirks normalised. | Existing catalogue is tennis-aware. Copy, don't recreate. Drop denormalised `exercise_type` column on subcategories; normalise inverted `status` semantics to `is_active`. | HL-5, HL-9 |
| 7 | One migrations folder, one lineage. If the migrations folder ever feels broken, stop and fix it — never apply raw SQL as a workaround. | ltat-fitness's dual-folder drift made Task 21 apply ALTER directly; reproducibility was destroyed. | HL-1 |

## Sprint 00 Exit Criteria (all met)

- [x] AI_AGENT_FRAMEWORK.md read end-to-end by agent; understanding confirmed by owner.
- [x] Predecessor project (`ltat-fitness-module`) analysed to sufficient depth (full-text docs + grep on SQL dump + targeted code reads).
- [x] Owner has stated Sprint 01 scope unambiguously.
- [x] All framework Tier 1-4 documents exist in `.ai/` subfolders with no stale path references.
- [x] Sprint 01 plan written with daily-level specificity.
- [x] Session 1 handover + Session 2 prompt both produced.
- [x] Git repo initialised with a clean first commit.

## Time Spent

Single agent session on 2026-04-22 (owner context ~Apr 22 afternoon IST). Mix of reading (≈60%), clarification with owner (≈20%), and writing documentation (≈20%).

## What Sprint 01 Will Do

See `.ai/sprints/sprint-01/sprint-plan.md`.
