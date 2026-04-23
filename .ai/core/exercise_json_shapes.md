# Exercise JSON Shapes — `plan_entries.target_json` and `actual_json`

> Reference doc. `plan_entries` holds two JSON columns whose expected keys
> differ by `exercise_type_id`. Those shapes are not enforced by the DB or the
> PlanEntriesModel validator — they are enforced by UI convention and by the
> Plan Builder JS that writes the blobs. A future agent touching these
> columns should keep shapes consistent with the list below so that the show
> view, the player actuals UI (Session 6+), and any future analytics agree
> on the same keys.

**Sources of truth for the shapes (all must agree):**
1. This file
2. `public/assets/js/plan-builder.js` — the `readTargetFor()` and
   `summariseTarget()` functions
3. `app/Views/coach/plans/show.php` + `app/Views/player/plans/show.php`
   — which render these blobs as badges

---

## Cardio  (exercise_types.name begins with "Cardio" — case-insensitive)

```json
{
  "max_hr_pct":   75,        // integer 40–100, percent of max HR
  "duration_min": 30         // integer 1–300, minutes
}
```

Both keys are nullable (coach may prescribe only one). Player actuals re-use
the same keys — e.g. the player may log `{ "duration_min": 28 }` to mean "I
ran for 28 minutes; no HR tracked."

## Weights  (exercise_types.name begins with "Weights")

```json
{
  "sets":     3,             // integer 1–20
  "reps":     8,             // integer 1–100
  "weight":   60.0,          // decimal, half-step (step=0.5), 0–500
  "rest_sec": 90             // integer 0–600
}
```

`weight` is a raw number; the unit (kg or lb) lives on the parent
`training_plans.weight_unit`, NOT on the entry. Do NOT suffix `_kg` or `_lb`
onto the key — a mixed-unit plan is not allowed by design, and the UI reads
the unit from the plan, not the entry.

## Agility  (exercise_types.name begins with "Agility")

```json
{
  "reps":     6,             // integer 1–100
  "rest_sec": 45             // integer 0–600
}
```

Same nullability — coach may leave rest_sec blank; player may log only reps.

---

## Why JSON instead of per-column fields

See CLAUDE.md §5.6 and `C:\xampp\htdocs\ltat-fitness-module\docs\TASK_21_IMPLEMENTATION_SUMMARY.md`. The inherited design intent is to keep
`plan_entries` narrow (200+ exercises with very different input shapes would
need 80% NULLs across rigid columns). The model layer does not enforce the
shapes because the validity of "`max_hr_pct` is required" is context-dependent
(Cardio only) and cheap to validate at the UI boundary.

## What NOT to put in these blobs

- **Unit strings** (`"kg"`, `"lb"`) — that lives on the plan, not the entry.
- **Timestamps** (`actual_at` is a proper column on the row; don't duplicate).
- **Free-text notes** — if per-exercise notes become a requirement, add a
  proper `notes` VARCHAR column to `plan_entries`, don't stuff them in JSON.
- **Foreign keys** — the entry row already carries `exercise_type_id`,
  `fitness_category_id`, `fitness_subcategory_id` as proper columns with FKs.
  Never re-encode those into the blob.

## What happens if a legacy blob has unexpected keys

Both show-view renderers walk the decoded JSON with a generic
`foreach ($target as $k => $v)` loop, so extra keys render as additional
badges without breaking the page. This is intentional — it means forward
compatibility (Session 6 may add `tempo` or `hr_zone` without migrations)
and it means malformed legacy blobs degrade gracefully to "show what we
have." If you need strict-only shapes in future, centralise the validation
in a helper and cite THIS doc so agents know the contract changed.

## Adding a new exercise type

1. `INSERT INTO exercise_types (name, sort_order, is_active)` — give the
   name a clear English root (the JS regex uses name-prefix matching).
2. Append a new section above to this doc with the blob shape.
3. Add the type-specific target-field group to `app/Views/coach/plans/new.php`
   under `<div class="row g-2 cf-target-group" data-target-group="<newkey>">`.
4. Extend `readTargetFor()` and `summariseTarget()` in plan-builder.js.
5. Extend the render loops in the two show views (if keys need special
   formatting — otherwise the generic renderer covers it).
