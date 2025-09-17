# إعداد Git Repository للمشروع

## 🚀 الخطوات الأولى

### 1. تهيئة Repository

```bash
# تهيئة git repository
git init

# إضافة ملف .gitignore
echo "# CodeIgniter 4 Blog Project .gitignore

# CodeIgniter 4 specific
/writable/logs/*
!/writable/logs/index.html
/writable/cache/*
!/writable/cache/index.html
/writable/session/*
!/writable/session/index.html
/writable/uploads/*
!/writable/uploads/index.html
/writable/debugbar/*
!/writable/debugbar/index.html

# Environment files
.env
.env.*
!env

# IDE files
.vscode/
.idea/
*.sublime-project
*.sublime-workspace

# OS files
.DS_Store
Thumbs.db

# Composer
/vendor/

# Node.js (if using)
node_modules/
npm-debug.log

# Build files
/builds/

# Temporary files
*.tmp
*.temp
*.log

# Database
*.sqlite
*.db

# PHPUnit
.phpunit.result.cache
/phpunit.xml

# Coverage reports
/coverage/

# Backup files
*.backup
*.bak
*.swp
*~" > .gitignore

# إضافة جميع الملفات للمرحلة
git add .

# أول commit
git commit -m "🎉 Initial commit: CodeIgniter 4 Blog Project setup

- Project structure initialized
- Environment configuration
- Database migrations and seeders
- Authentication system
- Blog CRUD functionality
- Admin panel
- Bootstrap 5 RTL UI
- Comprehensive documentation"
```

### 2. إعداد Remote Repository

```bash
# إضافة remote repository (استبدل URL بالخاص بك)
git remote add origin https://github.com/yourusername/codeigniter-blog.git

# أو للـ SSH
git remote add origin git@github.com:yourusername/codeigniter-blog.git

# push للـ main branch
git branch -M main
git push -u origin main
```

## 📝 مقترحات Commits للتطوير المستقبلي

### إضافة ميزات جديدة

```bash
# ميزة التعليقات
git add .
git commit -m "✨ Add comments system

- Comment model and migration
- Comment CRUD operations
- Nested comments support
- Comment moderation
- Email notifications"

# نظام رفع الصور
git add .
git commit -m "📸 Add image upload functionality

- Image upload validation
- Resize and optimization
- Multiple image support
- Gallery management
- Image compression"

# API RESTful
git add .
git commit -m "🔌 Add REST API endpoints

- API authentication (JWT)
- Posts API endpoints
- Users API endpoints
- API documentation
- Rate limiting"

# تحسينات الأمان
git add .
git commit -m "🔒 Enhance security features

- CSRF protection improvements
- XSS prevention
- SQL injection protection
- Input sanitization
- Security headers"

# تحسينات الأداء
git add .
git commit -m "⚡ Performance optimizations

- Database query optimization
- Caching implementation
- Image lazy loading
- CSS/JS minification
- Database indexing"
```

### إصلاح الأخطاء

```bash
# إصلاح bug في المصادقة
git add .
git commit -m "🐛 Fix authentication redirect issue

- Fix redirect loop in auth middleware
- Improve session handling
- Add proper error messages"

# إصلاح مشاكل UI
git add .
git commit -m "💄 Fix UI/UX issues

- Fix responsive layout on mobile
- Improve form validation display
- Fix RTL text alignment
- Update color scheme"
```

### التحديثات والصيانة

```bash
# تحديث التبعيات
git add .
git commit -m "⬆️ Update dependencies

- Update CodeIgniter to v4.x.x
- Update Bootstrap to v5.x.x
- Security patches
- Bug fixes"

# تحسين الكود
git add .
git commit -m "♻️ Code refactoring

- Improve code structure
- Add type hints
- Remove duplicate code
- Improve naming conventions"

# إضافة اختبارات
git add .
git commit -m "✅ Add comprehensive tests

- Unit tests for all models
- Feature tests for authentication
- Integration tests for API
- Test coverage improvements"
```

