# Plan Builder + Actuals Log — Canonical UX

> **Status:** LOCKED by owner, 2026-04-23 (Session 5 post-mortem).
> **Tier:** 2 — domain reference. Read before any UI work on the planning or logging screens.
> **Applies to:** `/coach/plans/*`, `/player/plans/*`, and any future screen that shows `training_plans` + `plan_entries` to a user.
>
> Every answer below came from the owner directly. Do not guess, do not reinterpret, do not "improve." If a design question comes up that is NOT answered here, stop and ask the owner.

---

## 0. Canonical reference — the LTAT live system

The design source of truth is **`https://tourtest.ltat.org/coach-exercises-<base64>.html`** (the live coach portal built on the predecessor ltat-fitness-module project). The owner shared two screenshots of this URL in session conversations on 2026-04-22 and 2026-04-23.

The screenshots themselves must live at `.ai/research-notes/screenshots/`. See HL-13 on why this matters. Until they are saved as image files, §1 below is their transcription — detailed enough that a fresh agent can reconstruct the layout without the image.

---

## 1. The LTAT screen — full transcription (from screenshots on 2026-04-22 and 2026-04-23)

**Browser chrome + sidebar:** standard desktop layout, ~1900px wide. Left sidebar with icons (home, bell, page, dumbbell, gear). Top bar has the user's name + role chip on the right.

**Page title:** "Player Training Plan" (blue heading).
**Breadcrumb:** Training Plan / Player Training Plan.

### 1.1 Fundamentals strip (top card, ~full page width)

A horizontal strip of **4 input fields**, each roughly ¼ page wide, with labels above + red asterisks on required fields:

| # | Label | Field | Example value |
|---|---|---|---|
| 1 | LTAT Player * | text / typeahead | `Samavee SUKSIRIRAT` |
| 2 | Weekof (Monday) * | date picker (Monday only) | `20 Apr, 2026(Mon)` |
| 3 | Training Target * | text input (with dropdown suggestions) | `Endurance` |
| 4 | Weight Format * | dropdown | `Kg` |

### 1.2 Training Date block (repeats per date)

Below the fundamentals, separated by a horizontal rule:

```
[ — ]  Training Date  [ 22-04-2026 | ▼ ] [ Morning | ▼ ] [ Pracharapol Khamsaman | ▼ ] [ Cardio | ▼ ]
       ┌─── exercise rows, one per line ─────────────────────────────────────────────────────┐
       │ [+] [ Aerobic Cardio ] [ Recovery r ▼ ] [ 10 ] [Max H ][ Durat ][ Sets ][ Reps ]... [-]│
       │ [+] [ Anaerobic Alactic ] [ Pro agility ▼ ] [ 15 ] [Max H ][ Durat ][ Sets ]...    [-]│
       └─────────────────────────────────────────────────────────────────────────────────────┘

[ — ]  [ 22-04-2026 | ▼ ] [ Evening | ▼ ] [ Pracharapol Khamsaman | ▼ ] [ Weights | ▼ ]
       │ [+] [ Squat ] [ Front Squa ▼ ] [ 10 ] [Sets][Reps (Ra..)][Set Rest]                 [-]│
       │ [+] [ Lunge ] [ Split Squat ▼ ] [ 15 ] [Sets][Reps (Ra..)][Set Rest]                [-]│
       │ [+] [ Hinge ] [ Kettlebell S ▼ ] [ 10 ] [Sets][Reps (Ra..)][Set Rest]               [-]│

[ — ]  [ 24-04-2026 | ▼ ] [ Evening | ▼ ] [ Pracharapol Khamsaman | ▼ ] [ Agility | ▼ ]
       │ [+] [ Speed ] [ Hill sprints short ▼ ] [Sets][Reps][Works][Rests][Total wo][Work Re][-]│
```

**Key observations:**

