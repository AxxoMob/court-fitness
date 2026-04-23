## Session Onboarding — court-fitness (Session 6, Sprint 01 Session 5)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at C:\xampp\htdocs\court-fitness on Windows 11 XAMPP. Production URL: https://fitness.hitcourt.com.

This is a long-horizon project under a strict AI Agent Framework. Before you do ANYTHING, read these docs **in order**:

1. .ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md — operating constitution. Sealed. Read-only.
2. CLAUDE.md (repo root) — project-specific conventions. §3 (reading protocol), §5 (architecture), §6 (session lifecycle) especially.
3. .ai/README.md — folder map. Note: `.ai/.ai2/` was renamed to `.ai/core/` in Session 4; any old path references you encounter need that substitution.
4. .ai/core/BRIEFING.md — 1-page project overview. **Quote line 10 verbatim in your Conformance Check.** Session 5 skimmed past it — the sentence that says both coach AND player record actuals — and the entire UI had to be rebuilt. Do NOT repeat this failure.
5. .ai/core/WIP.md — current state. The "Current Status" section explicitly notes Session 5's UI is being rebuilt in Session 6.
6. .ai/core/SESSION_LOG.md — project diary (5 rows now).
7. .ai/core/HARD_LESSONS.md — thirteen entries (HL-1..HL-13). Read all. HL-13 was added at Session 5 close because the prior agent (me) skimmed past BRIEFING.md and shipped the wrong UI. Read HL-13 twice.
8. .ai/core/SEALED_FILES.md — one sealed file (the framework itself).
9. .ai/core/KNOWN_ERRORS.md (still empty, template only).
10. .ai/core/ltat-fitness-findings.md — predecessor-project findings, especially §2 (the live coach workflow).
11. .ai/core/plan_builder_ux.md — **your primary UX reference. LOCKED by owner 2026-04-23. Read TWICE.** Transcribes the live LTAT screen, captures the owner's verbatim design answers (a) through (d), and lists every Session 5 file being rebuilt with its correct replacement. Any deviation from this doc = rebuilding the UI for a third time.
12. .ai/sprints/sprint-01/sprint-plan.md — the sprint playbook. §3 schema is implemented; §5 UI screens is now superseded by plan_builder_ux.md for layout detail.
13. .ai/.daily-docs/23 Apr 2026/session_5_handover.md — what Session 5 shipped, what it got right, what it got wrong, and what is preserved intact for Session 6.

After reading, DO NOT touch code yet. Run the **Framework Conformance Check** (Appendix D of AI_AGENT_FRAMEWORK.md) in chat AND commit it to .ai/.daily-docs/{today}/session_6_conformance.md per CLAUDE.md §3.3 before requesting "proceed." Your Conformance Check MUST quote BRIEFING.md line 10 verbatim, cite plan_builder_ux.md §2 answers (a) through (d) verbatim, and explicitly list the four view files being rebuilt. Paraphrasing will be rejected — see HL-13.

---

## Context

Sprint 01 Session 5 (last session) shipped a **working backend** — DB migrated + seeded, SSO + AuthFilter + CSRF live, Plan Builder controller + persist + obfuscated URLs, plan-show views rendering — but built the **wrong UI**. The Plan Builder was modal-driven mobile-first; the owner's canonical layout (per his live LTAT system) is a wide inline grid for desktop/tablet/iPad with responsive collapse to mobile. Root cause: screenshots shared with the Session 1 agent in chat were never saved to the repo, and a fresh Session 5 agent reinvented the UI from lossy prose. Fix recorded in HL-13. Canonical UX locked in .ai/core/plan_builder_ux.md. Session 6 rebuilds the views; the backend is untouched.

**Tech stack (summary):** PHP 8.2.12 / CodeIgniter 4.7.2 / MySQL 8.0+ / Bootstrap 5.3.3 loaded via CDN / Poppins 16.8px Falcon font stack. Brand: #F26522 orange. CSRF enabled globally. AuthFilter gates all routes except /, /sso, /dev, /dev/sso-stub. Full detail: CLAUDE.md §7.

---

## What Was Done Last Session (Session 5, 2026-04-23)

