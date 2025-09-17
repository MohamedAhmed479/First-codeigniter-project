# مشروع مدونة CodeIgniter 4

مشروع تعليمي شامل لتطوير مدونة باستخدام CodeIgniter 4، مصمم خصيصاً للمطورين القادمين من Laravel لتعلم الاختلافات والتشابهات بين الإطارين.

## 🎯 الهدف من المشروع

هذا المشروع يهدف إلى:
- تعليم أساسيات CodeIgniter 4 للمطورين المتمرسين في Laravel
- إظهار الاختلافات العملية بين CodeIgniter و Laravel
- بناء تطبيق مدونة كامل وقابل للاستخدام
- تطبيق أفضل الممارسات في كلا الإطارين

## 🚀 الميزات

### ✅ المميزات المنجزة
- **نظام المصادقة الكامل**: تسجيل، دخول، خروج
- **إدارة المقالات**: إنشاء، تعديل، حذف، نشر
- **تصنيف المقالات**: فئات منظمة
- **البحث والتصفية**: بحث في المحتوى والعناوين
- **واجهة مستخدم حديثة**: Bootstrap 5 RTL
- **حماية CSRF**: حماية من الهجمات
- **Validation**: التحقق من صحة البيانات
- **Soft Delete**: حذف آمن للبيانات
- **Pagination**: تقسيم النتائج

### 🔄 الميزات المخطط لها
- نظام التعليقات
- رفع الصور
- نظام الصلاحيات المتقدم
- API RESTful
- اختبارات شاملة

## 📋 المتطلبات

- **PHP**: 8.1 أو أحدث
- **Composer**: لإدارة التبعيات
- **قاعدة البيانات**: MySQL 5.7+ أو MariaDB 10.3+
- **خادم ويب**: Apache أو Nginx (أو PHP Built-in Server للتطوير)

## 🛠️ التثبيت والإعداد

### 1. تحضير البيئة

```bash
# التأكد من إصدار PHP
php -v

# التأكد من تثبيت Composer
composer --version

# التأكد من تثبيت Git
git --version
```

### 2. استنساخ المشروع

```bash
# استنساخ المشروع
git clone <repository-url> codeigniter-blog
cd codeigniter-blog

# تثبيت التبعيات
composer install
```

### 3. إعداد قاعدة البيانات

#### أ. إنشاء قاعدة البيانات
```sql
-- في phpMyAdmin أو MySQL CLI
CREATE DATABASE ci4_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

أو استخدم ملف `database_setup.sql` المرفق:
```bash
# في XAMPP/phpMyAdmin، استورد ملف database_setup.sql
```

#### ب. إعداد ملف البيئة
```bash
# نسخ ملف البيئة
cp env .env
```

تحرير `.env` وتحديث إعدادات قاعدة البيانات:
```ini
# بيئة التطوير
CI_ENVIRONMENT = development

# إعدادات التطبيق
app.baseURL = 'http://localhost:8080/'
app.forceGlobalSecureRequests = false
app.CSPEnabled = true

# إعدادات قاعدة البيانات
database.default.hostname = localhost
database.default.database = ci4_blog
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

# مفتاح التشفير (مهم جداً!)
encryption.key = hex2bin:1a2b3c4d5e6f708192a3b4c5d6e7f80910203040506070809101112131415161
```

### 4. إنشاء الجداول

```bash
# تشغيل migrations
php spark migrate

# ملء قاعدة البيانات ببيانات تجريبية
php spark db:seed DatabaseSeeder
```

### 5. تشغيل الخادم

```bash
# تشغيل خادم التطوير المدمج
php spark serve

# أو تحديد منفذ مخصص
php spark serve --port 8080
```

الآن يمكنك الوصول للتطبيق عبر: `http://localhost:8080`

## 🔐 بيانات الدخول التجريبية

بعد تشغيل Seeders، يمكنك استخدام:

| المستخدم | البريد الإلكتروني | كلمة المرور | الدور |
|---------|-----------------|------------|-------|
| أحمد محمد | admin@example.com | password123 | إداري |
| فاطمة علي | writer1@example.com | password123 | كاتب |
| محمد عبدالله | writer2@example.com | password123 | كاتب |

## 📁 هيكل المشروع

