# 🍽️ Responsive Steakhouse Website — Full-Stack PHP + MySQL

A production-ready, full-stack restaurant website converted from a static HTML/CSS/JS project.

The frontend keeps the original steakhouse design, animations, images, and responsiveness. The backend adds a working reservation system, a contact form, and a database-driven menu — all served through a clean PHP API.

> 🎓 This project is designed as a **project-based learning exercise**: every layer is deliberately small and readable so you can follow a request from the **Browser → JavaScript → PHP → MySQL → JSON → Browser** without a framework hiding the work.

---

## ✨ Features

- 📱 **Mobile-first responsive design** (dark theme, scroll animations, smooth scrolling)
- 📖 **Dynamic menu** loaded from MySQL via `GET /api/menu/list.php`
- 🪑 **Working reservation form** validated twice (JavaScript + PHP) and stored via PDO
- ✉️ **Working contact form** stored in `contact_messages`
- 🔧 **REST-style PHP API** with correct HTTP status codes and JSON responses
- 🔒 **Security basics**: prepared statements, server-side validation, output escaping, rate limiting, env-based secrets, no raw errors leaked
- 🐳 **Docker image** (PHP 8 + Apache + MySQL PDO) built to run on **Render**
- ⚙️ **GitHub Actions CI/CD** — PHP lint + real-MySQL integration tests + auto-deploy hook

---

## 🛠️ Tech Stack

| Layer      | Technology                                     |
| ---------- | ---------------------------------------------- |
| Frontend   | HTML5, CSS3, Vanilla JavaScript (Fetch API)    |
| Backend    | PHP 8+                                         |
| Database   | MySQL / MariaDB (via PDO)                      |
| UI kits    | Remixicon, ScrollReveal, Google Fonts          |
| Local dev  | XAMPP (Apache + MySQL + PHP)                   |
| Container  | Docker (php:8.2-apache)                        |
| CI/CD      | GitHub Actions → Render Deploy Hook            |
| Production | Render Web Service + external managed MySQL    |

No frameworks are used — no React, Vue, Laravel, or Node.

---

## 🏗️ Architecture

```
┌──────────────────────┐
│       Browser        │
│ HTML / CSS / JS      │
└──────────┬───────────┘
           │ fetch()
           ▼
┌──────────────────────┐
│       PHP API        │
│  api/ endpoints      │
│  Validation / Logic  │
└──────────┬───────────┘
           │ PDO (prepared statements)
           ▼
┌──────────────────────┐
│        MySQL         │
│ Persistent Data      │
└──────────────────────┘
```

The full lifecycle of a reservation:

```
User → Reservation Form → JS validation → POST /api/reservations/create.php
    → PHP validation → PDO prepared statement → MySQL → PHP JSON response
    → JS shows success/error message in the UI
```

---

## 📁 Project Structure

```
/
├── index.php                          # The whole website (entry point)
├── api/
│   ├── reservations/
│   │   ├── create.php                 # POST - create a reservation
│   │   └── list.php                   # GET  - list reservations (owner)
│   ├── contact/
│   │   └── create.php                 # POST - store a contact message
│   └── menu/
│       └── list.php                   # GET  - menu items JSON
├── config/
│   ├── bootstrap.php                  # Loads env + all source classes
│   ├── database.php                   # db() helper (PDO bridge)
│   └── env.php                        # Tiny .env loader
├── src/
│   ├── Database.php                   # PDO singleton
│   ├── Response.php                   # JSON + HTTP status helper
│   ├── RateLimiter.php                # Sliding-window rate limiting
│   ├── Reservation.php                # Validation + SQL for reservations
│   ├── Contact.php                    # Validation + SQL for messages
│   └── Menu.php                       # Read model for menu_items
├── assets/
│   ├── css/styles.css
│   ├── js/main.js                     # UI, menu, scroll animations
│   ├── js/api.js                      # fetch() calls to the PHP API
│   └── img/                           # Original images
├── database/
│   └── schema.sql                     # Full schema + seed menu data
├── docker/
│   ├── apache.conf                    # Virtual host (listens on $PORT)
│   └── entrypoint.sh                  # Resolves $PORT before Apache starts
├── .github/workflows/deploy.yml       # CI/CD pipeline
├── .env.example                       # Template for environment variables
├── .gitignore                         # Never commit .env or secrets
├── .dockerignore
├── Dockerfile
├── render.yaml                        # Optional Render blueprint
└── README.md
```