1. Bootstrap 5.3.3 loaded via CDN in app/Views/layouts/main.php; Falcon-exact font stack wired into court-fitness.css with --bs-body-* + --bs-primary overrides (commit ea3f40a).
2. App\Support\IdObfuscator (URL-safe base64 of "cf:<id>") + 9 unit tests — handles garbage, empty, prefix-missing, id=0, plain-integer inputs (commit ea3f40a).
3. Models TrainingPlansModel + PlanEntriesModel; controller Coach\Plans::{index, new, store, show}; route group; transactional save; obfuscated-URL redirect (commit 0303225) — **backend correct, view wrong (rebuild in Session 6).**
4. Coach\Players::index — assigned-players list-only, no add-player form (per owner: HitCourt is the sole identity source) (commit 0303225).
5. Player\Plans::show — read-only badge view (commit 0303225) — **rebuild as editable grid in Session 6.**
6. App\Filters\AuthFilter + global registration; 302s unauthenticated → ${HITCOURT_BASE_URL}/login?return=<path>; 2 unit tests; except list /, sso, dev, dev/sso-stub (commit d153c9a).
7. CSRF protection enabled globally (CI4 ships it commented out — captured as HL-12) (commit 0303225).
8. End-to-end smoke verified: /dev/sso-stub?as=coach → build a plan → redirect to /coach/plans/Y2Y6NQ → "Plan saved" flash. Backend pipeline is correct.
9. Post-close UX correction (commit ab05ea5): plan_builder_ux.md locked, HL-13 added, .ai/research-notes/screenshots/ folder created with README, Session 6 prompt rewritten as this rebuild brief.

Test suite at Session 5 close: **23/23 passing, 48 assertions** (10 JwtValidator + 9 IdObfuscator + 2 HealthTest + 2 AuthFilter).

Demo-ready URLs (will continue to work through the rebuild — only the view templates change):
- /dev — stub SSO landing
- /dev/sso-stub?as=coach — log in as Rajat (coach)
- /dev/sso-stub?as=player — log in as Rohan (player)
- /coach, /coach/plans, /coach/players, /coach/plans/new
- /player, /player/plans/Y2Y6MQ

---

## What Needs To Be Done Now (Session 6 — "Rebuild: inline grid + actuals for both parties")

> **⚠️ Owner directive at Session 5 close (2026-04-23):** the Plan Builder is NOT a mobile-first modal flow; it is a wide inline grid where the coach (on laptop or iPad in the gym while the player exercises) types target AND actual values directly in row cells. After save the coach must land on the SAME editable grid, ready to punch actuals. Both coach AND player can edit actuals from their own logins. See plan_builder_ux.md for the full locked spec. If anything in your implementation deviates, you are rebuilding for a third time and the owner will not be pleased.

### Session 6 priority order

