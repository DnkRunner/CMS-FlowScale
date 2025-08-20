-- 001_init.sql - PostgreSQL version
CREATE TABLE IF NOT EXISTS "cms_users" (
    id SERIAL PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'admin' CHECK (role IN ('admin','editor','author')),
    display_name VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS "cms_posts" (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft' CHECK (status IN ('draft','published')),
    template VARCHAR(20) NOT NULL DEFAULT 'default' CHECK (template IN ('default','blank')),
    author_id INTEGER NOT NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (author_id) REFERENCES "cms_users"(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS "cms_settings" (
    "key" VARCHAR(191) PRIMARY KEY,
    "value" TEXT NULL
);

CREATE INDEX idx_posts_status ON "cms_posts" (status);
