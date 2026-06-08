#!/bin/bash
# Script ini dijalankan Vercel saat build
# Generate .env untuk Laravel dari environment variables Vercel

echo "Generating .env for Laravel..."

cat > backend/.env << EOF
APP_NAME=${APP_NAME:-Golkrie}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://golkrie.vercel.app}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-6543}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=${DB_SSLMODE:-require}

SUPABASE_URL=${SUPABASE_URL}
SUPABASE_KEY=${SUPABASE_KEY}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@golkrie.com
MAIL_FROM_NAME=Golkrie
EOF

echo ".env generated successfully!"
