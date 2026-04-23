# ltat-fitness Findings — What the Predecessor Project Teaches Us

> A reference document, not a living log. Records what Sprint 0 Session 1 (2026-04-22) learned from reading the predecessor `ltat-fitness-module` project. A future agent with no memory of this session should be able to pick up court-fitness cleanly from this file.

---

## 1. Where the predecessor lives

- **Local path:** `C:\xampp\htdocs\ltat-fitness-module\`
- **Live URL:** `https://tourtest.ltat.org/` — this is a **test zone**, not production. Rajat has not shared production credentials.
- **Current status (2026-04-22):** Live and in use by coaches. Design baseline frozen 2025-09-14 per `docs/TASK_18A_DESIGN_FREEZE_AUDIT_REPORT.md`.
- **Stack:** CodeIgniter 4 + HMVC modules + MySQL + Falcon admin theme (locally synced, pinned to v2.8.0/v3.0.0-alpha10, AxxoMob repo).
- **Predecessor's governance:** `docs/warp-project-instructions.md` is Rajat's **Master Rules Set v1.0 (finalised 2025-09-17)** — the direct ancestor of our AI_AGENT_FRAMEWORK.md. Still useful as domain knowledge (captcha spec, BCRYPT requirement, Starter Kit globals list).

---

## 2. What coaches actually do (the workflow to replicate)

Verified with Rajat via 5 screenshots on 2026-04-22:

