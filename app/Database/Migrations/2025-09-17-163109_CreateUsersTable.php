<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        /**
         * إنشاء جدول المستخدمين
         * في CodeIgniter 4، نستخدم $this->forge للتعامل مع هيكل قاعدة البيانات
         * هذا مشابه لـ Schema::create في Laravel لكن بطريقة مختلفة
         */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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

        // تعيين المفتاح الأساسي
        $this->forge->addPrimaryKey('id');
        
        // إنشاء فهارس للبحث السريع
        // $this->forge->addKey('username');
        
        // إنشاء الجدول
        // ملاحظة: في Laravel نكتب Schema::create('users', function...)
        // في CodeIgniter نستخدم createTable مباشرة
        $this->forge->createTable('users');
    }

    public function down()
    {
        /**
         * حذف جدول المستخدمين عند التراجع عن Migration
         * مشابه لـ Schema::dropIfExists في Laravel
         */
        $this->forge->dropTable('users');
    }
}
