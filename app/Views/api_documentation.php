<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog API Documentation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .section {
            background: white;
            margin: 20px 0;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section h2 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .endpoint {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 10px;
        }
        
        .get { background: #28a745; color: white; }
        .post { background: #007bff; color: white; }
        .put { background: #ffc107; color: #212529; }
        .delete { background: #dc3545; color: white; }
        
        .url {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .description {
            margin-top: 10px;
            color: #6c757d;
        }
        
        .example {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Blog API Documentation</h1>
            <p>RESTful API for CodeIgniter 4 Blog Application</p>
            <p><strong>Base URL:</strong> <code><?= base_url('api') ?></code></p>
        </div>

        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3>Authentication</h3>
                <p>Secure user registration, login, and profile management</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Posts Management</h3>
                <p>CRUD operations for blog posts with advanced filtering</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3>Categories</h3>
                <p>Organize posts with categories and statistics</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Dashboard</h3>
                <p>Analytics, activity feeds, and search functionality</p>
            </div>
        </div>

        <div class="section">
            <h2>🔐 Authentication Endpoints</h2>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/auth/register</span>
                <div class="description">Register a new user account</div>
                <div class="example">
{
  "first_name": "أحمد",
  "last_name": "محمد",
  "username": "ahmed123",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirm": "password123"
}
                </div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/auth/login</span>
                <div class="description">User login</div>
                <div class="example">
{
  "email": "ahmed@example.com",
  "password": "password123"
}
                </div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/auth/profile</span>
                <div class="description">Get current user profile (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/api/auth/profile</span>
                <div class="description">Update user profile (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/auth/logout</span>
                <div class="description">User logout</div>
            </div>
        </div>

        <div class="section">
            <h2>📝 Posts Endpoints</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/posts</span>
                <div class="description">Get all published posts with pagination</div>
                <div class="example">
Query Parameters:
- page: Page number (default: 1)
- per_page: Items per page (default: 10)
- status: Filter by status (published, draft, archived)
- category: Filter by category slug
- search: Search in title and content
                </div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/posts/{id}</span>
                <div class="description">Get single post by ID or slug</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/posts/my-posts</span>
                <div class="description">Get current user's posts (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/posts</span>
                <div class="description">Create new post (requires authentication)</div>
                <div class="example">
{
  "title": "عنوان المقال",
  "content": "محتوى المقال...",
  "excerpt": "مقتطف من المقال",
  "status": "published",
  "category_id": 1,
  "featured_image": "image.jpg"
}
                </div>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <span class="url">/api/posts/{id}</span>
                <div class="description">Update existing post (requires authentication and ownership)</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/api/posts/{id}</span>
                <div class="description">Delete post (requires authentication and ownership)</div>
            </div>
        </div>

        <div class="section">
            <h2>📁 Categories Endpoints</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/categories</span>
                <div class="description">Get all active categories</div>
                <div class="example">Query Parameters:
- with_counts: Include post counts (true/false)</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/categories/{id}</span>
                <div class="description">Get single category by ID or slug</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/categories</span>
                <div class="description">Create new category (requires authentication)</div>
                <div class="example">
{
  "name": "اسم الفئة",
  "description": "وصف الفئة",
  "is_active": 1
}
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📊 Dashboard Endpoints</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/dashboard</span>
                <div class="description">Get user dashboard statistics (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/dashboard/admin</span>
                <div class="description">Get admin statistics (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/dashboard/activity</span>
                <div class="description">Get recent activity feed (requires authentication)</div>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/dashboard/search</span>
                <div class="description">Search across all content</div>
                <div class="example">Query Parameters:
- q: Search query (required)</div>
            </div>
        </div>

        <div class="section">
            <h2>📋 Response Format</h2>
            <p>All API responses follow a consistent JSON format:</p>
            
            <div class="example">
<span class="status-badge status-success">Success Response:</span>
{
  "status": true,
  "message": "Success message",
  "data": { ... }
}

<span class="status-badge status-error">Error Response:</span>
{
  "status": false,
  "message": "Error message",
  "data": null
}

<span class="status-badge status-error">Validation Error:</span>
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
            </div>
        </div>

        <div class="section">
            <h2>🔑 Authentication</h2>
            <p><strong>Token-based authentication</strong> similar to Laravel Sanctum is now implemented!</p>
            <p>For authenticated endpoints, make sure to:</p>
            <ul>
                <li>Login first via <code>/api/auth/login</code> to get your token</li>
                <li>Include <code>Authorization: Bearer {token}</code> header in subsequent requests</li>
                <li>Handle 401 responses for unauthorized access</li>
                <li>Manage your tokens via <code>/api/auth/tokens</code> endpoints</li>
            </ul>

            <h3>🎯 Token Management Endpoints:</h3>
            <div class="endpoint">
                <span class="method get">GET</span>
                <span class="url">/api/auth/tokens</span>
                <div class="description">Get all user's active tokens</div>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <span class="url">/api/auth/tokens</span>
                <div class="description">Create new token with custom abilities</div>
                <div class="example">
{
  "name": "Mobile App",
  "abilities": ["posts:read", "posts:write"],
  "expires_in_minutes": 1440
}
                </div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/api/auth/tokens/{token}</span>
                <div class="description">Revoke specific token</div>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <span class="url">/api/auth/tokens</span>
                <div class="description">Revoke all user tokens</div>
            </div>
        </div>

        <div class="section">
            <h2>🌍 CORS</h2>
            <p>CORS is enabled for all origins. The API supports:</p>
            <ul>
                <li><strong>Methods:</strong> GET, POST, PUT, DELETE, OPTIONS</li>
                <li><strong>Headers:</strong> Content-Type, Authorization, X-Requested-With, Accept, Origin</li>
                <li><strong>Origins:</strong> * (All origins allowed)</li>
            </ul>
        </div>

        <div class="section">
            <h2>💡 Getting Started</h2>
            <p>1. Register a new user account:</p>
            <div class="example">
curl -X POST <?= base_url('api/auth/register') ?> \
  -H "Content-Type: application/json" \
  -d '{"first_name":"Ahmed","last_name":"Mohamed","username":"ahmed123","email":"ahmed@example.com","password":"password123","password_confirm":"password123"}'
            </div>

            <p>2. Login to get your token:</p>
            <div class="example">
curl -X POST <?= base_url('api/auth/login') ?> \
  -H "Content-Type: application/json" \
  -d '{"email":"ahmed@example.com","password":"password123","device_name":"My App"}'
            </div>

            <p>3. Use the token to access protected endpoints:</p>
            <div class="example">
curl -X GET <?= base_url('api/auth/profile') ?> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
            </div>

            <p>4. Create a new post:</p>
            <div class="example">
curl -X POST <?= base_url('api/posts') ?> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"title":"My First Post","content":"Post content here","status":"published"}'
            </div>

            <p>5. Logout (revoke current token):</p>
            <div class="example">
curl -X POST <?= base_url('api/auth/logout') ?> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
            </div>
        </div>
    </div>
</body>
</html>
