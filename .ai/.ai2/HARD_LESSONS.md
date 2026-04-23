# Hard Lessons

Surprising discoveries, non-obvious gotchas, and patterns future agents must know. Append-only. Entry format per `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` Section 6.2.

The lessons below are inherited from reading the predecessor project `ltat-fitness-module`. They predate court-fitness's own code — they are avoidance-rules for court-fitness from day one.

---

## HL-1 — ltat-fitness had two competing migrations folders and Task 21 had to bypass them

**Discovered:** Sprint 0 Session 1, 2026-04-22 (while reading ltat-fitness)
**Summary:** ltat-fitness contains TWO migrations directories: `app/Modules/Fitness/Database/Migrations/` (3 files from 2025-09-12) and `app/Database/Migrations/` (13+ files from 2025-09-14 and 2025-09-15). Core tables like `CreateTrainersTable` appear in both folders, with different content, from consecutive days. Task 21 (in `docs/TASK_21_IMPLEMENTATION_SUMMARY.md`) notes: "Migration file created but applied directly due to existing migration conflicts." This is the smoking gun that the migration pipeline had become un-runnable, so the agent ran raw ALTER statements against the live DB.
**Why it matters:** The moment an agent "applies a migration directly" to work around a broken migration folder, the system's reproducibility is dead. No one can ever rebuild the database from migrations again. In court-fitness: **ONE migrations folder, one clean lineage, no exceptions.** If an agent ever feels the urge to apply raw SQL "just this once," that is the signal that the migrations folder is broken — stop and fix it, do not work around it.
**Where it lives in code:** Preventative rule in `CLAUDE.md` §5.3.
**Cross-refs:** HL-6 (FK decision related to migration integrity); `C:\xampp\htdocs\ltat-fitness-module\docs\TASK_21_IMPLEMENTATION_SUMMARY.md`; `C:\xampp\htdocs\ltat-fitness-module\docs\MOHAN_BRANCH_INTEGRATION_REPORT.md`.

---

## HL-2 — ltat-fitness documentation contradicts its live schema; trust the SQL dump

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** Three documents in `ltat-fitness-module/docs/` claim to describe the same schema but disagree:
- `CONSOLIDATED_DATABASE_SCHEMA_REPORT.md` (Sep 14, labelled "LIVE DATA ANALYSIS") uses `score` as the assessment column.
- `TASK_18A_DESIGN_FREEZE_AUDIT_REPORT.md` (Sep 14) DDL claims `value`.
- `MIGRATION_GUIDE.md` (Sep 15) describes players with columns `height_cm`, `weight_kg`, `emergency_contact`, `medical_notes`, `goals` — none of which exist in the live DB.

The live SQL dump at `Database/ltat_fitness.sql` is the only authoritative source. The docs are aspirational / frozen-in-time / written by different agents with different assumptions.
**Why it matters:** When porting schema from ltat-fitness to court-fitness, an agent who reads the docs and NOT the dump will build the wrong thing. Always grep the SQL dump for the actual CREATE TABLE statement before making schema decisions.
**Where it lives in code:** `CLAUDE.md` §5.3 ("Database conventions"); see `.ai/domain-reference/ltat-fitness-findings.md` for the correct canonical shapes to port.
**Cross-refs:** HL-3; HL-5.

---

## HL-3 — `assessments.score` (not `value`); `trainer_id` is nullable

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** ltat-fitness's `assessments` table (live SQL dump evidence) uses column `score DECIMAL(8,2)` — NOT `value` as TASK_18A's doc claimed. The `trainer_id` column is nullable in the DB even though `AssessmentsModel.php` validates it as required — another doc/code drift.
**Why it matters:** If/when court-fitness adds the fitness-testing kernel (Sprint 3+), we must name the column `score` to match the ltat-fitness convention and avoid renaming pain later. The trainer_id nullability should be decided deliberately — the model's required-validation is probably right, and the DB should match.
**Where it lives in code:** Future Sprint 3+ schema. Not in Sprint 1 scope.
**Cross-refs:** HL-2; `.ai/domain-reference/ltat-fitness-findings.md` §"Assessment feature state."

---

## HL-4 — ltat-fitness's `fitness_programs` is a season-scale macrocycle model, NOT how coaches actually use the system

