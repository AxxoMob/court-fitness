# Session Abort — Session {N}

> ⚠️ This session did NOT close cleanly. Read this file FIRST before any other action. Decide: resume, revert, or escalate.

**Date of abort:** YYYY-MM-DD HH:MM (local time)
**Sprint:** Sprint NN
**Agent:** {model name + variant}
**Framework version applied:** 1.0 (2026-04-17)

---

## 1. Abort reason (check one or more, expand below)

- [ ] Context budget exhausted mid-work — could not fit all 7 close artifacts.
- [ ] Tool call errored in a way that left repo state uncertain.
- [ ] Owner signalled an interrupt mid-step.
- [ ] Other (describe below).

**Detail:** {one paragraph — what specifically happened, at what step}

---

## 2. What was in-flight when the abort was triggered

{list — the specific task the agent was executing, e.g.:
- "Was running migration 5 of 8 (2026-04-23-130300_CreateSomeTable); migration command did not return."
- "Was writing app/Controllers/Coach/Plans.php — file exists but is incomplete (missing `store()` method)."
- "Had just run `composer require foo/bar` — composer.json updated but composer.lock state uncertain."}

---

## 3. Current repo state at abort time

### Git

```
$ git status
{paste output}

$ git log --oneline -5
{paste output}

$ git diff --stat HEAD
{paste output}
```

### Filesystem changes not yet committed

{list any new or modified files that are NOT in the above git status, if any — e.g. files written but then the write tool crashed before a later verification}

### Database state (if touched this session)

```
$ php spark migrate:status 2>&1
{paste output}
```

**Tables mutated this session:** {list}
**Seeders run this session:** {list}
**Any schema change that is NOT captured in a committed migration file:** {list — THIS IS THE DANGEROUS CATEGORY per HL-1}

### Other state

{filesystem / env / services — anything the next agent needs to know about}

---

## 4. What the next agent should do

Recommended action (pick one and explain):

- **RESUME:** continue from where the abort happened. Safe when the in-flight work is restart-idempotent (e.g. `composer install` can be re-run safely). Outline the exact next 3 steps.
- **REVERT:** roll back partial changes before doing anything else. List the specific commands needed to get back to the last clean commit (e.g. `git checkout -- path/to/file`, `php spark migrate:rollback`, etc.).
- **ESCALATE:** repo state is ambiguous — do not touch anything. Owner must decide the recovery path in chat.

{the aborting agent writes their recommendation here, with reasoning}

---

## 5. Files that may need attention

{list — ranked by risk. Example:
- `app/Database/Migrations/2026-04-23-130400_Foo.php` — created but never ran; safe to leave.
- `app/Controllers/Coach/Plans.php` — half-written; will fail PHP parse. Either delete or complete.}

---

## 6. Minimal repro of the abort (if applicable)

{if the abort was caused by a reproducible error, paste enough of it that the next agent can understand what went wrong. If the abort was context-exhaustion, skip — not reproducible.}

---

## 7. Agent declaration

I, {agent name/model}, declare:

- [ ] This session did NOT close cleanly.
- [ ] I have NOT committed any in-flight work to `main` unless it is in a coherent state.
- [ ] This abort file is being committed as the ONLY close artifact of this session.
- [ ] The next agent MUST read this file before touching the repo.

{signature line: agent model + the session number + the date}

---

## Usage note

The next agent's opening ritual when an abort file exists:

1. Read THIS file in full, FIRST — before CLAUDE.md, before WIP.md, before anything else.
2. Run the same baseline verification commands from §3 above to confirm current state matches what was recorded at abort time (state may have drifted if the owner did anything between sessions).
3. Decide resume vs revert vs escalate based on §4's recommendation AND your own judgement.
4. Proceed with the chosen path. ONLY after the chosen path is executed cleanly should you start any new session work.
5. In your handover, record what you did with the aborted session — did you resume it cleanly? revert? — so the project's narrative is continuous.
