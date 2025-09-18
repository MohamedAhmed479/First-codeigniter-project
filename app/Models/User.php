<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;  // تفعيل Soft Delete
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'username', 'email', 'password_hash', 'first_name', 'last_name', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;  // تفعيل timestamps تلقائياً
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    /**
     * قواعد التحقق من صحة البيانات
     * في CodeIgniter 4، نحدد قواعد التحقق مباشرة في Model
     * هذا مختلف عن Laravel حيث نستخدم FormRequest أو نحدد القواعد في Controller
     */
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password_hash' => 'permit_empty|min_length[8]',
        'first_name' => 'permit_empty|max_length[100]',
        'last_name'  => 'permit_empty|max_length[100]',
        'is_active'  => 'permit_empty|in_list[0,1]',
    ];
    
    protected $validationMessages = [
        'username' => [
            'required'    => 'اسم المستخدم مطلوب',
            'min_length'  => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل',
            'max_length'  => 'اسم المستخدم لا يجب أن يزيد عن 100 حرف',
            'is_unique'   => 'اسم المستخدم موجود مسبقاً',
        ],
        'email' => [
            'required'    => 'البريد الإلكتروني مطلوب',
            'valid_email' => 'البريد الإلكتروني غير صالح',
            'is_unique'   => 'البريد الإلكتروني موجود مسبقاً',
        ],
        'password_hash' => [
            'min_length' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * دوال مساعدة للـ Model
     * في CodeIgniter 4، نضيف الدوال المخصصة مباشرة في Model
     * هذا مشابه لـ Eloquent في Laravel لكن بطريقة مختلفة
     */

    /**
     * تشفير كلمة المرور قبل الحفظ
     * Callback function تعمل قبل insert و update
     */
    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password_hash'])) {
            return $data;
        }

        // تشفير كلمة المرور إذا لم تكن مشفرة مسبقاً
        if (! password_get_info($data['data']['password_hash'])['algo']) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * التحقق من كلمة المرور
     * @param string $password كلمة المرور المدخلة
     * @param string $hash كلمة المرور المشفرة في قاعدة البيانات
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * البحث عن مستخدم بالبريد الإلكتروني
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * البحث عن مستخدم بـ username
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * الحصول على المقالات الخاصة بالمستخدم
     * علاقة hasMany مع Posts
     * @param int $userId
     * @return array
     */
    public function getUserPosts(int $userId): array
    {
        $postModel = new \App\Models\Post();
        return $postModel->where('user_id', $userId)->findAll();
    }
}
