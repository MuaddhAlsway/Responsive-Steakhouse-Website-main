#!/bin/sh
set -e

# Render provides the PORT variable (e.g. 10000).
# When missing (local docker run), default to 8080.
export PORT="${PORT:-8080}"

# Bake the real port into the apache virtual host config so it works
# regardless of Apache's environment-variable expansion support.
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/conf-enabled/steakhouse.conf

# ------------------------------------------------------------
# Aiven CA certificate provisioning (MySQL "SSL mode: REQUIRED").
#
# The Aiven CA is NOT baked into the image (rotation without a
# rebuild, and nothing deployment-specific lives in Git).
#
# Supply the CA one of two ways:
#   1. Render env var AIVEN_CA_CERT = the PEM content (multi-line).
#      The entrypoint writes it to DB_SSL_CA and PHP reads it there.
#   2. Render Secret File mounted at DB_SSL_CA (no env content).
#      If the file already exists we simply keep it.
#
# Never disable TLS verification on the PHP side (DB_SSL_VERIFY_
# SERVER_CERT must stay true for Aiven).
# ------------------------------------------------------------
export DB_SSL_CA="${DB_SSL_CA:-/etc/ssl/certs/aiven-ca.pem}"

if [ -n "${AIVEN_CA_CERT:-}" ]; then
    ca_dir="$(dirname "$DB_SSL_CA")"
    mkdir -p "$ca_dir"
    printf '%s\n' "$AIVEN_CA_CERT" > "$DB_SSL_CA"
    chmod 0644 "$DB_SSL_CA"
fi

# Run the container command (apache2-foreground).
exec "$@"