```
codeigniter-blog/
├── app/
│   ├── Controllers/          # Controllers (مشابه لـ Laravel)
│   │   ├── Auth.php         # المصادقة
│   │   ├── Blog.php         # المدونة العامة
│   │   └── Admin.php        # لوحة الإدارة
│   ├── Models/              # Models (مختلف عن Eloquent)
│   │   ├── User.php
│   │   ├── Post.php
│   │   └── Category.php
│   ├── Views/               # Views (مشابه لـ Blade)
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── blog/
│   │   └── admin/
│   ├── Database/
│   │   ├── Migrations/      # Migrations (مشابه لـ Laravel)
│   │   └── Seeds/           # Seeders
│   └── Config/
│       ├── Routes.php       # التوجيه (مختلف عن web.php)
│       └── Database.php     # إعدادات قاعدة البيانات
├── public/                  # الملفات العامة
├── writable/               # ملفات الكتابة (logs, cache, etc.)
└── vendor/                 # تبعيات Composer
```

## 🔄 مقارنة مع Laravel

### التشابهات
- **MVC Pattern**: نفس النمط المعماري
- **Migrations**: نفس المفهوم لإدارة قاعدة البيانات
- **Routing**: مفهوم مشابه للتوجيه
- **Validation**: التحقق من صحة البيانات

### الاختلافات الرئيسية

| الجانب | Laravel | CodeIgniter 4 |
|-------|---------|---------------|
| **ORM** | Eloquent (Active Record) | Model (Query Builder) |
| **DI** | حقن تلقائي للتبعيات | تحميل يدوي |
| **Views** | Blade Templates | PHP العادي |
| **Config** | ملفات .env متقدمة | ملفات PHP + .env |
| **Middleware** | Middleware | Filters |
| **Commands** | Artisan | Spark |

### أمثلة عملية

#### 1. تعريف Route
```php
// Laravel
Route::get('/posts/{slug}', [PostController::class, 'show']);

// CodeIgniter 4
$routes->get('posts/(:segment)', 'Blog::show/$1');
```

#### 2. Model Query
```php
// Laravel Eloquent
$posts = Post::where('status', 'published')
             ->with('user', 'category')
             ->paginate(10);

// CodeIgniter 4
$posts = $this->postModel
              ->select('posts.*, users.name, categories.name as category')
              ->join('users', 'users.id = posts.user_id')
              ->join('categories', 'categories.id = posts.category_id')
              ->where('status', 'published')
              ->paginate(10);
```

#### 3. View Rendering
```php
// Laravel
return view('posts.index', compact('posts'));

// CodeIgniter 4
return view('posts/index', ['posts' => $posts]);
```

## 🧪 الاختبارات

```bash
# تشغيل جميع الاختبارات
php spark test

# تشغيل اختبارات محددة
php spark test --group authentication
```

## 🔧 أوامر مفيدة

```bash
# إنشاء controller جديد
php spark make:controller PostController

# إنشاء model جديد
php spark make:model Post

# إنشاء migration جديد
php spark make:migration CreatePostsTable

# إنشاء seeder جديد
php spark make:seeder PostSeeder

# مسح الـ cache
php spark cache:clear

# عرض قائمة Routes
php spark routes
```

## 📚 الموارد التعليمية

### CodeIgniter 4
- [الدليل الرسمي](https://codeigniter.com/user_guide/)
- [CodeIgniter 4 API](https://codeigniter4.github.io/api/)

### مقارنات مفيدة
- [Laravel vs CodeIgniter](https://blog.back4app.com/laravel-vs-codeigniter/)
- [Migration Guide](https://codeigniter.com/user_guide/installation/upgrade_4xx.html)

## 🤝 المساهمة

1. Fork المشروع
2. إنشاء feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit التغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push للـ branch (`git push origin feature/AmazingFeature`)
5. فتح Pull Request

## 📝 الترخيص

هذا المشروع مرخص تحت رخصة MIT - راجع ملف [LICENSE](LICENSE) للتفاصيل.

## 🐛 الإبلاغ عن المشاكل

إذا واجهت أي مشاكل، يرجى:
1. التأكد من المتطلبات
2. مراجعة هذا الدليل
3. البحث في Issues الموجودة
4. إنشاء Issue جديد مع التفاصيل الكاملة

## 📞 الدعم

- **التوثيق**: راجع هذا الملف أولاً
- **Issues**: للمشاكل التقنية
- **Discussions**: للأسئلة العامة

---

**ملاحظة**: هذا مشروع تعليمي مصمم لإظهار الاختلافات بين CodeIgniter 4 و Laravel. للاستخدام في بيئة الإنتاج، يرجى مراجعة إعدادات الأمان والأداء.