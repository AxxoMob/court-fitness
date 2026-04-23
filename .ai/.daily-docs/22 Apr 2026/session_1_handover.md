# Handover — court-fitness Session 1 (Sprint 00)

> ⚠️ **Historical document — paths updated post-session.** This handover was written at Sprint 00 Session 1 close (2026-04-22). Rajat reorganised the `.ai/` folder on 2026-04-23 between sessions, moving every Tier 1-3 file out of `briefing/`, `current-state/`, `domain-reference/`, and `institutional-memory/` sub-folders into a flat `.ai/.ai2/` folder. Path references in this document (like `.ai/briefing/BRIEFING.md`, `.ai/current-state/WIP.md`, etc.) now resolve to `.ai/.ai2/{same-filename}` — same files, new location. The current `.ai/README.md` has the live folder map. Body left as-written for historical accuracy.

---

**Date:** 2026-04-22
**Sprint:** Sprint 00 (Bootstrap & Plan) — closed by this session.
**Duration:** Single long session, 2026-04-22 afternoon IST.
**Agent:** Claude (Anthropic, Opus 4.7 1M context).

---

## 1. Session Goal (as stated at start)

Start court-fitness — a fitness module under the HitCourt family. Read the AI Agent Framework. Read the predecessor project `ltat-fitness-module` to understand what was already built and what to reuse. Align with the owner on scope. Produce all framework-mandated documentation so Session 2 (and every future session) can pick up cleanly without "AI amnesia."

## 2. In Scope / Out of Scope (as declared mid-session)

**In scope:**
- Read AI_AGENT_FRAMEWORK.md v1.0 (1368 lines) end-to-end.
- Read `ltat-fitness-module/docs/*.md` (19 files), live SQL dump (via grep), assessment-feature code, exercise-taxonomy tables, integration patches, helpful-project-documents (BPP PDF + xlsx + training-load xlsm).
- Multi-round scope clarification with the owner.
- Produce all framework-mandated Tier 1–4 documents in `.ai/` (organised into sub-folders after owner correction).
- Draft Sprint 01 plan with day-level specificity.
- Produce Session 1 handover + Session 2 prompt.
- `git init` and first commit.

**Out of scope (intentionally deferred):**
- Writing any code in `court-fitness` (PHP, HTML, CSS, JS).
- Installing CodeIgniter 4.
- Creating the database or running migrations.
- Pushing git to a remote.
- Any modification to `ltat-fitness-module` (read-only access only).

## 3. What Was Done

### 3.1 Reading

