<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        /**
         * تشغيل جميع Seeders بالترتيب الصحيح
         * في CodeIgniter 4، نحتاج لإنشاء Seeder رئيسي لتنظيم العملية
         * هذا مشابه لـ DatabaseSeeder في Laravel
         */
        
        // تشغيل seeders بالترتيب المطلوب
        // المستخدمون أولاً (لأن المقالات تحتاجهم)
        $this->call('UserSeeder');
        
        // ثم الفئات
        $this->call('CategorySeeder');
        
        // وأخيراً المقالات (تحتاج المستخدمين والفئات)
        $this->call('PostSeeder');
    }
}