**Pre-work before writing any code: re-read .ai/core/plan_builder_ux.md §1 (LTAT screen transcription) and §2 (owner's verbatim answers a through d). This is the rebuild spec. If the doc does not answer a layout question, STOP and ask the owner — do NOT guess.**

1. **Rebuild the Plan Builder view as an inline grid.** Rewrite `app/Views/coach/plans/new.php` + `public/assets/js/plan-builder.js` per plan_builder_ux.md §1 (the LTAT screen layout) and §4.2 (the "wrong → replace with" table). Wide inline grid at desktop/tablet/iPad; collapses to stacked-card-per-exercise at <768px via CSS. No modal. One row per exercise with: [+] add-row button | category label | sub-category dropdown | type-specific numeric input cells | [-] remove-row button. Sub-category change re-renders the row's cell strip (Cardio ≠ Weights ≠ Agility — plan_builder_ux.md §2.4). Keep the existing controller's POST form field names backwards compatible (entries_json + fundamentals).

2. **Rebuild Coach + Player show views as editable grids.** Same HTML partial as #1 (extract to app/Views/coach/plans/_grid.php or similar; mount on both Coach\Plans::show and Player\Plans::show). Coach (`/coach/plans/{obf}`): targets AND actuals editable, so coach can adjust targets on the fly during a training session. Player (`/player/plans/{obf}`): targets locked (readonly attr + server-side ignore), actuals editable. Server-side ownership check stays on both — never trust the client. On save, `plan_entries.actual_by_user_id` + `actual_at` get stamped with whoever saved (plan_builder_ux.md §3.3).

3. **Add POST routes for in-place update.** `POST /coach/plans/{obf}` updates targets + actuals. `POST /player/plans/{obf}` updates actuals only (any target field in the POST is silently ignored server-side — do not 400, just drop). Both CSRF-protected via the global filter, AuthFilter-gated, role-checked. **If an entry already has non-null `actual_json` and the coach re-saves, preserve the actuals** — do NOT clobber. Test this case explicitly; write a unit test for the no-clobber guarantee.

4. **Redirect-after-save fix.** `POST /coach/plans` (Session 5's store action — don't rename) now redirects to `GET /coach/plans/{obf}` which IS the editable grid. Flash notice: "Plan saved. You can now type actuals here." This kills the "where do I insert the values?" dead-end the owner hit in Session 5.

5. **Responsive widths across all plan screens.** Single HTML payload, CSS-driven, NO device-sniffing, NO two templates. Plan cards on /coach/plans + /player: 3 across at ≥992px, 2 at 768-991px, 1 stacked at <768px. Plan Builder + show grids: wide table on desktop, stacked per-exercise cards on mobile. Use Bootstrap's .col-lg-* / .col-md-* grid classes where sensible. Plan_builder_ux.md §2.2 is the owner's exact words.

6. **Audit display on show views.** Each logged actual renders "Logged by Coach Rajat · 2m ago" or "Logged by Rohan · 2m ago" using `plan_entries.actual_by_user_id` JOIN users + `actual_at` timestamp. Simple `<small class="text-muted">` under the actual cells. Plan_builder_ux.md §3.3.

### Session 6 WILL NOT do (deferred to Session 7)

- PWA manifest + service worker.
- Per-type server-side validation of target_json / actual_json shapes (trust the UI for one more session; harden later).
- Prettier target-badge labels (`HR 75%` vs `max_hr_pct: 75`) beyond what the inline grid shows natively.
- Plan Builder "Edit mode" as a separate URL — the show URL IS the editable grid now; there is no separate edit URL.
- Any new migrations, schema changes, or catalogue updates.
- Rich analytics, charts, progression views.
- Sprint-close chores (seal proposals + archival) unless Session 6 actually closes Sprint 01, in which case yes — per CLAUDE.md §11 + §12.

### Realistic session shape

Items 1-4 are the stakeholder-visible deliverable and MUST land complete. Items 5-6 are polish and may slip to Session 7. If context gets tight, trigger session close at 70% and defer 5-6 cleanly rather than cut corners on 1-4. The editable inline grid with actuals flowing both ways is what the owner is watching for.

---

## Decisions the Owner Made at Session 5 Close (all 4 UX questions resolved)

These are captured verbatim in .ai/core/plan_builder_ux.md §2 (the canonical source). Abridged here so you see them in priority-order context:

1. **Canonical target/actual row shape.** "These are the values that the Coach or Player must insert, to confirm that the exercise was followed. For the sake of ease, and usage, we kept it in one row. One row because a player would finish that particular exercise Aerobic Cardio (Recovery Run), fill in the details, and only then move on to Anaerobic Alactic (Pro Agility 5 10 5 5 Repeats)." Rule: one exercise = one row; NO modal; user fills each row sequentially before moving on.

2. **Responsive behaviour.** "You may wish to make your cards width larger across the page. Right now they are very short in width. Yes you may simplify when the user switches to mobile view, but that needs to be dynamic. We can not have the same view for laptop & pad, and mobile too. Right now it seems that everything is about mobile view, which is important, but it has to be handled dynamically." Rule: desktop/tablet/iPad is the primary workstation; mobile is a responsive collapse, not a separate template.

3. **Who edits actuals — BOTH coach and player.** "Both of course. It has been told by me multiple times in the previous conversation with Claude Agent. This is repetitive. Isnt this mentioned in the documentation anywhere? Very basic thing to miss." Also documented in BRIEFING.md line 10 since project inception. Rule: both logins have editable-actuals on the same plan; `actual_by_user_id` stamps whoever saved.

4. **Per-exercise column set.** "Please refer to the database. It is all following a pattern. The row and boxes adjust according to the max value that that particular exercise needs the value for. For exp (Refer to screenshot) Cardio, Weights and Agility, all have different amount of boxes. They come added automatically as per the exercise selected." Rule: row swaps its live cells when sub-category changes; disabled/greyed cells may be shown for visual alignment (matching LTAT's rendering pattern).

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **Foreign keys ARE used.** Engineering decision (HL-6).
- **ONE migrations folder.** Raw-SQL workaround is NEVER the answer (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder, not a coder. No jargon without definition.
- **Captain / Engine Engineer model.** Technical decisions within scope are yours by default; UX / scope / priority / product is his.
- **Responsive, not mobile-first.** Desktop/tablet/iPad primary; mobile is adaptive. (Session 5 got this wrong; plan_builder_ux.md §2.2 is the correction.)
- **Falcon theme cohesion** (CLAUDE.md §5.4). Font stack is correct from Session 5; grid density and spacing should match Falcon.
- **CSRF is live, AuthFilter is live.** Every POST form needs csrf_field(); every new route is role-checked + ownership-checked.
- **Commit per logical unit.** Typical Session 6 commits: view-inline-grid → js-inline-rows → show-views-editable → POST-routes → responsive-breakpoints → audit-display → session-close.
- **No --amend, no --force, no --no-verify.**
- **Sealed:** .ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md. Never modify.
- **Seven session-close artifacts** mandatory at close (CLAUDE.md §6.2), including memory-to-repo promotion. Binary design artifacts (screenshots, Figma exports, reference images) land in .ai/research-notes/screenshots/ or /design/ with sibling .md note the MOMENT they're received in chat, not at session close (HL-13).
- **Begin session close at 70% context.** An honest partial close > a rushed full close > the abort protocol (§13).

---

## Verification Commands

End-of-Session-6 targets:

cd C:/xampp/htdocs/court-fitness

# Tests: 23 existing + at least 2 new (POST /coach/plans/{obf}, POST /player/plans/{obf}; plus a no-clobber test)
./vendor/bin/phpunit tests/unit/                        # all pass; count ≥ 26

# Migrations unchanged
php spark migrate:status                                # 3 migrations applied

# Dev server
php spark serve --port 8080                             # background

# Flow 1 — Coach creates plan on laptop width (DevTools 1280px)
#   /dev/sso-stub?as=coach → /coach/plans/new (inline grid, wide layout)
#   Fill fundamentals (player, week_of Monday, training_target, weight_unit)
#   Add a training date + session; add exercise rows inline with numeric cells
#   Submit → redirect to /coach/plans/{obf} with targets filled + actual cells editable-and-empty
#   Type actuals inline → save → refresh → actuals persisted + "Logged by Coach Rajat · now" audit line

# Flow 2 — Player logs on mobile (DevTools 375px)
#   /dev/sso-stub?as=player → /player (card grid: 1 col at this width) → tap plan → /player/plans/{obf}
#   Responsive collapse: stacked cards per exercise, targets read-only, actuals editable
#   Save → refresh as coach → grid shows "Logged by Rohan · now"

# Flow 3 — No-clobber guarantee
#   As player: log an actual on an entry
#   As coach: open same plan, edit that entry's TARGET, save
#   Refresh as player: the actual_json is still there, target_json updated

# Flow 4 — Responsive sanity
#   Resize browser through 375 / 768 / 992 / 1280 px — layout adapts without re-fetching

# Flow 5 — AuthFilter + CSRF still gate the new POST routes
curl -sI -X POST "http://localhost:8080/coach/plans/Y2Y6MQ"   # 302 to HitCourt login unauth'd
# Submitting a POST form without a csrf_test_name token → 403

git status                                              # clean at session close
git log --oneline -10                                   # ≥ 4 meaningful commits this session

At close, paste in the handover: a screenshot of the new desktop grid and a screenshot of the mobile stacked view, saved under .ai/research-notes/screenshots/session6-*.png with sibling .md notes (HL-13 is policy now).

---

## Known Risks (descending likelihood)

1. **Guessing the column set per exercise type.** plan_builder_ux.md §2.4 says the live cell set varies by sub-category; do NOT hard-code defaults for "all types." Drive from the picked sub-category; if unsure which cells are active for a given sub-category, open the live LTAT system at tourtest.ltat.org and count, or ask owner. Guessing = rebuilding for a third time.
2. **Preserving logged actuals when coach edits targets.** If `plan_entries.actual_json IS NOT NULL` for an entry and the coach re-saves the plan, the actuals MUST NOT be clobbered. Test this explicitly (see Flow 3 above). Write a unit test for it.
3. **Responsive testing without a real device.** The only verification path on Windows XAMPP is Chrome DevTools mobile emulation. Test at 375 / 768 / 992 / 1280 px. Ask the owner to eyeball on his own phone + laptop before declaring Session 6 closed.
4. **CSRF + the single big-save form.** The editable grid is one form with one Save button — one POST, standard CSRF. Do NOT split into per-row AJAX saves in Session 6; that introduces token-rotation complexity (HL-12 + CI4's regenerate=true). Keep Big-Save simplicity for this session.
5. **Audit display string.** "Logged by Coach Rajat" is fine on the coach's own view, but ensure it renders correctly on the player's view too (player must see the coach's name + role, not just a bare first name).
6. **BRIEFING-skimming relapse.** Session 5's rebuild was caused by the agent skimming past BRIEFING.md line 10. If your Conformance Check cannot quote that line verbatim, you have not read it. Go back.

---

## When In Doubt

- Unclear UX detail → .ai/core/plan_builder_ux.md first. If not there, ASK Rajat (Rule 9).
- Unclear past decision → .ai/core/HARD_LESSONS.md + .ai/.daily-docs/23 Apr 2026/session_5_handover.md.
- Tempted by a destructive git op → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL because a migration fights you → STOP (HL-1).
- Tempted to mock auth or read identity from a query string → NEVER (HL-8).
- Tempted to skim BRIEFING or plan_builder_ux.md → STOP. Session 5 skimmed and rebuilt a day of work. Session 6 cannot repeat that. (HL-13.)
- Owner shares a screenshot / design artifact → save to .ai/research-notes/screenshots/ with sibling .md note the MOMENT it arrives, not at session close (HL-13).

Good luck. Run the Framework Conformance Check, commit it, wait for "proceed," rebuild the views per plan_builder_ux.md.
