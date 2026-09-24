# 📋 Project Report — Responsive Steakhouse Website (Full-Stack)

> **Date:** Thursday, 24 September 2026
> **Repo:** https://github.com/MuaddhAlsway/Responsive-Steakhouse-Website-main
> **Environment:** Windows 10/11 + XAMPP (local) · Render + Aiven (production)

---

## 1. Overview

A static restaurant website (`responsive-steakhouse-website`, original design by
Bedimcode) was converted into a complete **PHP 8 + MySQL** full-stack application:

- Frontend stays exactly as designed — **HTML/CSS/Vanilla JS** with a **JSON PHP API**.
- Backend is **framework-free PHP 8.2 + PDO** (no React/Vue/Laravel/Node).
- Local development runs on **XAMPP (MariaDB)**.
- Production runs on **Render (Docker)** connected to **Aiven MySQL 8.4** over **TLS (SSL REQUIRED)**.
- Delivery is automated with **GitHub Actions** (lint → integration → deploy hook).

The project serves double duty: a working restaurant website **and** a learning
exercise that traces a request end-to-end: `Browser → fetch() → PHP → PDO → MySQL → JSON → UI`.

---

## 2. Current Deployment Status

| Layer          | Status |
|----------------|--------|
| Local XAMPP    | ✅ Working (`APP_ENV=local`, 127.0.0.1:3306, db `steakhouse`) |
| Git repository | ✅ Pushed — `main` at commit `dda2def` (“Add Aiven production database support”) |
| Aiven MySQL 8.4 | ✅ `defaultdb` schema imported (3 tables + 4 seeded menu items) |
| Render Web Service | ✅ Env vars set (`DB_*`, `DB_SSL=true`, `AIVEN_CA_CERT`, `ADMIN_API_TOKEN`) |
| CI/CD (GitHub Actions) | ✅ Lint + real-MySQL integration tests on every push |
| Auto-deploy hook | 🔧 Enabled via `RENDER_DEPLOY_HOOK` GitHub secret |

---

## 3. Tech Stack

| Layer      | Technology                                     |
| ---------- | ---------------------------------------------- |
| Frontend   | HTML5, CSS3, Vanilla JavaScript (Fetch API)    |
| Backend    | PHP 8.2, Apache, PDO (mysqlnd)                 |
| Database   | MySQL / MariaDB                                |
| Local dev  | XAMPP (Apache + MariaDB + PHP)                 |
| Container  | Docker `php:8.2-apache`                        |
| Production | Render Web Service + Aiven MySQL 8.4 (TLS)     |
| CI/CD      | GitHub Actions → Render Deploy Hook            |

No frameworks. All queries are PDO prepared statements; everything is
environment-driven (no secrets in code or images).

---

## 4. Architecture

```
LOCAL
Browser
   ↓
XAMPP Apache (index.php + api/*.php)
   ↓
PDO (no TLS)
   ↓
MariaDB 127.0.0.1:3306 → steakhouse

PRODUCTION
Browser
   ↓
Render (Docker: php:8.2 + Apache, listens on $PORT)
   ↓
PDO + TLS (Aiven CA, server-cert verification ON)
   ↓
Aiven MySQL 8.4 → defaultdb
   ├── reservations
   ├── contact_messages
   └── menu_items

CI/CD
git push → main
   ↓
GitHub Actions: PHP lint → MySQL-8 integration tests → API smoke tests
   ↓
Render Deploy Hook (RENDER_DEPLOY_HOOK secret)
   ↓
Production
```

One request, end to end (reservation example):

```
User → Reservation Form → JS validation
  → POST /api/reservations/create.php
  → PHP validation + rate limiting
  → PDO prepared INSERT → MySQL
  → 201 JSON → JS shows the message in the UI
```

---

## 5. Repository Structure

```
/
├── index.php                      # Entire website (entry point)
├── api/
│   ├── reservations/create.php    # POST — new reservation
│   ├── reservations/list.php      # GET  — owner list (ADMIN_API_TOKEN)
│   ├── contact/create.php         # POST — contact message
│   └── menu/list.php              # GET  — menu JSON
├── config/
│   ├── bootstrap.php              # Env load + class autoload + error policy
│   ├── database.php               # db() PDO bridge
│   └── env.php                    # Tiny .env loader (real env wins)
├── src/
│   ├── Database.php               # PDO singleton + TLS logic
│   ├── Response.php               # JSON + HTTP status helper
│   ├── RateLimiter.php            # Sliding-window rate limiter
│   ├── Reservation.php            # Validation + SQL
│   ├── Contact.php                # Validation + SQL
│   └── Menu.php                   # Read model
├── database/schema.sql            # Portable schema + idempotent seeds
├── docker/apache.conf             # Virtual host on $PORT
├── docker/entrypoint.sh           # Resolves $PORT + writes CA cert
├── Dockerfile
├── render.yaml                    # Render blueprint
├── .github/workflows/deploy.yml   # CI/CD
├── .env.example                   # Env template
├── .gitignore / .dockerignore
├── README.md
└── PROJECT_REPORT.md
```