1. **Login** at `tourtest.ltat.org/coach.html` using an LTAT-issued code like `CHKHA198122` plus password. A math captcha ("What is 9 + 8 ?") protects the form. (In court-fitness, HitCourt handles all of this — we get users via SSO.)
2. **Dashboard** at `/coach-dashboard.html` shows stats about "Courses" (LTAT's tournament/coaching-course system, NOT fitness). The dashboard is misleadingly generic — fitness lives under a sidebar item.
3. **Training Program list view** at `/coach-training-program.html` — filters (Year, Weekof, Player), table with columns: S No | Weekof | Player | Assigned Trainer | Training Target | Exercise | Actions.
4. **Add Exercise form** at `/coach-exercises-{base64id}.html` — this is the workhorse screen. Top fields: LTAT Player, Weekof (Monday), Training Target (e.g. Endurance), Weight Format (kg/lb). Then one or more training dates; for each, one or more sessions (morning/evening/afternoon); for each session, a Format (Cardio/Weights/Agility) + rows of Category → Subcategory → target numbers.
5. **Save** — plan is now visible to both the coach AND the assigned player. Either can fill in actuals. When the other side refreshes, they see the latest data. (This is "live" in the colloquial sense — not WebSockets.)

---

## 3. What "fitness testing" means in this project (three distinct concepts)

A source of early confusion. Clarified by reading the four `helpful-project-documents` PDFs/workbooks + Rajat's walkthrough:

1. **Performance testing** — record a player's 10m sprint time or 1RM back-squat weight on a date, chart progress over time. This is what ltat-fitness's `assessments` + `metric_types` tables implement (backend only; frontend is placeholder). Covered by ltat-fitness's `MetricTypesModel::getCommonMetrics()` list of 14 metrics.
2. **Readiness / movement screening** — one-off clinical-style score at intake, outputs a recommendation for program phase. BPP's Assessment tab does this. ltat-fitness does NOT implement this.
3. **Training load monitoring** — daily RPE × duration, roll up into 7-day/28-day EWMA to compute Acute:Chronic Workload Ratio (ACWR) and flag injury risk. The `training load data collection.xlsm` workbook by Ryan White does this. ltat-fitness does NOT implement this.

Rajat's "fitness testing is exactly the same" statement was later clarified: the **larger workflow** (coach plans week + player logs actuals) is what's "exactly the same" from ltat-fitness. The narrower testing kernel (performance testing — concept 1) is deferred to Sprint 3+.

---

## 4. Authoritative data model (from the live SQL dump)

The ltat-fitness schema has many tables. Only some are relevant to court-fitness Sprint 1.

### 4.1 Reused in court-fitness Sprint 1 (seed from or mirror structure)

- **`exercise_type`** (3 rows: Cardio, Weights, Agility). Quirk: `status` column is 0=Active, 1=Inactive (inverted vs convention — see HL-5).
- **`fitness_categories`** (12 rows, linked to exercise_type). Cardio has 2 (Aerobic Cardio, Anaerobic Alactic); Weights has 8 (Push, Pull, Hinge, Squat, Lunge, Carry, Accessory, Core); Agility has 2 (Speed, Agility).
- **`fitness_subcategories`** (204 rows, linked to fitness_categories). The leaf-level exercises. Includes tennis-specific items ("On court rally blocks aerobic pace", "Short box service area shuttles", "Court diagonals moderate pace repeats"). Quirk: the denormalised `exercise_type` column on this table is unreliable — JOIN through category_id.
- **`players`, `trainers`, `trainer_player_assignments`** — structure reused, but court-fitness renames `trainers` to reflect the project's "coach" vocabulary; identity comes from HitCourt SSO (no local passwords).

### 4.2 NOT reused — the program/macrocycle machinery

- `fitness_programs`, `program_phases`, `program_exercises`, `program_assignments`, `sessions`, `session_sets` — these implement a 12-week macrocycle authoring model (program with phases, exercises prescribed per week/day). Coaches don't use this structure in practice (see HL-4). court-fitness replaces with a flatter `training_plans` + `plan_entries` shape.

### 4.3 Deferred — the assessment/testing kernel

- `assessments`, `metric_types` — not in Sprint 1. Will be reused in Sprint 3+ when performance testing is built. Column is `score` (not `value`). `assessment_context` enum: baseline / progress / final. Unit-independent via a free-form `unit` column.

### 4.4 Useful pattern — JSON blobs for variable fields

Task 21 (documented in ltat-fitness's `docs/TASK_21_IMPLEMENTATION_SUMMARY.md`) added `target_json` and `actual_json` columns to `session_sets` to hold varying exercise input fields (Cardio wants Max HR + Duration; Weights wants Sets + Reps + Weight + Rest; etc.). court-fitness adopts this pattern for `plan_entries` from day one.

---

## 5. What's built vs placeholder in ltat-fitness

A surprise: "working" doesn't mean "fully built." Specific gaps I found:

- **Admin fitness CRUD (players/exercises)** — fully built, deployed, demoed. ✓
- **Trainer dashboard + program authoring + session detail with Chart.js** — built via Mohan's branch + Task 21. ✓
- **Assessment feature (testing kernel)** — backend complete (model has rich query methods, controller has full CRUD + export + chart-data prep), BUT the player chart view is literally `<h2>Player Assessments Charts (placeholder)</h2>`. (HL-7)
- **Excel export** — stub. `exportToExcel()` calls `exportToCsv()` with a TODO comment.
- **Real authentication** — absent. Controllers take identity from `?mock_trainer_id=` query string. (HL-8)
- **Admin fitness-admin gate** — present but partially real (uses `is_fitness_admin` flag on trainers).

---

## 6. Known drift (do not repeat)

- **Dual migrations folders** — `app/Database/Migrations/` and `app/Modules/Fitness/Database/Migrations/` both contain CreateTrainersTable (dated 2025-09-14 and 2025-09-15). Task 21 had to apply ALTER statements directly. (HL-1)
- **Doc/DB disagreement** — `MIGRATION_GUIDE.md` describes columns that don't exist in the live DB; `TASK_18A` DDL uses `value` where live uses `score`. (HL-2, HL-3)
- **Master Rules Set §2 (no FKs) not followed** — live DB uses FK constraints everywhere. (HL-6)
- **Schema quirks in the taxonomy** — inverted `status` semantics, unreliable denormalised column. (HL-5)
- **Mock auth** shipped to the "working" system. (HL-8)

---

## 7. What this means for court-fitness

The ltat-fitness code base is a **cue, not a template**. Specifically:

- **Copy the live workflow** (week-based plan, 3-level taxonomy, sessions morning/afternoon/evening, two-way actuals logging).
- **Copy the taxonomy tables verbatim** (3 + 12 + 204 rows), clean up the quirks when seeding.
- **Copy the JSON-blob pattern** for target/actual fields.
- **Do NOT copy the program/phase/macrocycle machinery** — unused in practice.
- **Do NOT copy the mock authentication** — build SSO against HitCourt from day one.
- **Do NOT copy the dual-migrations-folder setup** — one clean folder.
- **Do NOT copy Falcon theme** — mobile-first UI is non-negotiable for court-fitness; start with Bootstrap 5 and reassess in Sprint 2.
- **Do NOT copy the Fitness Admin role** — court-fitness Sprint 1 is Coach + Player only; admin is deferred.

---

## 8. Files in `C:\xampp\htdocs\ltat-fitness-module\` worth re-reading when specific questions come up

- `Database/ltat_fitness.sql` — authoritative schema (13,398 lines; grep, don't read whole)
- `docs/warp-project-instructions.md` — Master Rules Set v1.0 (the doctrinal precursor)
- `docs/TASK_21_IMPLEMENTATION_SUMMARY.md` — JSON-blob pattern reasoning
- `docs/TASK_18A_DESIGN_FREEZE_AUDIT_REPORT.md` — design-freeze snapshot
- `docs/README-FITNESS-MODULE.md` — high-level fitness admin feature overview
- `app/Modules/Fitness/Models/AssessmentsModel.php` — the gold-standard model pattern (deferred feature, but the style is reusable)
- `app/Modules/Fitness/Models/MetricTypesModel.php` — the 14-metric `getCommonMetrics()` catalogue (deferred feature)
- `app/Modules/Fitness/Controllers/Trainer/Programs.php` + `Trainer/Sessions.php` + `Player/Sessions.php` — NOT read in detail yet; these implement the program-authoring + session-logging workflow that court-fitness Sprint 1 must recreate. Read deeply at Sprint 1 Session 1.

---

## 9. Four helpful-project-documents — what they contain

Located at `C:\xampp\htdocs\ltat-fitness-module\helpful-project-documents\`:

- `BPPAccessv.Full.pdf` — 7-line PDF, just a "thank you for purchase" download page. Ignore.
- `Big Picture Programming - 5 Day (Full - KG's).xlsx` — 11-sheet workbook, generic S&C 12-week periodisation tool (Outrajes LLC/Aquilina Strength Training, 2022). Sheets: Exercise Selection, Start Here Guide, Assessment, Pre-Phase, Exercise Selection Map, Program Design, Program Design Data, Month 1/2/3, The Big Picture. NOT tennis-specific. Shaped coaches' mental model but is NOT how court-fitness organises data.
- `Big Picture Programming - 5 Day (Full - LBs).xlsx` — same workbook, imperial units.
- `training load data collection.xlsm` — 4-sheet ACWR tracker by Ryan White (football/soccer sample data). Tracks daily RPE × duration, computes 7/28-day EWMA, flags injury risk. Informs a future sprint (training load monitoring, Sprint 3+ per CLAUDE.md §2).

None of these are tennis-specific. The tennis intelligence in ltat-fitness is entirely in the `fitness_subcategories` seeded data (HL-9).
