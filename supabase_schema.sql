-- ============================================================
-- GOLKRIE - Schema untuk Supabase (PostgreSQL)
-- Paste file ini ke SQL Editor Supabase project baru
-- ============================================================

-- 1. USERS
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'admin',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. PASSWORD RESET TOKENS
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- 3. SESSIONS
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL REFERENCES users(id),
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions(user_id);
CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions(last_activity);

-- 4. PERSONAL ACCESS TOKENS
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX IF NOT EXISTS personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens(tokenable_type, tokenable_id);

-- 5. CACHE
CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- 6. JOBS
CREATE TABLE IF NOT EXISTS jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS jobs_queue_index ON jobs(queue);

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- 7. MATCHES
CREATE TABLE IF NOT EXISTS matches (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    match_name VARCHAR(255) NOT NULL,
    date_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NULL,
    location VARCHAR(255) NOT NULL,
    location_url TEXT NULL,
    quota INTEGER NOT NULL DEFAULT 14,
    quota_gk INTEGER NOT NULL DEFAULT 2,
    quota_df INTEGER NOT NULL DEFAULT 4,
    quota_mf INTEGER NOT NULL DEFAULT 4,
    quota_fw INTEGER NOT NULL DEFAULT 4,
    price VARCHAR(255) NOT NULL DEFAULT '0',
    price_gk VARCHAR(255) NOT NULL DEFAULT '0',
    media_url TEXT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'upcoming' CHECK (status IN ('upcoming', 'finished')),
    team_config JSONB NULL,
    facilities TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 8. MEMBERS
CREATE TABLE IF NOT EXISTS members (
    id BIGSERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 9. REGISTRATIONS
CREATE TABLE IF NOT EXISTS registrations (
    id BIGSERIAL PRIMARY KEY,
    match_id BIGINT NOT NULL REFERENCES matches(id) ON DELETE CASCADE,
    member_id BIGINT NOT NULL REFERENCES members(id) ON DELETE CASCADE,
    player_name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    team_name VARCHAR(255) NULL,
    is_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    is_paid BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 10. SETTINGS
CREATE TABLE IF NOT EXISTS settings (
    id BIGSERIAL PRIMARY KEY,
    key VARCHAR(255) NOT NULL UNIQUE,
    value TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 11. SPONSORS
CREATE TABLE IF NOT EXISTS sponsors (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) NULL,
    "order" INTEGER NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 12. MIGRATIONS (untuk Laravel tracking)
CREATE TABLE IF NOT EXISTS migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

-- ============================================================
-- DEFAULT SETTINGS DATA
-- ============================================================
INSERT INTO settings (key, value, created_at, updated_at) VALUES
    ('app_name', 'Golkrie', NOW(), NOW()),
    ('app_tagline', 'Golek Kringet, Jalin Seduluran.', NOW(), NOW()),
    ('footer_text', '© 2024 Golkrie Community. All rights reserved.', NOW(), NOW()),
    ('instagram_url', 'https://instagram.com/golkrie', NOW(), NOW()),
    ('whatsapp_contact', '08123456789', NOW(), NOW()),
    ('hero_description', 'Tingkatkan skill dan jalin persaudaraan di lapangan hijau dua kali seminggu. Fun football yang kompetitif namun tetap seru.', NOW(), NOW()),
    ('hero_title', 'Selamat Datang di Golkrie', NOW(), NOW()),
    ('hero_subtitle', 'Fun Football Community', NOW(), NOW()),
    ('youtube_url', '', NOW(), NOW()),
    ('tiktok_url', '', NOW(), NOW())
ON CONFLICT (key) DO NOTHING;
