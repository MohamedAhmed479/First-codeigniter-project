<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        /**
         * إنشاء فئات تجريبية للمقالات
         */
        
        $categories = [
            [
                'name'        => 'تقنية',
                'slug'        => 'technology',
                'description' => 'مقالات حول التكنولوجيا والبرمجة',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'رياضة',
                'slug'        => 'sports',
                'description' => 'أخبار ومقالات رياضية',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'سفر',
                'slug'        => 'travel',
                'description' => 'مقالات عن السفر والسياحة',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'طبخ',
                'slug'        => 'cooking',
                'description' => 'وصفات وتقنيات الطبخ',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'صحة',
                'slug'        => 'health',
                'description' => 'مقالات حول الصحة واللياقة البدنية',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // إدراج الفئات
        $this->db->table('categories')->insertBatch($categories);
    }
}
