# Fix Bug Template

Use this template when fixing bugs in FATI-PROJET.

## Instructions
1. Copy the entire template below
2. Fill in the placeholders with your bug details
3. Paste into a new Copilot chat
4. Copilot will help diagnose and fix the issue

## Template

---

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

## ⚙️ Conventions de code

### Nommage
- **Contrôleurs:** PascalCase + "Controller" (ex: CourseController, Admin\QuizController)
- **Modèles:** Singulier PascalCase (ex: Course, Quiz, StudentProgress)
- **Tables:** Pluriel snake_case (ex: courses, quiz_retakes, lesson_user)
- **Colonnes:** snake_case (ex: massar_code, is_active, passing_score)
- **Routes:** kebab-case pour les URIs

### Patterns utilisés
- **Resource Controllers:** index, create, store, show, edit, update, destroy
- **Validation:** Form Requests (dans app/Http/Requests/)
- **Relationships:** Utilisation complète d'Eloquent ORM
- **Middlewares:** auth pour les routes protégées

### Points d'attention
- Pas de jQuery (Vanilla JS ou Alpine.js uniquement)
- Pas de sessions manuelles (utiliser auth()->user())
- Utiliser les Form Requests pour valider les données

## 🗄️ Database Schema

See: .claude/context/database-schema.md
See: .claude/context/api-routes.md
See: .claude/context/conventions.md

```

## Bug Report

**Bug Title:** [SHORT DESCRIPTION OF THE BUG]

**Severity:** [Critical / High / Medium / Low]

**Environment:**
- OS: [Windows / Mac / Linux]
- PHP Version: [if known]
- Database: [SQLite / MySQL]
- Steps to reproduce: [step by step]

**Expected Behavior:**
[What should happen]

**Actual Behavior:**
[What actually happens]

**Error Message/Stack Trace:**
```
[Paste full error message or stack trace]
```

**Affected Component:**
- **File Path:** [path/to/file.php]
- **Method/Function:** [methodName or area of code]
- **Related Models:** [List any related models]
- **Routes Involved:** [List any affected routes]

**Current Code (if applicable):**
```php
[Paste the problematic code snippet]
```

**Screenshots/Logs:**
[Attach or describe any relevant logs or screenshots]

## Additional Context

**When did this start happening?**
[Recently / After specific change / Always]

**Does it happen consistently?**
[Always / Sometimes / Under specific conditions]

**Related Issues/Features:**
[Link to related features or previous bugs if any]

---

## What I Need

Please:

1. **Diagnose the issue**
   - Identify root cause
   - Explain what's wrong

2. **Fix the code**
   - Provide corrected code
   - Explain why it fixes the bug

3. **Test the fix**
   - Suggest test cases
   - Verify solution works

4. **Prevention**
   - Suggest how to prevent in future
   - Any validation improvements?

---

## Notes for Assistant

- Follow all conventions in CLAUDE.md
- Maintain existing code style
- Don't break other functionality
- Consider edge cases
- Add validation if needed
- Update error messages to be clear
- Consider performance impact
- Check for similar bugs elsewhere
