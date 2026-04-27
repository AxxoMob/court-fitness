# Sealed Files

Files listed here may be READ by any agent but MAY NOT be MODIFIED without explicit owner (Rajat) approval in the current session. To modify a sealed file, follow the unsealing protocol in `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` Section 5.3.

Modifying a sealed file without owner approval is a **critical framework violation**.

---

## .ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md

- **Sealed by:** Owner (Rajat Kapoor)
- **Sealed on:** 2026-04-22 (Sprint 0 Session 1)
- **Reason:** This is the portfolio-wide operating constitution authored by the owner. The framework's own Preamble and Section 0 declare it read-only for agents — sealing it here enforces that at the project level. Any agent found modifying this file will be considered non-diligent and their session repudiated.
- **Unsealing requires:** Owner-only. No agent may unseal or propose changes. If an agent believes a framework rule is wrong or missing, they raise the observation in the session handover under "Follow-Ups Noticed" and let the owner decide whether to publish a new version (v1.1, v1.2, ...).
- **Related sessions/KEs:** Sprint 0 Session 1 (2026-04-22).

---

## app/Views/coach/plans/_grid.php

- **Sealed by:** Owner (Rajat Kapoor) — explicit chat directive 2026-04-26 ("Please seal this view for the Web, No future AI Agent should change this view unless I authorize it"), after browsing `/coach/plans/new` and approving the Plan Builder UI as "perfect as I would have expected."
- **Sealed on:** 2026-04-26 (Sprint 01 Session 6 post-close iteration)
- **Reason:** The shared partial that produces the Plan Builder grid AND the Coach + Player editable show grids (mounted via mode flag from `new.php`, `coach/plans/show.php`, `player/plans/show.php`). It took five iterations across Sessions 1, 5, 6, and 6-post-close to arrive at this layout — each prior version was wrong because the LTAT screenshots weren't on disk (HL-13). This file is the final UI contract; modifying it without owner approval risks regressing the layout the owner specifically designed with his developers team.
- **Unsealing requires:** Owner approval in chat + reason logged in next commit. Per AI_AGENT_FRAMEWORK.md §5.3, the agent must (a) state why the file must change, (b) state what specifically will change, (c) state risks, (d) state how the change will be verified, and (e) wait for explicit owner approval before editing.
- **Related sessions/KEs:** Sprint 01 Session 6 (2026-04-26 — d7664eb cascade commit was the last unsealed edit); HL-13 (the rebuild trigger); `.ai/core/plan_builder_ux.md` (the spec it implements).

---

## app/Views/coach/plans/new.php

- **Sealed by:** Owner (Rajat Kapoor) — same chat directive 2026-04-26.
- **Sealed on:** 2026-04-26 (Sprint 01 Session 6 post-close iteration)
- **Reason:** Mounts `_grid.php` in `mode='new'` with `$action_url = /coach/plans` (POST = store). Tiny but load-bearing: changing the mode value, the action URL, or removing the layout extension would break the Plan Builder. Sealed alongside `_grid.php` because the two files form one contract.
- **Unsealing requires:** Owner approval in chat. Same protocol as `_grid.php` (AI_AGENT_FRAMEWORK.md §5.3).
- **Related sessions/KEs:** Sprint 01 Session 6 (2026-04-26).

---

## public/assets/js/plan-builder.js

- **Sealed by:** Owner (Rajat Kapoor) — same chat directive 2026-04-26.
- **Sealed on:** 2026-04-26 (Sprint 01 Session 6 post-close iteration)
- **Reason:** The inline-grid controller — drives every interactive behaviour of the Plan Builder + show views. The `CELLS_BY_FORMAT` table (Cardio 10 / Weights 4 / Agility 6 cells, owner-confirmed against `same-day-3-sessions.jpg`), the explicit Format → Category → Sub-category cascade, the row-level add/remove logic, the no-clobber-friendly serialisation, and the audit display formatter all live here. A single accidental rename of a key (e.g. `set_rest_sec` → `rest_sec`) silently corrupts every saved entry. Sealing prevents drift.
- **Unsealing requires:** Owner approval in chat. Same protocol as `_grid.php`. Note: adding a NEW exercise type (Sprint 03+ kernel work) WILL require unsealing — the protocol is followed at that time.
- **Related sessions/KEs:** Sprint 01 Session 6 (2026-04-26 — `f9cc676` LTAT cell rebuild + `d7664eb` cascade); `.ai/core/exercise_json_shapes.md` (the JSON contract this file produces).

