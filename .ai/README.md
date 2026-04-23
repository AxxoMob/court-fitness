# `.ai/` — Agent Documentation Map

This folder holds every non-code document for court-fitness. The structure follows the AI Agent Framework's four-tier hierarchy (`.ai-agent-framework/AI_AGENT_FRAMEWORK.md` §2), adapted to the owner's preferred layout.

## Folder map (current — as of 2026-04-23)

| Folder | Framework tier | What's inside |
|---|---|---|
| `.ai-agent-framework/` | Tier 1 | The operating constitution (AI_AGENT_FRAMEWORK.md). **Sealed.** Never edit. Owner-only. |
| `core/` | Tier 1, 2, 3, and 4 combined | Flat folder holding: BRIEFING.md (Tier 1); SEALED_FILES.md + ltat-fitness-findings.md (Tier 2); SESSION_LOG.md + HARD_LESSONS.md + KNOWN_ERRORS.md (Tier 3); WIP.md (Tier 4). All together in one easy-to-browse folder. |
| `core/templates/` | Supporting | Templates for structured artifacts: `SESSION_CONFORMANCE_TEMPLATE.md` (agent copies at session open) + `SESSION_ABORT_TEMPLATE.md` (agent copies only if session cannot close cleanly). |
| `core/archive/` | Tier 3 historical | Archived institutional memory once SESSION_LOG / KNOWN_ERRORS entries age past the threshold defined in `CLAUDE.md` §11. Empty for now. |
| `sprints/` | Tier 4 | One sub-folder per sprint (`sprint-00/`, `sprint-01/`, …) with `sprint-plan.md`. |
| `.daily-docs/` | Tier 4 | One sub-folder per calendar day of work (e.g. `22 Apr 2026/`), containing session handover(s) and next-session prompt(s). |
| `research-notes/` | (extra) | Research artifacts (scripts, notes, transient evidence). Not a framework tier — a pragmatic bucket for "work someone did once that might be useful later." |

## How this folder's shape evolved

- **Sprint 00 Session 1 (2026-04-22, mid-session):** Owner asked the agent to organise `.ai/` into tier-named sub-folders (`briefing/`, `current-state/`, `domain-reference/`, `institutional-memory/`). Agent complied.
- **Between Session 1 and Session 2 (2026-04-23):** Owner flattened those sub-folders into a single `.ai2/` folder.
- **Session 4 (2026-04-23):** Owner-approved architecture retrospective renamed `.ai2/` → `core/` (self-describing) and added `core/templates/` + `core/archive/`. This is the current structure. Historical documents in `sprints/sprint-00/` and `.daily-docs/` still mention older path names in their body — header notes at the top of each redirect readers to the current layout.

## Naming rules

- Dot-prefixed folders (`.ai-agent-framework/`, `.ai2/`, `.daily-docs/`) are the owner's convention for system-like folders.
- Sprint plans: `sprints/sprint-NN/sprint-plan.md` where `NN` is zero-padded (`sprint-00`, `sprint-01`, … `sprint-10`).
- Session handovers: `.daily-docs/{DD Mon YYYY}/session_N_handover.md` (N = session number within that calendar day).
- Next-session prompts: `.daily-docs/{DD Mon YYYY}/prompt_for_session_N.md`.
- Per-day folders: `{DD Mon YYYY}` with spaces (e.g. `22 Apr 2026/`), NOT ISO date.

## Framework path overrides

The AI_AGENT_FRAMEWORK.md (sealed) uses its own default paths like `.ai/BRIEFING.md`, `.ai/WIP.md`. court-fitness overrides those to this project's layout. When the framework says:

- `.ai/BRIEFING.md` → read `.ai/core/BRIEFING.md`
- `.ai/WIP.md` → `.ai/core/WIP.md`
- `.ai/SESSION_LOG.md` → `.ai/core/SESSION_LOG.md`
- `.ai/HARD_LESSONS.md` → `.ai/core/HARD_LESSONS.md`
- `.ai/SEALED_FILES.md` → `.ai/core/SEALED_FILES.md`
- `_project-docs/KNOWN_ERRORS.md` → `.ai/core/KNOWN_ERRORS.md`
- `_project-docs/sprints/sprint-NN/sprint-plan.md` → `.ai/sprints/sprint-NN/sprint-plan.md`
- `_project-docs/sprints/sprint-NN/sessions/YYYY-MM-DD/HANDOVER.md` → `.ai/.daily-docs/{DD Mon YYYY}/session_N_handover.md`
- `_daily-docs/{DD Mon YYYY}/NEXT_SESSION_PROMPT.md` → `.ai/.daily-docs/{DD Mon YYYY}/prompt_for_session_N.md`

The *purpose* of each doc is unchanged — only the *location* differs. Everything else from the framework applies literally.

## Where to start reading (as an agent)

The canonical reading order for every agent at session start is in `CLAUDE.md` §3 at the repo root. Follow it literally, not from memory.