- Full read of `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — 1368 lines, all 14 sections + appendices A–E.
- Full read of all 19 files in `C:\xampp\htdocs\ltat-fitness-module\docs\`, including the two schema reports, the migration guide, all 5 TASK_xx reports, the two integration reports, the two view-fix reports, both READMEs, WARP.md, and (critically) `warp-project-instructions.md` which is Rajat's **Master Rules Set v1.0** — the precursor to the AI_AGENT_FRAMEWORK.
- Targeted grep of `ltat-fitness-module/Database/ltat_fitness.sql` (13,398 lines) for all CREATE TABLE statements and seed INSERTs for the fitness-testing and exercise-taxonomy tables.
- Full read of the assessment-feature code in ltat-fitness: `AssessmentsModel.php`, `MetricTypesModel.php`, `MetricTypesSeeder.php`, `Trainer/Assessments.php`, `Player/Assessments.php`, and the (placeholder) `player/assessments/charts.php` view.
- Read of the two integration patches (fitness_assets_integration.patch, fitness_menu_integration.patch).
- Read of the two verification scripts (verify_database_schema.php, verify_stage_02b.php).
- PDF extraction attempt on `BPPAccessv.Full.pdf` — turned out to be a 7-line "thank you for purchase" page, not methodology. Extracted via `pdftotext -layout`.
- xlsx extraction of the BPP 5-Day KG workbook (11 sheets: Start Here Guide, Assessment, Pre-Phase, Exercise Selection, Exercise Selection Map, Program Design, Program Design Data, Month 1/2/3, The Big Picture) and the training-load xlsm (4 sheets: Data Entry & Manipulation, Training Load Data, Control Panel, How to Guide). Used Python + openpyxl via the `anthropic-skills:xlsx` skill.

### 3.2 Owner clarification rounds

Four distinct rounds, each shifting my understanding materially. Recorded because the scope of Sprint 01 today is very different from my initial proposal.

1. **"Fitness testing is the shared starting point"** — I initially read this as the narrow `assessments` + `metric_types` kernel (benchmark tests like 10m sprint, chart progression). Proposed a narrow Sprint 01 around that.
2. **Owner corrected: "fitness testing" means the full coach-plans-week + player-logs-actuals workflow** — shared 5 screenshots from `tourtest.ltat.org` (the live LTAT coach portal, a test zone). I re-scoped Sprint 01 around that workflow.
3. **Sprint split question** — I asked whether Sprint 01 = both halves (coach + player) or just half A first. Owner: "Both halves. Players and Coaches are equal stakeholders and are eagerly awaiting launch."
4. **Auth, UI, data taxonomy** — Owner clarified: (a) auth is via HitCourt SSO JWT, not local login; (b) dropdowns come from DB tables `exercise_type` / `fitness_categories` / `fitness_subcategories`; (c) mobile-first PWA + Capacitor is the target stack; (d) existing UI is "shabbily designed" test zone — we rebuild, don't copy.

### 3.3 Documents produced

In `court-fitness/`:

| Path | Purpose | Size |
|---|---|---|
| `CLAUDE.md` | Project constitution — conventions, reading list, architecture rules, session protocol. | ~200 lines |
| `.ai/README.md` | Folder map of the `.ai/` tree + framework path overrides. | ~55 lines |
| `.ai/briefing/BRIEFING.md` | 1-page project overview. | ~25 lines |
| `.ai/domain-reference/SEALED_FILES.md` | 1 entry (AI_AGENT_FRAMEWORK.md) + candidate list for future sealing. | ~35 lines |
| `.ai/domain-reference/ltat-fitness-findings.md` | Distilled findings from the predecessor project. | ~150 lines |
| `.ai/institutional-memory/SESSION_LOG.md` | Append-only session diary. 1 row for today. | 5 lines (will grow) |
| `.ai/institutional-memory/HARD_LESSONS.md` | 9 entries (HL-1 through HL-9), all inherited from ltat-fitness analysis. | ~200 lines |
| `.ai/institutional-memory/KNOWN_ERRORS.md` | Empty catalogue + template + inherited-in-spirit anti-patterns. | ~50 lines |
| `.ai/current-state/WIP.md` | Current state: Sprint 00 closed, Sprint 01 ready pending 3 owner questions. | ~110 lines |
| `.ai/sprints/sprint-00/sprint-plan.md` | This sprint's plan (retrospective, since it's closing in this session). | ~90 lines |
| `.ai/sprints/sprint-01/sprint-plan.md` | Detailed day-level plan for Sprint 01. | ~200 lines |
| `.ai/.daily-docs/22 Apr 2026/session_1_handover.md` | **THIS FILE.** | ~250 lines |
| `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md` | Kickoff brief for the Session 2 agent. | ~180 lines |
| `.ai/research-notes/xlsx-survey/survey_xlsx.py` | Python script used to list xlsx sheet names. | 24 lines |
| `.ai/research-notes/xlsx-survey/dump_sheets.py` | Python script used to dump xlsx sheet contents. | 35 lines |
| `.ai/research-notes/xlsx-survey/README.md` | Explains what these scripts do and why they're kept. | ~10 lines |
| `.gitignore` | Standard CI4 ignores + `.env` + `.ai/.tmp/` (legacy). | ~20 lines |

## 4. Decisions Made

Each decision is also recorded in `.ai/institutional-memory/HARD_LESSONS.md` (by HL-N) and/or `CLAUDE.md` (Section 5 Architecture Rules). Summary here for the agent record:

1. **Schema: flat `training_plans` + `plan_entries` instead of ltat-fitness's 12-week macrocycle hierarchy.** Why: coaches work at week scale (confirmed via live screenshots), not macrocycle scale. Cross-ref HL-4, `sprint-01/sprint-plan.md` §3.
2. **Use MySQL foreign key constraints.** Why: database-level integrity catches impossible records that code-level checks can miss. Deviates from Master Rules Set §2. Owner can override. Cross-ref HL-6, `CLAUDE.md` §5.3.
3. **`target_json` + `actual_json` as JSON blobs on `plan_entries`.** Why: exercise input fields vary by type (Cardio, Weights, Agility). Rigid columns would be half-null. Reuses ltat-fitness Task 21 pattern. Cross-ref `CLAUDE.md` §5.6.
4. **UI: Bootstrap 5 mobile-first + PWA from day one, no Falcon in Sprint 01.** Why: Falcon is desktop-oriented; existing ltat-fitness Falcon UI is what players complain about on mobile. Revisit Ionic in Sprint 02 if needed. Cross-ref `CLAUDE.md` §5.4.
5. **Authentication: HitCourt SSO only, no local login / registration / captcha in court-fitness.** Why: HitCourt is the mothership identity provider. JWT HS256 handoff with 60-second expiry. Cross-ref HL-8, `CLAUDE.md` §5.2.
6. **Seed the 3 + 12 + 204-row exercise taxonomy from ltat-fitness's live SQL dump**, with schema quirks normalised. Why: existing catalogue is tennis-aware. Copy, don't recreate. Cross-ref HL-5, HL-9.
7. **One migrations folder, one lineage.** Why: HL-1 shows what happens otherwise.
8. **Vocabulary: "coach" not "trainer"** where new. Why: the live LTAT portal uses "Coach" in all user-facing labels; ltat-fitness's `trainer` in the DB is a legacy name. In court-fitness the local term is "coach" in views/URLs/doc, even if an underlying reused table is named `trainers` for consistency with the ltat-fitness DB structure. To be finalised Session 2.

## 5. Sealed File Modifications

**None.** No sealed files were modified this session. The only sealed file in court-fitness is `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`, which I read but did not modify.

## 6. Test Evidence

**Not applicable — no code was written in Sprint 00.** Test scaffolding will be set up in Sprint 01 Session 1.

## 7. Build Evidence

**Not applicable — no build exists in Sprint 00.** CodeIgniter 4 is not yet installed.

## 8. Files Modified / Created

### Created

```
court-fitness/
├── .gitignore                                                   (new, ~20 lines)
├── CLAUDE.md                                                    (new, ~200 lines)
└── .ai/
    ├── README.md                                                (new, ~55 lines)
    ├── briefing/BRIEFING.md                                     (new, ~25 lines)
    ├── current-state/WIP.md                                     (new, ~110 lines)
    ├── domain-reference/
    │   ├── SEALED_FILES.md                                      (new, ~35 lines)
    │   └── ltat-fitness-findings.md                             (new, ~150 lines)
    ├── institutional-memory/
    │   ├── HARD_LESSONS.md                                      (new, ~200 lines, 9 entries)
    │   ├── KNOWN_ERRORS.md                                      (new, ~50 lines)
    │   └── SESSION_LOG.md                                       (new, 5 lines)
    ├── sprints/
    │   ├── sprint-00/sprint-plan.md                             (new, ~90 lines)
    │   └── sprint-01/sprint-plan.md                             (new, ~200 lines)
    ├── .daily-docs/22 Apr 2026/
    │   ├── session_1_handover.md                                (new, ~250 lines — this file)
    │   └── prompt_for_session_2.md                              (new, ~180 lines)
    └── research-notes/xlsx-survey/
        ├── README.md                                            (new, ~10 lines)
        ├── survey_xlsx.py                                       (moved from .ai/.tmp/, 24 lines)
        └── dump_sheets.py                                       (moved from .ai/.tmp/, 35 lines)
