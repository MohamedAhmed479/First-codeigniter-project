<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Models\Category;
use App\Models\Post;

class CategoriesController extends BaseApiController
{
    protected $categoryModel;
    protected $postModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->postModel = new Post();
        helper(['url', 'form']);
    }

    /**
     * Get all categories
     * GET /api/categories
     */
    public function index()
    {
        $withCounts = $this->request->getGet('with_counts') === 'true';
        
        $categories = $this->categoryModel->where('is_active', 1)
                                          ->orderBy('name', 'ASC')
                                          ->findAll();

        if ($withCounts) {
            foreach ($categories as &$category) {
                $category['posts_count'] = $this->postModel->where('category_id', $category['id'])
                                                           ->where('status', 'published')
                                                           ->countAllResults();
            }
        }

        return $this->respondWithSuccess($categories, 'Categories retrieved successfully');
    }

    /**
     * Get single category by ID or slug
     * GET /api/categories/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->respondWithError('Category ID is required', 400);
        }

        // Check if ID is numeric (ID) or string (slug)
        if (is_numeric($id)) {
            $category = $this->categoryModel->find($id);
        } else {
            $category = $this->categoryModel->where('slug', $id)->first();
        }

        if (!$category) {
            return $this->respondWithError('Category not found', 404);
        }

        // Add posts count
        $category['posts_count'] = $this->postModel->where('category_id', $category['id'])
                                                   ->where('status', 'published')
                                                   ->countAllResults();

        return $this->respondWithSuccess($category, 'Category retrieved successfully');
    }

    /**
     * Create new category (Admin only)
     * POST /api/categories
     */
    public function create()
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        // TODO: Add admin role check
        // For now, any authenticated user can create categories

        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]',
            'is_active'   => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        $categoryData = [
            'name'        => $this->request->getVar('name'),
            'slug'        => url_title($this->request->getVar('name'), '-', true),
            'description' => $this->request->getVar('description'),
            'is_active'   => $this->request->getVar('is_active') ?? 1,
        ];

        if ($this->categoryModel->save($categoryData)) {
            $category = $this->categoryModel->find($this->categoryModel->getInsertID());
            return $this->respondWithSuccess($category, 'Category created successfully', 201);
        } else {
            return $this->respondWithError('Failed to create category', 500);
        }
    }

    /**
     * Update existing category (Admin only)
     * PUT /api/categories/{id}
     */
    public function update($id = null)
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        // TODO: Add admin role check
        // For now, any authenticated user can update categories

        if (!$id) {
            return $this->respondWithError('Category ID is required', 400);
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->respondWithError('Category not found', 404);
        }

        $rules = [
            'name'        => "required|min_length[2]|max_length[100]|is_unique[categories.name,id,{$id}]",
            'description' => 'permit_empty|max_length[500]',
            'is_active'   => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->respondWithValidationError($this->validator->getErrors());
        }

        $categoryData = [
            'id'          => $id,
            'name'        => $this->request->getVar('name'),
            'slug'        => url_title($this->request->getVar('name'), '-', true),
            'description' => $this->request->getVar('description'),
            'is_active'   => $this->request->getVar('is_active') ?? $category['is_active'],
        ];

        if ($this->categoryModel->save($categoryData)) {
            $updatedCategory = $this->categoryModel->find($id);
            return $this->respondWithSuccess($updatedCategory, 'Category updated successfully');
        } else {
            return $this->respondWithError('Failed to update category', 500);
        }
    }

    /**
     * Delete category (Admin only)
     * DELETE /api/categories/{id}
     */
    public function delete($id = null)
    {
        $user = $this->requireAuth();
        if (!is_array($user)) {
            return $user; // Return error response
        }

        // TODO: Add admin role check
        // For now, any authenticated user can delete categories

        if (!$id) {
            return $this->respondWithError('Category ID is required', 400);
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->respondWithError('Category not found', 404);
        }

        // Check if category has posts
        $postsCount = $this->postModel->where('category_id', $id)->countAllResults();
        if ($postsCount > 0) {
            return $this->respondWithError('Cannot delete category with existing posts. Move or delete posts first.', 400);
        }

        if ($this->categoryModel->delete($id)) {
            return $this->respondWithSuccess(null, 'Category deleted successfully');
        } else {
            return $this->respondWithError('Failed to delete category', 500);
        }
    }

    /**
     * Get category statistics
     * GET /api/categories/{id}/stats
     */
    public function stats($id = null)
    {
        if (!$id) {
            return $this->respondWithError('Category ID is required', 400);
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->respondWithError('Category not found', 404);
        }

        $stats = [
            'total_posts' => $this->postModel->where('category_id', $id)->countAllResults(),
            'published_posts' => $this->postModel->where('category_id', $id)->where('status', 'published')->countAllResults(),
            'draft_posts' => $this->postModel->where('category_id', $id)->where('status', 'draft')->countAllResults(),
            'archived_posts' => $this->postModel->where('category_id', $id)->where('status', 'archived')->countAllResults(),
        ];

        // Get recent posts
        $recentPosts = $this->postModel->select('id, title, slug, status, created_at')
                                       ->where('category_id', $id)
                                       ->orderBy('created_at', 'DESC')
                                       ->limit(5)
                                       ->findAll();

        $data = [
            'category' => $category,
            'stats' => $stats,
            'recent_posts' => $recentPosts
        ];

        return $this->respondWithSuccess($data, 'Category statistics retrieved successfully');
    }
}