**Discovered:** Sprint 0 Session 1, 2026-04-22 (clarified by Rajat via screenshots + walkthrough)
**Summary:** ltat-fitness's database has a classic 12-week periodisation model: `fitness_programs` → `program_phases` → `program_exercises` → `program_assignments`. The `BPPAccessv.Full.pdf` + BPP Excel files describe the same pattern (Pre-Phase + Month 1/2/3). But when Rajat walked me through the LIVE coach workflow at `tourtest.ltat.org`, coaches don't use any of that. They work at **week-scale**: "this is the Monday of Rohan's week." The whole macrocycle hierarchy is unused in practice.
**Why it matters:** A naive port of ltat-fitness's schema would give court-fitness 5 empty tables and the wrong mental model. court-fitness uses a flatter shape: `training_plans` (one row per week per player) → `plan_entries` (one row per exercise in a session on a date). Much simpler, matches actual usage.
**Where it lives in code:** `.ai/sprints/sprint-01/sprint-plan.md` §"Schema"; `CLAUDE.md` §5.6.
**Cross-refs:** HL-7.

---

## HL-5 — `exercise_type.status` is inverted; `fitness_subcategories.exercise_type` is unreliable

**Discovered:** Sprint 0 Session 1, 2026-04-22 (while reading live SQL dump)
**Summary:** Two schema quirks in the exercise taxonomy tables (which court-fitness seeds from):
1. `exercise_type.status` uses 0=Active, 1=Inactive (the CREATE TABLE comment confirms this). This is INVERTED from the usual convention (where 1=active, 0=inactive). A casual reader will get it backwards.
2. `fitness_subcategories` has an `exercise_type` column that is denormalised from `category_id → fitness_categories.exercise_type`, BUT most rows have it set to 0 — i.e. not actually populated. Only the JOIN via `category_id` is reliable. An agent who queries `fitness_subcategories.exercise_type = 1` to find all Cardio subcategories will get nothing or wrong results.
**Why it matters:** When court-fitness seeds the 3+12+204 catalogue from ltat-fitness, we should (a) normalise the `status` semantics to a consistent `is_active` column (1=active, 0=inactive), (b) drop the denormalised `exercise_type` column on `fitness_subcategories` and always JOIN through `category_id`.
**Where it lives in code:** `.ai/reference_exercise_taxonomy.md` (in memory); seeding logic in Sprint 1.
**Cross-refs:** `C:\xampp\htdocs\ltat-fitness-module\Database\ltat_fitness.sql` lines 126-200 (for the CREATE TABLE + seed data).

---

## HL-6 — Master Rules Set §2 says "no FKs"; ltat-fitness ignored it and used FKs; court-fitness sides with FKs

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** The Master Rules Set v1.0 (`C:\xampp\htdocs\ltat-fitness-module\docs\warp-project-instructions.md`) §2 explicitly states: "No MySQL foreign keys — manage relationships in application logic to avoid cascading issues." However, ltat-fitness's actual DDL (from `TASK_18A_DESIGN_FREEZE_AUDIT_REPORT.md` and the live SQL dump) uses FK constraints with CASCADE rules extensively. Either the rule evolved and wasn't written down, or the rule was ignored in practice.
**Why it matters:** court-fitness must decide this explicitly. My engineering call on 2026-04-22: **use FKs**. Reason in plain English: an FK is the database refusing to save an impossible record (e.g. a plan pointing at a non-existent player). This safety net is cheap and catches bugs at the earliest possible moment. The flexibility cost is minor and the portfolio is a 3-5 year effort with multiple agents — safety matters more than migration flexibility. Rajat knows about this deviation (recorded in session 1 handover) and can override.
**Where it lives in code:** `CLAUDE.md` §5.3.
**Cross-refs:** `C:\xampp\htdocs\ltat-fitness-module\docs\warp-project-instructions.md` §2.

---

## HL-7 — ltat-fitness's Player assessment chart view is a placeholder despite a fully-built controller

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** The file `app/Modules/Fitness/Views/player/assessments/charts.php` in ltat-fitness is literally one line: `<h2>Player Assessments Charts (placeholder)</h2>`. But the controller (`Player/Assessments.php`) has a fully-built `prepareMetricChartData()` method that produces Chart.js-ready data. The backend was done; the frontend view was never built. Yet reports like `LTAT_FITNESS_UI_IMPLEMENTATION_SUMMARY.md` don't flag this gap.
**Why it matters:** "ltat-fitness is working" ≠ "all ltat-fitness features are built." When court-fitness ports a feature, check not just that the backend exists but that the view actually has content. Do not assume parity just because file paths match.
**Where it lives in code:** Not court-fitness code; applies to decisions about what to port.
**Cross-refs:** `.ai/domain-reference/ltat-fitness-findings.md` §"What's built vs placeholder"; HL-4.