```

### Already present (not touched this session)

- `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — placed by owner before session started.

### Moved / removed

- `.ai/.tmp/` — temporary working folder; contents moved to `.ai/research-notes/xlsx-survey/`; empty folder removed.
- Mid-session reorganisation: 5 files (`BRIEFING.md`, `SEALED_FILES.md`, `HARD_LESSONS.md`, `KNOWN_ERRORS.md`, `ltat-fitness-findings.md`) were initially written flat under `.ai/`, then moved into sub-folders per owner's direction. Internal cross-references updated.

## 9. Open Issues / Unfinished Work

**Three unanswered questions to the owner, blocking Sprint 01 Session 1 start.** See `.ai/sprints/sprint-01/sprint-plan.md` §"Open questions" for full text. In brief:

1. Does HitCourt's JWT include a `role` claim?
2. Dev SSO: stub in court-fitness, or credentials to a HitCourt dev instance?
3. Git remote: push to GitHub (which account?) or local-only for now?

Each has a documented default I will assume if no answer is received by Session 2 start.

No other unfinished work. No "this task is half-done" situations. Sprint 00 closes cleanly.

## 10. Follow-Ups Noticed (NOT done this session)

- Vocabulary decision: "trainer" (ltat-fitness legacy) vs "coach" (what the live UI says) across schema / URLs / views. Session 2 pre-work.
- `fitness_subcategories.slug` in ltat-fitness often equals `name` verbatim (spaces not slugified) — normalise during seed.
- Base64-obfuscated URL pattern in ltat-fitness (`coach-exercises-{base64id}.html`) — decide whether court-fitness follows or uses plain CI4 routing.
- Multi-language Thai/English — deferred to Sprint 02.
- Training Target dropdown list — tentative list in Sprint 01 schema (Endurance, Strength, Power, Speed, Agility, Recovery, Mixed). Confirm with owner when Sprint 01 Session 1 starts.

