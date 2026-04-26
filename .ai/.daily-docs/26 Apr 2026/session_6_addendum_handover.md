# Handover Addendum — court-fitness Session 6 (post-close iteration, 2026-04-26)

**Framework version:** 1.0 (2026-04-17)
**Date:** 2026-04-26 (same calendar day as Session 6 close)
**Sprint:** Sprint 01
**Agent:** Claude (Anthropic, Opus 4.7, 1M-context) — same conversation as Session 6 main handover.

---

## Why this addendum exists

Session 6 closed cleanly at commit `310c07f` with all seven artifacts in place. The closing handover noted: "Visual sign-off (DevTools at 375/768/992/1280 + screenshots) deferred to owner — terminal-only dev box can't run Chrome. Code-wise the session is closed and unblocked."

The owner then ran the visual sign-off **in the same calendar day**, immediately after close. The browser eyeball turned up real issues — the inline-grid view that I shipped was still wrong (cells stacked vertically; modal-block separation; missing the dense LTAT cell strip; wrong cascade pattern). The owner re-shared the LTAT screenshots that had been on disk since this morning but without sibling `.md` notes (HL-13 cure was incomplete). I diagnosed, asked three disambiguation questions, the owner answered each, and we iterated four more commits until the owner approved at:

> "I have checked the Exercise Builder (http://localhost:8080/coach/plans/new). This is as perfect as I would have expected. Please seal this view for the Web."

So this addendum documents what happened **between** Session 6's close commit (`310c07f`) and now. It is NOT a new session (no fresh Conformance Check was run; the conversation was continuous with the owner driving each iteration). It IS a meaningful chunk of work that needs its own paper trail so a future agent reading the project chronologically understands why the sealed view exists and which commits produced it.

## Five post-close commits

```
0ed4928  sprint-1: seal Plan Builder view per owner directive 2026-04-26
d7664eb  sprint-1: explicit Format→Category→Sub-cat cascade + move Notes to bottom
f9cc676  sprint-1: rebuild grid to LTAT cell layout (10/4/6 cells, dense rows, no h-scroll)
0da18d5  docs: sibling .md notes for the 5 LTAT screenshots (HL-13 / §6.2 binary-artifact rule)
310c07f  sprint-1: session 6 close — 7 artifacts (Plan Builder rebuilt, no-clobber tested) [the close commit]
```

Read top-to-bottom: close → screenshots-finally-have-notes → rebuild-cells-per-LTAT → cascade-and-Notes-relocation → owner-approved-and-sealed.

## What changed in each commit

### `0da18d5` — Sibling `.md` notes for screenshots (HL-13 follow-through)
Owner had saved the 5 LTAT screenshots to `.ai/research-notes/screenshots/` first thing, but the sibling `.md` notes the new CLAUDE.md §6.2 artifact #7 rule mandates were never written. Five notes added retroactively, one per image:
- `same-day-3-sessions.md` — the canonical Plan Builder layout transcription (compound row + N exercise rows; cell strip per format).
- `creating-training-session.md` — Plan Builder when first opened; flags the time-component on Training Date as a Sprint 02 candidate.
- `program-list-view.md` — the post-save table view; flags the LTAT-style table-with-filters as a Sprint 02 candidate (court-fitness ships card-grid in Sprint 01).
- `week-picker.md` — Mondays-only calendar dropdown; flags UI-level greying as Sprint 02 polish.
- `date-picker.md` — Training-Date picker greys out non-Weekof days; flags the schema question (DATE vs DATETIME for `plan_entries.training_date`).

### `f9cc676` — LTAT cell layout (10/4/6 cells)
Owner answered the cell-count question:
- Cardio = 10 cells: Target / Max HR / Duration / Sets / Reps / Work / Set Rest / Set / Rep / Weight.
- Weights = 4 cells: Weight / Sets / Reps / Set Rest.
- Agility = 6 cells: Sets / Reps / Works / Rests / Total Work / Work Rest.

All cells are typable on every row; the LTAT screenshot's greying is presentational only, not a `disabled` attribute. Owner explicitly clarified: "they appear grey but are not disabled, they are typable."

`CELLS_BY_FORMAT` in `plan-builder.js` rewritten to match. Cardio's 7 less-common cells get `secondary: true` → a faint visual style (`.cf-cell--secondary` class) but stay typable. CSS rewritten as a dense single-line row (~44px tall, was 80-100px) with `grid-auto-flow: column` for the cell strip. Mobile collapse threshold raised to <992px (was <768px) per owner's Option A. **No horizontal scroll at any breakpoint** — owner overruled LTAT's dev team on that.

### `d7664eb` — Cascade + Notes relocation
Two owner directives in one commit:

1. **Notes textarea moved from top to bottom.** The fundamentals strip on top now contains only the four required fields (Player, Weekof, Training Target, Weight Format). Notes appears below the blocks, just above the save bar, in `mode='new'` only.