---

## HL-8 — ltat-fitness runs on mock authentication via `?mock_trainer_id=` query param

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** Both `Trainer/Assessments.php` and `Player/Assessments.php` in ltat-fitness have identical private methods: `return $this->request->getGet('mock_trainer_id') ?? 1;` (or `mock_player_id`). The identity of the current user is taken from the URL query string, with a hardcoded fallback to user ID 1. There is no session, no JWT, no password check.
**Why it matters:** ltat-fitness is NOT production-secure in any meaningful sense — anyone who knows the URL pattern can impersonate any trainer or player. court-fitness's SSO via HitCourt JWT is specifically designed to replace this with real auth. The lesson: never ship a "temporary" auth stub — it always becomes permanent. If SSO isn't ready on session 2 day 1, we do NOT build features first and auth later. Auth is built first.
**Where it lives in code:** Sprint 1's `/sso` endpoint must be built before any feature controller; see `.ai/sprints/sprint-01/sprint-plan.md`.
**Cross-refs:** `CLAUDE.md` §5.2; HL-2 (doc says auth exists, code says otherwise — another doc/reality drift).

---

## HL-10 — Don't assume the project folder is empty; `ls -la` the repo root as Pre-Flight step 1

**Discovered:** Sprint 00 Session 1, 2026-04-22 (at session close, while preparing `git init`)
**Summary:** I scoped Sprint 01 assuming `C:\xampp\htdocs\court-fitness` was an empty folder, because the owner had said "I have already created the folder for this project." When I finally listed the folder at session close (for the `git init` step), I found that CodeIgniter 4 was already present — full framework clone (not `codeigniter4/appstarter`), `composer install` not yet run (no `vendor/`), no `.env` (only the `env` template), default `Home::index` route. My Sprint 01 plan's opening step ("Install CodeIgniter 4 via Composer") was therefore wrong.
**Why it matters:** The framework (§10.3) mandates pre-flight verification specifically to catch this kind of assumption. I did a partial pre-flight (read the framework, read the predecessor, confirmed scope with owner) but skipped the most basic step — `ls` the working directory. The fix is one bash call; the cost of missing it is an entire sprint plan pointing the next agent in the wrong direction. Also: the word "created" in a project-kickoff message is ambiguous — it can mean "made an empty directory" OR "set up the starter."
**Where it lives in code:** Process rule. Before any sprint planning, run `find . -type f -not -path './.git/*' | head -100` at the target working directory and state in chat what already exists.
**Cross-refs:** `.ai/sprints/sprint-01/sprint-plan.md` §1 (revised post-discovery); Sprint 00 Session 1 handover §"Post-script finding at session close."

---

## HL-9 — BPP and training-load source docs are generic S&C, NOT tennis-specific; ltat-fitness's catalogue IS tennis-specific

**Discovered:** Sprint 0 Session 1, 2026-04-22
**Summary:** Rajat pointed me at four "helpful-project-documents": a BPP access PDF (just a download page, no content), two BPP 5-Day programming workbooks (KG and LBs), and a training-load ACWR workbook. I initially assumed these would inform tennis-specific design. They don't — BPP is a generic strength periodisation tool (copyright Outrajes LLC/Aquilina Strength Training, 2022), and the training-load xlsm has football/soccer sample data. HOWEVER, when I later read ltat-fitness's `fitness_subcategories` table (204 rows), it turned out to contain tennis-specific court work already — "On court rally blocks aerobic pace," "Court diagonals moderate pace repeats," "Short box service area shuttles." The tennis intelligence is in the LTAT SQL data, not in the generic source docs.
**Why it matters:** Two things. (1) When Rajat asks for "tennis-specific tests" in a later sprint, the starting point is NOT the 14 generic metrics in `MetricTypesModel::getCommonMetrics()` — it's the tennis-coloured names in `fitness_subcategories`. (2) BPP's 12-week macrocycle model is real S&C science but NOT how coaches actually use the LTAT system — they work in week slices. So the BPP methodology is interesting background, not a blueprint.
**Where it lives in code:** `.ai/reference_exercise_taxonomy.md` (in memory); will be seeded verbatim in Sprint 1.
**Cross-refs:** HL-4; `.ai/domain-reference/ltat-fitness-findings.md`.
