# Sprint 02 — court-fitness

**Status:** Draft. Sprint 01 still in progress (Session 7 of Sprint 01 is open as this is written).
**Authored:** 2026-04-27 (Sprint 01 Session 7 — owner directive to lock Sprint 02 reservations before they're forgotten).
**Estimated size:** TBD; depends on which items make the cut at Sprint 01 close.

---

## Sprint 02's load-bearing reservations (locked at Sprint 01 Session 7 — DO NOT lose)

These are owner-approved decisions, recorded in chat 2026-04-27, that MUST land in Sprint 02. They are ahead-of-time scope locks so that whatever else Sprint 02 contains, these decisions are honoured.

### Reservation 1 — Trainer ↔ Player association: Option 1 + Option 4 (paired)

**Owner approval (verbatim, 2026-04-27 chat):**
> "I am more inclined with your recommendation - Pair Option 1 (Player Invite Code) with Option 4 (Admin Assignment). Please add it to the Sprint 02 Plan and of course a follow up note in WIP.md. It is important that this decision is not lost and does get implemented."

**The decision:**

Two complementary mechanisms write to the same `coach_player_assignments` table. Which one a user sees depends on their role.

#### Option 1 — Player Invite Code (standalone freelance trainer flow)

- The trainer generates a short code in their dashboard (e.g. `RAJAT-7K9P`).
- Trainer shares the code with the player out-of-band (WhatsApp / email / in person).
- Player logs in, finds a "Join a trainer" CTA, types the code → server creates a `coach_player_assignments` row.
- **Code expires after first successful use OR after 24 hours**, whichever is sooner. This plugs the leak risk: a leaked code can't be reused or stockpiled.
- New table: `trainer_invite_codes` (id, code VARCHAR(16) UNIQUE, coach_user_id FK, expires_at DATETIME, redeemed_at DATETIME NULL, redeemed_by_user_id FK NULL, timestamps + soft delete).

#### Option 4 — Admin Assignment (1(b) institutional flow)

- A `users.role` enum value `admin` is added (in addition to `coach` and `player`).
- The admin role is **scenario 1(b)**: a setup-manager who runs multiple trainers under one institution. The admin lives inside court-fitness (NOT on HitCourt).
- New module: `app/Modules/Admin/` with controllers `Admin\Dashboard`, `Admin\Trainers`, `Admin\Players`, `Admin\Assignments`.
- Admin dashboard surfaces:
  - All trainers under the admin's institution.
  - All players the institution wants to assign.
  - Drag-and-drop UI to attach players to trainers (or simpler: a select-and-assign form for Sprint 02).
- The admin's assignment writes the same `coach_player_assignments` row a trainer would write via Option 1, but with `assigned_by_user_id = admin's user_id` so audit trails show institutional vs self-issued.

#### How the two flows coexist

| User flow | Mechanism | Who creates the assignment |
|---|---|---|
| Standalone freelance trainer + standalone player | Option 1 (invite code) | Trainer (self-issued) |
| Institution with admin + multiple trainers + players | Option 4 (admin drag-drop) | Admin |
| Hybrid: admin-managed trainer also takes a side gig | Option 1 — the trainer can still issue their own codes for one-off players | Trainer (self-issued) |

Schema-wise: ONE table (`coach_player_assignments`) handles both; only the audit-trail field `assigned_by_user_id` differs. NO schema fork between scenarios.

#### First-visit role choice screen

When a brand-new HitCourt user first lands on `fitness.hitcourt.com`, court-fitness presents a one-time choice screen:

> "Welcome to court-fitness. How do you want to use it?
>
> ☐ I'm a trainer working for myself (standalone freelancer)
> ☐ I run a tennis academy or coaching setup with multiple trainers
> ☐ I'm a player looking to log my training"

The chosen role is written to `users.role`. After the choice, court-fitness routes to `/coach`, `/admin`, or `/player` accordingly. The choice is reversible only by a HitCourt-admin escalation in Sprint 03+.

### Reservation 2 — Admin role enum value

`users.role` enum needs a third value `admin` (in addition to existing `coach` and `player`). Sprint 02 migration to add it. Existing users default to whatever they registered as; new admins go through the role-choice flow above.

### Reservation 3 — `trainer_invite_codes` table migration

Sprint 02 ships a new migration adding the `trainer_invite_codes` table per Option 1's schema sketch above.

### Reservation 4 — AuthFilter updates

When `admin` role is added, the AuthFilter's role-routing must learn the new value (`/admin/*` paths gated to admin role only; coach + player paths stay as-is).

---

## Other Sprint 02 candidates (not yet locked — owner reviews at Sprint 01 close)

The following items were on Sprint 01's deferred list but never landed; they're candidates for Sprint 02 unless owner re-prioritises:

- **PWA manifest + service worker.** `public/manifest.json` + `public/sw.js` + icons. Lighthouse PWA baseline.
- **Per-type server-side validation of `target_json` / `actual_json`.** `App\Validation\PlanEntryShapeValidator` static helper, wired into `Coach\Plans::store/update` and `Player\Plans::update`. Tests in `tests/unit/PlanEntryShapeValidatorTest.php`.
- **Prettier label prettifier in plan-builder.js** for tooltips/summaries.
- **Empty-block edge case.** When a coach removes the last exercise row from a block via `[-]`, the block has no rows and no row-level `[+]` to add one back. Either disable `[-]` when only 1 row remains, or auto-delete the block when emptied. Owner to decide.
- **Real start-times on Training Date.** LTAT's date input accepts `dd-mm-yyyy hh:mm`; court-fitness currently stores DATE only. Schema change to DATETIME or add a `start_time TIME NULL` column.
- **UI-level greying of non-Mondays in Weekof picker** + non-window dates in Training Date picker. Both are server-enforced today; UI parity is polish.
- **Dense table view for `/coach/plans`** (alternative to card grid) when plan counts grow. LTAT's `program-list-view.jpg` shows the pattern.
- **Per-row autosave on actuals.** Currently single big-Save form. AJAX per-row saves bring CSRF-token-rotation complexity (HL-12). Sprint 02 only if owner wants the better UX.
- **Trainer/Coach vocabulary cleanup** in any remaining ltat-fitness-style text.
- **No-clobber unit test for the player-update silent-drop of target fields.** Currently behaviour is enforced; not yet unit-tested.

---

## Out of Sprint 02 (reserved for Sprint 03+)

- Assessments / metric_types kernel (the testing-feature backend).
- Tennis-specific testing catalogue.
- Training-load / ACWR monitoring (BPP-style).
- Rich player analytics, charts, progression views.
- Fitness directory (exercise encyclopaedia with descriptions + videos).
- Capacitor native wrap (App Store / Play Store).
- Multi-language (Thai / English).
- HitCourt-managed admin scenario 1(a) — alternative architecture to 1(b) which we explicitly chose against.

---

## Reading list for the Sprint 02 Session 1 agent

In addition to the standard Sprint 01 reading list (CLAUDE.md §3), specifically:

1. **THIS file** in full — it locks Sprint 02's reservations.
2. `.ai/core/WIP.md` — for the running state and the "Sprint 02 reservations" pointer at the top.
3. `.ai/core/SEALED_FILES.md` — six (or more) sealed files at Sprint 01 close; do not edit any of them without §5.3 protocol.
4. `.ai/.daily-docs/{Sprint 01 close date}/session_N_handover.md` — the closing Sprint 01 session's handover, which will record what landed vs deferred.
5. `.ai/sprints/sprint-01/sprint-plan.md` — for context on what Sprint 01 was supposed to do vs what shipped.

Standard agent protocol: read the full mandatory list, run the Conformance Check, commit it, request "proceed."
