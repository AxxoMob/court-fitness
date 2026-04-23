# court-fitness — Session 6 Kickoff Prompt (REBUILD)

> **Paste this entire file as the first message to the Session 6 agent in a FRESH Claude conversation.**
> The §3.2 re-entry path does NOT apply. You are a new Claude with a blank whiteboard.
>
> **Session 5 built the wrong UI. Session 6 rebuilds it correctly.** Do not defend or preserve what Session 5 shipped in the views; the backend is fine but the Plan Builder view and JS are getting replaced with an inline grid.

---

## Session Onboarding — court-fitness (Session 6, Sprint 01 Session 5 — "Rebuild: inline grid + actuals for both parties")

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at `C:\xampp\htdocs\court-fitness` on Windows 11 XAMPP. Production URL: `https://fitness.hitcourt.com`.

You are a **FRESH AGENT** in a new conversation. Follow the full reading list in `CLAUDE.md §3.1` (13 items). Then commit your Conformance Check to `.ai/.daily-docs/{today}/session_6_conformance.md` per CLAUDE.md §3.3 BEFORE requesting owner "proceed."

### 14th mandatory read, Session 6 only

After the §3.1 13 items, you MUST also read `.ai/core/plan_builder_ux.md` — this is the **canonical LOCKED UX** for every screen you are about to build or rebuild. It was written at Session 5 close after the owner had to re-explain design decisions multiple times. If you skip it, you will build the wrong thing again and the owner will escalate. The file transcribes the live LTAT screen, captures owner's verbatim answers from the 2026-04-23 feedback session, and lists every file Session 5 got wrong with its correct replacement.

### Additionally required before any code

