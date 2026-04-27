# Handover — court-fitness Session 7 (Sprint 01 Session 6 — "Mobile-responsive pass + plans-index rebuild + Sprint 02 lock-ins")

**Framework version:** 1.0 (2026-04-17)
**Date:** 2026-04-27
**Sprint:** Sprint 01 — "Coach plans a week, player logs actuals, on a phone"
**Duration:** Single session, full owner-collaboration, ended by owner with "please call it off" after round 7c.
**Agent:** Claude (Anthropic, Opus 4.7, 1M-context)

---

## 1. Session Goal (as stated at start)

Per `.ai/.daily-docs/27 Apr 2026/prompt_for_session_7.md`: mobile-responsive pass over the now-sealed desktop Plan Builder view, baseline screenshots before code, then targeted fixes per breakpoint (375 / 568 / 768 / 992 / 1280 px). PWA + per-type validation + label prettifier as carry-forward lower priority.

**Outcome:** mobile-responsive pass landed in seven §5.3-unsealing rounds. PLUS a much bigger second deliverable that emerged mid-session — the **plans-index page rebuild** (filters, format chips, fixed-size cards, top-right "+ New Plan" CTA). PLUS the **Sprint 02 reservations lock-in** (trainer↔player association decision recorded in three places). PWA + validation + prettifier slipped to Session 8.

## 2. In Scope / Out of Scope (as evolved)

**In scope at start (per Session 7 prompt):**
- Mobile-responsive baseline at 5 breakpoints + targeted fixes via §5.3 unsealing.

**Added mid-session by owner directive:**
- Plans-index page rebuild (`/coach/plans` + `/player`) with filters, format chips, fixed cards, CTA layout.
- Three new seals on the plans-index views + CSS section.
- Sprint 02 reservations: trainer↔player association via Option 1 (invite code) + Option 4 (admin assignment) paired.
- Standalone-vs-institutional admin model clarified (1(b) confirmed as the chosen architecture for the admin role).

**Out of scope (intentionally deferred to Session 8):**
- PWA manifest + service worker.
- Per-type server-side `target_json` / `actual_json` shape validation.
- Prettier label prettifier in plan-builder.js for tooltips/summaries.
- Unit test for the player-update silent-drop of target fields.
- Empty-block edge case (last row removed → no [+] visible).
- Real start-times on Training Date (LTAT supports dd-mm-yyyy hh:mm; we store DATE only).
- Capitalisation sweep across all UI headings (apply opportunistically going forward, not retroactively).

## 3. What Was Done — eleven commits in order

```
1ce7b75  sprint-1: session 7 open — Framework Conformance Check committed
c745b60  sprint-1: session 7 baseline static analysis (pre-screenshot)
5906fa4  sprint-1: unseal + apply Fix 1, Fix 2, Fix 4 (owner-approved 2026-04-27)
9ccc8f8  sprint-1: unseal round 2 — single-row block head, cat+sub side by side, save btn smaller
e65d907  sprint-1: unseal round 3 — boundary 576 → 480 + defensive grid-item min-width
0ba5919  sprint-1: unseal round 4 — Target/Actual capitalised, colours differentiated, button label
a86d1f8  sprint-1: rebuild plans-index pages with filters, format chips, fixed cards
c478f59  sprint-1: seal plans-index views + lock Sprint 02 trainer-association decision
e6761ee  sprint-1: unseal round 7 — Back To My Plans into save bar with brand colour
edd5fa2  sprint-1: unseal round 7b — match Back button size to Save (both btn-sm)
c83435d  sprint-1: unseal round 7c — capitalise Save Changes / Save Plan
<close>  sprint-1: session 7 close — 7 artifacts (mobile-responsive pass + plans-index + Sprint 02 lock-in)
```

### 3.1 Conformance Check + static-analysis baseline (commits `1ce7b75`, `c745b60`)

