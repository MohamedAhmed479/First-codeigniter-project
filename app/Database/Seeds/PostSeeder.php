<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        /**
         * إنشاء مقالات تجريبية
         * نفترض وجود المستخدمين والفئات مسبقاً
         */
        
        $posts = [
            [
                'title'        => 'مقدمة في CodeIgniter 4',
                'slug'         => 'introduction-to-codeigniter-4',
                'content'      => 'CodeIgniter 4 هو إطار عمل PHP حديث يوفر مرونة وقوة في تطوير تطبيقات الويب. يتميز بسهولة الاستخدام والأداء العالي...',
                'excerpt'      => 'تعرف على أساسيات CodeIgniter 4 وكيفية البدء في استخدامه لتطوير تطبيقات الويب الحديثة.',
                'status'       => 'published',
                'user_id'      => 1,
                'category_id'  => 1, // تقنية
                'published_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'أهمية ممارسة الرياضة اليومية',
                'slug'         => 'importance-of-daily-exercise',
                'content'      => 'ممارسة الرياضة بانتظام لها فوائد عديدة على الصحة الجسدية والنفسية. تساعد على تقوية العضلات وتحسين الدورة الدموية...',
                'excerpt'      => 'اكتشف الفوائد المذهلة لممارسة الرياضة يومياً وتأثيرها الإيجابي على صحتك العامة.',
                'status'       => 'published',
                'user_id'      => 2,
                'category_id'  => 2, // رياضة
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'created_at'   => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at'   => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'title'        => 'أجمل الوجهات السياحية في العالم',
                'slug'         => 'beautiful-tourist-destinations',
                'content'      => 'السفر يوسع الآفاق ويثري التجارب الشخصية. في هذا المقال نستكشف أجمل الوجهات السياحية حول العالم...',
                'excerpt'      => 'رحلة مصورة إلى أجمل الأماكن السياحية التي يجب زيارتها مرة واحدة على الأقل في العمر.',
                'status'       => 'published',
                'user_id'      => 3,
                'category_id'  => 3, // سفر
                'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'created_at'   => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'   => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'title'        => 'وصفة الكبسة السعودية الأصلية',
                'slug'         => 'authentic-saudi-kabsa-recipe',
                'content'      => 'الكبسة من أشهر الأطباق في المطبخ السعودي والخليجي. تتميز بطعمها الغني ونكهاتها المميزة...',
                'excerpt'      => 'تعلم طريقة تحضير الكبسة السعودية الأصلية بالخطوات المفصلة والمكونات الطازجة.',
                'status'       => 'published',
                'user_id'      => 1,
                'category_id'  => 4, // طبخ
                'published_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'created_at'   => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at'   => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'title'        => 'نصائح للحفاظ على الصحة النفسية',
                'slug'         => 'mental-health-tips',
                'content'      => 'الصحة النفسية لا تقل أهمية عن الصحة الجسدية. في هذا المقال نقدم نصائح عملية للحفاظ على التوازن النفسي...',
                'excerpt'      => 'نصائح مهمة وعملية للحفاظ على صحتك النفسية وتحقيق التوازن في الحياة اليومية.',
                'status'       => 'published',
                'user_id'      => 2,
                'category_id'  => 5, // صحة
                'published_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'created_at'   => date('Y-m-d H:i:s', strtotime('-4 days')),
                'updated_at'   => date('Y-m-d H:i:s', strtotime('-4 days')),
            ],
            [
                'title'        => 'مسودة: مقال قيد التطوير',
                'slug'         => 'draft-article-in-development',
                'content'      => 'هذا مقال لا يزال قيد التطوير والكتابة...',
                'excerpt'      => 'مقال تجريبي في حالة مسودة.',
                'status'       => 'draft',
                'user_id'      => 3,
                'category_id'  => 1,
                'published_at' => null,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // إدراج المقالات
        $this->db->table('posts')->insertBatch($posts);
    }
}
