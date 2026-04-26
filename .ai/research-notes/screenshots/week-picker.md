# LTAT live system — Plan Builder, Weekof picker open

**File:** `week-picker.jpg`
**Source URL:** `https://tourtest.ltat.org/coach-exercises-MCMjlzA=.html`
**Shared by:** Owner (Rajat) in chat
**First shared:** Session 1 (2026-04-22)
**Saved to repo:** 2026-04-26 (Session 6 post-close, HL-13 / CLAUDE.md §6.2 binary-artifact rule)
**References feature/screen:** Plan Builder fundamentals — Weekof field date picker.

## What it shows

The Weekof input focused with a calendar dropdown open. The grid shows April 2026; Mondays (column "Mon") are typeable; all other days of the week are greyed/disabled. Selected: 27 Apr, 2026 (Mon).

## Implication

Weekof is a Monday-only date picker. Server-side validation rejects non-Mondays (court-fitness `Coach\Plans::store::validatePlanInput` enforces this via `(int)$d->format('N') !== 1`). The LTAT picker visually constrains the selection so the user can't pick a non-Monday in the first place.

court-fitness's current implementation uses `<input type="date">` (no day-of-week filter at the UI level) plus server-side validation. A friendlier UX would use a custom JS picker that disables non-Mondays — Sprint 02 polish.

## Cross-refs

Sibling notes: `same-day-3-sessions.md`, `creating-training-session.md`, `program-list-view.md`, `date-picker.md`.
