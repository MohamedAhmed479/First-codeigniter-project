<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\User;

class UserModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    public function testCanCreateUser()
    {
        /**
         * اختبار إنشاء مستخدم جديد
         * في CodeIgniter 4، نستخدم DatabaseTestTrait للاختبارات
         * هذا مشابه لـ RefreshDatabase في Laravel
         */
        $userModel = new User();
        
        $userData = [
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => 'testpassword123', // سيتم تشفيرها في Model
            'first_name'    => 'Test',
            'last_name'     => 'User',
            'is_active'     => 1,
        ];

        $userId = $userModel->insert($userData);
        
        $this->assertIsNumeric($userId);
        $this->assertGreaterThan(0, $userId);
        
        // التحقق من حفظ البيانات
        $savedUser = $userModel->find($userId);
        $this->assertEquals('testuser', $savedUser['username']);
        $this->assertEquals('test@example.com', $savedUser['email']);
        $this->assertTrue(password_verify('testpassword123', $savedUser['password_hash']));
    }

    public function testCanFindUserByEmail()
    {
        /**
         * اختبار البحث عن مستخدم بالبريد الإلكتروني
         */
        $userModel = new User();
        
        // إنشاء مستخدم تجريبي
        $userData = [
            'username'      => 'testuser2',
            'email'         => 'test2@example.com',
            'password_hash' => 'testpassword123',
            'first_name'    => 'Test2',
            'last_name'     => 'User2',
            'is_active'     => 1,
        ];
        
        $userModel->insert($userData);
        
        // البحث بالبريد الإلكتروني
        $foundUser = $userModel->findByEmail('test2@example.com');
        
        $this->assertNotNull($foundUser);
        $this->assertEquals('testuser2', $foundUser['username']);
        $this->assertEquals('test2@example.com', $foundUser['email']);
    }

    public function testCanVerifyPassword()
    {
        /**
         * اختبار التحقق من كلمة المرور
         */
        $userModel = new User();
        
        $password = 'mySecretPassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertTrue($userModel->verifyPassword($password, $hash));
        $this->assertFalse($userModel->verifyPassword('wrongPassword', $hash));
    }

    public function testValidationRules()
    {
        /**
         * اختبار قواعد التحقق من صحة البيانات
         */
        $userModel = new User();
        
        // بيانات غير صالحة (بريد إلكتروني غير صالح)
        $invalidData = [
            'username'      => 'test',
            'email'         => 'invalid-email',
            'password_hash' => '123', // قصير جداً
            'first_name'    => '',
            'last_name'     => '',
        ];
        
        $result = $userModel->insert($invalidData);
        
        // يجب أن يفشل الإدراج
        $this->assertFalse($result);
        
        // التحقق من وجود أخطاء
        $errors = $userModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testUniqueConstraints()
    {
        /**
         * اختبار قيود الفرادة (unique constraints)
         */
        $userModel = new User();
        
        $userData = [
            'username'      => 'uniquetest',
            'email'         => 'unique@example.com',
            'password_hash' => 'testpassword123',
            'first_name'    => 'Unique',
            'last_name'     => 'Test',
            'is_active'     => 1,
        ];
        
        // إنشاء المستخدم الأول
        $userId1 = $userModel->insert($userData);
        $this->assertIsNumeric($userId1);
        
        // محاولة إنشاء مستخدم آخر بنفس البريد الإلكتروني
        $userData['username'] = 'uniquetest2'; // تغيير اسم المستخدم فقط
        $result = $userModel->insert($userData);
        
        // يجب أن يفشل بسبب تكرار البريد الإلكتروني
        $this->assertFalse($result);
    }
}