---

## public/assets/css/court-fitness.css — section between SEALED-BEGIN and SEALED-END markers

- **Sealed by:** Owner (Rajat Kapoor) — same chat directive 2026-04-26.
- **Sealed on:** 2026-04-26 (Sprint 01 Session 6 post-close iteration)
- **Reason:** The inline-grid CSS section is the visual implementation of the Plan Builder UI the owner approved. Density, column widths, secondary-cell muting, dense-row heights, and the <992px collapse breakpoint were tuned over multiple iterations. Modifying the CSS could regress the layout even if the HTML/JS stay intact. The seal scope is delimited by `SEALED-BEGIN: Plan Builder + show inline-grid styles` and `SEALED-END: Plan Builder + show inline-grid styles` comment markers in the file (currently lines ~371 and ~675); those markers are themselves part of the seal — do not move them. **Only this section is sealed**; other sections of `court-fitness.css` (Header, Main, Cards, Plan-cards, Buttons, etc.) remain unsealed and editable under normal review.
- **Unsealing requires:** Owner approval in chat. Same protocol as `_grid.php`. Edits to OTHER (non-bracketed) sections of `court-fitness.css` do not require unsealing — only changes between the SEALED markers.
- **Related sessions/KEs:** Sprint 01 Session 6 (2026-04-26).

---

## .ai/core/plan_builder_ux.md

