<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\Post;
use App\Models\Category;

class PostsController extends BaseApiController
{
    protected $postModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        helper(['url', 'form', 'text']);
    }

    /**
     * Get all posts with pagination
     * GET /api/posts
     */
    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $status = $this->request->getGet('status') ?? 'published';
        $category = $this->request->getGet('category');
        $search = $this->request->getGet('search');

        $builder = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name, categories.name as category_name, categories.slug as category_slug')
                                   ->join('users', 'users.id = posts.user_id')
                                   ->join('categories', 'categories.id = posts.category_id', 'left')
                                   ->orderBy('posts.published_at', 'DESC');

        // Filter by status
        if ($status) {
            $builder->where('posts.status', $status);
        }

        // Filter by category
        if ($category) {
            $builder->where('categories.slug', $category);
        }

        // Search in title and content
        if ($search) {
            $builder->groupStart()
                    ->like('posts.title', $search)
                    ->orLike('posts.content', $search)
                    ->orLike('posts.excerpt', $search)
                    ->groupEnd();
        }

        $posts = $builder->paginate($perPage, 'default', $page);
        $pager = $this->postModel->pager;

        return $this->respondWithPagination($posts, $pager, 'Posts retrieved successfully');
    }

    /**
     * Get single post by ID or slug
     * GET /api/posts/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->respondWithError('Post ID is required', 400);
        }

        // Check if ID is numeric (ID) or string (slug)
        if (is_numeric($id)) {
            $post = $this->postModel->getPostWithUser($id);
        } else {
            $post = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name, categories.name as category_name, categories.slug as category_slug')
                                   ->join('users', 'users.id = posts.user_id')
                                   ->join('categories', 'categories.id = posts.category_id', 'left')
                                   ->where('posts.slug', $id)
                                   ->where('posts.status', 'published')
                                   ->first();
        }

        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }

        return $this->respondWithSuccess($post, 'Post retrieved successfully');
    }

    /**
     * Create new post
     * POST /api/posts
     */
    public function create()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $rules = [
            'title'   => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[10]',
            'status'  => 'required|in_list[draft,published]',
            'category_id' => 'permit_empty|integer|is_not_unique[categories.id]',
            'excerpt' => 'permit_empty|max_length[500]',
            'featured_image' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $jsonData = $this->request->getJSON(true);
        $postData = [
            'title'       => $jsonData['title'] ?? $this->request->getVar('title'),
            'content'     => $jsonData['content'] ?? $this->request->getVar('content'),
            'excerpt'     => $jsonData['excerpt'] ?? $this->request->getVar('excerpt'),
            'status'      => $jsonData['status'] ?? $this->request->getVar('status'),
            'category_id' => ($jsonData['category_id'] ?? $this->request->getVar('category_id')) ?: null,
            'featured_image' => $jsonData['featured_image'] ?? $this->request->getVar('featured_image'),
            'user_id'     => $user['id'],
        ];

        if ($this->postModel->save($postData)) {
            $post = $this->postModel->find($this->postModel->getInsertID());
            return $this->respondWithSuccess($post, 'Post created successfully', 201);
        } else {
            return $this->respondWithError('Failed to create post', 500);
        }
    }

    /**
     * Update existing post
     * PUT /api/posts/{id}
     */
    public function update($id = null)
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        if (!$id) {
            return $this->respondWithError('Post ID is required', 400);
        }

        $post = $this->postModel->find($id);
        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }

        // Check if user owns the post
        if ($post['user_id'] != $user['id']) {
            return $this->respondWithError('Unauthorized to update this post', 403);
        }

        $rules = [
            'title'   => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[10]',
            'status'  => 'required|in_list[draft,published,archived]',
            'category_id' => 'permit_empty|integer|is_not_unique[categories.id]',
            'excerpt' => 'permit_empty|max_length[500]',
            'featured_image' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        // Get data from JSON or POST
        $jsonData = $this->request->getJSON(true);
        $postData = [
            'id'          => $id,
            'title'       => $jsonData['title'] ?? $this->request->getVar('title'),
            'content'     => $jsonData['content'] ?? $this->request->getVar('content'),
            'excerpt'     => $jsonData['excerpt'] ?? $this->request->getVar('excerpt'),
            'status'      => $jsonData['status'] ?? $this->request->getVar('status'),
            'category_id' => ($jsonData['category_id'] ?? $this->request->getVar('category_id')) ?: null,
            'featured_image' => $jsonData['featured_image'] ?? $this->request->getVar('featured_image')
        ];

        if ($this->postModel->save($postData)) {
            $updatedPost = $this->postModel->find($id);
            return $this->respondWithSuccess($updatedPost, 'Post updated successfully');
        } else {
            return $this->respondWithError('Failed to update post', 500);
        }
    }

    /**
     * Delete post
     * DELETE /api/posts/{id}
     */
    public function delete($id = null)
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        if (!$id) {
            return $this->respondWithError('Post ID is required', 400);
        }

        $post = $this->postModel->find($id);
        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }

        // Check if user owns the post
        if ($post['user_id'] != $user['id']) {
            return $this->respondWithError('Unauthorized to delete this post', 403);
        }

        if ($this->postModel->delete($id)) {
            return $this->respondWithSuccess(null, 'Post deleted successfully');
        } else {
            return $this->respondWithError('Failed to delete post', 500);
        }
    }

    /**
     * Get user's posts
     * GET /api/posts/my-posts
     */
    public function myPosts()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $status = $this->request->getGet('status');

        $builder = $this->postModel->where('user_id', $user['id'])
                                   ->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        $posts = $builder->paginate($perPage, 'default', $page);
        $pager = $this->postModel->pager;

        return $this->respondWithPagination($posts, $pager, 'User posts retrieved successfully');
    }

    /**
     * Get posts by category
     * GET /api/posts/category/{slug}
     */
    public function byCategory($categorySlug = null)
    {
        if (!$categorySlug) {
            return $this->respondWithError('Category slug is required', 400);
        }

        $category = $this->categoryModel->where('slug', $categorySlug)->first();
        if (!$category) {
            return $this->respondWithError('Category not found', 404);
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        $posts = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name, categories.name as category_name, categories.slug as category_slug')
                                 ->join('users', 'users.id = posts.user_id')
                                 ->join('categories', 'categories.id = posts.category_id')
                                 ->where('posts.category_id', $category['id'])
                                 ->where('posts.status', 'published')
                                 ->orderBy('posts.published_at', 'DESC')
                                 ->paginate($perPage, 'default', $page);

        $pager = $this->postModel->pager;

        return $this->respondWithPagination($posts, $pager, 'Category posts retrieved successfully');
    }

    /**
     * Get posts by author
     * GET /api/posts/author/{username}
     */
    public function byAuthor($username = null)
    {
        if (!$username) {
            return $this->respondWithError('Username is required', 400);
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        $posts = $this->postModel->select('posts.*, users.username, users.first_name, users.last_name, categories.name as category_name, categories.slug as category_slug')
                                 ->join('users', 'users.id = posts.user_id')
                                 ->join('categories', 'categories.id = posts.category_id', 'left')
                                 ->where('users.username', $username)
                                 ->where('posts.status', 'published')
                                 ->orderBy('posts.published_at', 'DESC')
                                 ->paginate($perPage, 'default', $page);

        $pager = $this->postModel->pager;

        if (empty($posts)) {
            return $this->respondWithError('Author not found or has no posts', 404);
        }

        return $this->respondWithPagination($posts, $pager, 'Author posts retrieved successfully');
    }
}
