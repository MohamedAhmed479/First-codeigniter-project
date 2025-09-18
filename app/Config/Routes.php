<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/**
 * Blog API Routes
 * RESTful API endpoints for the blog application
 */

// API Routes Group
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // Authentication Routes
    $routes->group('auth', function($routes) {
        // Public routes
        $routes->post('register', 'AuthController::register');
        $routes->post('login', 'AuthController::login');
        
        // Protected routes (require token)
        $routes->group('', ['filter' => 'token-auth'], function($routes) {
            $routes->post('logout', 'AuthController::logout');
            $routes->get('profile', 'AuthController::profile');
            $routes->put('profile', 'AuthController::updateProfile');
            $routes->put('change-password', 'AuthController::changePassword');
            
            // Token management
            $routes->get('tokens', 'AuthController::tokens');
            $routes->post('tokens', 'AuthController::createToken');
            $routes->delete('tokens/(:segment)', 'AuthController::revokeToken/$1');
            $routes->delete('tokens', 'AuthController::revokeAllTokens');
        });
    });

    // Posts Routes
    $routes->group('posts', function($routes) {
        // Public routes
        $routes->get('/', 'PostsController::index');
        $routes->get('category/(:segment)', 'PostsController::byCategory/$1');
        $routes->get('author/(:segment)', 'PostsController::byAuthor/$1');

        // Protected routes (require token)
        $routes->group('', ['filter' => 'token-auth'], function($routes) {
            $routes->get('my-posts', 'PostsController::myPosts');
            $routes->post('/', 'PostsController::create');
            $routes->put('(:num)', 'PostsController::update/$1');
            $routes->delete('(:num)', 'PostsController::delete/$1');
        });

        // Catch-all for a single post (must come AFTER more specific routes)
        $routes->get('(:segment)', 'PostsController::show/$1');
    });

    // Categories Routes
    $routes->group('categories', function($routes) {
        // Public routes
        $routes->get('/', 'CategoriesController::index');
        $routes->get('(:segment)', 'CategoriesController::show/$1');
        
        // Protected routes (require token)
        $routes->group('', ['filter' => 'token-auth'], function($routes) {
            $routes->get('(:num)/stats', 'CategoriesController::stats/$1');
            $routes->post('/', 'CategoriesController::create');
            $routes->put('(:num)', 'CategoriesController::update/$1');
            $routes->delete('(:num)', 'CategoriesController::delete/$1');
        });
    });

    // Dashboard Routes (all protected)
    $routes->group('dashboard', ['filter' => 'token-auth'], function($routes) {
        $routes->get('/', 'DashboardController::index');
        $routes->get('admin', 'DashboardController::adminStats');
        $routes->get('activity', 'DashboardController::activity');
        $routes->get('search', 'DashboardController::search');
    });

});

// Default route - API documentation
$routes->get('/', function() {
    return view('api_documentation');
});

// Catch-all route for API
$routes->get('(.*)', function() {
    $response = [
        'status' => false,
        'message' => 'Endpoint not found',
        'available_endpoints' => [
            'Authentication' => [
                'POST /api/auth/register',
                'POST /api/auth/login',
                'POST /api/auth/logout',
                'GET /api/auth/profile',
                'PUT /api/auth/profile',
                'PUT /api/auth/change-password'
            ],
            'Posts' => [
                'GET /api/posts',
                'GET /api/posts/{id}',
                'GET /api/posts/my-posts',
                'GET /api/posts/category/{slug}',
                'GET /api/posts/author/{username}',
                'POST /api/posts',
                'PUT /api/posts/{id}',
                'DELETE /api/posts/{id}'
            ],
            'Categories' => [
                'GET /api/categories',
                'GET /api/categories/{id}',
                'GET /api/categories/{id}/stats',
                'POST /api/categories',
                'PUT /api/categories/{id}',
                'DELETE /api/categories/{id}'
            ],
            'Dashboard' => [
                'GET /api/dashboard',
                'GET /api/dashboard/admin',
                'GET /api/dashboard/activity',
                'GET /api/dashboard/search'
            ]
        ]
    ];
    
    return service('response')->setJSON($response)->setStatusCode(404);
});