2. **Explicit Format → Category → Sub-category cascade.** Replaced the single `<optgroup>`-grouped sub-category dropdown with TWO dropdowns at the row level: Category ▼ then Sub-category ▼. Category options are filtered by the block's Format; Sub-category options are filtered by the row's chosen Category. Sub-category dropdown stays disabled until Category is picked. The auto-derived `cf-row__catlabel` text element was removed (the Category dropdown's selected value already shows the name).

Owner reason: "the dropdown will become too long" — particularly for Agility where flattening all sub-categories under the format would produce an unwieldy single list. The explicit cascade keeps each dropdown short.

### `0ed4928` — SEAL
Owner authorization in chat (verbatim):
> "I have checked the Exercise Builder (http://localhost:8080/coach/plans/new). This is as perfect as I would have expected. Please seal this view for the Web, No future AI Agent should change this view unless I authorize it."

Five seal entries added to `.ai/core/SEALED_FILES.md`:
1. `app/Views/coach/plans/_grid.php` — the shared inline-grid partial.
2. `app/Views/coach/plans/new.php` — the new-mode mount stub.
3. `public/assets/js/plan-builder.js` — the inline-grid controller logic + `CELLS_BY_FORMAT`.
4. `public/assets/css/court-fitness.css` — only the section between explicit `SEALED-BEGIN: Plan Builder + show inline-grid styles` and `SEALED-END:` comment markers (lines ~371-675). Other sections remain unsealed.
5. `.ai/core/plan_builder_ux.md` — the canonical UX spec, sealed alongside the implementation.

`SEALED-BEGIN` / `SEALED-END` comment markers added to the CSS file itself so an agent grep-ing the file hits the warning before editing.

Watchlist updated: IdObfuscator removed (shipped Session 5); `PlanEntriesModel::decideActualUpdate`, the two `show.php` mount stubs, and the two `::update` controller actions added as Sprint 01 close candidates.

## What this means for the Plan Builder

**The Plan Builder UI is now FROZEN for desktop.** Mobile responsive collapse is in scope (Option A, <992px → stacked card per exercise) but the underlying LAYOUT is fixed:

- Compound row per Training Date (date | session | coach | format).
- Multiple exercise rows per block, each with: `[+]` add | `[−]` remove | Category ▼ | Sub-category ▼ | cell strip.
- Cell counts: Cardio 10 / Weights 4 / Agility 6, all typable.
- No horizontal scroll. Ever.
- Notes textarea at the bottom, optional.

Any future change to this contract requires owner approval per `SEALED_FILES.md` and `AI_AGENT_FRAMEWORK.md` §5.3 unsealing protocol.

## Test status at end-of-day

```
$ ./vendor/bin/phpunit tests/unit/
28 tests, 63 assertions, all pass.
```

Same as Session 6 close. The five post-close commits did NOT change controllers, models, routes, or any test surface — only views, JS, CSS, and docs.

## Owner's call-off + tomorrow's mandate

Verbatim from the owner this evening:

> "This design should be our base, and we should build the mobile view which should dynamically adjust this web view, as per the device size. I am not feeling too well now, hence I must take a break. Could you please prepare to call off the session by updating appropriate files and creating the prompt for tomorrow mornings session?"

The mobile view is Session 7's mandate. Existing `<992px` collapse already does some of this work, but the owner wants a formal pass at all four breakpoints (375 / 568 / 768 / 992 / 1280 px) to confirm the CSS handles them gracefully. **Crucially: the desktop layout is sealed, so mobile-responsive work must NOT modify the SEALED-BEGIN/SEALED-END section of `court-fitness.css`.** Mobile-specific rules go in a NEW section below SEALED-END, or the sealed section is unsealed first via the §5.3 protocol.

## Cross-references for tomorrow's agent

- `.ai/core/SEALED_FILES.md` — read first; the seal is real and the protocol is binding.
- `.ai/.daily-docs/27 Apr 2026/prompt_for_session_7.md` — tomorrow's kickoff (mobile-responsive adaptation).
- `.ai/research-notes/screenshots/same-day-3-sessions.md` — the canonical desktop reference; mobile must compress THIS without breaking it.
- `.ai/core/plan_builder_ux.md` §2.2 — the responsive directive verbatim from the owner: "We can not have the same view for laptop & pad, and mobile too. Right now it seems that everything is about mobile view, which is important, but it has to be handled dynamically."
- `.ai/core/HARD_LESSONS.md` HL-13, HL-14 — still the most active lessons.

## Framework conformance notes

- **No new Conformance Check was run for this addendum work.** It was a continuous owner-driven iteration immediately after Session 6's close, in the same conversation. The agent did NOT change scope unilaterally — every commit responded to a specific owner instruction in chat. Per the spirit of Rule 7 (stay in scope) and Rule 9 (when in doubt, ask), the iterations qualify as "in scope" because the owner was explicitly directing each one.
- **No sealed file was modified** during the addendum work; the seal commit `0ed4928` is the FIRST modification of `SEALED_FILES.md` since Sprint 0 Session 1 and was itself owner-authorized.
- **Test count went UP**: 23 → 28 (in Session 6's close commit) and stayed at 28 through the addendum. Never went down.
- **No destructive git operations.** No `--amend`, no `--force`, no `--no-verify`.

## Final state at end of day

- Working tree: **clean** (will be after the close commit lands).
- Branch: `rajat`.
- Last commit: `0ed4928` (the seal). This addendum + WIP + SESSION_LOG + tomorrow's prompt go into the next commit.
- Test suite: 28/28 passing.
- Sealed files: 6 (was 1 — added 5 today).
- Plan Builder + Coach show + Player show — owner-approved.
- Visual screenshot evidence at four breakpoints — STILL deferred to owner; tomorrow's first task is to grab them while the desktop view is fresh in mind.

Session 7 is the mobile-responsive pass. See the new prompt for the priority list.
