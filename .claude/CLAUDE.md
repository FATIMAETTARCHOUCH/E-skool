# CLAUDE.md — FATI-PROJET

## 🎯 Description du projet
Application Laravel de gestion de cours, leçons et quizzes pour une plateforme éducative multi-niveaux.
Système complet de gestion d'écoles, branches, groupes d'étudiants et parcours d'apprentissage.

**Framework:** Laravel 11+ (PHP 8.2)
**Frontend:** Tailwind CSS, Alpine.js, Vite
**Base de données:** MySQL (dev & production)
**Authentification:** Laravel Breeze (avec rôles: admin, student)

## 📁 Structure importante
```
app/
  ├─ Models/              → 15 modèles Eloquent (User, Lesson, Quiz, Course, etc.)
  ├─ Http/Controllers/
  │  ├─ Admin/           → 12 contrôleurs Resource (School, Branch, Group, User, etc.)
  │  ├─ StudentController
  │  ├─ MessageController
  │  ├─ ProfileController
  │  └─ InstallController
  └─ Requests/           → Form Requests pour validation
database/
  ├─ migrations/         → 18 migrations (structure complète)
  └─ seeders/            → AdminUserSeeder et autres
routes/
  ├─ web.php             → Routes principales (auth, admin, student)
  └─ auth.php            → Routes d'authentification (Breeze)
resources/views/         → Vues Blade (student, admin, auth)
config/
  ├─ auth.php            → Config authentification
  ├─ database.php        → Config base de données
  └─ [autres configs]
```

## 🗄️ Base de données (18 tables)

### Tables principales
- **users** – Authentification (role, group_id, first_name, last_name, age, massar_code, username)
- **schools** – Écoles
- **branches** – Branches d'une école (school_id)
- **academic_years** – Années académiques (is_active)
- **groups** – Groupes d'étudiants (branch_id, academic_year_id, name)
- **courses** – Cours (title, description, category, level, teacher_id)
- **course_group** – Pivot: courses ↔ groups
- **lessons** – Leçons/parties de cours (course_id, order, title, content_text, pdf_path, video_path, image_path, tag)
- **lesson_group** – Pivot: lessons ↔ groups (legacy, en transition)
- **lesson_user** – Suivi complétions (lesson_id, user_id, completed_at)
- **quizzes** – Quiz (lesson_id, title, scheduled_at, is_active, passing_score)
- **questions** – Questions de quiz (quiz_id, content_text, image_path, video_path)
- **options** – Réponses possibles (question_id, content_text, is_correct)
- **answers** – Réponses soumises par étudiants (user_id, quiz_id, question_id, option_id)
- **results** – Résultats de quiz (user_id, quiz_id, score, total_questions, is_passed)
- **quiz_retakes** – Tentatives de relance (quiz_id, user_id)
- **messages** – Messagerie (sender_id, receiver_id, content, read_at)
- **student_progress** – Suivi de progression (user_id, lesson_id, is_completed, last_accessed_at)

## ⚙️ Conventions de code

### Nommage
- **Contrôleurs:** PascalCase + "Controller" (ex: `CourseController`, `Admin\QuizController`)
- **Modèles:** Singulier PascalCase (ex: `Course`, `Quiz`, `StudentProgress`)
- **Tables:** Pluriel snake_case (ex: `courses`, `quiz_retakes`, `lesson_user`)
- **Colonnes:** snake_case (ex: `massar_code`, `is_active`, `passing_score`)
- **Routes:** kebab-case pour les URIs (ex: `/admin/academic_years`, `/student/lessons/{id}`)
- **Vues:** Blade, dossiers organisés par rôle (ex: `resources/views/admin/courses/index.blade.php`)

