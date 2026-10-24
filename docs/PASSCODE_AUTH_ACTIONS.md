# Passcode Auth: Changes and Follow-ups

## Implemented

### Login and session

- `AuthSeeder` stores a bcrypt hash as `student_passcode` and initializes `auth_epoch`.
- `POST /api/auth/passcode` allows five failed attempts per IP in a 15-minute window; the next request receives `429 Too Many Requests`.
- A successful login issues an encrypted `angkatan_session` cookie containing the student role, auth epoch, expiry, and a unique session ID. The cookie is `HttpOnly`, `Secure`, and `SameSite=Lax`.
- The token is encrypted with Laravel `Crypt`. `bootstrap/app.php` excludes this cookie from `EncryptCookies` to prevent double encryption when the browser sends it to web routes.
- `GET /api/auth/session` validates the token, expiry, current epoch, and active session record.
- `DELETE /api/auth/passcode` deletes that session's record and clears the cookie. Replaying a copied token after logout is rejected without logging out other students.
- Active session IDs and expirations are stored in the `student_sessions` table.

### Student page protection and data scope

- The `student.epoch` and `student.role` middleware protect `/directory` and `/students/{nim}`. Guests and invalid sessions are redirected to `/unlock`.
- `AppConfigService::authEpoch()` reads the current value directly from `app_config`, so a direct database update takes effect without waiting for a cache invalidation.
- `Student::public()` selects an allowlist of public columns and excludes `address` and `whatsapp_no`. Directory and profile queries use this scope. There is no emergency-contact column in the current `students` schema.
- `/gallery` is not implemented and is intentionally not included in the protected routes.

### Unlock page

- `/unlock` submits the passcode with Fetch API to `POST /api/auth/passcode`.
- A successful response redirects to `/directory`; server errors, invalid passcodes, and rate limiting are shown in the form.

## Deployment and Manual Checks

1. Set `ANGKATAN_PASSCODE` in the environment before initial seeding. If it is empty, `AuthSeeder` generates a random passcode and prints it once.
2. Run `php artisan migrate` in each target environment to create `student_sessions`, then seed initial auth configuration with `php artisan db:seed --class=AuthSeeder`.
3. Test `/unlock` and `/directory` in a browser over HTTPS and confirm the `angkatan_session` cookie is stored. The cookie always has `Secure`; browsers may reject it over plain HTTP.
4. Run `npm run build` (or `npm run dev`) to compile the unlock page's Tailwind styles.

## Remaining Notes

- Running `AuthSeeder` again resets `auth_epoch` to `1`. Avoid re-running it after session epochs have been rotated; making this seeder safe to rerun remains a follow-up.
- The `PASSCODE_FAIL` audit log mentioned in `docs/API_CONTRACTS.md` is not implemented.
- There is no admin endpoint for rotating the passcode or incrementing `auth_epoch` yet.
- `EnsureAngkatanPasscode` remains a separate guard for the public storage API and still checks `config('angkatan.passcode')`; it is not the student session guard.
- The student session is a custom encrypted cookie backed by `student_sessions`, not a Sanctum personal access token. Students are not `User` records.