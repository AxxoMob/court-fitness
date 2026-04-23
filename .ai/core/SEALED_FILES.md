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

## How to propose sealing a new file

When an agent notices a file that has become load-bearing, fragile, or has been the source of multiple regressions, they may propose sealing it. The agent drafts an entry matching the schema above and asks Rajat in chat to confirm. Rajat decides; only Rajat may add new seals.

## Seal-candidates review cadence (per `CLAUDE.md` §12)

At every **sprint close**, the closing agent reviews code written during that sprint and lists seal candidates in the sprint handover under a "Seal Candidates for Owner Review" section. Owner confirms which, if any, to add here.

Opportunistic mid-sprint sealing is also welcome via the same mechanism: draft the entry, propose to owner in chat, wait for explicit approval.

## Candidates currently being watched

- `app/Services/JwtValidator.php` — BUILT Session 2, tested 10/10. Identity boundary to HitCourt. Bug here silently grants or denies access. Propose sealing at Sprint 01 close once it's run in at least a few real sessions.
- `app/Support/IdObfuscator.php` — planned Session 4. URL pattern helper; if broken, plan URLs break globally. Propose sealing once stable.
- Seed migrations for `exercise_types`, `fitness_categories`, `fitness_subcategories` — ran Session 3 with 3+12+204 rows. Arbitrary edits to names would invalidate existing `plan_entries` FK references and break every saved plan. Propose sealing at Sprint 01 close.
- `app/Controllers/Sso.php` — BUILT Session 3. The SSO handoff logic. Revisit at Sprint 02 once it's seen real HitCourt traffic.

Revisit at every sprint close.
