# G5 Member Runtime

This repository contains only the runtime files needed to run the member-only G5 application.

## Runtime Requirements

- PHP 8.3 or compatible PHP runtime
- MySQL or MariaDB
- Web server document root pointed at this directory
- Writable `data/` directory on the deployed server

## Required Local Files

Create the deployment-only DB config file at:

```text
data/dbconfig.php
```

The application reads the filename from `G5_DBCONFIG_FILE`, which defaults to `dbconfig.php`.

The runtime also needs these writable paths under `data/` when the related features are used:

```text
data/session/
data/cache/
```

## Runtime Configuration

Environment-specific settings live in:

```text
config.runtime.php
```

Edit this file per deployment for domain, cookie, debug, SQL error, DB charset, and SMTP settings. Keep `debug`, `collect_query`, and `display_sql_error` disabled in production unless you are actively diagnosing a private environment.

Important keys:

- `domain`: canonical site URL, or empty for auto-detected URL
- `https_domain`: HTTPS URL used for secure member flows
- `cookie_domain`: shared cookie domain, such as `.example.com`
- `dbconfig_file`: DB config filename under `data/`
- `smtp_host`, `smtp_port`: mail transport settings

## Frontend Asset Build

Tailwind 4 source files live in:

```text
tailwind4/
```

Install dependencies and rebuild generated CSS with:

```text
npm ci
npm run build
```

The build writes generated assets to `css/common.css`, `css/theme.css`, and `adm/css/admin.css`.

## Remaining Runtime Directories

- `adm/`: admin pages and admin assets
- `member/`: member authentication, registration, account pages, and member templates
- `lib/`: shared runtime libraries and domain helpers
- `plugin/`: mailer, captcha, purifier, and identity verification plugins
- `css/`, `js/`: prebuilt frontend assets

## Deployment Check

After deployment, verify these flows with a real database:

1. Login and logout
2. Register member
3. Email verification
4. Password reset
5. Member profile update
6. Admin login
7. Admin member list and member edit
8. Member XLSX export
