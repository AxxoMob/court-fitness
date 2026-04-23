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

Candidate files to consider sealing once court-fitness has code (do NOT seal in Sprint 0):
- `app/Modules/Shared/Services/SsoService.php` — when built — because it is the identity boundary to HitCourt and any bug silently grants or denies access. Worth sealing once stable + tested.
- Seed migrations for `exercise_types`, `fitness_categories`, `fitness_subcategories` — once the 204-row catalogue is loaded, those rows are the shared language of the product; arbitrary edits to names would break existing plans.

Neither of the above is built yet. Revisit in Sprint 1 close / Sprint 2 start.