- **Sealed by:** Owner (Rajat Kapoor) — implicit (the doc itself was already locked 2026-04-23 per its header; explicit chat seal 2026-04-26 covers the implementation files; this entry seals the spec doc to keep spec and implementation aligned).
- **Sealed on:** 2026-04-26 (Sprint 01 Session 6 post-close iteration)
- **Reason:** The canonical UX spec for the Plan Builder. Its §1 (LTAT screen transcription) and §2 (owner's verbatim a-d answers) were the load-bearing inputs for the implementation. Editing the spec and the implementation independently is exactly how doc/code drift starts (see HL-2). Sealing both keeps them in lock-step. Note: the spec doc may need an addendum in a future session to add (e.g.) the new Format type for Sprint 03's testing kernel — that's a deliberate unsealing event, not casual cleanup.
- **Unsealing requires:** Owner approval in chat + reason logged + a corresponding implementation update committed in the same session OR an explicit "spec lands first, code follows next session" plan from the owner.
- **Related sessions/KEs:** Sprint 01 Session 5 (initial lock 2026-04-23); Sprint 01 Session 6 (implementation arrival 2026-04-26); HL-13.

---

## app/Views/coach/plans/index.php

- **Sealed by:** Owner (Rajat Kapoor) — explicit chat directive 2026-04-27 round 6 ("Once done, this view should be sealed. Or at least that part of the code must be sealed and should not be changed by any incoming AI Agent unless specified to do so"), after visual sign-off on the rebuilt plans-index page (filters, format chips, fixed cards, "+ New Plan" button at top-right).
- **Sealed on:** 2026-04-27 (Sprint 01 Session 7)
- **Reason:** The coach's plans listing — top-of-app for any returning coach. Five iterations of changes during Session 7 to land the layout the owner approved. Filter strip (Year / Week Of From-To / Player), fixed-size cards with format chips and ellipsis-truncated Target chip, "+ New Plan" button in the header, no bottom-of-page CTA, capitalised heading text per the convention "Start with Capital Letter when not a sentence." Sealed alongside the player mirror (`player/dashboard.php`) and the plans-index CSS section as one contract.
- **Unsealing requires:** Owner approval in chat per AI_AGENT_FRAMEWORK.md §5.3.
- **Related sessions/KEs:** Sprint 01 Session 7 (2026-04-27 — `a86d1f8` rebuild + this commit's button-move + cap).

---

## app/Views/player/dashboard.php

- **Sealed by:** Owner (Rajat Kapoor) — same chat directive 2026-04-27 round 6.
- **Sealed on:** 2026-04-27 (Sprint 01 Session 7)
- **Reason:** The player's plan listing — mirrors `coach/plans/index.php` with a Coach dropdown instead of Player. Same fixed-card / format-chip / filter pattern. Sealed together with the coach view because they share visual contract; an unsealed change to one would create asymmetry.
- **Unsealing requires:** Owner approval in chat per AI_AGENT_FRAMEWORK.md §5.3.
- **Related sessions/KEs:** Sprint 01 Session 7.

---

## public/assets/css/court-fitness.css — section between SEALED-BEGIN: Plans-index page styles and SEALED-END: Plans-index page styles

- **Sealed by:** Owner (Rajat Kapoor) — same chat directive 2026-04-27 round 6.
- **Sealed on:** 2026-04-27 (Sprint 01 Session 7)
- **Reason:** The CSS rules implementing the plans-index visuals — `.cf-filters`, `.cf-plan-card` (fixed-grid override), `.cf-plan-card__chips`, `.cf-format-chip` colour variants (cardio green / weights amber / agility blue), `.cf-section__head--with-action`. Distinct sealed section from the Plan-Builder one earlier in the file; bracketed by its own `SEALED-BEGIN: Plans-index page styles` / `SEALED-END: Plans-index page styles` comment markers. **Only this section is sealed**; sections before it (Header, Cards, Plan Builder sealed-block, etc.) and any future sections after the SEALED-END marker remain editable under normal review.
- **Unsealing requires:** Owner approval in chat per AI_AGENT_FRAMEWORK.md §5.3. Edits to OTHER (non-bracketed) sections of `court-fitness.css` do not require unsealing.
- **Related sessions/KEs:** Sprint 01 Session 7.

---

## How to propose sealing a new file

When an agent notices a file that has become load-bearing, fragile, or has been the source of multiple regressions, they may propose sealing it. The agent drafts an entry matching the schema above and asks Rajat in chat to confirm. Rajat decides; only Rajat may add new seals.

## Seal-candidates review cadence (per `CLAUDE.md` §12)

At every **sprint close**, the closing agent reviews code written during that sprint and lists seal candidates in the sprint handover under a "Seal Candidates for Owner Review" section. Owner confirms which, if any, to add here.

Opportunistic mid-sprint sealing is also welcome via the same mechanism: draft the entry, propose to owner in chat, wait for explicit approval.

## Candidates currently being watched

- `app/Services/JwtValidator.php` — BUILT Session 2, tested 10/10. Identity boundary to HitCourt. Bug here silently grants or denies access. Propose sealing at Sprint 01 close once it's run in at least a few real sessions.
- `app/Support/IdObfuscator.php` — built Session 5, 9 unit tests. URL pattern helper; if broken, plan URLs break globally. Propose sealing at Sprint 01 close.
- Seed migrations for `exercise_types`, `fitness_categories`, `fitness_subcategories` — ran Session 3 with 3+12+204 rows. Arbitrary edits to names would invalidate existing `plan_entries` FK references and break every saved plan. Propose sealing at Sprint 01 close.
- `app/Controllers/Sso.php` — BUILT Session 3. The SSO handoff logic. Revisit at Sprint 02 once it's seen real HitCourt traffic.
- `app/Models/PlanEntriesModel.php::decideActualUpdate` — pure-function home of the no-clobber rule (Session 6, 5 unit tests). The whole model file is more than just this method, so sealing the whole file is heavy-handed; sealing just the static method is cleaner. Propose at Sprint 01 close: either seal the method (via comment markers) or split it into a dedicated `App\Support\PlanEntryActualUpdate` class and seal that.
- `app/Views/coach/plans/show.php` + `app/Views/player/plans/show.php` — both mount the now-sealed `_grid.php`. The mount stubs themselves are tiny (5-10 lines each) and load-bearing; consider sealing alongside `_grid.php` at Sprint 01 close if no further changes land.
- `app/Controllers/Coach/Plans.php::update` + `app/Controllers/Player/Plans.php::update` — the in-place save actions. Risk: target/actual silently mis-routed = data corruption. Propose sealing at Sprint 01 close.

Revisit at every sprint close.
