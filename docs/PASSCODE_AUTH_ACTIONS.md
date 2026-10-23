# Passcode Auth: Action Needed

Follow-ups before merging `feat/passcode-auth`.

## Required

1. Set `ANGKATAN_PASSCODE` in `.env` (and in production env) before seeding.
   If it is empty, `AuthSeeder` generates a random passcode and prints it once.
2. Run migrations, then the seeder:
   ```
   php artisan migrate
   php artisan db:seed --class=AuthSeeder
   ```
   Not yet run against a real database. Automated tests used in-memory SQLite only.
3. Verify manually in a browser:
   - `/unlock` form submits and redirects to the directory.
   - The `angkatan_session` cookie is stored. It has the `Secure` flag, so some
     browsers will not store it over plain `http://localhost`. Use HTTPS locally
     or test on the deployed URL.
4. Run `npm run build` (or `npm run dev`) so the Tailwind styles on `unlock.blade.php` appear.

## Decisions for the team

- The session is an encrypted cookie holding role, `auth_epoch` and expiry. It is
  not a Sanctum token, because there is no `personal_access_tokens` table and
  students are not `User` records. If real Sanctum tokens are required, add that
  migration and a model for students.
- Logout clears the cookie but cannot revoke a copy held elsewhere. Bump
  `auth_epoch` in `app_config` to invalidate all sessions.
- The `Secure` cookie flag is always on. Make it environment-aware if local HTTP testing is needed.

## Not included in this task

- Route middleware protecting directory and student pages (`student` guard).
- Audit log entry `PASSCODE_FAIL` mentioned in `docs/API_CONTRACTS.md`.
- Admin endpoint to rotate the passcode and bump `auth_epoch`.
- The existing `EnsureAngkatanPasscode` middleware is unchanged and still uses `config('angkatan.passcode')`.
