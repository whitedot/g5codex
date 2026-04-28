# G5 Member Runtime

This repository contains the runtime, installer, documentation, and asset build files for a member-focused G5 application with restored community features.

## Runtime Requirements

- PHP 8.3 or compatible PHP runtime
- MySQL or MariaDB
- Web server document root pointed at this directory
- Writable `data/` directory on the deployed server
- Node.js and npm only when rebuilding Tailwind assets

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
data/file/
data/tmp/
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
- `db_engine`, `db_charset`: schema engine/charset defaults used by installation paths
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
- `community/`: community board, latest, comment, attachment, scrap, and download controllers
- `member/`: member authentication, registration, account pages, and member templates
- `lib/`: shared runtime libraries and domain helpers
- `plugin/`: mailer, captcha, purifier, and identity verification plugins
- `css/`, `js/`: prebuilt frontend assets
- `install/`: installer and schema SQL; remove or block this directory after production installation
- `docs/`: current architecture, security, and performance notes
- `scripts/`: local policy checks

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
9. Community board, post, comment, attachment, latest, scrap flows
10. Community admin board/post/comment/notification/point screens

After installation, remove the `install/` directory from production or block direct web access to it.

## Community Documentation

Current community structure and performance checks are documented in:

```text
docs/community-restoration-plan.md
docs/community-performance-checklist.md
```

## Security Development Policy

Input handling, SQL helper usage, and token/key generation rules are documented in:

```text
docs/security-input-sql-policy.md
```

Run the lightweight policy check before changing request, SQL, or authentication code:

```text
scripts/check-security-policy.sh
```

## License

This project is licensed under the MIT License. See `LICENSE.txt`.
