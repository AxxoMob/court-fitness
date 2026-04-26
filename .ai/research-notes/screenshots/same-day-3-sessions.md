# LTAT live system — same-day, 3 sessions Plan Builder view

**File:** `same-day-3-sessions.jpg`
**Source URL:** `https://tourtest.ltat.org/coach-exercises-MCMjlzA=.html` (live coach portal)
**Shared by:** Owner (Rajat) in chat
**First shared:** Session 1 (2026-04-22) — but agent did not save it. Session 5 reinvented from prose; UI was rebuilt in Session 6.
**Re-shared:** Session 6 (2026-04-26) post-Session-6-close, after owner saw the rebuilt UI was still wrong.
**Saved to repo:** 2026-04-26 (this note created during Session 6 post-close work, as part of the HL-13 + new CLAUDE.md §6.2 binary-artifact promotion rule).
**References feature/screen:** Plan Builder (`/coach/plans/new`) and the editable show grid (`/coach/plans/{obf}`, `/player/plans/{obf}`).

## What it shows

A single training week for player **Jitsupang Samrej**, Weekof 27-Apr-2026 Mon, Training Target Endurance, Weight Format Kg. Three Training Date blocks for the same date 27-04-2026 — Morning Cardio, Afternoon Weights, Evening Agility — coached by Pracharapol Khamsaman.

**Compound row layout per Training Date:**
- Header line (one row, side-by-side): `[-]` delete | date+time picker | Session period dropdown | Coach name | Format dropdown
- Exercise rows below the header (one per exercise): `[+]` add | Category text (auto-populated read-only) | Sub-category dropdown | **horizontal cell strip of 10 fixed cells** | `[-]` remove

**Cell strip (per owner's confirmation 2026-04-26):**
1. Target
2. Max H (likely "Max HR %")
3. Duration
4. Sets
5. Reps (Repetitions)
6. Work
7. Set Rest
8. Set
9. Repetition
10. Weight

All 10 cells render on every exercise row regardless of format. Format determines which cells are active (typeable) vs greyed/disabled. Demonstrated in the screenshot:
- Cardio row "Aerobic Cardio / Recovery r": Targe + Max H + Durat appear typeable; Sets, Reps, Work, S.Rest, SET, REP, Weigl appear greyed.
- Weights row "Push / Pin Loaded Chest Fly": Weight, Sets, Reps (R, Set Rest visible; the strip cuts off (horizontal scroll active in LTAT — but owner does NOT want this in court-fitness).
- Agility row "Speed / Hill sprints short": Sets, Reps, Works, Rests, Total wo, Work Re visible.

## Owner's 2026-04-26 directives on this layout

- **No horizontal scroll** at any breakpoint. The LTAT live system has horizontal scroll; owner disagrees with the dev team on that.
- Mobile responsive must collapse each exercise row into a stacked card; same data, different geometry. Single template, CSS-driven.
- Cell strip is fixed-position, all 10 cells always render.

## Bottom controls

- `+ Add Row` (bottom-left, green): adds a NEW Training Date block (compound header + empty exercise rows).
- Cancel (red, bottom-right): discard.
- Submit (green, bottom-right with paper-plane icon): save the plan.

## What this changes vs Session 6's build

Session 6 built a "block + body of stacked rows" with cells in a vertically-stacked column on the right of each row. The correct LTAT shape is the same (one block per date+session+format with multiple exercise rows underneath) BUT the cells must be a TIGHT HORIZONTAL STRIP, not stacked, and the row height must be much denser (~36-40px not 80-100px). Cells per row = 10 fixed (not format-relevant only).

## Cross-refs

- `.ai/core/HARD_LESSONS.md` HL-13 (the canonical "save screenshots immediately" lesson — this very file is the cure)
- `.ai/core/plan_builder_ux.md` (LOCKED 2026-04-23; will need a §1 transcription update to reflect "one compound row per Training Date" and the 10-cell strip)
- `.ai/.daily-docs/26 Apr 2026/session_6_handover.md` (Session 6 shipped the wrong cell layout; Session 7 fixes per this screenshot)
- Sibling notes: `creating-training-session.md`, `program-list-view.md`, `week-picker.md`, `date-picker.md`
