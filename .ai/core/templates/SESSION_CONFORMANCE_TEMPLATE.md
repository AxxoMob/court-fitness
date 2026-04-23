# Framework Conformance Check — Session {N}

**Date:** YYYY-MM-DD
**Sprint:** Sprint NN
**Framework version applied:** 1.0 (2026-04-17)
**Agent type:** fresh | returning (see `CLAUDE.md` §3)
**Agent:** {model name + variant}

---

## Baseline verification (output pasted from bash)

```
$ git status
{paste output}

$ git log --oneline -5
{paste output}

$ ls -la .ai/
{paste output}

# Optional but recommended: runtime sanity
$ php -v
{paste}

$ php spark migrate:status 2>&1 | tail -5
{paste if applicable}
```

---

## The 8 Conformance Questions

### Q1. What was the outcome of the previous session, in 1 sentence?

{answer — must match latest SESSION_LOG entry}

### Q2. What is the current sprint number and sprint goal?

{answer — must match current `sprints/sprint-NN/sprint-plan.md`}

### Q3. Name up to 3 sealed files currently in `.ai/core/SEALED_FILES.md`. If none, say "none."

{answer}

### Q4. Name 1 Hard Lesson from `.ai/core/HARD_LESSONS.md` that is relevant to this session's work, and explain in 1 sentence why it's relevant.

{answer}

### Q5. Name 1 open Known Error from `.ai/core/KNOWN_ERRORS.md` and its status. If none open, say "none."

{answer}

### Q6. What is THIS session's in-scope list (3-5 bullets)?

{answer — from `prompt_for_session_N.md` and/or owner's opening message}

### Q7. What is THIS session's out-of-scope list (3-5 bullets — explicit deferrals)?

{answer}

### Q8. Which framework rules are most relevant to this session's work? Name at least 2 from AI_AGENT_FRAMEWORK.md Section 1 (the 12 rules), and explain in 1 sentence each why.

{answer}

---

## Agent declaration

I, {agent name/model}, have:
- [ ] Read the mandatory reading list for my agent type (fresh §3.1 or returning §3.2).
- [ ] Run the baseline verification commands above.
- [ ] Answered all 8 questions truthfully; my answers come from actual reading, not inference.
- [ ] Committed this file before requesting owner "proceed."

**Awaiting owner acknowledgment.** Only on explicit "proceed" do I begin state-changing work.

---

## Usage note for the owner

When you receive this file as part of a session-open commit, verify:
- The answers are specific, not generic. "Made improvements" is a failure; "Added JwtValidator with 10 tests, all passing" is an honest answer.
- Baseline verification output is real, not fabricated.
- The agent correctly identifies fresh vs returning.

If any check fails, respond with corrections in chat BEFORE saying "proceed." The framework's enforcement depends on owner audit here.