### Patterns utilisés
- **Resource Controllers:** `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- **Validation:** Form Requests (dans `app/Http/Requests/`)
- **Relationships:** Utilisation complète d'Eloquent ORM
- **Middlewares:** `auth` pour les routes protégées, contrôle des rôles dans les contrôleurs
- **Migrations:** Noms horodatés, utilisation de `foreignId()` et `cascadeOnDelete()`
- **Seeders:** AdminUserSeeder pour initialiser l'admin

### Points d'attention
- Pas de jQuery (Vanilla JS ou Alpine.js uniquement)
- Pas de sessions manuelles (utiliser `auth()->user()`)
- Utiliser les Form Requests pour valider les données
- Les quizzes supportent les **retakes partielles** (seules les mauvaises réponses)
- Les **courses** sont liés aux **groups** via pivot
- Les **lessons** sont liés aux **courses** (avec un ordre)
- Les **students** peuvent accéder à des lessons seulement si le groupe y a accès

## ✅ Routes principales

### Routes d'authentification
```
GET  /login                           → LoginController
POST /login                           → LoginController
GET  /register                        → RegisterController
POST /register                        → RegisterController
POST /logout                          → LogoutController
```

### Routes publiques
```
GET  /                                → Welcome page ou install
GET  /install                         → InstallController@index
POST /install                         → InstallController@store
```

### Routes Student
```
GET  /dashboard                       → redirect vers student.dashboard ou admin.dashboard
GET  /student/dashboard               → StudentController@dashboard
GET  /student/courses/{id}            → StudentController@course
GET  /student/lessons/{id}            → StudentController@lesson
POST /student/lessons/{id}/complete   → StudentController@completeLesson
GET  /student/quizzes/{id}            → StudentController@quiz
POST /student/quizzes/{id}/submit     → StudentController@submitQuiz
GET  /student/analytics               → StudentController@analytics
```

### Routes Messages
```
GET  /messages                        → MessageController@index
GET  /messages/{id}                   → MessageController@show
POST /messages/{id}/send              → MessageController@store
```

### Routes Admin (préfixe: `/admin`, middleware: `auth`)
```
GET    /admin/dashboard               → AdminController@index
GET    /admin/analytics/students      → AnalyticsController@students
GET    /admin/analytics/students/{id} → AnalyticsController@studentProfile
POST   /admin/analytics/students/{id}/reset-quizzes → AnalyticsController@resetQuizzes
DELETE /admin/analytics/results/{id}  → AnalyticsController@deleteResult
POST   /admin/maintenance/toggle      → AdminController@toggleMaintenance

Resource Controllers (index/create/store/show/edit/update/destroy):
- /admin/schools
- /admin/branches
- /admin/academic_years (+ POST toggle)
- /admin/groups (+ POST import)
- /admin/users
- /admin/students
- /admin/courses
- /admin/lessons
- /admin/quizzes

Quiz Questions (nested):
GET  /admin/quizzes/{quiz}/questions       → QuestionController@index
POST /admin/quizzes/{quiz}/questions       → QuestionController@store
DELETE /admin/questions/{question}         → QuestionController@destroy
```

### Routes Profile
```
GET    /profile                       → ProfileController@edit (auth)
PATCH  /profile                       → ProfileController@update (auth)
DELETE /profile                       → ProfileController@destroy (auth)
```

## ✅ Commandes utiles

```bash
# Développement
php artisan serve                     # Lancer le serveur (http://localhost:8000)
npm run dev                           # Lancer Vite pour Tailwind + JS

# Base de données
php artisan migrate                   # Exécuter les migrations
php artisan migrate:fresh --seed      # Reset DB + seed
php artisan db:seed                   # Exécuter les seeders
php artisan tinker                    # REPL pour tester le code

# Installation initiale
php artisan key:generate              # Générer APP_KEY
php artisan storage:link              # Créer lien storage/app/public

# Utilitaires
php artisan make:model Model          # Créer un modèle
php artisan make:controller Admin/ModelController -r  # Créer un Resource Controller
php artisan make:request StoreModelRequest             # Créer une Form Request
php artisan make:migration create_table_table         # Créer une migration
```

## 🚫 Ce qu'on n'utilise PAS
- jQuery
- Sessions manuelles (utiliser l'auth Laravel)
- API REST séparée (tout est en web.php pour le moment)
- Packages non-essentiels

## 📦 Dépendances principales
- `laravel/framework: ^11.31`
- `laravel/tinker: ^2.9`
- `maatwebsite/excel: ^3.1` (pour import/export Excel)
- `laravel-lang/common: ^6.8` (pour traductions)
- `laravel/breeze: ^2.4` (pour scaffolding auth)
- Tailwind CSS, Alpine.js (via npm)

## 🌐 Variables d'environnement (.env)
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite  # ou mysql pour production
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```