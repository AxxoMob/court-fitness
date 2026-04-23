# Screenshots Reference Folder

Per HL-13, every design artifact (screenshot, Figma export, reference image) shared by the owner in chat lands here the moment it arrives, accompanied by a sibling `.md` note.

## Expected conventions

- File naming: `{YYYY-MM-DD}-{short-slug}.png` — e.g. `2026-04-23-ltat-coach-exercises.png`
- Sibling note: same slug, `.md` extension — describes what it shows, who shared it, when, and which feature it references. Template below.

## Pending — owner must re-share + save

The following design artifacts have been **described in prose** (see `.ai/core/plan_builder_ux.md §1` for the full transcription) but the binary image files were NOT saved to the repo at the time they were received. They should be saved the next time the owner shares them:

- `2026-04-22-ltat-coach-exercises.png` — original 5-screenshot walkthrough by owner to Session 1 agent. Showed the live LTAT coach portal including login, dashboard, training-program list, add-exercise form, saved-plan view. Live URL: `https://tourtest.ltat.org/coach-exercises-<base64>.html` (requires coach login).
- `2026-04-23-ltat-coach-exercises-wide.png` — the wide-layout reference owner sent at Session 5 post-mortem. Shows the full-page inline grid with four columns of fundamentals on top, then per-date/per-session exercise rows with inline numeric cells — see `.ai/core/plan_builder_ux.md §1` for the complete transcription.
- `2026-04-23-court-fitness-modal-mobile.png` — owner's screenshot of my wrong Session 5 "Add exercise" modal at mobile width, sent as a visual comparison to show why modal-per-exercise is wrong.

## Template for sibling .md notes

```markdown
# {filename.png}

- **Shared by:** {owner / agent}
- **Date received:** YYYY-MM-DD (session N)
- **Source:** {where it came from — live URL, design tool, camera, etc.}
- **What it shows:** {2-3 sentences describing the visual content without interpretation}
- **Why it was shared:** {owner's intent in sharing it — e.g. "to show the correct Plan Builder layout"}
- **Feature/Screen referenced:** {link to the code path or doc this relates to}
- **Related docs:** {cross-references — HLs, other screenshots, canonical UX docs}
```
