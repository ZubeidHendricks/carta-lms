#!/bin/sh
###############################################################################
# Container entrypoint for the FrankenPHP/Octane web service.
#
# Runtime env vars (DB creds, APP_KEY, Redis, …) are injected by App Platform,
# so the production caches are built HERE — not at image-build time — to make
# sure cached config reflects the real environment.
###############################################################################
set -e

# The application lives in /app (Dockerfile WORKDIR). Be explicit — the base
# image also ships an empty /var/www/html which must NOT be used.
cd /app

# Ensure writable runtime directories exist (defensive on fresh containers).
mkdir -p storage/framework/cache storage/framework/sessions \
         storage/framework/views storage/logs bootstrap/cache

# Build config cache first so the rest of the boot reflects the real env.
# Kept non-fatal so a transient hiccup can't stop the container from starting
# (Octane below is what determines liveness).
php artisan config:cache || echo "[entrypoint] config:cache failed"

# First-run/idempotent provisioning: migrate (+ seed on empty DB) and ensure the
# admin user exists. Safe to run on every boot while instance_count=1. Kept
# non-fatal so the container still starts (and /up responds) for debugging if
# the database is briefly unreachable.
php artisan app:provision || echo "[entrypoint] provisioning failed — check DB connectivity and logs"

# Remaining caches are best-effort so a single closure can't block boot.
php artisan route:cache  || echo "route:cache skipped"
php artisan view:cache   || echo "view:cache skipped"
php artisan event:cache  || echo "event:cache skipped"

# Start Laravel Octane on FrankenPHP. Workers stay resident between requests.
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port="${PORT:-8080}" \
    --workers="${OCTANE_WORKERS:-auto}" \
    --max-requests="${OCTANE_MAX_REQUESTS:-512}"
