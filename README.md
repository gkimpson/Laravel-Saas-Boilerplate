# SaaS Boilerplate

A reusable Laravel starting point for SaaS products, grown from `laravel/vue-starter-kit`. It ships team-based multi-tenancy, authentication, and an admin panel, with billing and entitlements on the way.

There's a longer-term PRD for this project describing a much larger target (organisations, entitlements, usage metering, Flowbite, MySQL, ULIDs). Most of that isn't built yet — see [What's not built yet](#whats-not-built-yet) before assuming a feature exists.

## Stack

- **Backend:** Laravel 13, PHP 8.4
- **Frontend:** Inertia v3, Vue 3, TypeScript, Tailwind CSS v4
- **UI components:** shadcn-vue (`new-york-v4`) on reka-ui, lucide icons
- **Auth:** Laravel Fortify — email verification, 2FA, passkeys
- **Admin:** Filament v5 at `/admin`
- **Testing:** Pest 5
- **Also installed:** Laravel Cashier, Laravel Pennant, Spatie Permission, Spatie Activitylog, Spatie Media Library, Maatwebsite Excel, Flysystem S3 (mostly not wired up yet — see below)

## Getting started

```bash
composer setup
```

This installs PHP and JS dependencies, copies `.env.example` to `.env`, generates an app key, runs migrations, and builds the frontend. The database defaults to SQLite.

Once set up, run:

```bash
composer dev
```

This starts the app server, queue worker, log tailer, and Vite dev server together.

The app is served by [Laravel Herd](https://herd.laravel.com) at `https://saas-boilerplate.test`. Don't run `artisan serve`.

## Common commands

```bash
composer dev            # serve + queue + logs + vite, for local development
composer ci:check        # everything CI runs: frontend checks, types, tests
composer lint            # fix PHP style (Pint)
composer types:check     # PHPStan (Larastan, level 7)
php artisan test --compact --filter=SomeTest
vendor/bin/pint --dirty --format agent

npm run dev              # Vite dev server
npm run check:fix        # lint + format frontend (vite-plus, not ESLint/Prettier)
npm run types:check      # vue-tsc
```

Don't hand-edit generated frontend code: `resources/js/{actions,routes,wayfinder}` (Wayfinder) and `resources/js/components/ui` (shadcn-vue).

## How tenancy works

The tenant is a **Team**, not an "organisation". The chain is `User -> Membership (team_members) -> Team`, and every user gets a personal team on registration.

Tenant-scoped routes are prefixed with the team's slug (e.g. `/{current_team}/dashboard`). Three pieces work together to make that transparent:

- `EnsureTeamMembership` resolves the team from the URL, blocks non-members, and switches the user's current team to match. It can also require a minimum role.
- `SetTeamUrlDefaults` fills in the team slug automatically, so `route('dashboard')` doesn't need it passed explicitly.
- `HandleInertiaRequests` shares the current team and team list with every page.

Authorisation for team actions uses `TeamRole`/`TeamPermission` enums and a `TeamPolicy` (e.g. `$user->hasTeamPermission($team, TeamPermission::UpdateTeam)`). Spatie's permission package is installed but not used for this — don't reach for it here.

## What's not built yet

Installed but not yet wired into application code: Cashier (billing), Pennant (feature flags), Spatie Permission, Activitylog, Media Library, Excel, Flysystem S3.

Not present despite the longer-term PRD: a domain-oriented `app/Domain` structure, Flowbite (the UI kit is shadcn-vue), MySQL as the default (SQLite ships by default), ULID identifiers (integer keys, teams addressed by slug), Redis (database driver for queue/cache/session), entitlements/usage metering, Horizon, Pulse, Sentry, Socialite.

## For AI coding agents

See `CLAUDE.md` (mirrored in `AGENTS.md`) for the codebase map, verification commands, and the full list of gaps between this repo and its target architecture.