---

## 💻 Local Development

### 🐘 PHP Setup (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Put this folder inside `C:\xampp\htdocs\` (it already is).
3. Open `http://localhost/Responsive-Steakhouse-Website-main/`.

The site is served by XAMPP Apache, exactly as before, but now `index.php` is the entry file and `assets/js/api.js` talks to the API endpoints you will set up below.

### 🗄️ MySQL Setup

1. Start MySQL from the XAMPP control panel.
2. Create the local database. `database/schema.sql` is **database-agnostic** — it never creates or selects a database, so you create/select it on the command line:

```
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS steakhouse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

3. Import the schema **into that database** (this also seeds the 4 menu items):

```
C:\xampp\mysql\bin\mysql.exe -u root steakhouse < database\schema.sql
```

> On XAMPP the `root` user has an empty password by default. Or use **phpMyAdmin** → *Import* → choose `database/schema.sql`.

3. Create your local environment file:

```
copy .env.example .env
```

Then set the values for your machine (XAMPP defaults shown below).

### 🔌 Environment Variables

Copy `.env.example` → `.env` and fill in:

| Variable                  | Example (XAMPP)  | Purpose                                   |
| ------------------------- | ---------------- | ----------------------------------------- |
| `APP_ENV`                 | `local`          | `production` disables error display       |
| `DB_HOST`                 | `127.0.0.1`      | Database host                             |
| `DB_PORT`                 | `3306`           | Database port                             |
| `DB_NAME`                 | `steakhouse`     | Database name                             |
| `DB_USER`                 | `root`           | Database user                             |
| `DB_PASSWORD`             | *(empty)*        | Database password                         |
| `DB_SSL`                  | *(empty)*        | `true` forces TLS; defaults to on when `APP_ENV=production` |
| `DB_SSL_CA`               | *(empty)*        | CA cert file path inside the container    |
| `DB_SSL_VERIFY_SERVER_CERT` | *(empty)*     | `true` (default) verifies the server cert — do not disable for Aiven |
| `AIVEN_CA_CERT`           | *(empty)*        | Optional Aiven CA PEM content written by the Docker entrypoint |
| `ADMIN_API_TOKEN`         | *(optional)*     | Protects `/api/reservations/list.php`     |
| `RATE_LIMIT_MAX`          | `10`             | Max requests per IP per window (default)  |
| `RATE_LIMIT_WINDOW_SECONDS` | `60`           | Rate limit window in seconds              |

`.env` is ignored by Git (`see .gitignore`) — **never commit real credentials**.
TLS vars only matter in production; local XAMPP leaves them empty.

---

## 🔌 API Endpoints

| Method | Endpoint                        | Body / Params                          | Success            | Errors                                    |
| ------ | ------------------------------- | -------------------------------------- | ------------------ | ----------------------------------------- |
| POST   | `/api/reservations/create.php`  | `customer_name, phone, email, reservation_date, reservation_time, guests, message` | `201`              | `400` bad JSON, `405`, `422` validation, `429` rate limit, `500` |
| GET    | `/api/reservations/list.php`    | `?token=` or `Authorization: Bearer` (if `ADMIN_API_TOKEN` set) | `200`              | `401` if token required/missing, `405`, `500` |
| POST   | `/api/contact/create.php`       | `name, email, phone?, subject, message` | `201`             | same error set as reservations            |
| GET    | `/api/menu/list.php`            | `?category=starter\|main\|salad\|dessert` (optional) | `200` | `405`, `422` bad category, `500`          |

Example responses:

```json
// POST /api/reservations/create.php -> 201
{ "success": true, "message": "Reservation submitted successfully." }

// invalid input -> 422
{
  "success": false,
  "message": "Please fix the highlighted fields.",
  "errors": { "email": "The email address is not valid." }
}