- **Each exercise row has a Category label** (Aerobic Cardio, Squat, Lunge, Hinge, Speed…) **then a Sub-category dropdown** (Recovery run, Front Squat, Split Squat…) **then a horizontal strip of numeric input cells** that ends with a `[-]` remove button.
- **The column strip is LONGER than what applies to this exercise type.** Unused cells are rendered greyed / disabled (the Aerobic Cardio row shows Max HR, Duration, Sets, Reps, Work, S.Rest, SET, REP, Weight — but most are faded, meaning not applicable to this specific exercise).
- **The row chooses its own live cells based on the picked Sub-category.** Cardio exercises have HR/Duration live, Weights exercises have Sets/Reps/Weight/Rest live, Agility exercises have Reps/Rests/Total work live. The rest remain disabled placeholders so the row visually lines up.
- **Integer shown in the leftmost numeric cell (the "10", "15", "10" values)** appears to be a concrete editable value for this exercise — likely a global value (duration-in-minutes for Cardio, a default? needs clarification with owner if we copy the exact shape).
- **[+] button on each row** = add another exercise to this session under the same date/session/format.
- **[-] button on each row** = remove that exercise.
- **[ — ] button at the date level** = remove the entire training-date block.

### 1.3 Bottom buttons

```
[ + Add Row ]                            ... [ ← Cancel ]  [ 🚀 Submit ]
```

- **+ Add Row** = add a new Training Date block.
- **Cancel** = discard.
- **Submit** = save the plan.

### 1.4 One form, many exercises

A single form holds unlimited dates × unlimited exercises-per-date-per-session. A single Submit persists everything. There is NO modal to add an exercise. There is NO multi-step flow. The coach types values **inline, in the row**, while the player exercises in front of them.

---

## 2. Owner's locked answers (Session 5, 2026-04-23)

### 2.1 Column strip — what goes in each row?

> "All the values need to be inserted for sure, by the player or the coach. For the sake of ease, and usage, we kept it in one row. One row because a player would finish that particular exercise Aerobic Cardio (Recovery Run), fill in the details, and only then move on to Anaerobic Alactic (Pro Agility 5 10 5 5 Repeats)."

**Rule:** one exercise = one row. All its numeric cells are on that row. The user fills each row sequentially as the workout progresses. You may simplify the exact column set, but you may NOT fracture a row into a modal or multi-step.

### 2.2 Responsive behaviour — how does this adapt?

> "You may wish to make your cards width larger across the page. Right now they are very short in width. Yes you may simplify when the user switches to mobile view, but that needs to be dynamic. We can not have the same view for laptop & pad, and mobile too. Right now it seems that everything is about mobile view, which is important, but it has to be handled dynamically."

**Rule:**
- **Desktop + tablet + iPad (default):** wide inline grid. Cards / plan-list cards / Plan Builder grid span the page, not a narrow 640px centred column.
- **Mobile (<768px):** collapse each exercise row into a card with stacked fields. Same data, different geometry.
- **Single HTML payload, CSS-driven responsive collapse** — NOT two templates, NOT a device-sniffing server branch.
- Plan card grid (`coach/plans/index`, `player/dashboard`) should also widen — right now they are single-column at phablet width, should be 3+ across on desktop.

### 2.3 Who edits actuals?

> "Both of course. … When the player and coach are working together, the coach or player may fill the exercises up from their logins. If the player is travelling without the coach, he logs into his account, goes to his exercise plan and saves the values. The Coach can then see the values when he logs into his zone back home. It works both ways."

**Rule:**
- Coach **and** Player both have an editable-actuals view of the same plan.
- Player may also edit targets? — unclear. Default for Session 6: **player can only edit actuals; coach can edit both.** Revisit if owner says otherwise.
- Two URLs mount the same grid: `/coach/plans/{obf}` and `/player/plans/{obf}`. Server-side ownership check on each; `actual_by_user_id` stamps whoever saved.
- **This answer has been in the project since day 1** — see `.ai/core/BRIEFING.md:10` and `.ai/core/ltat-fitness-findings.md §2` step 5. Agents must re-read BRIEFING every session. See HL-13.

### 2.4 Which boxes show per exercise type?

> "Please refer to the database. It is all following a pattern. The row and boxes adjust according to the max value that that particular exercise needs the value for. For exp (Refer to screenshot) Cardio, Weights and Agility, all have different amount of boxes. They come added automatically as per the exercise selected."

**Rule:** the live-cell set is a function of the exercise type (possibly even the sub-category). Cardio, Weights, and Agility each get a different set. The row swaps its live cells when the sub-category changes. **Disabled/greyed cells for non-applicable measures may be shown for visual alignment** (matching the LTAT screenshot) — ltat-fitness's rendering pattern. Alternatively the row may re-render with only the applicable cells; either is acceptable as long as it is dynamic.

The active cell sets observed in the LTAT screenshot:
- **Cardio** → Max HR%, Duration, (possibly Work, SET, REP depending on sub-category)
- **Weights** → Sets, Reps (range), Set Rest, possibly Weight
- **Agility** → Sets, Reps, Works, Rests, Total work, Work Rest