## 11. Known Errors Added or Updated

**None.** No KEs exist yet in court-fitness (no code to host them). `KNOWN_ERRORS.md` is empty with a template for future entries.

## 12. Hard Lessons Added

Nine entries (HL-1 through HL-9), all inherited from analysis of `ltat-fitness-module`. Each is an avoidance-rule for court-fitness from day one. Full text in `.ai/institutional-memory/HARD_LESSONS.md`. One-line each:

- **HL-1:** ltat-fitness had dual migrations folders and Task 21 bypassed them with raw SQL → always one clean migrations folder.
- **HL-2:** ltat-fitness docs contradict its live schema → always trust the SQL dump.
- **HL-3:** `assessments.score` (not `value`); `trainer_id` nullable in DB despite model requiring.
- **HL-4:** The 12-week macrocycle tables in ltat-fitness are unused in practice; coaches work at week scale.
- **HL-5:** `exercise_type.status` inverted (0=Active); `fitness_subcategories.exercise_type` column unreliable.
- **HL-6:** Master Rules Set says no FKs; ltat-fitness uses them; court-fitness sides with FKs.
- **HL-7:** "Working" ≠ "built"; ltat-fitness player chart view is a placeholder despite full controller logic.
- **HL-8:** ltat-fitness runs on mock auth via query param; court-fitness must build real SSO first.
- **HL-9:** BPP/training-load source docs are generic S&C; tennis intelligence is in ltat-fitness's `fitness_subcategories` seed data.

## 13. Next Session

See `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md`.

## 14. Framework Conformance Declaration

I, Claude (Anthropic, Opus 4.7 1M context), followed `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` v1.0 for this session.

I did not:
- Modify the sealed file (`.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`).
- Use destructive git operations (`--amend`, `--force`, `reset --hard`, `clean -f`). Git was initialised and a first commit created.
- Delete or disable tests (no tests exist yet).
- Expand scope beyond what the owner approved (the owner's mid-session folder-reorganisation instruction was followed literally).

The six session-close artifacts all exist:
1. `.ai/current-state/WIP.md` — updated with Sprint 00 close state.
2. `.ai/institutional-memory/SESSION_LOG.md` — new row for Session 1.
3. `.ai/.daily-docs/22 Apr 2026/session_1_handover.md` — this file.
4. `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md` — kickoff brief for next session.
5. `.ai/institutional-memory/HARD_LESSONS.md` — 9 entries added (HL-1 through HL-9).
6. Git commit — created by this session's close process.

The three evidence items are partially applicable: test output and build output are N/A (no code this sprint); file-modification summary is §8 above.

Any exceptions or deviations from the default framework:
- **Path overrides:** the framework's default flat `.ai/*.md` path layout is replaced by sub-folder organisation per owner's direction. Documented in `.ai/README.md` and `CLAUDE.md` §6.
- **Daily-docs file naming:** `session_N_handover.md` + `prompt_for_session_N.md` instead of the framework's `HANDOVER.md` + `NEXT_SESSION_PROMPT.md`. Documented in `CLAUDE.md` §6.
- **Foreign key usage:** deviates from Master Rules Set v1.0 §2 "no MySQL foreign keys." Engineering call with rationale in HL-6. Owner can override.

Session closed cleanly. Ready for Session 2.

---

## Post-script finding at session close (2026-04-22)

While preparing `git init`, I ran a full `find` over the repo and discovered **CodeIgniter 4 is already installed** in `court-fitness/` — full framework clone (not `appstarter`), composer dependencies NOT yet installed (`vendor/` missing), `.env` does NOT yet exist (only the `env` template). My Sprint 01 plan had "Install CodeIgniter 4 via Composer" as the opening task, which is now wrong.

**Actions taken at session close:**
1. Added **HL-10** ("Don't assume the project folder is empty; `ls -la` the repo root as Pre-Flight step 1") to `.ai/institutional-memory/HARD_LESSONS.md`.
2. Corrected `.ai/sprints/sprint-01/sprint-plan.md` §1 to reflect the actual starting state (verify + `composer install` + configure).
3. Corrected `.ai/.daily-docs/22 Apr 2026/prompt_for_session_2.md` task 1 to match.
4. Noted in `.ai/current-state/WIP.md` under follow-ups.
5. Fixed `CLAUDE.md` Tech Stack row — PHP version requirement is `^8.2` per the pre-installed `composer.json`, not 8.1 as I had initially written.

**Consequence:** Session 2 gets a free step-skip — roughly 30 minutes of installer work is not needed. Sprint 01 scope is otherwise unchanged.

Handover, finalised.
