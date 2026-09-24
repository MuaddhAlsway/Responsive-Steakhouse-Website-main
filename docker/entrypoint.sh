#!/bin/sh
set -e

# Render provides the PORT variable (e.g. 10000).
# When missing (local docker run), default to 8080.
export PORT="${PORT:-8080}"

# Bake the real port into the apache virtual host config so it works
# regardless of Apache's environment-variable expansion support.
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/conf-enabled/steakhouse.conf

# Run the container command (apache2-foreground).
exec "$@"