---

## 6. Database

3 tables — `reservations`, `contact_messages`, `menu_items`:

- `reservations`: customer_name, phone, email, date, time, guests (CHECK 1–20),
  message, status ENUM (pending/confirmed/cancelled/completed), timestamps, indexes.
- `contact_messages`: name, email, phone (opt.), subject, message, created_at, index.
- `menu_items`: name, description, DECIMAL price, category ENUM
  (starter/main/salad/dessert), image, is_available, indexes, CHECK price >= 0.

**Portable schema.** `schema.sql` contains **no** `CREATE DATABASE` / `USE` — you
connect to the target database (local `steakhouse`, Aiven `defaultdb`) from the client:

- **Idempotent seeds**: the 4 default dishes insert only when `menu_items` is empty,
  so re-importing never duplicates data.
- Verified locally against a scratch DB: import → 3 tables + 4 rows; re-import → still 4 rows.

---

## 7. API Endpoints

| Method | Endpoint                        | Success | Errors                                   |
| ------ | ------------------------------- | ------- | ---------------------------------------- |
| GET    | `/api/menu/list.php`            | `200`   | `405`, `422` bad category, `500`         |
| POST   | `/api/reservations/create.php`  | `201`   | `400` bad JSON, `405`, `422`, `429`, `500` |
| GET    | `/api/reservations/list.php`    | `200`   | `401` (token), `405`, `500`              |
| POST   | `/api/contact/create.php`       | `201`   | `400`, `405`, `422`, `429`, `500`        |

- `/api/reservations/list.php` accepts `Authorization: Bearer <token>`,
  `X-API-Key: <token>` or `?token=<token>` when `ADMIN_API_TOKEN` is set.
- Every body field is validated server-side (names/emails/lengths/date-not-in-past/
  opening hours 9–20 (Sat) / 9–18 (Sun)/guests 1–20).

---

## 8. Environment Variables

| Variable                      | Local (XAMPP)   | Production (Render)              |
| ----------------------------- | --------------- | -------------------------------- |
| `APP_ENV`                     | `local`         | `production`                     |
| `DB_HOST`                     | `127.0.0.1`     | `…aivencloud.com`                |
| `DB_PORT`                     | `3306`          | `15303`                          |
| `DB_NAME`                     | `steakhouse`    | `defaultdb`                      |
| `DB_USER`                     | `root`          | `avnadmin`                       |
| `DB_PASSWORD`                 | *(empty)*       | Aiven password (secret)          |
| `DB_SSL`                      | *(empty)*       | `true`                           |
| `DB_SSL_CA`                   | *(empty)*       | `/etc/ssl/certs/aiven-ca.pem`    |
| `DB_SSL_VERIFY_SERVER_CERT`   | *(empty/true)*  | `true` (never disable)           |
| `AIVEN_CA_CERT`               | *(empty)*       | CA PEM content (multi-line secret) |
| `ADMIN_API_TOKEN`             | *(optional)*    | owner token (secret)             |
| `RATE_LIMIT_MAX` / `RATE_LIMIT_WINDOW_SECONDS` | `10` / `60` | tunable          |

Rules: `.env` and `.env.*` are gitignored; real environment variables always win
over `.env`; nothing is baked into the Docker image; secrets live only in Render
env vars / GitHub secrets.

---

## 9. TLS / Aiven Solution

- Aiven enforces **SSL mode: REQUIRED** (hosts are `…aivencloud.com`, port `15303`).
- `src/Database.php` adds TLS driver options:
  - `DB_SSL=true` forces TLS; when unset it defaults **on** for `APP_ENV=production`.
  - `DB_SSL_CA` → `PDO::MYSQL_ATTR_SSL_CA` (default `/etc/ssl/certs/aiven-ca.pem`).
  - `DB_SSL_VERIFY_SERVER_CERT` defaults to **true** → `PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT`.
  - **Fail-closed**: TLS on + CA file missing → the app returns a generic `500`
    and logs the real reason (never to the client). Verification is never disabled.
- `docker/entrypoint.sh` writes the CA from the `AIVEN_CA_CERT` env var to
  `DB_SSL_CA` at boot (or honors a Render Secret File mounted at that path), so the
  certificate can rotate without rebuilding the image.
- Local XAMPP (`APP_ENV=local`, no TLS vars) connects **without** TLS, exactly as before.

