## Session Onboarding — court-fitness (Session 7, Sprint 01 Session 6)

You are picking up work on **court-fitness** — a mobile-first PWA for tennis coaches to plan weekly training for their players, with both sides logging actual session results. It lives at C:\xampp\htdocs\court-fitness on Windows 11 XAMPP. Production URL: https://fitness.hitcourt.com.

**Incoming AI Agent must not skim past any of the files. A thorough read is a must.** Sessions 5 and 6 both shipped the wrong UI before iterating to the right one — HL-13 (save design artifacts immediately) and HL-14 (don't split the reading list) are the standing rules.

This is a long-horizon project under a strict AI Agent Framework. Before you do ANYTHING, read these docs **in order**:

1. `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` — operating constitution. Sealed. Read-only.
2. `CLAUDE.md` (repo root) — project-specific conventions. §3 (reading), §5 (architecture), §6 (lifecycle), §6.2 artifact #7 (binary-design-artifact rule).
3. `.ai/README.md` — folder map.
4. `.ai/core/BRIEFING.md` — 1-page project overview. **The "Who records actuals — both sides, equally" paragraph is load-bearing — quote it verbatim in your Conformance Check.**
5. `.ai/core/WIP.md` — current state. Notes the sealed-files count is now 6 (was 1).
6. `.ai/core/SESSION_LOG.md` — 6 rows now, plus a Session 6-addendum row appended at session-close 2026-04-26.
7. `.ai/core/HARD_LESSONS.md` — fourteen entries (HL-1..HL-14).
8. `.ai/core/SEALED_FILES.md` — **read carefully.** Six sealed files now: AI_AGENT_FRAMEWORK.md (always), plus the entire Plan Builder UI (`_grid.php`, `new.php`, `plan-builder.js`, the SEALED-bracketed CSS section, and `plan_builder_ux.md`). Sealed 2026-04-26 by owner. Modifying any sealed file without explicit owner approval is a critical framework violation.
9. `.ai/core/KNOWN_ERRORS.md` — still template-only.
10. `.ai/core/ltat-fitness-findings.md` — predecessor findings.
11. `.ai/core/plan_builder_ux.md` — **SEALED.** Read for spec fidelity; do not modify.
12. `.ai/core/exercise_json_shapes.md` — JSON contract per format (Cardio 10 / Weights 4 / Agility 6 cells).
13. `.ai/sprints/sprint-01/sprint-plan.md` — sprint playbook.
14. `.ai/research-notes/screenshots/` — five LTAT JPGs + sibling `.md` notes; `same-day-3-sessions.md` is the canonical desktop reference for what mobile must compress without breaking.
15. `.ai/.daily-docs/26 Apr 2026/session_6_handover.md` — original Session 6 close.
16. `.ai/.daily-docs/26 Apr 2026/session_6_addendum_handover.md` — **the post-close iteration that produced the sealed view.** Read this carefully; it explains why each post-close commit happened.

After reading, DO NOT touch code yet. Run the **Framework Conformance Check** (Appendix D of `AI_AGENT_FRAMEWORK.md`) in chat AND commit it to `.ai/.daily-docs/{today}/session_7_conformance.md` per CLAUDE.md §3.1 + §6.1 before requesting "proceed." Quote BRIEFING.md's "Who records actuals" paragraph verbatim. List the six sealed files explicitly and state that you understand they are immutable without owner approval.

---

## Context — what just happened

Sprint 01 Session 6 closed cleanly at commit `310c07f` on 2026-04-26 with a working inline-grid Plan Builder. Owner ran the visual sign-off the same day, found the layout was still wrong (cells stacked vertically, missing dense cell strip, single grouped sub-category dropdown). Five post-close commits over the same evening rebuilt the grid to match the LTAT live system cell-for-cell:

- `0da18d5` — Sibling `.md` notes for the 5 LTAT screenshots (HL-13 follow-through).
- `f9cc676` — LTAT 10/4/6 cell layout (Cardio 10, Weights 4, Agility 6 cells; all typable; no horizontal scroll; <992px stacked-card collapse).
- `d7664eb` — Notes textarea moved to bottom; explicit Format → Category → Sub-category cascade per row (was a single grouped dropdown; new = two dropdowns at row level).
- `0ed4928` — **SEAL.** Owner approved the desktop view at `/coach/plans/new` and authorized sealing the Plan Builder UI (5 new entries in SEALED_FILES.md).
- `<close>`  — Session 6 addendum + Session 7 prompt + WIP + SESSION_LOG.

Test suite at the end of 2026-04-26: **28/28 passing, 63 assertions** — unchanged from Session 6 close (no test surface was touched in the post-close iterations).

---

## What Was Done at Session 6 Close + Addendum (2026-04-26)

1. Original Session 6 close: shipped Plan Builder rebuild, no-clobber decision + 5 unit tests, owner-approved Tier-1 docs (BRIEFING + CLAUDE).
2. Post-close visual sign-off iteration:
   - Saved sibling `.md` notes for the LTAT screenshots that had been on disk all day.
   - Rebuilt the cell strip to match LTAT (10/4/6 horizontal cells per row, dense ~44px row height).
   - Moved Notes textarea from top to bottom of page.
   - Switched to explicit Format → Category → Sub-category cascade.
   - **Owner approved and sealed the Plan Builder UI** — 5 new entries in `SEALED_FILES.md`.

---

## What Needs To Be Done Now (Session 7 — "Mobile-responsive pass over the sealed desktop view")

> **⚠️ Owner directive at end of 2026-04-26 (verbatim):** "This design should be our base, and we should build the mobile view which should dynamically adjust this web view, as per the device size."
>
> **Sealed-files reminder:** the desktop view is FROZEN. Mobile-responsive work happens through **new CSS rules below the `SEALED-END` marker** in `court-fitness.css`, OR the sealed section is unsealed first via AI_AGENT_FRAMEWORK.md §5.3 protocol (state why, what, risks, verification plan; wait for owner approval). The current sealed CSS already includes a `<992px` Option-A collapse — extending or refining it counts as modifying the sealed section. **Get owner approval first** before touching anything between SEALED-BEGIN and SEALED-END.

### Session 7 priority order

**Pre-work before writing any code:**
1. Re-read `.ai/research-notes/screenshots/same-day-3-sessions.md` — the desktop reference.
2. Re-read `.ai/core/plan_builder_ux.md` §2.2 (responsive behaviour, owner verbatim) — the only-doc-to-trust on responsive intent.
3. Open `http://localhost:8080/coach/plans/new` (after `php spark serve --port 8080` and `/dev/sso-stub?as=coach`) in Chrome DevTools. Test the layout at **375 / 568 / 768 / 992 / 1280 px** — note exactly what's wrong at each.
4. Save 5 screenshots to `.ai/research-notes/screenshots/session7-{viewport}px.png` with sibling `.md` notes BEFORE you start fixing — this is the baseline. HL-13 / CLAUDE.md §6.2 artifact #7 mandates this.

### Session 7 priority list

1. **Mobile (<568px) review.** The current `<992px` Option A collapse stacks each exercise row into a card with cells in a 2-col grid. At 375px (iPhone SE), is the card readable? Is the Format dropdown in the block header still tap-friendly? Is the Notes textarea at the bottom still discoverable? Document findings, propose fixes.

2. **Tablet portrait (768-991px) review.** The collapse already kicks in here per the sealed CSS. Verify the cards are sized appropriately for thumb input — too narrow looks cramped, too wide is hard to scan. Tweak gap / padding.

3. **Tablet landscape / small laptop (992-1279px) review.** This is the boundary where the desktop layout starts. Cardio rows need 70 + 140 + 200 + (10 × 72) = 1130px minimum for the dense cell strip. At 992px there's only ~960px usable — the strip will compress or wrap. Confirm gracefully and decide: do we narrow cells further, or extend the collapse threshold to 1199px?

4. **Real device test.** Owner has access to his own phone and laptop — ask him to test on real devices. Eyeball before declaring close. CLAUDE.md §6.2 artifact #7 mandates screenshots saved to `.ai/research-notes/screenshots/`.

5. **Sealed-files audit.** If your mobile work requires any change inside the SEALED-BEGIN / SEALED-END markers, follow the unsealing protocol explicitly. Do NOT silently edit. State the proposed change in chat, wait for "ok unseal that," then commit with a message that quotes the approval.

### Session 7 ALSO (carried forward from earlier prompt, lower priority than mobile)

These items were on Session 7's earlier list before the mobile-responsive work landed at owner's request. Pick up if context allows:

- **PWA manifest + service worker.** `public/manifest.json` + `public/sw.js` + icons + register in layout. Lighthouse PWA audit baseline.
- **Per-type server-side validation of `target_json` / `actual_json`.** `App\Validation\PlanEntryShapeValidator` static helper, wired into `Coach\Plans::store` / `::update` and `Player\Plans::update`. Tests in `tests/unit/PlanEntryShapeValidatorTest.php`.
- **Prettier label prettifier in plan-builder.js** for tooltips/summaries.

### Session 7 WILL NOT do

- Modify the sealed Plan Builder UI without explicit owner unsealing.
- Schema/migrations/catalogue changes.
- Capacitor wrap.
- Assessments / metric_types kernel.
- Tennis-specific testing catalogue.
- Multi-language.

### Sprint-close candidate

If Session 7 lands the mobile pass + at least PWA scaffolding cleanly, Session 7 is a Sprint 01 close candidate. Per CLAUDE.md §11 + §12:
- **Seal candidates review** (the watchlist in SEALED_FILES.md): JwtValidator, IdObfuscator, the 3 catalogue seed migrations, Sso, PlanEntriesModel::decideActualUpdate, the two show.php mount stubs, the two ::update controller actions.
- **Archival** of SESSION_LOG / KNOWN_ERRORS — too small still; nothing to archive yet.
- **HL_INDEX.md** kicks in at HL-20 — currently at HL-14, six entries away.

---

## Key Conventions (quick reference — full in CLAUDE.md)

- **6 sealed files now** (was 1 at start of Session 6). Read SEALED_FILES.md before any UI edit. Modifying without owner approval is a critical framework violation.
- **Foreign keys ARE used.** Engineering decision (HL-6).
- **ONE migrations folder.** Raw-SQL workaround is NEVER the answer (HL-1).
- **Plain English with Rajat.** He is a Vibe-Coder, not a coder. No jargon without definition.
- **Captain / Engine Engineer model.** Technical decisions within scope are yours by default; UX / scope / priority / product is his. New nuance (2026-04-26): "Act if consequential, defer if not" — when you flag a tangential observation, default is your judgment call.
- **Responsive, not mobile-first.** Desktop/tablet/iPad primary; mobile is adaptive. Sealed view is the desktop baseline.
- **Falcon theme cohesion** (CLAUDE.md §5.4). Font stack + 16.8px base.
- **CSRF + AuthFilter alive globally.** Every POST form needs `csrf_field()`; every new route is role-checked + ownership-checked.
- **Single-form Big Save model.** No per-row AJAX (HL-12 + CI4 token rotation).
- **Commit per logical unit.** Typical Session 7 commits: mobile-screenshot-baseline → mobile-tweaks → pwa-manifest → pwa-sw → session-close.
- **No --amend, no --force, no --no-verify.**
- **Seven session-close artifacts** mandatory at close (CLAUDE.md §6.2), including binary-artifact promotion (artifact #7's binary half — screenshots saved with sibling notes the moment they're taken).
- **Begin session close at 70% context.**

---

## Verification Commands

End-of-Session-7 targets:

```
cd C:/xampp/htdocs/court-fitness

# Tests
./vendor/bin/phpunit tests/unit/                        # >= 28; if PWA/validation work lands, target 31+

# Migrations unchanged
php spark migrate:status                                # 3 migrations applied

# Dev server (note: .env app.baseURL = http://localhost:8080/, so use port 8080)
php spark serve --port 8080                             # background

# Mobile responsive baseline at 5 breakpoints
#   /dev/sso-stub?as=coach → /coach/plans/new
#   Open Chrome DevTools, mobile emulation
#   Walk through 375 / 568 / 768 / 992 / 1280 px
#   Save screenshots to .ai/research-notes/screenshots/session7-{w}px.png
#   Sibling .md note: what works, what doesn't

# Real device test (ask owner to confirm on his phone + laptop)

# PWA verification (if implemented)
#   /manifest.json → 200, valid JSON
#   /sw.js → 200, text/javascript
#   Chrome DevTools → Application tab → manifest renders + SW registers
#   Lighthouse PWA audit baseline noted

# Regression
#   Sealed view still works: /coach/plans/new, /coach/plans/{obf}, /player/plans/{obf} all 200
#   no-clobber test still passing
git status                                              # clean at session close
git log --oneline -10                                   # >= 4 meaningful commits this session
```

---

## Known Risks (descending likelihood)

1. **Touching the sealed CSS section accidentally.** The mobile-responsive work tempts you to "just tweak the breakpoint from 991.98px to 1199.98px" — but that single-character change is a sealed-file modification. **Always check whether your change is inside SEALED-BEGIN/SEALED-END before saving.** If it is, follow the §5.3 unsealing protocol and wait for owner approval.
2. **Real-device vs DevTools-emulator drift.** DevTools mobile emulation isn't a real phone — touch targets, scroll behaviour, viewport meta, and font rendering all differ. Get the owner to test on his real iPhone before declaring closed.
3. **PWA scope creep.** Workbox routing, push notifications, install prompts, and splash screens are all interesting and all out of scope for Session 7. The bar is: manifest + minimal SW + offline shell. Anything more is Sprint 02.
4. **Service worker caching wrong things.** A bad SW cache will serve stale plan data or stale auth pages. Network-first or no-cache for `/coach/plans/*`, `/player/plans/*`, `/sso`, `/dev/*`.
5. **Owner stakeholder meeting Monday morning 2026-04-27.** If the meeting raises a feature request, it could pivot Session 7's scope mid-stream. Communicate clearly and treat the pivot as an explicit owner-direction case under Rule 7.
6. **The owner mentioned not feeling well at the end of 2026-04-26.** Be patient with response time; do not assume silence = approval to proceed. Wait for explicit "proceed."

---

## When In Doubt

- Unclear UX detail → `.ai/core/plan_builder_ux.md` first. If not there, ASK Rajat (Rule 9).
- Unclear what's sealed → `.ai/core/SEALED_FILES.md`. If a file is sealed, the protocol is binding.
- Unclear past decision → `.ai/core/HARD_LESSONS.md` + `.ai/.daily-docs/26 Apr 2026/session_6_handover.md` + `session_6_addendum_handover.md`.
- Tempted by a destructive git op → do not.
- Tempted by "while I'm here, let me also…" → log in WIP.md follow-ups, do not do it (Rule 7).
- Tempted to apply raw SQL → STOP (HL-1).
- Tempted to mock auth → NEVER (HL-8).
- Tempted to modify a sealed file silently → STOP. Follow §5.3.
- Tempted to skim BRIEFING / plan_builder_ux.md / SEALED_FILES.md → STOP (HL-13, HL-14).
- Owner shares a screenshot → save to `.ai/research-notes/screenshots/` with sibling `.md` note immediately.

Good luck. Run the Framework Conformance Check, commit it, wait for "proceed," then mobile-responsive baseline at 5 breakpoints first — screenshots before code.
