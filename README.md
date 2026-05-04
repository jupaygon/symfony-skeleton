# Symfony Skeleton — Hexagonal, Hardened, Auth-Ready

Symfony 8 starter template with hexagonal architecture and the security baseline already wired. **Brings no authentication of its own** — drop in a custom authenticator (form login, API-backed, OIDC, …) and you have a project ready to ship.

This repo is the auth-less counterpart of [`symfony-dashboard-skeleton`](https://github.com/jupaygon/symfony-dashboard-skeleton). Use the dashboard variant when you need EasyAdmin out of the box; use this one when the admin panel lives elsewhere (separate API repo, headless backend, chat UI, etc.).

## What you get

- **Symfony 8.0** + PHP 8.4 + Doctrine ORM 3 + PHPUnit 11
- **Hexagonal architecture** — Domain / Application / Infrastructure with strict layer rules
- **Security headers** via `nelmio/security-bundle` (CSP, X-Frame-Options, referrer policy, …)
- **Stateless CSRF** ready (`framework.csrf_protection.stateless_token_ids`)
- **Trusted hosts** placeholder (`TRUSTED_HOSTS` env)
- **Rate limiter** (`symfony/rate-limiter`) ready to wire on login/auth endpoints
- **UUID v7** Doctrine type registered as `uuid`
- **Cookie hardening** — `httponly: true`, `secure: auto`, `samesite: lax`
- **APP_SECRET placeholder** — `.env` ships with `changeme`, override in `.env.local` or env var
- **i18n** — English + Spanish ICU bundles, allowlisted `_locale` query param
- **Brand skinning** — host-based brand resolution with CSS-variable themes (default brand included; add more for white-label)
- **AssetMapper + Twig** wired
- **Watch mode** for assets: `php bin/console app:assets:watch`

## What you do NOT get (by design)

- No User / Organization entity
- No `form_login`, no `login_throttling`, no `remember_me` (security bundle is installed but firewalls are unprotected stubs — wire your own authenticator)
- No EasyAdmin
- No symfony/form, no symfony/validator, no symfony/monolog (add when you need them; the skeleton boots without them)

## Requirements

- PHP >= 8.4
- Composer
- A database (SQLite default; MySQL / Postgres via `.env.local`)

## Quick Start

```bash
git clone https://github.com/jupaygon/symfony-skeleton.git my-project
cd my-project
rm -rf .git && git init

composer install

# Override APP_SECRET (and DATABASE_URL if not using SQLite) in .env.local
cp .env .env.local
# edit .env.local
```

Run the app with the Symfony binary, `php -S`, or your usual Docker stack.

## Architecture

```
src/
├── Application/
│   └── Service/                # Use cases (BrandContext)
├── Domain/
│   ├── Contract/               # Interfaces (BrandInterface)
│   ├── Model/                  # Entities (empty — add yours)
│   ├── Port/                   # Repository interfaces (empty — add yours)
│   └── ValueObject/            # Value objects (Brand)
└── Infrastructure/
    ├── Branding/               # BrandResolver
    ├── Command/                # AssetsWatchCommand
    ├── EventSubscriber/        # BrandResolverSubscriber, LocaleSubscriber
    ├── Http/
    │   ├── Api/                # API endpoints (empty)
    │   └── Controller/         # Web controllers (IndexController = landing)
    ├── Persistence/Doctrine/   # Repository implementations (empty)
    └── Translations/           # i18n files (en, es)
```

### Layer rules

- **Domain** — no dependencies on Infrastructure or Application
- **Application** — orchestrates domain objects, depends on Domain only (via Port interfaces)
- **Infrastructure** — implements Port interfaces, handles HTTP / database / templates

## Wiring authentication

`config/packages/security.yaml` ships with no provider and an unprotected `main` firewall. To add auth:

1. Configure a provider (Doctrine entity, in-memory, custom service)
2. Add an authenticator to the `main` firewall (`form_login`, `custom_authenticators`, …)
3. Add `login_throttling` + `remember_me` if appropriate
4. Tighten `access_control` rules

See the comments in `config/packages/security.yaml` for examples.

## Brand skinning

Brands resolve from the request hostname via `BrandResolverSubscriber`. Each brand defines its own CSS variables in `assets/brands/<key>/css/skin.css`.

```yaml
# config/brands.yaml
parameters:
    brands_default: 'default'
    brands_map:
        'app.example.com': 'default'
        'partner.example.com': 'partner'
    brands_defs:
        default: { name: 'Default', dark: false }
        partner: { name: 'Partner', dark: false }
```

To add a brand: copy `assets/brands/default/` → `assets/brands/<key>/`, edit `skin.css`, register in `brands.yaml`.

## Translations

Translation files live in `src/Infrastructure/Translations/messages+intl-icu.<locale>.yaml`. Allowed locales are configured in `config/parameters.yaml`:

```yaml
parameters:
    app.languages:
        'en': { code: 'en', name: 'English' }
        'es': { code: 'es', name: 'Español' }
```

`LocaleSubscriber` allowlists the `_locale` query param against this list — unknown locales are silently rejected.

## Tests

```bash
./vendor/bin/phpunit
```

## License

MIT
