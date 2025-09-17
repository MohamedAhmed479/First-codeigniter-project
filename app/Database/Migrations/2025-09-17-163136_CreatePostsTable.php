<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        /**
         * إنشاء جدول المقالات
         * يحتوي على علاقات مع المستخدمين والفئات
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'featured_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default'    => 'draft',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        
        // إضافة فهارس للبحث والأداء
        // ملاحظة: لا نحتاج لإضافة فهرس للـ slug لأنه محدد كـ UNIQUE مسبقاً
        $this->forge->addKey('status');
        $this->forge->addKey('user_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('published_at');
        
        $this->forge->createTable('posts');
        
        /**
         * إضافة Foreign Keys
         * في CodeIgniter 4، نحتاج لإضافة Foreign Keys بعد إنشاء الجدول
         * هذا مختلف عن Laravel حيث يمكن إضافتها مباشرة في التعريف
         */
        
        // Foreign key للمستخدم
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        // Foreign key للفئة (مع SET NULL عند الحذف)
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        /**
         * حذف الجدول مع Foreign Keys
         * CodeIgniter سيحذف Foreign Keys تلقائياً
         */
        $this->forge->dropTable('posts');
    }
}
