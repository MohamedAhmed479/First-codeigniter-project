<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        /**
         * إنشاء مستخدمين تجريبيين
         * في CodeIgniter 4، نستخدم $this->db للوصول لقاعدة البيانات
         * مشابه لـ DB::table في Laravel لكن مع اختلافات في الطريقة
         */
        
        $users = [
            [
                'username'      => 'admin',
                'email'         => 'admin@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name'    => 'أحمد',
                'last_name'     => 'محمد',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'username'      => 'writer1',
                'email'         => 'writer1@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name'    => 'فاطمة',
                'last_name'     => 'علي',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'username'      => 'writer2',
                'email'         => 'writer2@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'first_name'    => 'محمد',
                'last_name'     => 'عبدالله',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        /**
         * طريقة إدراج البيانات في CodeIgniter 4
         * نستخدم $this->db->table() بدلاً من DB::table() في Laravel
         */
        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }
        
        // يمكن أيضاً استخدام insertBatch لإدراج عدة صفوف مرة واحدة
        // $this->db->table('users')->insertBatch($users);
    }
}
