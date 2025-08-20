import { Pool } from 'pg';

const pool = new Pool({
  connectionString: process.env.database_url,
  ssl: {
    rejectUnauthorized: false
  }
});

export default async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  try {
    // Initialize database tables
    await pool.query(`
      CREATE TABLE IF NOT EXISTS categories (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        color VARCHAR(7) DEFAULT '#3b82f6',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // Insert default categories if table is empty
    const countResult = await pool.query('SELECT COUNT(*) as count FROM categories');
    if (parseInt(countResult.rows[0].count) === 0) {
      await pool.query(`
        INSERT INTO categories (name, slug, description, color) VALUES 
        ('Technologie', 'technologie', 'Wpisy o technologiach', '#3b82f6'),
        ('Design', 'design', 'Wpisy o designie', '#10b981'),
        ('Bezpieczeństwo', 'bezpieczenstwo', 'Wpisy o bezpieczeństwie', '#f59e0b'),
        ('Wydajność', 'wydajnosc', 'Wpisy o wydajności', '#ef4444')
      `);
    }

    if (req.method === 'GET') {
      const result = await pool.query('SELECT * FROM categories ORDER BY name ASC');
      res.status(200).json({ success: true, data: result.rows });
    } else if (req.method === 'POST') {
      const { name, slug, description, color } = req.body;
      
      const result = await pool.query(`
        INSERT INTO categories (name, slug, description, color)
        VALUES ($1, $2, $3, $4)
        RETURNING id
      `, [name, slug, description, color]);
      
      res.status(200).json({ success: true, message: 'Category created successfully', id: result.rows[0].id });
    } else if (req.method === 'PUT') {
      const id = req.url.split('/').pop();
      const { name, slug, description, color } = req.body;
      
      await pool.query(`
        UPDATE categories SET 
          name = $1, 
          slug = $2, 
          description = $3,
          color = $4
        WHERE id = $5
      `, [name, slug, description, color, id]);
      
      res.status(200).json({ success: true, message: 'Category updated successfully' });
    } else if (req.method === 'DELETE') {
      const id = req.url.split('/').pop();
      
      await pool.query('DELETE FROM categories WHERE id = $1', [id]);
      
      res.status(200).json({ success: true, message: 'Category deleted successfully' });
    } else {
      res.status(405).json({ success: false, message: 'Method not allowed' });
    }
  } catch (error) {
    console.error('Database error:', error);
    res.status(500).json({ success: false, message: 'Database error: ' + error.message });
  }
}
