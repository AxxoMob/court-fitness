# LTAT live system — Program list (saved plans, the post-save view)

**File:** `program-list-view.jpg`
**Source URL:** `https://tourtest.ltat.org/coach-training-program.html`
**Shared by:** Owner (Rajat) in chat
**First shared:** Session 1 (2026-04-22)
**Saved to repo:** 2026-04-26 (Session 6 post-close, HL-13 / CLAUDE.md §6.2 binary-artifact rule)
**References feature/screen:** Coach plan-list / dashboard (`/coach/plans` in court-fitness terminology).

## What it shows

The Coach's list of saved training programs.

**Top filters strip:**
- Year (text input, default 2026)
- Weekof (dropdown — picks a Monday from a list)
- Player (search-by-name autocomplete, with a "X Clear" button)

**Below filters:** a "Show 50 entries" page-size selector + "Search:" free-text filter (right-aligned).

**Table columns:**
1. S no
2. Weekof (e.g. 20-04-2026)
3. Player (e.g. Samavee Suksirirat)
4. Assigned Trainer (rendered as a small pill/badge with a count — e.g. "1" — clickable, presumably opens a list of trainers if more than one)
5. Training Target (e.g. Endurance)
6. Excercise (rendered as colored pills — yellow "Weights" + green "Cardio" — one pill per Format used in the plan)
7. Actions (two icon buttons: pencil edit + blue file/download icon — likely Excel export per ltat-fitness `exportToExcel` stub)

**Bottom:** "Showing 1 to 2 of 2 entries" + pagination buttons (First / Previous / 1 / Next / Last).

**Top-right:** a green "+ Add Exercises" button opens the Plan Builder for a new plan.

## What this changes vs court-fitness's current build

court-fitness Session 6's `/coach/plans` is a **card grid** (per plan_builder_ux.md §2.2, 3-up at ≥992px). The LTAT version is a **dense table** with sortable columns, server-side filters, pagination, and per-row action icons. This is a richer view than what we have.

- For Sprint 01, the card-grid is acceptable (smaller plan counts; mobile-friendly first).
- For Sprint 02+, when plan counts grow, switching to a table-with-filters view is a candidate. Owner should weigh card-grid vs table.

## Notable LTAT specifics not yet in court-fitness

- "Excercise" column = format chips. Per court-fitness's schema (`plan_entries.exercise_type_id`), we have this data; the index controller could derive a DISTINCT list of formats per plan and render them as chips. Sprint 02 polish.
- Excel export (xls icon). Out of Sprint 01 scope; ltat-fitness's was a stub anyway.
- Edit icon → opens Plan Builder for that plan. court-fitness already does this via "Open plan →" CTA on the card.
- Note the column header "Excercise" is misspelled in LTAT — do NOT replicate (HL-13 lesson #2: trust the screenshot for layout, not for spelling).

## Cross-refs

Sibling notes: `same-day-3-sessions.md`, `creating-training-session.md`, `week-picker.md`, `date-picker.md`.
