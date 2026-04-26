# Exercise JSON Shapes — `plan_entries.target_json` and `actual_json`

> Reference doc. `plan_entries` holds two JSON columns whose expected keys
> differ by `exercise_type_id`. Those shapes are not enforced by the DB or
> the PlanEntriesModel validator — they are enforced by UI convention and by
> the Plan Builder JS that writes the blobs.

**Cell-count contract per format (owner directive 2026-04-26, confirmed against `.ai/research-notes/screenshots/same-day-3-sessions.jpg`):**

| Format  | Cells per row | All typable? | Rendering note |
|---------|---------------|--------------|----------------|
| Cardio  | 10            | Yes          | First 3 (Target, Max HR %, Duration) render normally; remaining 7 render with a faint/muted style as a workflow hint. **Functionally typable regardless.** |
| Weights | 4             | Yes          | All 4 render normally. |
| Agility | 6             | Yes          | All 6 render normally. |

The greying observed in the LTAT screenshot is **presentational only** — owner explicitly clarified it does not gate input. Any UI implementation must keep all cells fully typable.

**Sources of truth (must agree):**
1. This file
2. `public/assets/js/plan-builder.js` — the `CELLS_BY_FORMAT` table
3. `app/Views/coach/plans/_grid.php` — the partial mounted on new + show views

---

## Cardio  (exercise_types.name begins with "Cardio" — case-insensitive)

10 cells per row. Keys, in display order:

```json
{
  "target":       null,    // free numeric (e.g. target distance / interval count)
  "max_hr_pct":    75,     // integer 40–100, percent of max HR
  "duration_min":  30,     // integer 1–300, minutes
  "sets":         null,    // integer 1–20         (secondary)
  "reps":         null,    // integer 1–100        (secondary)
  "work":         null,    // integer, work seconds (secondary)
  "set_rest_sec": null,    // integer 0–600, between-set rest, secs (secondary)
  "set_count":    null,    // integer, current set marker (secondary)
  "rep_count":    null,    // integer, current rep marker (secondary)
  "weight":       null     // decimal step=0.5, 0–500 (secondary; for weighted Cardio circuits)
}
```

Every key is nullable. Coach prescribes only what's relevant — e.g. for a recovery run the typical bag is `{ "max_hr_pct": 65, "duration_min": 30 }` and the other 8 keys remain null.

## Weights  (exercise_types.name begins with "Weights")

4 cells per row. Keys, in display order:

```json
{
  "weight":        60.0,   // decimal step=0.5, 0–500
  "sets":           3,     // integer 1–20
  "reps":           8,     // integer 1–100
  "set_rest_sec":  90      // integer 0–600
}
```

`weight` is a raw number; the unit (kg or lb) lives on the parent `training_plans.weight_unit`, NOT on the entry. Do NOT suffix `_kg` or `_lb` onto the key — a mixed-unit plan is not allowed by design.

## Agility  (exercise_types.name begins with "Agility")

6 cells per row. Keys, in display order:

```json
{
  "sets":         4,     // integer 1–20
  "reps":         6,     // integer 1–100
  "works":       null,   // integer (work-bout count)
  "rests":       null,   // integer (rest-bout count)
  "total_work":  null,   // integer (cumulative work seconds or bouts)
  "work_rest":    45     // integer 0–600 (rest between work bouts)
}
```

---

## Why JSON instead of per-column fields

See CLAUDE.md §5.6 and `C:\xampp\htdocs\ltat-fitness-module\docs\TASK_21_IMPLEMENTATION_SUMMARY.md`. The inherited design intent is to keep `plan_entries` narrow (200+ exercises with very different input shapes would need 80% NULLs across rigid columns). The model layer does not enforce the shapes because the validity of each key is context-dependent (Cardio only, Weights only, etc.) and cheap to validate at the UI boundary.

## What NOT to put in these blobs

- **Unit strings** (`"kg"`, `"lb"`) — that lives on the plan, not the entry.
- **Timestamps** (`actual_at` is a proper column on the row; don't duplicate).
- **Free-text notes** — if per-exercise notes become a requirement, add a proper `notes` VARCHAR column to `plan_entries`, don't stuff them in JSON.
- **Foreign keys** — the entry row already carries `exercise_type_id`, `fitness_category_id`, `fitness_subcategory_id` as proper columns with FKs. Never re-encode those into the blob.

## What happens if a legacy blob has unexpected keys

The Plan Builder JS reads the blob into pre-defined cell inputs by key. Unknown keys are **ignored** (their values aren't lost — they remain in `target_json` and round-trip on save) but they don't render as cells. The new contract is forward-compatible: a future format with additional cells just needs an entry in `CELLS_BY_FORMAT` and this doc.

## Adding a new exercise type

1. `INSERT INTO exercise_types (name, sort_order, is_active)` — give the name a clear English root (the JS regex uses name-prefix matching).
2. Append a new section above to this doc with the cell list and per-cell key/label/min/max/step.
3. Add the format's entry to `CELLS_BY_FORMAT` in `public/assets/js/plan-builder.js`. Use `secondary: true` on cells that should render with the muted-but-typable style.
4. No view changes needed — `_grid.php` reads from the same JS table.

## Demo seed data caveat (Session 6 follow-up)

The Session 3 demo seed wrote `weight_kg` and `rest_sec` for the Bench Press entry; the canonical key per this doc is `weight` and `set_rest_sec`. The new Plan Builder won't pre-populate those cells for the demo row because the keys don't match. **Cosmetic issue for the demo only**; the schema supports any keys. Fix at next demo seed refresh.
