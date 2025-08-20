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
      CREATE TABLE IF NOT EXISTS posts (
        id SERIAL PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        content TEXT,
        excerpt TEXT,
        status VARCHAR(20) DEFAULT 'draft',
        author VARCHAR(100) DEFAULT 'Admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        featured_image VARCHAR(500),
        meta_title VARCHAR(255),
        meta_description TEXT,
        categories JSONB,
        template VARCHAR(50) DEFAULT 'default'
      )
    `);

    if (req.method === 'GET') {
      const result = await pool.query('SELECT * FROM posts ORDER BY created_at DESC');
      res.status(200).json({ success: true, data: result.rows });
    } else if (req.method === 'POST') {
      const { title, slug, content, excerpt, status, author, featured_image, meta_title, meta_description, categories, template } = req.body;
      
      const result = await pool.query(`
        INSERT INTO posts (title, slug, content, excerpt, status, author, featured_image, meta_title, meta_description, categories, template)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)
        RETURNING id
      `, [title, slug, content, excerpt, status, author, featured_image, meta_title, meta_description, JSON.stringify(categories), template]);
      
      res.status(200).json({ success: true, message: 'Post created successfully', id: result.rows[0].id });
    } else if (req.method === 'PUT') {
      const id = req.url.split('/').pop();
      const { title, slug, content, excerpt, status, featured_image, meta_title, meta_description, categories, template } = req.body;
      
      await pool.query(`
        UPDATE posts SET 
          title = $1, 
          slug = $2, 
          content = $3, 
          excerpt = $4, 
          status = $5,
          featured_image = $6,
          meta_title = $7,
          meta_description = $8,
          categories = $9,
          template = $10,
          updated_at = CURRENT_TIMESTAMP
        WHERE id = $11
      `, [title, slug, content, excerpt, status, featured_image, meta_title, meta_description, JSON.stringify(categories), template, id]);
      
      res.status(200).json({ success: true, message: 'Post updated successfully' });
    } else if (req.method === 'DELETE') {
      const id = req.url.split('/').pop();
      
      await pool.query('DELETE FROM posts WHERE id = $1', [id]);
      
      res.status(200).json({ success: true, message: 'Post deleted successfully' });
    } else {
      res.status(405).json({ success: false, message: 'Method not allowed' });
    }
  } catch (error) {
    console.error('Database error:', error);
    res.status(500).json({ success: false, message: 'Database error: ' + error.message });
  }
}
