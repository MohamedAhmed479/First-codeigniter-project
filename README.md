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

## 📖 دليل عملي موسّع: من Laravel إلى CodeIgniter 4

> هذا القسم يقدم شرحًا عميقًا ومكثفًا لأهم الفروق العملية بين Laravel وCodeIgniter 4، مع أمثلة جانبية لكل مفهوم لتسهيل الانتقال بين الإطارين.

### 1) التوجيه (Routing) وطبقة المرور

- في Laravel: التوجيه في `routes/web.php` و`routes/api.php`، مع Middleware كطبقة مرور.
- في CodeIgniter 4: التوجيه في `app/Config/Routes.php`، وFilters تقوم بدور مشابه للـ Middleware.

أمثلة:

```php
// Laravel (routes/api.php)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/posts/my-posts', [PostController::class, 'myPosts']);
});

// CodeIgniter 4 (app/Config/Routes.php)
$routes->group('api', function($routes) {
    $routes->group('posts', function($routes) {
        $routes->group('', ['filter' => 'token-auth'], function($routes) {
            $routes->get('my-posts', 'PostsController::myPosts');
        });
    });
});
```

نصيحة: في CodeIgniter 4 احرص على ترتيب الراوتات الأكثر تحديدًا قبل الراوتات العامة مثل `(:segment)` حتى لا تتصادم.

### 2) الكنترولر (Controller) وهيكل الطبقات

- Laravel يعتمد على حقن التبعيات (DI) تلقائيًا في الدوال.
- CodeIgniter 4 يستخدم تحميلًا يدويًا عادةً عبر الـ constructor أو `Services`.

```php
// Laravel
class PostController extends Controller {
    public function __construct(private PostService $service) {}
    public function index() { return PostResource::collection(Post::paginate()); }
}

// CodeIgniter 4
class PostsController extends BaseApiController {
    protected $postModel;
    public function __construct() { $this->postModel = new \App\Models\Post(); }
    public function index() { return $this->respondWithPagination($this->postModel->paginate(10), $this->postModel->pager); }
}
```

### 3) الموديل (Model) وطبقة البيانات

- Laravel: Eloquent ORM (Active Record متقدم) مع علاقات ودوال Scope.
- CodeIgniter 4: Model + Query Builder. يدعم `allowedFields`, `validationRules`, callbacks.

```php
// Laravel Eloquent
class Post extends Model { protected $fillable = ['title','slug','content','status','category_id']; }

// CodeIgniter 4 Model
class Post extends Model {
    protected $allowedFields = ['title','slug','content','status','category_id','user_id','published_at'];
    protected $validationRules = [ 'title' => 'required|min_length[5]' /* ... */ ];
}
```

نقطة مهمة: في CI4 لو استخدمت placeholders مثل `{id}` في `is_unique`، عرّف قاعدة للحقل `id` وأضِفه إلى `allowedFields` أثناء التحديث.

### 4) التحقق من المدخلات (Validation)

- Laravel: Requests مخصصة (FormRequest) ورسائل خطأ عبر `messages()`.
- CodeIgniter 4: قواعد في الـ Model أو داخل الـ Controller عبر `$this->validate()`.

```php
// Laravel FormRequest
public function rules() { return ['title' => 'required|min:5']; }

// CodeIgniter 4 Controller
$rules = ['title' => 'required|min_length[5]'];
if (! $this->validate($rules)) { return $this->respondWithValidationError($this->validator->getErrors()); }
```

Placeholders في CI4 مثل `is_unique[table.field,id,{id}]` تتطلب وجود حقل `id` في قواعد التحقق.

### 5) الفلاتر مقابل الميدلوير (Filters vs Middleware)

- Laravel Middleware تسجَّل في kernel وتُطبّق على المجموعات.
- CI4 Filters تُعرف في `app/Config/Filters.php` وتُستدعى في الراوت عبر `['filter' => 'token-auth']`.