- Read full 16-item mandatory list (HL-14 forbids the split). Conformance Check committed before any state-changing tool call. Quoted BRIEFING.md's "Who records actuals" paragraph verbatim and plan_builder_ux.md §2.2 verbatim. Listed all six sealed files at the time and confirmed §5.3 understanding.
- Static analysis baseline written at `c745b60` predicted the 992px Cardio overflow without screenshots — terminal-only dev box can't take real screenshots. Three proposed unsealing changes (Fix 1, 2, 3) preview'd in the doc.

### 3.2 Round 1 — Fix 1 + Fix 2 + Fix 4 (commit `5906fa4`)

After owner took 5 screenshots and confirmed the 992px Cardio overflow, owner approved (B): "Apply Fix 1 & Fix 2, skip Fix 3 — don't try to fix what's not broken."
- **Fix 1**: collapse threshold raised `<992` → `<1200`. iPad-landscape (1024) + small laptops (1080-1180) now get the stacked-card layout instead of an overflowing strip.
- **Fix 2**: block-head delete icon spans both wrap-rows at 768px so col 1 row 2 isn't visually empty.
- **Fix 4**: removed redundant per-block "+ Add exercise" button. Owner: "If this is correct then we can remove 'Add Exercise'." The row-level [+] does the same job and inserts in the middle when wanted.
- **Skipped Fix 3** (tap-targets ≥44px on phones) per owner's explicit "don't try to fix what's not broken."

### 3.3 Round 2 — single-row block head + cat+sub side-by-side + Save btn-sm (commit `9ccc8f8`)

