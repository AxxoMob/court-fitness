# Session Log

Append-only. One row per session. Most recent at the top. Each row is 1–3 lines. Per framework §6.1.

| Session | Date | Sprint | Summary |
|---|---|---|---|
| 2 | 2026-04-23 | Sprint 01 | Sprint 01 Session 1. All 3 Sprint 00 blockers resolved at session open (JWT carries `role`; stub SSO for dev; GitHub remote already live). Housekeeping: fixed 18 stale path references across 10 files after owner flattened `.ai/` subfolders into `.ai/.ai2/` (commit 54a12b0). `composer install` + `firebase/php-jwt ^7.0` (commit 42d4300). `App\Services\JwtValidator` + `JwtValidationException` + `App\Controllers\Sso` skeleton + `/sso` route + 10 unit tests (23 assertions, all passing). `.env` configured locally (gitignored). HL-11 added (firebase/php-jwt `DomainException` path). No blockers. Session 3 picks up: AuthFilter, `users` + `coach_player_assignments` migrations, DB wiring in SsoController, role-based redirect, then PWA manifest. |
| 1 | 2026-04-22 | Sprint 00 | Project bootstrap. Read AI_AGENT_FRAMEWORK.md (1368 lines) + ltat-fitness-module predecessor project. Clarified scope with owner across four rounds (fitness-testing → full workflow; Falcon → mobile-first; self-auth → HitCourt SSO; free-text target → DB dropdown). Produced all framework scaffolding (CLAUDE.md, BRIEFING, WIP, SESSION_LOG, HARD_LESSONS x9, KNOWN_ERRORS, SEALED_FILES, ltat-fitness-findings) in `.ai/` subfolders. Drafted Sprint 01 plan (coach-plans-week + player-logs-actuals). No code written. git init + first commit. 3 open questions to owner carried into Session 2. |
