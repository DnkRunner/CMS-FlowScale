-- 003_theme_settings.sql - PostgreSQL version
CREATE TABLE IF NOT EXISTS "cms_theme_settings" (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(191) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

-- Domyślne ustawienia motywu
INSERT INTO "cms_theme_settings" (setting_key, setting_value) VALUES
('theme_mode', 'light'),
('brand_logo', ''),
('logo_width', '150'),
('menu_background_color', '#ffffff'),
('menu_text_color', '#333333'),
('menu_font_family', 'Arial, sans-serif'),
('menu_font_size', '16'),
('show_header', '1'),
('cta_type', 'phone'),
('cta_text', 'Zadzwoń teraz'),
('cta_value', '+48 123 456 789'),
('cta_url', ''),
('cta_background_color', '#007bff'),
('cta_text_color', '#ffffff'),
('show_cta', '1'),
('footer_background_color', '#333333'),
('footer_text_color', '#ffffff'),
('footer_columns', '3'),
('google_analytics_id', ''),
('facebook_pixel_id', '')
ON CONFLICT (setting_key) DO NOTHING;