> The old XAMPP `mysql.exe` CLI cannot authenticate to Aiven
> (`caching_sha2_password` plugin missing). PHP PDO (mysqlnd) is unaffected.
> For admin/import work use **MySQL Shell** or the **MySQL 8.x `mysql` client**
> (both understand `caching_sha2_password` + TLS).

---

## 10. Docker / Render

- `Dockerfile`: `php:8.2-apache` + `pdo_mysql` (mysqlnd), `a2enmod rewrite`,
  custom vhost, entrypoint, `COPY . .` (image excludes `.env`, certs, git).
- `docker/apache.conf` listens on `${PORT}` (baked by the entrypoint; Render
  injects `PORT`, default `8080` locally).
- `render.yaml` blueprint is provided; `DB_PASSWORD`, `AIVEN_CA_CERT`,
  `ADMIN_API_TOKEN` are intentionally `sync: false` (dashboard-only).

---

## 11. CI/CD — GitHub Actions (`.github/workflows/deploy.yml`)

Runs on push to `main` (and `workflow_dispatch`):

1. **Validate** — `php -l` on every `.php` file.
2. **Integration** — boots a real MySQL 8.0 service container, imports the portable
   schema into `steakhouse_test`, starts PHP's built-in server, and asserts:
   - menu `200` with seeded items, reservation `201`/`422`, contact `201`,
     reservations list `200`, and a **TLS fail-closed check** (missing CA → generic
     `500`, body contains no credential details).
3. **Deploy** — POSTs the `RENDER_DEPLOY_HOOK` (GitHub secret) to Render; skipped
   cleanly if the secret is absent.

CI never connects to the Aiven production database — tests run against the
service container only.

---

## 12. Security & Secrets Audit

**Implemented:** PDO prepared statements everywhere · emulated prepares off ·
double validation (JS + PHP) · length caps · XSS escaping in JS · `nosniff`/
`no-store` headers · rate limiting on public writes · `hash_equals` token check ·
production `display_errors=0` with server-side logging · generic client errors ·
env-only secrets · fail-closed TLS with certificate verification.

**Secrets scan (this machine, working tree + git history):** no `AVNS_` passwords,
no `BEGIN … PRIVATE KEY`, no `mysql://` URIs, no real `DB_PASSWORD` values were
found committed. `.env`, `.env.*` (`!.env.example`), `*.pem`, `*.crt`, `*.key`
are ignored by Git and by `.dockerignore`. Nothing needs rotating.
`database/ca.pem` is a **public** Aiven CA certificate (not a private key) and is
safe to keep in the repo for import commands.

---

## 13. Validation Evidence (what was actually tested)

| Check | Result |
|-------|--------|
| `php -l` on all 14 PHP files | ✅ 0 errors |
| `sh -n docker/entrypoint.sh` | ✅ valid |
| Portable schema (scratch DB, import twice) | ✅ 3 tables, 4 menu rows, 0 duplicates |
| Local API smoke (built-in server vs `steakhouse`) | ✅ `/` 200 · menu 200 (4 items) · 405 · 400 bad JSON · 422 invalid · 201 reservation · 201 contact · 401 no token · 200 with token |
| TLS fail-closed (`DB_SSL=true`, missing CA) | ✅ client `500` generic, no leak; server log has the real reason |
| Auto-TLS default (`APP_ENV=production`, no `DB_SSL`) | ✅ TLS on, fail-closed |
| Docker build | ⚠️ not run — Docker not installed on this machine (validated statically) |
| Live Aiven handshake / Render | 🔧 to be confirmed from the deployed site (see checklist) |

---

## 14. Production Verification Checklist

After the Render service is green:

1. `GET https://<app>.onrender.com/` → `200`.
2. `GET …/api/menu/list.php` → JSON with the 4 dishes (proves PHP→PDO→Aiven TLS).
3. `POST …/api/reservations/create.php` (valid) → `201`; row appears in Aiven `defaultdb`.
4. `POST …/api/contact/create.php` → `201`; row appears.
5. `GET …/api/reservations/list.php` → `401` without token; `200` with `Bearer <ADMIN_API_TOKEN>`.
6. Render logs contain no `[Database] connection failed:`; if they do, the real
   reason is in the log line (never in the HTTP response).

---

## 15. Remaining / Manual Notes

- Confirm the live app behaves per §14 (requires visiting the deployed URL).
- Keep `ADMIN_API_TOKEN` random and stored only in Render (`sync:false`).
- Menu prices are placeholder values; update via Aiven console (`menu_items`) or
  the app once seeded.
- For local admin work against Aiven, use MySQL Shell (not the bundled XAMPP client).