// GET /api/menu/list.php -> 200
{
  "success": true,
  "message": "Menu items retrieved.",
  "data": [
    {
      "id": 1,
      "name": "Bruschetta",
      "description": "Start with our fresh baked bread with an egg and basil on top.",
      "price": 180,
      "category": "starter",
      "image": "assets/img/menu-dish-1.png"
    }
  ]
}
```

---

## 🐳 Docker

The `Dockerfile` builds a production image:

```dockerfile
FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql
COPY docker/apache.conf docker/entrypoint.sh ...
COPY . .
```

- Apache is configured to listen on **Render's `$PORT`** (default `8080` locally).
- No database credentials are baked into the image — everything comes from env vars at runtime.

Build & run locally (requires Docker):

```bash
docker build -t steakhouse .
docker run --rm -p 8080:8080 \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=3306 \
  -e DB_NAME=steakhouse \
  -e DB_USER=root \
  -e DB_PASSWORD= \
  -e APP_ENV=production \
  -e DB_SSL=false \          # local only - Aiven requires DB_SSL=true
  steakhouse
```

Then open `http://localhost:8080`.

> `host.docker.internal` reaches the MySQL running on your host machine (works on Docker Desktop for Windows/Mac).

**Local vs Production flows**

```
Local:
Browser → XAMPP Apache → PHP → local MySQL (same machine)

Production:
Browser → Render → PHP container → production MySQL (external provider)
```

---

## ⚙️ GitHub Actions

`.github/workflows/deploy.yml` runs on every push to `main` (or manually via *workflow_dispatch*):

```
git push → GitHub Repository → GitHub Actions
    → PHP syntax lint (all *.php)
    → Integration tests against a real MySQL 8 service container
    → Trigger Render Deploy Hook (curl)
    → Render builds Dockerfile → production PHP app
```

The workflow uses GitHub Actions **Secrets** — never write credentials in the YAML.

**Required secrets**

Configure them in *Repository → Settings → Secrets and variables → Actions*:

| Secret                | Purpose                                                        |
| --------------------- | -------------------------------------------------------------- |
| `RENDER_DEPLOY_HOOK`  | Your Render service's manual deploy hook URL (triggers deploy) |

If the secret is missing, the pipeline still runs lint + integration tests and simply skips the deploy step.

---

## 🚀 Render Deployment

Run the website as a **Docker Web Service** on Render. Production database: **Aiven MySQL 8.4** (`defaultdb`, **SSL mode: REQUIRED**).

### Option A — Render Dashboard (manual)

1. **New → Web Service** → connect the GitHub repo → choose **Docker** runtime.
2. Set the build/start fields to point at the root `Dockerfile`.
3. Add the following environment variables:

| Variable        | Value / advice                                             |
| --------------- | ---------------------------------------------------------- |
| `APP_ENV`       | `production`                                               |
| `DB_HOST`       | Aiven host, e.g. `steakhouse-mysql-<name>-<project>.aivencloud.com` |
| `DB_PORT`       | `15303`                                                    |
| `DB_NAME`       | `defaultdb`                                                |
| `DB_USER`       | `avnadmin`                                                 |
| `DB_PASSWORD`   | Aiven password (store as secret)                           |
| `DB_SSL`        | `true`                                                     |
| `DB_SSL_CA`     | `/etc/ssl/certs/aiven-ca.pem`                              |
| `DB_SSL_VERIFY_SERVER_CERT` | `true`                                        |
| `AIVEN_CA_CERT` | The Aiven CA certificate PEM content (multi-line secret)   |
| `ADMIN_API_TOKEN` | optional: protect the reservations list endpoint         |

4. Supply the **Aiven CA certificate**. The Docker entrypoint writes the contents of `AIVEN_CA_CERT` to `/etc/ssl/certs/aiven-ca.pem` at boot. Alternative: mount a **Render Secret File** and point `DB_SSL_CA` at its path.
5. Import `database/schema.sql` into Aiven (see below) — do it **before** the app goes live.
6. Deploy, then configure the **Deploy Hook** URL from *Settings → Deploy Hooks*. Put it in the `RENDER_DEPLOY_HOOK` GitHub secret.

### Import schema into Aiven (`defaultdb`)

XAMPP's bundled `mysql.exe` (MariaDB client) **cannot** log in to Aiven — it lacks the `caching_sha2_password` plugin. Use a modern MySQL client:

**Option 1 — MySQL Shell (recommended)**. Download from https://dev.mysql.com/downloads/shell/. It prompts for the password (nothing lands in shell history):

