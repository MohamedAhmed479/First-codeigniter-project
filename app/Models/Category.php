<?php

namespace App\Models;

use CodeIgniter\Model;

class Category extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;  // تفعيل Soft Delete
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'slug', 'description', 'is_active'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean',
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
        'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name,id,{id}]',
        'slug' => 'required|min_length[2]|max_length[100]|is_unique[categories.slug,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required'    => 'اسم الفئة مطلوب',
            'min_length'  => 'اسم الفئة يجب أن يكون حرفين على الأقل',
            'max_length'  => 'اسم الفئة لا يجب أن يزيد عن 100 حرف',
            'is_unique'   => 'اسم الفئة موجود مسبقاً',
        ],
        'slug' => [
            'required'    => 'رابط الفئة مطلوب',
            'min_length'  => 'رابط الفئة يجب أن يكون حرفين على الأقل',
            'max_length'  => 'رابط الفئة لا يجب أن يزيد عن 100 حرف',
            'is_unique'   => 'رابط الفئة موجود مسبقاً',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['generateSlug'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * توليد slug تلقائياً من اسم الفئة
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    /**
     * الحصول على الفئات النشطة فقط
     * @return array
     */
    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * البحث عن فئة بـ slug
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * الحصول على المقالات الخاصة بالفئة
     * علاقة hasMany مع Posts
     * @param int $categoryId
     * @return array
     */
    public function getCategoryPosts(int $categoryId): array
    {
        $postModel = new \App\Models\Post();
        return $postModel->where('category_id', $categoryId)
                        ->where('status', 'published')
                        ->orderBy('published_at', 'DESC')
                        ->findAll();
    }
}
