<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

class DashboardController extends BaseApiController
{
    protected $postModel;
    protected $categoryModel;
    protected $userModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->userModel = new User();
        helper(['url', 'form', 'text']);
    }

    /**
     * Get dashboard statistics
     * GET /api/dashboard
     */
    public function index()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        // إحصائيات للمستخدم الحالي
        $userStats = [
            'total_posts' => $this->postModel->where('user_id', $user['id'])->countAllResults(),
            'published_posts' => $this->postModel->where('user_id', $user['id'])->where('status', 'published')->countAllResults(),
            'draft_posts' => $this->postModel->where('user_id', $user['id'])->where('status', 'draft')->countAllResults(),
            'archived_posts' => $this->postModel->where('user_id', $user['id'])->where('status', 'archived')->countAllResults(),
        ];

        // إحصائيات عامة
        $generalStats = [
            'total_categories' => $this->categoryModel->where('is_active', 1)->countAllResults(),
            'total_users' => $this->userModel->where('is_active', 1)->countAllResults(),
            'total_posts_all' => $this->postModel->countAllResults(),
            'published_posts_all' => $this->postModel->where('status', 'published')->countAllResults(),
        ];

        // أحدث المقالات للمستخدم الحالي
        $recentPosts = $this->postModel->where('user_id', $user['id'])
                                       ->orderBy('created_at', 'DESC')
                                       ->limit(5)
                                       ->findAll();

        // أحدث المقالات المنشورة عامة
        $recentPublishedPosts = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name')
                                                ->join('users', 'users.id = posts.user_id')
                                                ->where('posts.status', 'published')
                                                ->orderBy('posts.published_at', 'DESC')
                                                ->limit(5)
                                                ->findAll();

        $data = [
            'user_stats' => $userStats,
            'general_stats' => $generalStats,
            'recent_posts' => $recentPosts,
            'recent_published_posts' => $recentPublishedPosts,
            'current_user' => $user
        ];

        return $this->respondWithSuccess($data, 'Dashboard data retrieved successfully');
    }

    /**
     * Get system statistics (Admin only)
     * GET /api/dashboard/admin
     */
    public function adminStats()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        // TODO: Add admin role check
        // For now, any authenticated user can access admin stats

        // إحصائيات تفصيلية
        $stats = [
            'users' => [
                'total' => $this->userModel->countAllResults(),
                'active' => $this->userModel->where('is_active', 1)->countAllResults(),
                'inactive' => $this->userModel->where('is_active', 0)->countAllResults(),
            ],
            'posts' => [
                'total' => $this->postModel->countAllResults(),
                'published' => $this->postModel->where('status', 'published')->countAllResults(),
                'draft' => $this->postModel->where('status', 'draft')->countAllResults(),
                'archived' => $this->postModel->where('status', 'archived')->countAllResults(),
            ],
            'categories' => [
                'total' => $this->categoryModel->countAllResults(),
                'active' => $this->categoryModel->where('is_active', 1)->countAllResults(),
                'inactive' => $this->categoryModel->where('is_active', 0)->countAllResults(),
            ]
        ];

        // إحصائيات المستخدمين الأكثر نشاطاً
        $topAuthors = $this->postModel->select('users.id, users.username, users.first_name, users.last_name, COUNT(posts.id) as posts_count')
                                      ->join('users', 'users.id = posts.user_id')
                                      ->where('posts.status', 'published')
                                      ->groupBy('users.id')
                                      ->orderBy('posts_count', 'DESC')
                                      ->limit(10)
                                      ->findAll();

        // إحصائيات الفئات الأكثر استخداماً
        $topCategories = $this->categoryModel->select('categories.*, COUNT(posts.id) as posts_count')
                                             ->join('posts', 'posts.category_id = categories.id', 'left')
                                             ->where('categories.is_active', 1)
                                             ->groupBy('categories.id')
                                             ->orderBy('posts_count', 'DESC')
                                             ->limit(10)
                                             ->findAll();

        // إحصائيات شهرية للمقالات (آخر 6 شهور)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-{$i} months"));
            $count = $this->postModel->where("DATE_FORMAT(created_at, '%Y-%m')", $date)
                                     ->where('status', 'published')
                                     ->countAllResults();
            $monthlyStats[] = [
                'month' => $date,
                'posts_count' => $count
            ];
        }

        $data = [
            'stats' => $stats,
            'top_authors' => $topAuthors,
            'top_categories' => $topCategories,
            'monthly_stats' => $monthlyStats
        ];

        return $this->respondWithSuccess($data, 'Admin statistics retrieved successfully');
    }

    /**
     * Get recent activity
     * GET /api/dashboard/activity
     */
    public function activity()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $limitParam = $this->request->getGet('limit');
        $limit = is_null($limitParam) ? 20 : (int) $limitParam;
        if ($limit <= 0) {
            $limit = 20;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        // أحدث الأنشطة (مقالات جديدة، تحديثات، إلخ)
        $activities = [];

        // أحدث المقالات
        $recentPosts = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name')
                                       ->join('users', 'users.id = posts.user_id')
                                       ->orderBy('posts.created_at', 'DESC')
                                       ->limit($limit)
                                       ->findAll();

        foreach ($recentPosts as $post) {
            $activities[] = [
                'type' => 'post_created',
                'title' => 'مقال جديد: ' . $post['title'],
                'description' => 'تم إنشاء مقال جديد بواسطة ' . $post['first_name'] . ' ' . $post['last_name'],
                'user' => $post['first_name'] . ' ' . $post['last_name'],
                'username' => $post['username'],
                'date' => $post['created_at'],
                'data' => [
                    'post_id' => $post['id'],
                    'post_title' => $post['title'],
                    'post_status' => $post['status']
                ]
            ];
        }

        // ترتيب الأنشطة حسب التاريخ
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // أخذ العدد المطلوب فقط
        $activities = array_slice($activities, 0, $limit);

        return $this->respondWithSuccess($activities, 'Activity feed retrieved successfully');
    }

    /**
     * Search across all content
     * GET /api/dashboard/search
     */
    public function search()
    {
        $query = $this->request->getGet('q');
        if (!$query) {
            return $this->respondWithError('Search query is required', 400);
        }

        $results = [
            'posts' => [],
            'categories' => [],
            'users' => []
        ];

        // البحث في المقالات
        $posts = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name')
                                 ->join('users', 'users.id = posts.user_id')
                                 ->groupStart()
                                     ->like('posts.title', $query)
                                     ->orLike('posts.content', $query)
                                     ->orLike('posts.excerpt', $query)
                                 ->groupEnd()
                                 ->limit(10)
                                 ->findAll();

        $results['posts'] = $posts;

        // البحث في الفئات
        $categories = $this->categoryModel->like('name', $query)
                                          ->orLike('description', $query)
                                          ->where('is_active', 1)
                                          ->limit(10)
                                          ->findAll();

        $results['categories'] = $categories;

        // البحث في المستخدمين
        $users = $this->userModel->select('id, username, first_name, last_name, email, created_at')
                                 ->groupStart()
                                     ->like('username', $query)
                                     ->orLike('first_name', $query)
                                     ->orLike('last_name', $query)
                                     ->orLike('email', $query)
                                 ->groupEnd()
                                 ->where('is_active', 1)
                                 ->limit(10)
                                 ->findAll();

        $results['users'] = $users;

        $totalResults = count($results['posts']) + count($results['categories']) + count($results['users']);

        return $this->respondWithSuccess([
            'query' => $query,
            'total_results' => $totalResults,
            'results' => $results
        ], 'Search completed successfully');
    }
}