In your Conformance Check, **quote verbatim** from:
- `.ai/core/BRIEFING.md` — the sentences about what coach does and what player does (one of those sentences says both can record actuals; if you can't find it, reread)
- `.ai/core/ltat-fitness-findings.md §2` — the step-by-step live workflow description
- `.ai/core/plan_builder_ux.md §2` — owner's locked answers (a), (b), (c), (d)

Paraphrasing is not acceptable. Quote, cite line numbers, prove you read each line. The Conformance Check's 8 questions were updated after Session 5 to catch the skimmer failure that caused this rebuild — see HL-13.

---

## What Session 5 shipped — what is correct and what is wrong

### Correct (DO NOT TOUCH)

- Backend models: `App\Models\TrainingPlansModel`, `App\Models\PlanEntriesModel`. Keep.
- Backend controllers: `App\Controllers\Coach\Plans::{index, new, store, show}`, `App\Controllers\Coach\Players::index`, `App\Controllers\Player\Plans::show`, `App\Controllers\Coach\Dashboard`, `App\Controllers\Player\Dashboard`. Keep structure; the show methods will gain a POST sibling.
- Routes: keep the existing route groups; add `POST /coach/plans/{obf}` and `POST /player/plans/{obf}` for in-place actuals/target updates.
- `App\Filters\AuthFilter` + global registration. Keep.
- `App\Support\IdObfuscator` + 9 tests. Keep.
- CSRF enablement (commit `0303225`). Keep. HL-12 still stands.
- Database schema (migrations). Keep. No new migrations expected.
- Exercise taxonomy seeded data. Keep.
- `public/assets/css/court-fitness.css` — keep the brand + Falcon font variables. Grid styles will be augmented, not replaced.
- `app/Views/layouts/main.php` — keep.
- `.env`, composer.json, phpunit.xml.dist — keep.

### Wrong (REBUILD IN THIS ORDER)

1. **`app/Views/coach/plans/new.php`** — Bootstrap accordion + per-exercise modal. Delete; replace with the inline grid transcribed in `.ai/core/plan_builder_ux.md §1`.
2. **`public/assets/js/plan-builder.js`** — modal state machine. Delete most of it; replace with inline-row behaviour: sub-category dropdown change re-renders the row's value-cell strip (Cardio cells vs Weights cells vs Agility cells — see §2.4 of the UX doc).
3. **`app/Views/coach/plans/show.php`** — read-only badge list. Replace with the **same editable grid template** as `new.php`. Target cells locked (readonly attr), Actual cells editable.
4. **`app/Views/player/plans/show.php`** — read-only badge list. Replace with the same editable grid template. Target cells locked, Actual cells editable, server-side ownership check enforces player owns this plan.
5. **Redirect target after `POST /coach/plans` store** — currently lands on read-only show page. Switch so it lands on the editable grid with targets filled and actuals empty-ready-to-type.
6. **Plan-list card widths** — currently narrow, phablet-first. Make them span the page on desktop (3 columns at >=992px, 2 at 768-991px, 1 stacked at <768px). Same for Player Dashboard plan cards.

### New routes required

- **`POST /coach/plans/{obf}`** — update plan (may edit targets and actuals together). CSRF-protected. `actual_by_user_id` = coach's user id when actuals cells are non-empty.
- **`POST /player/plans/{obf}`** — update plan (actuals only). CSRF-protected. Server IGNORES any target edits posted by a player. `actual_by_user_id` = player's user id.

---

## Session 6 priority order

1. **Read `.ai/core/plan_builder_ux.md` end-to-end** before touching any code. If something is ambiguous, ask the owner. Do NOT guess.
2. **Rebuild the Plan Builder view as an inline grid** — the wide desktop/tablet/iPad layout matching the LTAT screenshot. Keep the Session 5 controller (`Coach\Plans::new`) and its form action (`POST /coach/plans`). Rewrite the view file and the JS. The form fields it submits should stay backwards compatible: `player_user_id`, `week_of`, `training_target`, `training_target_custom`, `weight_unit`, `notes`, `entries_json`.
3. **Rebuild Coach show + Player show as editable grid** — one shared partial if possible (`app/Views/coach/plans/_grid.php` or similar); both coach and player views extend it with different locked/editable column rules.
4. **Add POST /coach/plans/{obf} and POST /player/plans/{obf}** routes + controller methods. Each loads the plan, ownership-checks, applies the posted target/actual deltas, saves. `actual_by_user_id` stamped. `actual_at` stamped.
5. **Responsive breakpoints** — CSS only, single template. Desktop grid, tablet grid, mobile stacked-card. Use Bootstrap's grid `.col-*` classes where sensible.
6. **Redirect-after-save fix** — `POST /coach/plans` store action now redirects to `GET /coach/plans/{obf}` which is the editable grid. Flash notice "Plan saved. You can now type actuals here."
7. **End-to-end smoke test** — create a plan, type actuals as coach, refresh as player, verify same data visible; type actuals as player, refresh as coach, verify same data + audit trail.
8. **Seal-candidates pass** (if Session 6 closes Sprint 01) — per CLAUDE.md §12.

### Session 6 WILL NOT do

- Any new migrations or schema changes.
- Actuals-logging OUTSIDE the inline grid (no modal, no separate log-actuals URL).
- Any change to the 3+12+204 catalogue seed.
- Plan EDIT mode as a separate URL — the show URL IS the editable grid.
- PWA manifest + service worker — slip to Session 7 unless Session 6 finishes ahead.
- Rich analytics, charts, progression views — Sprint 02+.

---

## Owner's locked answers — reproduce these verbatim in your Conformance Check

From `.ai/core/plan_builder_ux.md §2`, Session 5 owner feedback:

**(a) Canonical target/actual shape:** "These are the values that the Coach or Player must insert, to confirm that the exercise was followed. For the sake of ease, and usage, we kept it in one row. One row because a player would finish that particular exercise Aerobic Cardio (Recovery Run), fill in the details, and only then move on to Anaerobic Alactic (Pro Agility 5 10 5 5 Repeats). Same pattern to be followed for evening session."

**(b) Responsive behaviour:** "You may wish to make your cards width larger across the page. Right now they are very short in width. Yes you may simplify when the user switches to mobile view, but that needs to be dynamic. We can not have the same view for laptop & pad, and mobile too. Right now it seems that everything is about mobile view, which is important, but it has to be handled dynamically."

**(c) Who edits actuals — BOTH:** "Both of course. It has been told by me multiple times in the previous conversation with Claude Agent. This is repetitive. Isnt this mentioned in the documentation anywhere? Very basic thing to miss. When the player and coach are working together, the coach or player may fill the exercises up from their logins. If the player is travelling without the coach, he logs into his account, goes to his exercise plan and saves the values. The Coach can then see the values when he logs into his zone back home. It works both ways."

**(d) Per-exercise column set:** "Please refer to the database. It is all following a pattern. The row and boxes adjust according to the max value that that particular exercise needs the value for. For exp (Refer to screenshot) Cardio, Weights and Agility, all have different amount of boxes. They come added automatically as per the exercise selected."

**These answers are NON-NEGOTIABLE.** If your design deviates from them, you are rebuilding for a third time. Session 5 was rebuilt once already — cost a day of owner time.

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **Framework reading is mandatory.** 13-item list for fresh agents (CLAUDE.md §3.1) **plus** `.ai/core/plan_builder_ux.md` for Session 6 specifically.
- **Seven close artifacts** (§6.2): WIP · SESSION_LOG · handover (framework version stamp at top) · next prompt · HARD_LESSONS (if non-obvious things surfaced) · git commits · memory-to-repo promotion. **Screenshots/design artifacts (HL-13) go into `.ai/research-notes/screenshots/` or `.ai/research-notes/design/` with a sibling .md note — the moment they arrive, not at session close.**
- **Project-wide session-N naming** — `session_6_handover.md` regardless of calendar date.
- **Captain / Engine Engineer.** Technical decisions inside the plan are yours; product/scope/priority/UX is Rajat's. When you see a UX choice — STOP and check `plan_builder_ux.md` first; if the doc answers it, follow; if not, ASK.
- **Responsive, not mobile-first.** Desktop/tablet/iPad is the primary workstation for coach-in-gym; mobile is a travel-mode fallback.
- **Falcon theme cohesion** (CLAUDE.md §5.4). Font stack is already correct from Session 5; grid styles should match Falcon's density and spacing.
- **CSRF is live** (HL-12). Every POST form needs `csrf_field()`.
- **AuthFilter is live.** Both new POST routes go through it. Coach role check on `POST /coach/plans/{obf}`; Player role check on `POST /player/plans/{obf}`.
- **Sealed files** (§5): only `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md`. Never modify.
- **Commit cadence:** one logical unit per commit. Likely Session 6 commits: plan-builder-view-inline-grid → plan-builder-js-inline-rows → coach-show-editable → player-show-editable → responsive-breakpoints → session-close.
- **No `--amend`, `--force`, `--no-verify`.**
- **Scope discipline.** The rebuild is itself the scope; do NOT add new features. No PWA, no actuals-outside-the-grid, no analytics. If you finish early, stop. Session 7 is waiting.
- **Evidence over assertion** (Rule 11). Every "works" claim in the handover comes with test or curl output.
- **If anything about the UX feels like a guess — STOP and ask the owner.** Session 5 was rebuilt because the agent (me) guessed. Don't repeat.

---

## Verification Commands

End-of-Session-6 targets:

```bash
cd C:/xampp/htdocs/court-fitness

# Tests: existing 23 + at least 2 new (post-handler tests for coach+player update)
./vendor/bin/phpunit tests/unit/              # all pass; count ≥ 25

# Migrations unchanged
php spark migrate:status                      # 3 migrations applied

# Dev server
php spark serve --port 8080                   # background

# Flow — Coach creates plan on laptop width (1280px)
#   /dev/sso-stub?as=coach → /coach/plans/new
#   inline-grid form, fill fundamentals + Add Row for a date + inline exercise rows with numeric cells
#   Submit → redirect to /coach/plans/{obf} with targets filled and actual cells editable-and-empty
#   Type actuals inline → save → refresh → actuals persisted

# Flow — Player opens same plan on mobile (375px)
#   /dev/sso-stub?as=player → /player → tap plan card → /player/plans/{obf}
#   Responsive collapse: exercise rows are stacked cards, target read-only, actual inputs editable
#   Type actuals → save → refresh → persisted, actual_by_user_id shows Rohan

# Flow — Coach sees player's actuals
#   /dev/sso-stub?as=coach → /coach/plans/{obf} → grid shows player's actuals with "Logged by Rohan"

# Flow — Responsive sanity
#   Open /coach/plans on desktop — cards 3 across at 1280px, 2 at ~900px, 1 at <768px
#   Open /coach/plans/new on desktop — inline grid wide; mobile — stacked rows

# Flow — AuthFilter still works on new POSTs
curl -sI "http://localhost:8080/coach/plans/Y2Y6MQ"      # 302 to HitCourt login unauth'd
curl -sI -X POST "http://localhost:8080/coach/plans/Y2Y6MQ" # 302 likewise (filter runs before CSRF)

git status                                    # clean at close
git log --oneline -10                         # ≥ 4 meaningful commits this session
```

Also at close, paste actual screenshot comparison in the handover (now that HL-13 is policy): take a screenshot of YOUR new grid at desktop width and save to `.ai/research-notes/screenshots/session6-plan-builder-desktop.png` alongside a sibling `.md` note. Do the same for mobile. Compare side-by-side with the LTAT reference.

---

## Known Risks

1. **Guessing the column set per exercise type.** `plan_builder_ux.md §2.4` says the live set varies by exercise; do not hard-code. Drive from `exercise_types.name` plus possibly a small lookup table. If unsure which cells are active for a given sub-category, open the live LTAT system (`tourtest.ltat.org`) and count. Do NOT guess from the JS code you find.
2. **Preserving already-logged actuals when coach edits targets.** If the coach re-saves the plan, `actual_json` for already-logged entries must NOT be clobbered. Controller logic must update target_json only, leaving actual_json untouched unless the same POST also contained actual-cell values.
3. **CSRF token regeneration with multiple POST actions on one page.** The editable grid is one big form with one Save button → fine, one POST, standard CSRF. Do NOT split into per-row AJAX saves in Session 6 — stick to the Big-Save pattern for simplicity.
4. **`target_json` schema drift.** The inline grid rewrites how the cells are submitted. You MUST match the same JSON keys defined in `.ai/core/exercise_json_shapes.md` so existing seeded plans render correctly.
5. **Responsive test without a real device.** The only verification path on Windows XAMPP is Chrome DevTools mobile emulation. Test at 375px, 768px, 992px, 1280px. Ask the owner to sanity-check on his own phone + laptop before closing.

---

## Sprint 01 state entering Session 6 (post-correction)

Sprint 01 scope per `.ai/sprints/sprint-01/sprint-plan.md`: "Coach plans a week, player logs actuals, on a phone."

**Done:**
- Auth (SSO + AuthFilter)
- DB schema (3 migrations)
- Exercise taxonomy seeded (3+12+204)
- Player Dashboard (narrow, needs responsive fix)
- Coach Dashboard
- Plan create (wrong UI; controller works)
- Plan list (narrow, needs responsive fix)
- Plan show as read-only (wrong — should be editable grid)

**Remaining for Sprint 01 "done" — this is Session 6's work:**
- Inline-grid Plan Builder (rewrite)
- Editable show view for coach (rewrite)
- Editable show view for player (rewrite)
- POST /coach/plans/{obf}, POST /player/plans/{obf}
- Responsive widths across all plan-list and plan-detail screens
- Audit display ("Logged by X, 2m ago")

**Remaining after Session 6 (Session 7 or Sprint 02):**
- PWA manifest + service worker
- Prettified target-badge labels (if not done as part of grid polish)
- Seal proposals at Sprint 01 close
- Archival pass (SESSION_LOG) at Sprint 01 close

Session 6 likely closes Sprint 01 if all the rebuild work lands. If responsive polish or audit display slips, Session 7 does those plus sprint-close chores.

---

## Demo URLs (post-rebuild)

- `http://localhost:8080/dev/sso-stub?as=coach` → `/coach` (dashboard with CTAs)
- `http://localhost:8080/coach/plans` → My Plans (wide card grid on desktop, 3 cols at ≥992px)
- `http://localhost:8080/coach/plans/new` → inline-grid Plan Builder
- `http://localhost:8080/coach/plans/Y2Y6MQ` → editable grid (coach side) with Rohan's actuals visible if logged
- `http://localhost:8080/dev/sso-stub?as=player` → `/player` (wide or stacked cards depending on width)
- `http://localhost:8080/player/plans/Y2Y6MQ` → editable grid (player side)
- Unauth'd → 302 to `https://www.org.hitcourt.com/login?return=…`

---

## Proposed CLAUDE.md / BRIEFING.md edits (owner must approve)

Session 5 identified two needed Tier-1 edits that I couldn't make myself. Sending them to the owner at Session 5 close:

**BRIEFING.md:** escalate the "both record actuals" line from a bullet to a stand-alone paragraph with the word **BOTH** bolded. Justification: agents (me included) skimmed past the bullet.

**CLAUDE.md §6.2 artifact 7 (memory-to-repo promotion):** explicitly include binary design artifacts (screenshots, Figma exports, sketches, videos) with the rule that they land in `.ai/research-notes/screenshots/` or `.ai/research-notes/design/` with a sibling `.md` note, **the moment they are shared, not at session close**.

If the owner has approved either edit by the time Session 6 opens, you'll see them in CLAUDE.md / BRIEFING.md directly. If not, apply the spirit anyway.

---

## When In Doubt

- **Unclear UX detail?** Check `.ai/core/plan_builder_ux.md` first. If not there, ASK the owner. Do NOT guess.
- **Unclear requirement?** Rule 9, ask. 30 seconds vs hours of rework.
- **Tempted by "while I'm here…"?** Don't (Rule 7). Session 6 scope is explicitly the rebuild; do NOT add Session 7 items.
- **A test feels flaky?** Investigate. Do NOT delete.
- **Tempted by `--amend` / `--force` / `--no-verify`?** Stop.
- **Context feels tight?** Initiate close EARLY (70% rule). The rebuild is large; if items 5-8 slip they slip. Better a clean partial than an abort.
- **Owner mentions "screenshots" or "design"?** Save the image to `.ai/research-notes/screenshots/` and write a sibling note BEFORE you write any code that depends on it (HL-13).
- **Owner seems to be repeating something?** You probably missed it in the docs. Grep `.ai/` for the key noun before you answer.

---

## Fresh-Agent Checklist

1. Read the 13-item list per CLAUDE.md §3.1.
2. Read `.ai/core/plan_builder_ux.md` (14th mandatory read for Session 6).
3. Read HL-13 and HL-12 (added Session 5).
4. Run baseline verification (git, migrate status, phpunit — expect 23/23 green).
5. Write `.ai/.daily-docs/{today}/session_6_conformance.md` with:
   - All 8 standard questions answered with verbatim quotes from BRIEFING, findings, and plan_builder_ux.md
   - Explicit acknowledgement that the Session 5 UI is being rebuilt; list the 4 files being replaced and the 2 new routes being added
6. Commit it: `git commit -m "sprint-1: session 6 open — Framework Conformance Check committed (rebuild scope)"`.
7. Tell Rajat in chat what you plan to build first, in owner-facing English. Ask if there's any late design input before you start the grid.
8. Wait for explicit "proceed."
9. On proceed — start the rebuild.

Good luck. Session 5 built the wrong thing; Session 6 builds the right thing. The design is locked, the backend is ready, just follow the UX doc.
