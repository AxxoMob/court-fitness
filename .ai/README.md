# `.ai/` — Agent Documentation Map

This folder holds every non-code document for court-fitness. The structure follows the four-tier hierarchy from `.ai-agent-framework/AI_AGENT_FRAMEWORK.md` §2, adapted for a cleaner sub-folder layout (owner's direction, 2026-04-22).

## Folder map

| Folder | Framework tier | What's inside |
|---|---|---|
| `.ai-agent-framework/` | Tier 1 | The operating constitution (AI_AGENT_FRAMEWORK.md). **Sealed.** Never edit. Owner-only. |
| `briefing/` | Tier 1 | One-page project overview (BRIEFING.md). |
| `domain-reference/` | Tier 2 | Reference material cited often: sealed-files list, predecessor-project findings. |
| `institutional-memory/` | Tier 3 | Append-only project history: SESSION_LOG, HARD_LESSONS, KNOWN_ERRORS. |
| `current-state/` | Tier 4 | What's happening right now: WIP.md. |
| `sprints/` | Tier 4 | One sub-folder per sprint (`sprint-00/`, `sprint-01/`…) with `sprint-plan.md`. |
| `.daily-docs/` | Tier 4 | One sub-folder per calendar day of work (e.g. `22 Apr 2026/`), containing the session handover(s) and the next-session prompt(s). |
| `research-notes/` | (extra) | One-off research artifacts: scripts, notes, transient evidence. Not a framework tier — a pragmatic bucket for "work someone did once that might be useful later." |

## Naming rules

- Framework-owned folders (`.ai-agent-framework/`, `.daily-docs/`) keep their dot prefix because that is the owner's convention for system-like folders.
- Content folders (`briefing/`, `sprints/`, etc.) use plain kebab-case so they appear in normal file-manager listings.
- Inside `.daily-docs/`, the per-day folder is literal: `22 Apr 2026/` (space-separated, not ISO). Matches the owner's preference.
- Session handovers are named `session_N_handover.md` where `N` is the session number within that day; next-session prompts are `prompt_for_session_N.md`.
- Sprint plans are named `sprint-plan.md` inside `sprints/sprint-NN/` where `NN` is zero-padded (sprint-00, sprint-01, … sprint-10).

## Framework path overrides (important)

The AI_AGENT_FRAMEWORK.md file (sealed, at `.ai/.ai-agent-framework/`) uses its own default paths like `.ai/BRIEFING.md`, `.ai/WIP.md`, `.ai/HARD_LESSONS.md`. court-fitness **overrides** those to the sub-folder structure in the table above. When the framework says:

- `.ai/BRIEFING.md` → in court-fitness, read `.ai/briefing/BRIEFING.md`
- `.ai/WIP.md` → `.ai/current-state/WIP.md`
- `.ai/SESSION_LOG.md` → `.ai/institutional-memory/SESSION_LOG.md`
- `.ai/HARD_LESSONS.md` → `.ai/institutional-memory/HARD_LESSONS.md`
- `.ai/SEALED_FILES.md` → `.ai/domain-reference/SEALED_FILES.md`
- `_project-docs/KNOWN_ERRORS.md` → `.ai/institutional-memory/KNOWN_ERRORS.md`
- `_project-docs/sprints/sprint-NN/sprint-plan.md` → `.ai/sprints/sprint-NN/sprint-plan.md`
- `_project-docs/sprints/sprint-NN/sessions/YYYY-MM-DD/HANDOVER.md` → `.ai/.daily-docs/{DD Mon YYYY}/session_N_handover.md`
- `_daily-docs/{DD Mon YYYY}/NEXT_SESSION_PROMPT.md` → `.ai/.daily-docs/{DD Mon YYYY}/prompt_for_session_N.md`

The *purpose* of each doc is unchanged — only the *location* differs. Everything else from the framework applies literally.

## Where to start reading (as an agent)

Exact order is in `CLAUDE.md` at the repo root, Section 3 "On Startup — Mandatory Reading Order." Follow it literally, not from memory.