Exact canonical set per sub-category is still to be confirmed with owner — when in doubt, open a sub-category in the live LTAT system and count its active cells. This doc will be revised as each type is confirmed.

---

## 3. Interaction flow

### 3.1 Coach creates a plan

```
GET  /coach/plans/new      → Plan Builder grid, empty
     coach fills fundamentals + adds dates + fills rows inline
     coach clicks Submit
POST /coach/plans          → server persists plan + entries transactionally
     → redirect to /coach/plans/{obf}  (NOT a read-only badge page)
GET  /coach/plans/{obf}    → the SAME grid, now populated with targets, with actual cells editable
     coach may immediately start typing actuals (typical when working with player in person)
POST /coach/plans/{obf}    → server persists actual cells (and any target edits, if coach changed them)
     → redirect back to same URL with "Saved" flash
```

### 3.2 Player opens a plan

```
GET  /player/plans/{obf}   → same grid layout; targets locked, actuals editable
     player fills actuals during/after session
POST /player/plans/{obf}   → server persists actual_json on the entries owned by this player
     actual_by_user_id = player's user id
     → redirect back with "Saved" flash
```

### 3.3 Audit — who typed what?

`plan_entries.actual_by_user_id` records the user id of whoever saved that entry's actuals last. `actual_at` records when. Show views display "Logged by Coach Rajat · 2m ago" or "Logged by Rohan · 2m ago" so both sides see the provenance.

---

## 4. What Session 5 shipped vs what is correct

### 4.1 Correct (keep)

- **Backend**: models (`TrainingPlansModel`, `PlanEntriesModel`), controllers Coach\Plans::{index, new, store}, Coach\Players, Player\Plans, Coach\Dashboard, Player\Dashboard. Routes. CSRF. AuthFilter. IdObfuscator. DB schema.
- **The PWA + Falcon font cohesion work** — layout.php, CSS variables, Bootstrap 5 wiring.
- **Exercise taxonomy seeding** (3 + 12 + 204).
- **JSON blob pattern** on `plan_entries.target_json` / `actual_json`.

### 4.2 Wrong, rebuild in Session 6

| File | What's wrong | What to replace with |
|---|---|---|
| `app/Views/coach/plans/new.php` | Bootstrap accordion + modal drilldown + mobile-first single-column layout | **Wide inline grid** per §1 above. CSS-responsive collapse to stacked cards on <768px. No modal. |
| `public/assets/js/plan-builder.js` | Modal state machine, `openModalFor()`, `readTargetFor()` modal helpers | Re-purposed for **inline-row dynamic cell swapping** based on sub-category selection. Row-level add/remove. |
| `app/Views/coach/plans/show.php` | Read-only badge list | **Editable grid** — same template as `new.php` but targets locked, actuals editable. One unified template serves both screens. |
| `app/Views/player/plans/show.php` | Read-only badge list | Editable grid with actuals editable, targets locked. |
| Redirect after `POST /coach/plans` store | Lands on read-only show page | Must land on editable grid with actuals ready to punch. |
| Plan card grid widths | Narrow, phablet-first | Wide, desktop-first; 3+ cards across on desktop; stacked only on <768px. |
| Coach\Plans::show, Player\Plans::show | Render read-only | Accept POST updates to `actual_json` (and target_json on coach side). |

### 4.3 New routes required (Session 6)

- `POST /coach/plans/{obf}` — update targets + actuals together. CSRF-protected.
- `POST /player/plans/{obf}` — update actuals only. CSRF-protected.

(The `GET /coach/plans/{obf}/edit` I previously proposed goes away — the show URL IS the editable grid.)

---

## 5. Dark patterns to avoid

- **Don't modal anything.** The owner rejected modals explicitly. A modal is a tap-tax per exercise.
- **Don't make mobile the default.** Tablet and laptop are the primary devices for the coach-in-gym workflow.
- **Don't redirect save to a read-only view.** "Where do I insert the values?" is the bug that prompted this rewrite.
- **Don't split target and actual into two screens.** One grid, two editable column sets, one Save.
- **Don't forget who can edit.** Coach and Player both. Check BRIEFING.md every session.

---

## 6. Revision history

- 2026-04-23, Session 5 close — initial lock after owner feedback on Session 5's modal-driven mobile-first build. Answers (a) through (d) captured verbatim from owner.
