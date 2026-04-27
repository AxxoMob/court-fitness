# Session 7 Mobile-Responsive Baseline — Static Analysis

**Date:** 2026-04-27
**Sprint:** Sprint 01 Session 7
**Author:** Claude (Opus 4.7, 1M-context)
**Status:** Pre-screenshot static analysis. Owner takes Chrome DevTools screenshots; this doc is updated when they land.

---

## Why static analysis first

The court-fitness dev box is terminal-only. Real Chrome rendering at the five mandated breakpoints (375 / 568 / 768 / 992 / 1280 px) requires the owner's machine. Per HL-13 + CLAUDE.md §6.2 artifact #7, screenshots must land in `.ai/research-notes/screenshots/session7-{viewport}px.png` with sibling `.md` notes BEFORE any CSS change. This doc captures what I predict will happen at each breakpoint by reading the sealed CSS — so when the owner takes the screenshots, we have a concrete prediction to compare against, and any surprise is easy to spot.

This is NOT a substitute for the screenshots. It's the diagnostic baseline that **says, in advance, what should be broken** so the owner's eyeball pass is a confirmation, not a discovery.

---

## Predictions per breakpoint

### 375px (iPhone SE portrait, small phone)

**Active rules:** `(max-width: 991.98px)` → mobile stacked-card collapse. `(max-width: 575.98px)` → block-head fully stacks each fundamentals field beneath the delete icon.

**Layout:**
- Top fundamentals strip — 4 fields stacked vertically (Player, Weekof, Training Target, Weight Format).
- Each Training Date block: header card with `[-]` icon at top-left, then the 4 head-fields stacked vertically (date, session, coach, format), then the exercise rows.
- Each exercise row collapses into a stacked card with grid-areas:
  - `[+/−]` controls top-left, Category dropdown top-right, Sub-category dropdown middle-right.
  - Cell strip at bottom in a 2-column grid (Cardio = 5 rows × 2, Weights = 2 rows × 2, Agility = 3 rows × 2).
- Notes textarea full-width at the bottom.
- Sticky save bar at bottom with `flex-wrap: wrap` — should hold 2 elements.

**Predicted issues:**
1. **Tap targets too small.** `.cf-row__addremove button` is `28×28px`; `.cf-block__head-icon` is `30×30px`. iOS guideline is 44×44; Android Material is 48×48. **Both are inside SEALED — unsealing required to fix.**
2. **Block-head delete icon visual oddness:** at 575.98 the icon `[-]` sits in row 1 col 1, but ALL the head-cells (date, session, coach, format) span column 2 in their own rows — so the icon visually floats next to the first field only, with empty space below. **Inside SEALED.**
3. **Cell labels (`MAX HR %`, `DURATION`, etc.) are 0.7rem ≈ 11px** at this breakpoint. Borderline readable. Inside SEALED.

### 568px (medium phone, iPhone SE landscape, Galaxy fold cover)

**Active rules:** same as 375px — both `<992` and `<576` match. Layout identical.

**Predicted issues:** same as 375px. The 568→376 transition is invisible because both fall under the same rule.

### 768px (iPad portrait)

**Active rules:** `(max-width: 991.98px)` only — the `(max-width: 575.98px)` rule does NOT apply.

**Block-head layout** changes to: `grid-template-columns: 36px 1fr 1fr` (3 columns).
- Row 1: `[-]` icon | date | session
- Row 2: `<empty>` | coach | format

**THIS IS A LAYOUT BUG:** the icon in row 1 col 1 leaves row 2 col 1 EMPTY. CSS grid auto-place fills row 2 col 1 with the next available item (coach, the 4th child). But the icon's column span is implicit `1`, so row 2 col 1 *is* visually empty — the coach lands at row 2 col 2 only if the icon explicitly spans both rows. It doesn't. **Likely visual:** `[icon] [date] [session] / [coach] [format] [empty]`. Awkward — the icon doesn't visually bind to the block. **Inside SEALED.**

**Exercise rows** still in mobile stacked-card mode (because `<992` rule applies). Each exercise card sized for 768px viewport is comfortable.

**Predicted issues:**
1. The block-head row-2 col-1 gap (described above). Inside SEALED.
2. Stacked-card width at 768px is fine — ~720px content area; cards fill it.

### 992px (iPad landscape, small laptop)

**Active rules:** desktop. NONE of the max-width rules match.

**Exercise row** uses default `grid-template-columns: 70px minmax(140px, 160px) minmax(170px, 200px) 1fr`.
- Minimum content widths: 70 + 140 + 170 + (3 × 8px gap) = 404px for the controls + dropdowns.
- Cells column (the `1fr`) gets: viewport (992) − body padding (~32) − controls (404) ≈ **556px**.

**Cell strip** is `grid-auto-columns: minmax(72px, 1fr)` with `grid-auto-flow: column`. Each cell at minimum 72px wide. Cell-strip totals:
- **Cardio (10 cells):** 10 × 72 + 9 × 6 (gap) = **774px required.**
- Weights (4 cells): 4 × 72 + 3 × 6 = 306px.
- Agility (6 cells): 6 × 72 + 5 × 6 = 462px.

**THE 992px PROBLEM:** Cardio's 774px > 556px available. The `minmax(72px, 1fr)` means each cell column is AT LEAST 72px. Grid will not shrink below 72px — Cardio rows **overflow horizontally**. Either the row breaks the layout, or `.cf-cells` extends past the 1fr column's right edge. Either way: visible breakage at the most common laptop/iPad-landscape viewport.

Weights and Agility rows fit comfortably at 992px. **Cardio is the only failing format at this breakpoint.**

