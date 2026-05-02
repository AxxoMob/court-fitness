This system uses JWT-based SSO between hitcourt.com (auth provider)
and fitness.hitcourt.com (consumer app).

AI MUST NOT implement login inside fitness domain.

All authentication must happen via JWT passed to /sso endpoint.

JWT must:
- Be signed with HS256
- Use HITCOURT_JWT_SECRET
- Expire within 60 seconds

The fitness app must:
- Validate JWT
- Upsert user
- Create session
- Redirect based on role
