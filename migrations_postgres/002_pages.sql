-- 002_pages.sql - PostgreSQL version
CREATE TABLE IF NOT EXISTS "cms_pages" (
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

CREATE INDEX idx_pages_status ON "cms_pages" (status);
CREATE INDEX idx_pages_slug ON "cms_pages" (slug);