### التوثيق

```bash
# تحديث التوثيق
git add .
git commit -m "📚 Update documentation

- API documentation
- Installation guide improvements
- Code comments
- Contributing guidelines"

# إضافة أمثلة
git add .
git commit -m "📖 Add code examples

- Usage examples
- Best practices guide
- Common patterns
- Troubleshooting guide"
```

## 🏷️ استراتيجية Tagging

```bash
# إصدار أولي
git tag -a v1.0.0 -m "🎉 First stable release

Features:
- Complete authentication system
- Blog post management
- Admin panel
- Responsive UI
- Basic security features"

git push origin v1.0.0

# إصدارات لاحقة
git tag -a v1.1.0 -m "✨ Feature update

New features:
- Comments system
- Image upload
- Enhanced search
- Performance improvements"

git push origin v1.1.0
```

## 🌿 Branching Strategy

```bash
# إنشاء branch للتطوير
git checkout -b develop

# إنشاء feature branch
git checkout -b feature/comments-system

# العمل على الميزة...
git add .
git commit -m "✨ Add comments functionality"

# العودة للـ develop والدمج
git checkout develop
git merge feature/comments-system

# حذف الـ feature branch
git branch -d feature/comments-system

# إنشاء release branch
git checkout -b release/v1.1.0

# إجراء اختبارات نهائية وإصلاحات...
git add .
git commit -m "🔧 Prepare v1.1.0 release"

# الدمج في main
git checkout main
git merge release/v1.1.0

# إنشاء tag
git tag -a v1.1.0 -m "Release v1.1.0"

# Push للـ remote
git push origin main
git push origin develop
git push origin v1.1.0
```

## 📋 Git Hooks المفيدة

### Pre-commit Hook

```bash
# إنشاء pre-commit hook
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/sh

# تشغيل PHP CS Fixer
if command -v php-cs-fixer > /dev/null 2>&1; then
    php-cs-fixer fix --dry-run --diff
    if [ $? != 0 ]; then
        echo "❌ Code style issues found. Please run php-cs-fixer fix"
        exit 1
    fi
fi

# تشغيل الاختبارات
if [ -f "vendor/bin/phpunit" ]; then
    php spark test
    if [ $? != 0 ]; then
        echo "❌ Tests failed. Please fix before committing."
        exit 1
    fi
fi

echo "✅ All checks passed!"
EOF

chmod +x .git/hooks/pre-commit
```

## 🔄 Workflow المقترح

1. **Feature Development**:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/new-feature
   # تطوير الميزة
   git add .
   git commit -m "✨ Add new feature"
   git push origin feature/new-feature
   # إنشاء Pull Request
   ```

2. **Bug Fixes**:
   ```bash
   git checkout main
   git pull origin main
   git checkout -b hotfix/fix-critical-bug
   # إصلاح المشكلة
   git add .
   git commit -m "🐛 Fix critical bug"
   git push origin hotfix/fix-critical-bug
   # إنشاء Pull Request للـ main
   ```

3. **Release Process**:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b release/v1.x.x
   # اختبارات نهائية وتحضير الإصدار
   git add .
   git commit -m "🔖 Prepare v1.x.x release"
   git checkout main
   git merge release/v1.x.x
   git tag -a v1.x.x -m "Release v1.x.x"
   git push origin main
   git push origin v1.x.x
   ```

## 📊 مراجعة التقدم

```bash
# عرض تاريخ الـ commits
git log --oneline --graph --decorate --all

# عرض الإحصائيات
git shortlog -sn

# عرض التغييرات بين الإصدارات
git diff v1.0.0..v1.1.0 --stat

# عرض المساهمين
git shortlog -sn --all
```

هذا الدليل يوفر استراتيجية شاملة لإدارة المشروع باستخدام Git مع رسائل commit واضحة ومنظمة.
