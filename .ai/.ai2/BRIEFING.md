# court-fitness — 1-Page Briefing

**What it is:** A mobile-first Progressive Web App (PWA) for tennis coaches and their players. Coaches build weekly training plans; players and coaches both log actual session results. Both sides stay in sync via ordinary database refresh.

**Where it lives:** Production URL `https://fitness.hitcourt.com`. Local dev at `C:\xampp\htdocs\court-fitness` (XAMPP).

**Who uses it:**
- **Coach** — creates and edits weekly training plans; can also record session actuals when training the player in person.
- **Player** — sees assigned plans; records their own session actuals (especially when travelling without access to a laptop).

**Parent project:** HitCourt (`https://www.hitcourt.com`, currently `https://www.org.hitcourt.com` during Google AdSense processing). HitCourt owns all authentication — court-fitness has no login of its own. Users authenticate on HitCourt and are handed off via a signed JWT to `fitness.hitcourt.com/sso?token=...`.

**Predecessor:** `ltat-fitness-module` at `C:\xampp\htdocs\ltat-fitness-module`, live at `tourtest.ltat.org`. Coaches use it today. court-fitness rebuilds the same workflow cleaner, mobile-friendly, on SSO, better UI. ltat-fitness is a reference, not a template.

**Primary sprint (Sprint 1):** coach-builds-weekly-plan + player-logs-actuals, working on a phone.

**Out of Sprint 1 but on the roadmap:** notifications, rich player analytics dashboard, fitness exercise directory (encyclopaedia), fitness testing & assessments kernel, tennis-specific testing catalogue, training-load / ACWR monitoring, Capacitor native wrap, admin role, multi-language (Thai/English).

**Non-negotiable constraints:**
- Mobile-first UI. Non-responsive layouts are unacceptable.
- No backwards compatibility with ltat-fitness needed.
- One clean migrations folder. No migration drift.
- BCRYPT/ARGON2 passwords (but we don't handle passwords directly — HitCourt does).
- Foreign keys are used (engineering call, deviates from Master Rules Set §2).

**Project horizon:** 3–5 years with a rotating cast of AI coding agents. Hence the AI Agent Framework and its strict session lifecycle.

**Owner:** Rajat Kapoor. Works as Captain (product, scope, priorities); the agent works as Engine Engineer (technical decisions, code). Plain English communication; Rajat is a Vibe-Coder, not a coder.

**Where to go next:** `CLAUDE.md` at repo root for full conventions; `.ai/current-state/WIP.md` for current state; `.ai/sprints/sprint-01/sprint-plan.md` for the upcoming build.
