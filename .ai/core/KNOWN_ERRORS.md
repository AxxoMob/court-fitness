# Known Errors

Bug catalogue for court-fitness. Every bug found during the project's life goes here — open or closed. Format per `.ai/.ai-agent-framework/AI_AGENT_FRAMEWORK.md` Section 6.3.

---

**As of 2026-04-22 (end of Sprint 0 Session 1): no Known Errors.**

No code has been written yet. Bugs will be catalogued as they are found in Sprint 1 onwards.

---

## Template for new entries (do not delete this section)

```markdown
## KE-N — <short title>

**Discovered:** Session N of Sprint M, YYYY-MM-DD
**Status:** open | fixed | won't fix | duplicate-of-KE-X
**Severity:** critical | high | medium | low
**Reproduction:**
1. <step>
2. <step>
3. <expected vs actual>

**Root cause:** <once identified — "not yet investigated" if unknown>
**Fix:** <file paths + session if fixed; blank if open>
**Cross-refs:** <related HL-IDs, sessions, sealed files>
```

---

## Bugs inherited-in-spirit from ltat-fitness (NOT present in court-fitness code but worth knowing)

These are not KEs in the court-fitness sense — there is no court-fitness code to host them yet. They are patterns to AVOID recreating. See `.ai/core/HARD_LESSONS.md` for the full analysis.

- ltat-fitness migration pipeline drift (HL-1) — Task 21 had to apply ALTER directly because the migrations folder had duplicate CreateTrainersTable entries. If we ever have to "apply a migration directly" in court-fitness, that is a sign the migrations folder itself is broken. Stop and fix the folder.
- ltat-fitness mock authentication (HL-8) — controllers read `?mock_trainer_id=` from query string. If court-fitness ever develops a pattern where a user ID is taken from a non-session source, that is a critical bug — stop.
- ltat-fitness doc/code/DB drift (HL-2, HL-3) — if documentation and live schema disagree in court-fitness, always trust the live schema and update the doc. Never trust a doc over evidence.
