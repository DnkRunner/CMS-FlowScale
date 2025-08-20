export default function handler(req, res) {
  res.status(200).json({
    status: 'API is working',
    timestamp: new Date().toISOString(),
    node_version: process.version,
    environment: process.env.NODE_ENV
  });
}
