<?php

namespace App\Models;

use CodeIgniter\Model;

class Post extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;  // تفعيل Soft Delete
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'slug', 'content', 'excerpt', 'featured_image', 
        'status', 'user_id', 'category_id', 'published_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;  // تفعيل timestamps
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title'       => 'required|min_length[5]|max_length[255]',
        'slug'        => 'permit_empty|min_length[5]|max_length[255]|is_unique[posts.slug,id,{id}]',
        'content'     => 'required|min_length[10]',
        'excerpt'     => 'permit_empty|max_length[500]',
        'status'      => 'required|in_list[draft,published,archived]',
        'user_id'     => 'required|integer|is_not_unique[users.id]',
        'category_id' => 'permit_empty|integer|is_not_unique[categories.id]',
        'featured_image' => 'permit_empty|max_length[255]',
    ];
    
    protected $validationMessages = [
        'title' => [
            'required'    => 'عنوان المقال مطلوب',
            'min_length'  => 'عنوان المقال يجب أن يكون 5 أحرف على الأقل',
            'max_length'  => 'عنوان المقال لا يجب أن يزيد عن 255 حرف',
        ],
        'slug' => [
            'required'    => 'رابط المقال مطلوب',
            'min_length'  => 'رابط المقال يجب أن يكون 5 أحرف على الأقل',
            'max_length'  => 'رابط المقال لا يجب أن يزيد عن 255 حرف',
            'is_unique'   => 'رابط المقال موجود مسبقاً',
        ],
        'content' => [
            'required'    => 'محتوى المقال مطلوب',
            'min_length'  => 'محتوى المقال يجب أن يكون 10 أحرف على الأقل',
        ],
        'status' => [
            'required'  => 'حالة المقال مطلوبة',
            'in_list'   => 'حالة المقال يجب أن تكون مسودة أو منشورة أو مؤرشفة',
        ],
        'user_id' => [
            'required'      => 'معرف المستخدم مطلوب',
            'integer'       => 'معرف المستخدم يجب أن يكون رقم',
            'is_not_unique' => 'المستخدم غير موجود',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug', 'setPublishedDate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['generateSlug', 'setPublishedDate'];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['handleNullDates'];
    protected $afterFind      = ['handleNullDates'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * توليد slug تلقائياً من عنوان المقال
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['title']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
        }
        return $data;
    }

    /**
     * تحديد تاريخ النشر عند تغيير الحالة إلى منشور
     */
    protected function setPublishedDate(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'published') {
            if (empty($data['data']['published_at'])) {
                $data['data']['published_at'] = date('Y-m-d H:i:s');
            }
        } elseif (isset($data['data']['status']) && $data['data']['status'] === 'draft') {
            // إذا كان المقال مسودة، تأكد من أن published_at هو null
            $data['data']['published_at'] = null;
        }
        return $data;
    }

    /**
     * التعامل مع التواريخ null
     */
    protected function handleNullDates(array $data)
    {
        // لا نحتاج لفعل شيء هنا، فقط للتأكد من عدم وجود مشاكل
        return $data;
    }

    /**
     * الحصول على المقالات المنشورة فقط
     * @param int $limit
     * @return array
     */
    public function getPublishedPosts(int $limit = 10): array
    {
        return $this->where('status', 'published')
                   ->where('published_at IS NOT NULL')
                   ->orderBy('published_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * البحث عن مقال بـ slug
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
                   ->where('status', 'published')
                   ->first();
    }

    /**
     * الحصول على معلومات المستخدم مع المقال
     * علاقة belongsTo مع User
     * @param int $postId
     * @return array|null
     */
    public function getPostWithUser(int $postId): ?array
    {
        return $this->select('posts.*, users.username, users.first_name, users.last_name')
                   ->join('users', 'users.id = posts.user_id')
                   ->where('posts.id', $postId)
                   ->first();
    }

    /**
     * الحصول على معلومات الفئة مع المقال
     * علاقة belongsTo مع Category
     * @param int $postId
     * @return array|null
     */
    public function getPostWithCategory(int $postId): ?array
    {
        return $this->select('posts.*, categories.name as category_name, categories.slug as category_slug')
                   ->join('categories', 'categories.id = posts.category_id', 'left')
                   ->where('posts.id', $postId)
                   ->first();
    }

    /**
     * الحصول على المقالات مع جميع العلاقات
     * @param array $where
     * @param int $limit
     * @return array
     */
    public function getPostsWithRelations(array $where = [], int $limit = 10): array
    {
        $builder = $this->select('posts.*, users.username, users.first_name, users.last_name, categories.name as category_name, categories.slug as category_slug')
                       ->join('users', 'users.id = posts.user_id')
                       ->join('categories', 'categories.id = posts.category_id', 'left')
                       ->orderBy('posts.published_at', 'DESC')
                       ->limit($limit);
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->findAll();
    }
}
