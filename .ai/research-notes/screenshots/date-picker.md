# LTAT live system — Plan Builder, Training Date picker open

**File:** `date-picker.jpg`
**Source URL:** `https://tourtest.ltat.org/coach-exercises-MCMjlzA=.html`
**Shared by:** Owner (Rajat) in chat
**First shared:** Session 1 (2026-04-22)
**Saved to repo:** 2026-04-26 (Session 6 post-close, HL-13 / CLAUDE.md §6.2 binary-artifact rule)
**References feature/screen:** Plan Builder — Training Date input within the compound-row date header.

## What it shows

The Training Date input focused (placeholder `dd-mm-yyyy`) with the calendar+time picker open.

**Constraint visible in the calendar:** dates outside the selected Weekof's 7-day window are greyed (e.g. Weekof Mon 27 Apr is selected → only 27 Apr through 03 May are typeable; days before 27 Apr and after 03 May are greyed).

**Time picker at the bottom:** `00 : 00` with separate hour and minute selectors. The Training Date stores both date and start-time (e.g. `27-04-2026 00:00` in the saved row, see `same-day-3-sessions.jpg`).

## Implications

1. **Training Date must fall within Weekof's 7-day window.** Already enforced server-side in court-fitness `Coach\Plans::normaliseEntries` (returns "date must fall within the selected week" error). UI parity would disable out-of-window days in the picker — Sprint 02 polish.
2. **Training Date carries a start-time component.** Currently court-fitness uses `<input type="date">` and stores `training_date` as DATE only (no time). LTAT stores `27-04-2026 00:00` — i.e. a DATETIME. The 00:00 default suggests time is optional in practice, BUT the schema supports per-session start times if the coach wants to specify "Morning at 06:30".
   - **Decision needed:** does court-fitness need session start-time support in Sprint 01? Or defer to Sprint 02?
   - Current schema: `plan_entries.training_date DATE NOT NULL` — date only. To support start-times, we'd either add a `start_time TIME NULL` column or change `training_date` to DATETIME. Either is a migration change, hence not Session 6/7 territory unless owner asks.

## Cross-refs

Sibling notes: `same-day-3-sessions.md`, `creating-training-session.md`, `program-list-view.md`, `week-picker.md`.