```php
// CodeIgniter 4 (app/Filters/TokenAuthFilter.php)
public function before(RequestInterface $request, $arguments = null) {
    // تحقق من التوكن واجلب المستخدم
}
```

### 6) الطلب والاستجابة (Request/Response)

- Laravel: `request()->input()`, `response()->json()`.
- CI4: `$this->request->getVar()`, `$this->request->getJSON(true)`, والردود عبر `ResponseTrait`.

```php
// CodeIgniter 4
$data = $this->request->getJSON(true) ?? $this->request->getVar();
return $this->respond(['status' => true, 'data' => $data]);
```

### 7) المايجريشن والسيـدر (Migrations & Seeders)

- متشابهة بالمفهوم؛ الأوامر في Laravel عبر Artisan وفي CI4 عبر Spark.

```bash
# Laravel
php artisan migrate --seed

# CodeIgniter 4
php spark migrate
php spark db:seed DatabaseSeeder
```

### 8) الخدمات وDI (Services & DI)

- Laravel Container يحقن تلقائيًا.
- CI4 يقدم `Services` في `app/Config/Services.php` ويمكنك إنشاء مكتبات ضمن `app/Libraries` ثم حقنها يدويًا.

```php
// CodeIgniter 4
$this->tokenService = new \App\Libraries\TokenService();
```

### 9) واجهة العرض (Views)

- Laravel Blade بميزات قوالب قوية.
- CI4 يستخدم PHP Views بسيطة؛ يمكن إضافة Twig/Plates إذا رغبت.

### 10) أوامر CLI

- Laravel: Artisan.
- CI4: Spark. أمثلة في هذا المشروع موجودة داخل قسم "أوامر مفيدة".

### 11) الاختبارات (Testing)

- Laravel: PHPUnit + Helpers مدمجة.
- CI4: PHPUnit كذلك، مع ملفات ضمن `tests/` واستخدام `php spark test`.

### 12) التعامل مع الأخطاء والاستثناءات

- Laravel يعرض صفحات خطأ مخصصة (Ignition).
- CI4 يعرض صفحات خطأ حسب البيئة، ويمكنك تخصيصها ضمن `app/Views/errors`.

### 13) نقاط أخرى سريعة

- الحماية: CSRF مفعّل في الحالتين. في CI4 تأكد من الضبط في `app/Config/Security.php`.
- الجلسات: CI4 يديرها عبر `app/Config/Session.php`.
- الكاش: خدمات عبر `Services::cache()` في CI4.
- الملفات والرفع: متشابهان بالمبدأ، مع اختلاف واجهات API.
- جدولة المهام/الصفوف: Laravel أقوى (Queues, Scheduler). في CI4 ستحتاج حزم خارجية أو كرون بسيط.

### 14) مثال كامل: إنشاء/تحديث منشور عبر API

- Laravel (تبسيط):
```php
Route::middleware('auth:sanctum')->post('/posts', [PostController::class, 'store']);
public function store(Request $request) {
    $data = $request->validate(['title' => 'required|min:5', 'content' => 'required|min:10']);
    $data['user_id'] = $request->user()->id;
    return Post::create($data);
}
```

- CodeIgniter 4:
```php
// Routes
$routes->group('api', ['namespace' => 'App\\Controllers\\Api'], function($routes) {
    $routes->group('posts', ['filter' => 'token-auth'], function($routes) {
        $routes->post('/', 'PostsController::create');
    });
});

// Controller (PostsController::create)
$rules = [ 'title' => 'required|min_length[5]', 'content' => 'required|min_length[10]' /* ... */ ];
if (! $this->validate($rules)) { return $this->respondWithValidationError($this->validator->getErrors()); }
$json = $this->request->getJSON(true);
$postData = [ 'title' => $json['title'] ?? $this->request->getVar('title'), /* ... */ 'user_id' => $user['id'] ];
$this->postModel->save($postData);
```

بهذا الدليل، ستتمكن من ترجمة معظم أنماط Laravel إلى ما يقابلها في CodeIgniter 4 بسرعة وبأقل مفاجآت.

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