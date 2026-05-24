I am working on **FATI-PROJET**, a Laravel 11 course management platform.

## Project Context

This is the official project documentation:

```
# CLAUDE.md — FATI-PROJET

## 🎯 Description du projet
Application Laravel de gestion de cours, leçons et quizzes pour une plateforme éducative multi-niveaux.
Système complet de gestion d'écoles, branches, groupes d'étudiants et parcours d'apprentissage.

**Framework:** Laravel 11+ (PHP 8.2)
**Frontend:** Tailwind CSS, Alpine.js, Vite
**Base de données:** SQLite (dev) / MySQL (production)
**Authentification:** Laravel Breeze (avec rôles: admin, student)

## 📁 Structure importante
```
app/
  ├─ Models/              → 15 modèles Eloquent
  ├─ Http/Controllers/
  │  ├─ Admin/           → 12 contrôleurs Resource
  │  ├─ StudentController
  │  ├─ MessageController
  │  ├─ ProfileController
  │  └─ InstallController
  └─ Requests/           → Form Requests pour validation
database/
  ├─ migrations/         → 18 migrations
  └─ seeders/            → AdminUserSeeder et autres
routes/
  ├─ web.php             → Routes principales
  └─ auth.php            → Routes d'authentification
```

## ⚙️ Conventions de code

### Nommage
- **Contrôleurs:** PascalCase + "Controller" (ex: CourseController, Admin\QuizController)
- **Modèles:** Singulier PascalCase (ex: Course, Quiz, StudentProgress)
- **Tables:** Pluriel snake_case (ex: courses, quiz_retakes, lesson_user)
- **Colonnes:** snake_case (ex: massar_code, is_active, passing_score)
- **Routes:** kebab-case pour les URIs (ex: /admin/academic_years, /student/lessons/{id})

### Patterns utilisés
- **Resource Controllers:** index, create, store, show, edit, update, destroy
- **Validation:** Form Requests (dans app/Http/Requests/)
- **Relationships:** Utilisation complète d'Eloquent ORM
- **Middlewares:** auth pour les routes protégées
- **Migrations:** Noms horodatés, foreignId() et cascadeOnDelete()

### Points d'attention
- Pas de jQuery (Vanilla JS ou Alpine.js uniquement)
- Pas de sessions manuelles (utiliser auth()->user())
- Utiliser les Form Requests pour valider les données
- Les quizzes supportent les retakes partielles
- Les courses sont liés aux groups via pivot
- Les lessons sont liés aux courses (avec un ordre)

## 🗄️ Database Schema

See: .claude/context/database-schema.md
See: .claude/context/api-routes.md
See: .claude/context/conventions.md

```

## Feature Request

**Feature Name:** Backup simpliest lesson for students who did not succesed the quiz 


**Description:** The student who start a lesson'part then pass the quiz but got under the score , means he did not understand the lessons that means , the system show him more simple lesson then 

**Acceptance Criteria:**
- Admin view wher ehe create lesson , quiz ; he must now be able to create other lesson types for the same part of lesson let add some column of type=Normal/simpliest
he can see both , edit ,create delete 
- Make sure the business logic of lesson and quiz is valid in Admin side and also in student side


**Affected Areas:**
- [Model 1 or new model?]
- [Controller area?]
- [Routes affected?]
- [UI/Views?]

**User Stories:**
As a [user role], I want to [action], so that [benefit].

---

Please create:

1. **Migration** (if new data structure)
   - File: `database/migrations/YYYY_MM_DD_HHMMSS_[description].php`
   - What it should do: [describe the schema changes]

2. **Model** (if new entity)
   - File: `app/Models/[ModelName].php`
   - Relationships: [list relationships to other models]
   - Fillable fields: [list mass-assignable fields]

3. **Form Request** (for validation)
   - File: `app/Http/Requests/[Store|Update][Feature]Request.php`
   - Rules: [describe validation rules]

4. **Controller**
   - File: `app/Http/Controllers/Admin/[FeatureController].php`
   - Methods: [list required methods]
   - Custom methods: [describe any custom logic]

5. **Routes**
   - Add to: `routes/web.php`
   - Routes needed: [list all routes with methods]

6. **Views** (Blade templates)
   - Files needed: [list view files]
   - What they show: [describe each view]

Please follow all conventions in CLAUDE.md and the project structure.

---

## Notes

- Use resource routing pattern where applicable
- Include proper error handling and validation
- Add user feedback (success/error messages)
- Follow existing code style
- Use Eloquent relationships properly
- Add timestamps (created_at, updated_at) where appropriate
- Consider soft deletes if data shouldn't be permanently deleted