**Inside SEALED.** Three possible fixes (all need unsealing):
- (A) **Extend the collapse threshold** from `991.98px` to a higher value (e.g. `1199.98px`) so Cardio rows go to stacked-card mode at 992-1199px. Simple, conservative; Cardio gets stacked-card UX on iPad-landscape and small laptops, which arguably IS the right call (those devices are touch-input territory).
- (B) **Shrink cells** to `minmax(56px, 1fr)` or even `minmax(48px, 1fr)` so the strip fits in 556px. Cardio: 10 × 48 + 9 × 6 = 534px. Tight but works. Trade-off: cells become numeric-only, no labels above (label `MAX HR %` is already truncated at 72px font 0.62rem).
- (C) **Hybrid:** keep cells at 72px on the 1fr column but mark the row `overflow-x: auto` with a horizontal scroll on JUST the cell strip. Owner has explicitly REJECTED horizontal scroll across all breakpoints — Option C is OFF the table.

**Recommendation: Option A** — extend the collapse threshold to ~1199px. Reason: at 992-1199px the user is most likely on a touch device or a small laptop where stacked cards are easier to type into anyway; the ~775px-required strip needs ~1280px to render comfortably regardless. The mobile-responsive contract owner laid out (plan_builder_ux.md §2.2: "We can not have the same view for laptop & pad, and mobile too") fits this.

### 1280px (laptop, the LTAT-screenshot baseline)

**Active rules:** desktop. Available cells column ≈ 844px. All three formats (Cardio 774px / Weights 306px / Agility 462px) fit. Layout matches `same-day-3-sessions.jpg`. **No issues predicted.**

---

## Summary table

| Viewport | Verdict | Issue | Sealed? |
|---|---|---|---|
| 375 px  | Mostly OK | Tap-targets 28-30px (need 44+) | YES |
| 568 px  | Mostly OK | Same as 375 (rules collapse identically) | YES |
| 768 px  | Mostly OK | Block-head row-2 has empty col-1 | YES |
| 992 px  | **Cardio overflow** | Cell strip needs 774px, has 556px | YES |
| 1280 px | OK         | None predicted             | n/a |

---

## What this means for Session 7's plan

**Every issue I can identify from static analysis is INSIDE the sealed CSS section.** Adding new rules below `SEALED-END` cannot fix any of these because the problems are in selectors and breakpoints already inside the seal — overriding them from below would either be ignored (specificity) or would only add new rules, not change the existing ones.

Therefore Session 7's mobile-responsive pass requires the **AI_AGENT_FRAMEWORK.md §5.3 unsealing protocol** before code can land. The protocol asks for:

1. **Why the file must change** — Cardio rows overflow at 992px (the most common laptop/iPad-landscape viewport); tap-targets are below 44px on phones; block-head awkwardness at 768px.
2. **What specifically will change** — see proposed diff below.
3. **Risks** — regressing the 1280px desktop layout that the owner approved as "perfect."
4. **Verification plan** — owner takes 5 fresh screenshots after the change at the same breakpoints; visual diff against the pre-change baseline screenshots.
5. **Wait for explicit owner approval** — quoted in the next commit message.

## Proposed unsealing diff (preview only — no code yet)

**Change 1 — Extend mobile-collapse threshold from `<992px` to `<1200px`** (fixes 992px Cardio overflow + improves iPad-landscape touch UX):

```css
/* Was:  @media (max-width: 991.98px) { ... } */
/*       @media (max-width: 575.98px) { ... } */
/* Becomes: */
@media (max-width: 1199.98px) { ... mobile-stacked-card rules ... }
@media (max-width: 575.98px)  { ... small-phone block-head stack ... }
```

**Change 2 — Block-head delete icon spans both rows at 768-1199px** (fixes the empty col-1 row-2):

```css
@media (max-width: 1199.98px) and (min-width: 576px) {
    .cf-block__head { grid-template-columns: 36px 1fr 1fr; }
    .cf-block__head-icon { grid-row: 1 / span 2; align-self: start; }
}
```

**Change 3 — Tap-target sizing on phones** (44×44 minimum):

```css
@media (max-width: 575.98px) {
    .cf-row__addremove button { width: 44px; height: 44px; font-size: 1.1rem; }
    .cf-block__head-icon { width: 40px; height: 40px; font-size: 1.05rem; }
    .cf-row__cat .form-select,
    .cf-row__sub .form-select { min-height: 44px; padding: 8px 10px; font-size: 0.95rem; }
}
```

**Total:** ~12-15 lines changed inside SEALED-BEGIN/END. Sealed-section rules removed/modified rather than additions; can't be done from below SEALED-END.

## What lands below SEALED-END (no unsealing needed)

A small **mobile-only utility class** for any non-grid CSS that's purely additive — nothing critical, but useful tweaks like:

```css
@media (max-width: 575.98px) {
    .cf-fundamentals { padding: var(--cf-gap-3); }   /* tighter padding on small phones */
    .cf-section__head h1 { font-size: 1.3rem; }      /* slightly smaller heading */
}
```

These do not touch the sealed selectors and can be added without unsealing.

---

## Owner ask

1. **Take 5 screenshots** of `/coach/plans/new` in Chrome DevTools mobile emulation at 375 / 568 / 768 / 992 / 1280 px. Save to `.ai/research-notes/screenshots/session7-{viewport}px.png`. The agent writes sibling `.md` notes once the PNGs land.
2. **Confirm the predicted issues above** match what you see on screen — or correct any I got wrong.
3. **Approve or reject** the three proposed unsealing changes (1, 2, 3 above). Each can be approved independently. The agent will commit with the approval quote in the message.

Standing by.