```
mysqlsh --sql --host=DB_HOST --port=15303 --user=avnadmin --database=defaultdb --ssl-mode=VERIFY_CA --ssl-ca=database\ca.pem --file=database\schema.sql
```

Replace `DB_HOST` with your Aiven host. Enter the `avnadmin` password when prompted.

**Option 2 — MySQL 8.x `mysql` client** (or via Docker, so no install):

```
docker run --rm -it -v "%CD%":/app mysql:8.0 mysql -h DB_HOST -P 15303 -u avnadmin -p --ssl-ca=/app/database/ca.pem defaultdb < /app/database/schema.sql
```

The `-p` flag prompts for the password (not stored in history). On Linux/macOS replace `%CD%` with `$(pwd)` and the path separators accordingly.

> Verify after import: the tables `reservations`, `contact_messages`, `menu_items` exist and the four initial menu items are present. Importing again is safe — the seed only runs when the table is empty.

### Option B — Render Blueprint (`render.yaml`)

Open the repo on Render using **Blueprint**. Env vars marked `sync: false` must be filled in under the Blueprint's **Environments** tab.

---

## 🔒 Security

- **SQL injection** — all queries use PDO prepared statements; emulated prepares are disabled.
- **Server-side validation** — every endpoint validates and sanitizes input independently (never trusts the frontend).
- **XSS** — API data is escaped with `escapeHtml()` in JavaScript before rendering; JSON is echoed with `JSON_UNESCAPED_*` + `nosniff`.
- **No credential leaks** — real secrets come from env vars; `.env` is gitignored; errors are logged server-side and generic messages returned to clients.
- **Correct HTTP methods** — endpoints reject wrong methods with `405`.
- **Input limits** — length constraints enforced in PHP (and mirrored in the DB schema).
- **Rate limiting** — sliding-window limiter on public write endpoints (`RATE_LIMIT_MAX` / `RATE_LIMIT_WINDOW_SECONDS`).
- **Error handling** — in production `display_errors` is off and database details never reach the browser.
- **TLS (Aiven)** — production connects over TLS with the Aiven CA and full server-cert verification (`DB_SSL_VERIFY_SERVER_CERT=true`). The app fails closed if the CA is missing; verification is never silently disabled.

---

## 📱 Responsive Design

- Mobile-first layout with the original dark steakhouse theme.
- Breakpoints: mobile `≤900px`, tablet `901–1150px`, desktop `≥1150px`.
- Fully semantic headings (`h1` page title → `h2` sections → `h3` cards).
- Decorative images carry `alt=""`; meaningful images have descriptive alt text; the map iframe has a `title`.

---

## 🧪 Testing

Run locally after starting MySQL and importing the schema:

```bash
# Syntax-check every PHP file
find . -name '*.php' -exec php -l {} \;

# Start the PHP dev server
php -S 127.0.0.1:8000 -t .

# Menu
curl http://127.0.0.1:8000/api/menu/list.php

# Reservation
curl -X POST http://127.0.0.1:8000/api/reservations/create.php \
  -H "Content-Type: application/json" \
  -d '{"customer_name":"Ahmed","phone":"01154053377","email":"a@example.com","reservation_date":"2026-10-01","reservation_time":"11:00","guests":2}'

# Contact
curl -X POST http://127.0.0.1:8000/api/contact/create.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Ahmed","email":"a@example.com","subject":"Hi","message":"Hello!"}'
```

CI runs **PHP lint** + **integration tests against real MySQL** on every push.

---

## 📚 Learning Outcomes

By studying this project you will learn:

1. How HTML/CSS/JS talk to a backend using `fetch()`.
2. How PHP reads JSON input, validates it, and returns JSON.
3. What PDO prepared statements are and why they prevent SQL injection.
4. How to design a small MySQL schema with sensible constraints and indexes.
5. How to keep secrets out of code with `.env` and `.gitignore`.
6. How to containerize a PHP app with Docker and listen on a platform-provided port.
7. How to build a CI/CD pipeline (lint → integration tests → deploy hook).
8. How to deploy and debug a PHP/MySQL app on Render with GitHub Actions.

---

🍽️ Original design by Bedimcode and the responsive-steakhouse-website tutorial.