After round-1 visual sign-off (which the owner discovered was actually showing CACHED round-1 CSS due to Chrome's HTTP cache):
- Block-head split into 576-1199 (4-col single-row) + 0-575 (stacked) ranges. Defensive `width: 100%` on form-controls inside the head's 1fr column to prevent the "tiny We format-box" Bootstrap quirk.
- Exercise row split into 576-1199 (3-col `auto 1fr 1fr` with `cat sub` side-by-side via grid-areas) + 0-575 (cat above sub stacked).
- Save button: `btn btn-primary btn-lg` → `btn btn-primary btn-sm`. Owner's "by 50%" feel.

### 3.4 Round 3 — boundary 576 → 480 + defensive min-width (commit `e65d907`)

Owner's directive after round-2 cache-issue diagnosis: "let's try the changed boundary to 480 or 540."
- Boundary lowered 576 → 480 in BOTH `.cf-row` and `.cf-block__head` rules so 568px (iPhone-SE landscape) gets the compact 4-col / cat+sub-side-by-side layout.
- Defensive `min-width: 0` on `.cf-block__head .cf-block__head-cell` to prevent CSS-grid's default `min-width: auto` from stopping the cell from shrinking inside the 1fr track.

### 3.5 Round 4 — Target/Actual capitalised + colours + button label (commit `0ba5919`)

Owner's directive: capitalise placeholders, differentiate colours, change button text.
- `tgt.placeholder` and `act.placeholder` capitalised: 'Target' / 'Actual'.
- Target colour: warm beige `#FBF1E5` / border `#E8D5BC`. Actual colour: soft green `#EDF5E8` / border `#B8D4B8`. Visibly different.
- Secondary-cell rule simplified to `opacity: 0.7` (was a flat `background: #F5F2EE` that flattened both target and actual to the same colour). Cardio's 7 secondary cells stay muted but stay differentiated.
- Button label '+ Add training date / session' → '+ Add Training Week / Date / Session'. Owner's UI convention: "Start with Capital Letter when not a sentence."
- Confirmed in chat (no code change): Target editable only by Coach, NOT by Player — already enforced both client-side (`tgt.readOnly = true` when `canEditTargets` false) and server-side (`Player\Plans::update` silently drops any target_<key>).

### 3.6 Plans-index rebuild — filters, chips, fixed cards (commit `a86d1f8`)

Owner directive: rebuild `/coach/plans` per `plans-view-page.png`. Three demands: fixed-size cards (long Training Target shouldn't break layout); format chips (max 3 per card showing which formats are scheduled); filter dropdowns at the top (Year, Week Of From, Week Of To, Player on coach side / Coach on player side).

- `Coach\Plans::index`: rewritten with GET filter handling. Defaults: year = current year, week_to = this Monday, week_from = this Monday minus 4 weeks, player = all assigned. SQL aggregates each plan's distinct format names (DISTINCT et.name from plan_entries+exercise_types) into a GROUP_CONCAT split into format_list[] for the view.
- `Player\Dashboard::index`: rewritten as the player's plans listing with filters mirror — Coach dropdown instead of Player dropdown.
- Views: `coach/plans/index.php` and `player/dashboard.php` both rewritten with filter form (4 inputs + Apply + Reset), card grid with format chips strip + fixed-grid card layout + truncating Target chip + Open-plan button always at bottom.
- CSS additions below SEALED-END (no unsealing): `.cf-filters` + responsive grid; `.cf-plan-card` overridden to `display: grid` with 4 rows so the Open-plan button always sits at the bottom regardless of head content; `.cf-plan-card__target-chip` max-width: 50% + ellipsis truncation + smaller font; `.cf-format-chip` colour variants matching LTAT's program-list chip palette (cardio green, weights amber, agility blue).

### 3.7 Round 6 — `+ New Plan` button to top-right + 3 new seals + Sprint 02 lock-in (commit `c478f59`)

Owner directive: "+ New Plan" CTA moves to the top-right of the page header per `new-plan.png`; remove the bottom-of-page CTA; capitalise per the convention "Capital Letter when not a sentence"; SEAL the plans-index view.

- `coach/plans/index.php`: header restructured with `cf-section__head--with-action` modifier; "+ New Plan" button on the right; bottom CTA removed; "My Plans" + "+ New Plan" capitalised.
- `cf-section__head--with-action` CSS rule added inside a new sealed block (`SEALED-BEGIN: Plans-index page styles` / `SEALED-END: Plans-index page styles`) bracketing the previously-unsealed plans-index CSS.
- Three new entries in SEALED_FILES.md: `coach/plans/index.php`, `player/dashboard.php`, the plans-index CSS section. Sealed file count grew **6 → 9**.
- **Sprint 02 lock-in:** owner approved my recommendation of pairing **Option 1** (Player Invite Code for standalone freelance trainers) with **Option 4** (Admin Assignment for institutional 1(b) setup-managers). Both flows write to the same `coach_player_assignments` table; only `assigned_by_user_id` distinguishes the source.
- New file: `.ai/sprints/sprint-02/sprint-plan.md` records four reservations (the association decision + admin role enum + trainer_invite_codes table + AuthFilter admin gating) and catalogues other Sprint 01 deferred items as Sprint 02 candidates.
- WIP.md gains a "Sprint 02 reservations" section at the top so any future agent reading WIP sees the lock-in immediately, before the Sprint 01 narrative.

### 3.8 Round 7, 7b, 7c — Back-into-save-bar + size match + Save Changes capitalisation (commits `e6761ee`, `edd5fa2`, `c83435d`)

Three small sealed-section rounds at the very end of the day per owner directives:
- **Round 7 (e6761ee):** `← Back To My Plans` moved INTO the sticky save bar to the LEFT of `Save Changes`, with brand-secondary outlined-orange colour. New `.cf-save-bar__actions` flex container groups the two buttons on the right while `N exercises` count stays on the left. Coach show + Player show views pass `$back_url` and `$back_label` to the partial; standalone bottom-of-page back-link removed.
- **Round 7b (edd5fa2):** Back button's classes changed from `cf-btn cf-btn--secondary` (~44px tall) to `btn btn-outline-primary btn-sm` (~30px) to match Save's `btn-sm` size. Both buttons now identical in height.
- **Round 7c (c83435d):** `Save changes` → `Save Changes`, `Save plan` → `Save Plan`, applying the round-6 capitalisation convention to the button labels.

## 4. Decisions Made

1. **Static-analysis-before-screenshot diagnosis is OK on a terminal-only box.** Standard HL-13 says "screenshots before code." But when the dev box can't run Chrome, the static-analysis prediction documented in `session_7_baseline.md` is the agent's substitute, with the explicit ask for owner to take the actual screenshots and validate the prediction. Worked cleanly: the 992px Cardio overflow was correctly predicted from the CSS math.
2. **§5.3 unsealing protocol applied seven times** (round 1, 2, 3, 4, 7, 7b, 7c) plus once for the round-6 SEAL itself. Each unseal commit quotes the owner's verbatim approval. No silent sealed-file edits.
3. **Boundary 480 over 540** for the cat-sub-side-by-side cutoff. 480 lets 568px (iPhone landscape) and any phone wider than ~iPhone-SE-portrait get the compact layout; 540 would have stayed too narrow. Testing-friendly threshold.
4. **Cell colour pair: warm beige Target + soft green Actual.** Tasteful contrast; brand-orange-aligned. Survives the secondary-cell muted style via opacity-only.
5. **Plans-index card grid uses `grid-template-rows: auto auto 1fr auto`** so the Open-plan button always sits at the bottom regardless of how long the Target chip text is. Simpler than min-height + max-content tricks.
6. **Format chip colours: cardio green / weights amber / agility blue.** Matches LTAT's `program-list-view.jpg` palette so users seeing both systems recognise the convention.
7. **Sprint 02 trainer↔player association: Option 1 + Option 4 paired.** Owner's choice, recorded in three places (sprint-02 plan + WIP top + this handover) so it can't slip through.
8. **The capitalisation convention "Capital Letter when not a sentence" applies opportunistically going forward.** I capitalised what I touched in Session 7 (My Plans, + New Plan, Save Changes / Save Plan, Back To My Plans, + Add Training Week / Date / Session). I did NOT do a retroactive sweep across the codebase — that's scope creep. Future agents who touch a heading/button label should apply the rule at that touch.

## 5. Sealed File Modifications

**Seven §5.3-protocol modifications + one new seal-additions commit, all owner-approved.** Detail in commit messages; summary:

| Commit | Sealed file(s) modified | Owner approval (verbatim, abridged) |
|---|---|---|
| `5906fa4` | court-fitness.css (Plan Builder section) | "Yes please go ahead with (B), Apply Fix 1 & Fix 2, skip Fix 3" |
| `9ccc8f8` | court-fitness.css (Plan Builder), _grid.php | "we can also squeeze in the first row, Date \| Afternoon \| Exercise also in one row" + "we can make the Save Changes button a little smaller" |
| `e65d907` | court-fitness.css (Plan Builder) | "lets try the changed boundary to 480 or 540" |
| `0ba5919` | plan-builder.js, court-fitness.css (Plan Builder), _grid.php | "Target & Actual placeholders... beginning with capital T & A" + "Target and Actual boxes must be differently coloured" + "+ Add Training Week / Date / Session" |
| `c478f59` | court-fitness.css (NEW Plans-index seal) | "Once done, this view should be sealed" — 3 new entries added to SEALED_FILES.md |
| `e6761ee` | _grid.php, court-fitness.css (Plan Builder) | "The button 'Back To My Plans' has been moved... Please give a color" |
| `edd5fa2` | _grid.php | "Please make both the buttons in same size" |
| `c83435d` | _grid.php | "Save Changes - C needs to be Capitalized" |

**Zero unauthorised sealed-file edits.** All modifications include the owner's quote in the commit message per §5.3.

## 6. Test Evidence

```
$ ./vendor/bin/phpunit tests/unit/
PHPUnit 11.5.55 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.2.12
Configuration: C:\xampp\htdocs\court-fitness\phpunit.xml.dist
............................                                      28 / 28 (100%)
Time: 00:00.240, Memory: 14.00 MB
OK, but there were issues!
Tests: 28, Assertions: 63, PHPUnit Warnings: 1 (no code coverage driver — harmless).
```

Test count invariant: **28/28** — no tests added or removed in Session 7. Test surface (controllers + helpers) was extended (Coach\Plans::index gained filter handling, Player\Dashboard::index rewritten) but tests for that are deferred to Session 8 along with PWA and shape validation.

## 7. Build Evidence

No build step (PHP + static assets). Dev server (`php spark serve --port 8080`) running throughout for curl smoke tests; killed at session close.

## 8. Files Modified / Created

### New this session
```
.ai/.daily-docs/27 Apr 2026/session_7_conformance.md       (session-open artifact)
.ai/.daily-docs/27 Apr 2026/session_7_baseline.md          (static-analysis baseline)
.ai/.daily-docs/27 Apr 2026/session_7_handover.md          (this file)
.ai/.daily-docs/27 Apr 2026/prompt_for_session_8.md        (next-session kickoff)
.ai/sprints/sprint-02/sprint-plan.md                        (Sprint 02 reservations + locks)
.ai/research-notes/screenshots/session7-{375,568,768,992}px.png         (5 baseline + 4 round-2 + 1 plans-view)
.ai/research-notes/screenshots/session7-after-{375,568,768,992,1280}px.png
.ai/research-notes/screenshots/session7-round2-{375,568,768,992}px.png
.ai/research-notes/screenshots/new-plan.png
.ai/research-notes/screenshots/back-to-my-plans.png
.ai/research-notes/screenshots/plans-view-page.png
```

### Modified this session
```
.ai/core/SEALED_FILES.md                  (3 new seal entries; sealed count 6 → 9)
.ai/core/WIP.md                           (Sprint 02 reservations top section + Session 7 close state)
.ai/core/SESSION_LOG.md                   (Session 7 row appended)
.ai/core/exercise_json_shapes.md          (round 4 — minor consistency)
app/Controllers/Coach/Plans.php           (::index filter handling)
app/Controllers/Player/Dashboard.php      (rewritten as plans listing with filters)
app/Views/coach/plans/_grid.php           (sealed; 5 §5.3 rounds — placeholder + button + back-link + capitalisation)
app/Views/coach/plans/index.php           (rebuilt; new seal)
app/Views/coach/plans/show.php            ($back_url passed)
app/Views/player/dashboard.php            (rebuilt; new seal)
app/Views/player/plans/show.php           ($back_url passed)
public/assets/css/court-fitness.css       (sealed; 4 §5.3 rounds + new sealed block for plans-index)
public/assets/js/plan-builder.js          (sealed; round 4 placeholder change)
```

### Commits this session (12 in order)
```
1ce7b75  sprint-1: session 7 open — Framework Conformance Check committed
c745b60  sprint-1: session 7 baseline static analysis (pre-screenshot)
5906fa4  sprint-1: unseal + apply Fix 1, Fix 2, Fix 4 (owner-approved 2026-04-27)
9ccc8f8  sprint-1: unseal round 2 — single-row block head, cat+sub side by side, save btn smaller
e65d907  sprint-1: unseal round 3 — boundary 576 → 480 + defensive grid-item min-width
0ba5919  sprint-1: unseal round 4 — Target/Actual capitalised, colours differentiated, button label
a86d1f8  sprint-1: rebuild plans-index pages with filters, format chips, fixed cards
c478f59  sprint-1: seal plans-index views + lock Sprint 02 trainer-association decision
e6761ee  sprint-1: unseal round 7 — Back To My Plans into save bar with brand colour
edd5fa2  sprint-1: unseal round 7b — match Back button size to Save (both btn-sm)
c83435d  sprint-1: unseal round 7c — capitalise Save Changes / Save Plan
<close>  sprint-1: session 7 close — 7 artifacts
```

## 9. Open Issues / Unfinished Work

**Visual sign-off pending owner.** The round 7 / 7b / 7c changes (Back-into-save-bar + size match + capitalisation) shipped at the very end of the day; owner asked to call it off without taking final screenshots. Tomorrow morning: a quick hard-refresh on `/coach/plans/Y2Y6MQ` to confirm the save bar layout matches the `back-to-my-plans.png` annotation. If anything's off, Session 8's first item is the fix.

**No fresh validation screenshots for the plans-index rebuild at all 5 breakpoints.** Owner viewed it but didn't save a full set. Session 8 should ask for those screenshots before declaring Sprint 01 close-ready.

## 10. Follow-Ups Noticed (NOT done this session)

See `.ai/sprints/sprint-02/sprint-plan.md` § "Other Sprint 02 candidates" for the full list. Top items:

- **PWA manifest + service worker** — Session 8 priority.
- **Per-type validation of `target_json` / `actual_json`** — Session 8 hardening.
- **Prettier label prettifier in plan-builder.js** — Session 8 polish.
- **Empty-block edge case** — when a coach removes the last row from a block via `[-]`, no `+` exists to add one back. Sprint 02.
- **Capitalisation sweep** — owner's "Capital Letter when not a sentence" convention applies retroactively if/when an agent feels like it; not blocking.
- **Real start-times on Training Date** (DATETIME instead of DATE) — Sprint 02.
- **No-clobber unit test for player-side target silent-drop** — currently behaviour is enforced; not yet unit-tested. Sprint 02.

## 11. Known Errors Added or Updated

**None new.** `.ai/core/KNOWN_ERRORS.md` is still template-only — no court-fitness bugs have been opened in this session's work. The browser-cache-served-stale-CSS quirk that confused round-2 screenshots is an environmental issue (Chrome's HTTP cache aggression), not a court-fitness bug.

## 12. Hard Lessons Added

**None new.** Session 7 was a smooth iterative-polish session against owner-led directives. The HL-13 + HL-14 standing rules were respected throughout (owner-shared screenshots saved + sibling notes per binary-artifact rule; full mandatory list read; no fresh-vs-returning split). The browser-cache surprise was annoying but didn't rise to lesson-status — Chrome's caching is just a fact of life and a hard-refresh is the standing fix.

## 13. Next Session

See `.ai/.daily-docs/27 Apr 2026/prompt_for_session_8.md`. Session 8 focus: PWA manifest + service worker, per-type `target_json` / `actual_json` shape validation, prettier label prettifier, plus any visual touch-ups needed from owner's morning hard-refresh check. **Sprint 01 is close to done** — if PWA + validation land cleanly in Session 8, Sprint 01 closes with sprint-close artefacts (seal candidates review per §12 + archival check per §11).

## 14. Framework Conformance Declaration

I, Claude (Opus 4.7, 1M-context), followed `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` v1.0 for this session.

I did not:
- Modify the sealed framework file.
- Use destructive git operations (no `--amend`, no `--force`, no `reset --hard`, no `--no-verify`).
- Delete or disable tests. Count stayed at 28/28.
- Modify any sealed file without explicit owner approval — every sealed-file edit is bracketed by a §5.3 commit-message quote of the owner's chat directive that authorised it.
- Expand scope beyond what the owner directed. The plans-index rebuild + Sprint 02 lock-in were owner-asked, not agent-initiated.

### All seven session-close artifacts (per CLAUDE.md §6.2)

1. ✅ `.ai/core/WIP.md` updated — Session 7 close state at the top + Sprint 02 reservations section preserved.
2. ✅ `.ai/core/SESSION_LOG.md` — Session 7 row appended.
3. ✅ This file — `.ai/.daily-docs/27 Apr 2026/session_7_handover.md` — with framework version stamp at top per §6.2.
4. ✅ `.ai/.daily-docs/27 Apr 2026/prompt_for_session_8.md` — next-session kickoff.
5. ✅ `.ai/core/HARD_LESSONS.md` — reviewed; no new HL needed (no surprising discoveries; HL-13 + HL-14 respected).
6. ✅ Meaningful git commits — 12 this session, one per logical unit + this close commit.
7. ✅ Memory-to-repo promotion — Sprint 02 reservations are the load-bearing memory of the session, captured in three repo locations (sprint-02 plan + WIP top + this handover). Owner verbatim quoted in all three.

Plus the session-open artifacts:
- ✅ `.ai/.daily-docs/27 Apr 2026/session_7_conformance.md` — committed as `1ce7b75` before any state-changing work.
- ✅ `.ai/.daily-docs/27 Apr 2026/session_7_baseline.md` — committed as `c745b60` before any sealed-section edit.

**Session closed cleanly. Sealed file count 6 → 9. Sprint 02 reservations locked in three places. Awaiting owner's hard-refresh confirmation tomorrow morning.**
