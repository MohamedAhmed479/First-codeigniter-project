<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class AuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم تجريبي للاختبارات
        $this->db->table('users')->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name'    => 'Test',
            'last_name'     => 'User',
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function testCanAccessLoginPage()
    {
        /**
         * اختبار الوصول لصفحة تسجيل الدخول
         * في CodeIgniter 4، نستخدم FeatureTestTrait للاختبارات الوظيفية
         * هذا مشابه لـ Feature Tests في Laravel
         */
        $result = $this->get('/auth/login');
        
        $result->assertStatus(200);
        $result->assertSee('تسجيل الدخول');
        $result->assertSee('البريد الإلكتروني');
        $result->assertSee('كلمة المرور');
    }

    public function testCanAccessRegisterPage()
    {
        /**
         * اختبار الوصول لصفحة التسجيل
         */
        $result = $this->get('/auth/register');
        
        $result->assertStatus(200);
        $result->assertSee('إنشاء حساب جديد');
        $result->assertSee('اسم المستخدم');
        $result->assertSee('البريد الإلكتروني');
    }

    public function testCanLoginWithValidCredentials()
    {
        /**
         * اختبار تسجيل الدخول ببيانات صحيحة
         */
        $result = $this->post('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);
        
        // يجب إعادة التوجيه بعد تسجيل الدخول الناجح
        $result->assertRedirectTo('/');
        
        // التحقق من إنشاء الجلسة
        $this->assertTrue($this->session->has('is_logged_in'));
        $this->assertEquals('testuser', $this->session->get('username'));
    }

    public function testCannotLoginWithInvalidCredentials()
    {
        /**
         * اختبار منع تسجيل الدخول ببيانات خاطئة
         */
        $result = $this->post('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        
        // يجب العودة لصفحة تسجيل الدخول مع رسالة خطأ
        $result->assertRedirectTo('/auth/login');
        
        // التحقق من عدم إنشاء جلسة
        $this->assertFalse($this->session->has('is_logged_in'));
    }

    public function testCanRegisterNewUser()
    {
        /**
         * اختبار تسجيل مستخدم جديد
         */
        $userData = [
            'username'         => 'newuser',
            'email'           => 'newuser@example.com',
            'password'        => 'newpassword123',
            'password_confirm'=> 'newpassword123',
            'first_name'      => 'New',
            'last_name'       => 'User',
        ];
        
        $result = $this->post('/auth/register', $userData);
        
        // يجب إعادة التوجيه لصفحة تسجيل الدخول
        $result->assertRedirectTo('/auth/login');
        
        // التحقق من حفظ المستخدم في قاعدة البيانات
        $this->seeInDatabase('users', [
            'username' => 'newuser',
            'email'    => 'newuser@example.com',
        ]);
    }

    public function testCannotRegisterWithExistingEmail()
    {
        /**
         * اختبار منع التسجيل ببريد إلكتروني موجود مسبقاً
         */
        $userData = [
            'username'         => 'anotheruser',
            'email'           => 'test@example.com', // بريد موجود مسبقاً
            'password'        => 'password123',
            'password_confirm'=> 'password123',
            'first_name'      => 'Another',
            'last_name'       => 'User',
        ];
        
        $result = $this->post('/auth/register', $userData);
        
        // يجب العودة لصفحة التسجيل مع أخطاء
        $result->assertRedirectTo('/auth/register');
        
        // التحقق من عدم حفظ المستخدم الجديد
        $this->dontSeeInDatabase('users', [
            'username' => 'anotheruser',
        ]);
    }

    public function testCanLogout()
    {
        /**
         * اختبار تسجيل الخروج
         */
        // تسجيل الدخول أولاً
        $this->session->set([
            'user_id'      => 1,
            'username'     => 'testuser',
            'email'        => 'test@example.com',
            'is_logged_in' => true,
        ]);
        
        $result = $this->get('/auth/logout');
        
        // يجب إعادة التوجيه للصفحة الرئيسية
        $result->assertRedirectTo('/');
        
        // التحقق من إزالة بيانات الجلسة
        $this->assertFalse($this->session->has('is_logged_in'));
        $this->assertFalse($this->session->has('user_id'));
    }

    public function testValidationErrors()
    {
        /**
         * اختبار أخطاء التحقق من صحة البيانات
         */
        $result = $this->post('/auth/login', [
            'email'    => 'invalid-email', // بريد غير صالح
            'password' => '123',           // كلمة مرور قصيرة
        ]);
        
        $result->assertRedirectTo('/auth/login');
        
        // التحقق من وجود أخطاء في الجلسة
        $this->assertTrue($this->session->has('errors'));
    }

    public function testRedirectIfAlreadyLoggedIn()
    {
        /**
         * اختبار إعادة التوجيه إذا كان المستخدم مسجل دخول مسبقاً
         */
        // تسجيل الدخول
        $this->session->set([
            'user_id'      => 1,
            'is_logged_in' => true,
        ]);
        
        // محاولة الوصول لصفحة تسجيل الدخول
        $result = $this->get('/auth/login');
        
        // يجب إعادة التوجيه للصفحة الرئيسية
        $result->assertRedirectTo('/');
    }
}
