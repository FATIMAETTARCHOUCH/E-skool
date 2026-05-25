# TODO Command & Context

## Context
### Core Models
- **Course**: Contains `Chapter`.
- **Chapter**: Replaces `Lesson`. Belongs to `Course`. Has many `ChapterContent`, `Quiz`, and `StudentProgress`.
- **ChapterContent**: Replaces `LessonContent`. Belongs to `Chapter`. Stores content like PDF, text, video, image.
- **ChapterVariant**: Replaces `LessonVariant`. Links `original_chapter_id` to `variant_chapter_id` for specific triggers (e.g., 'quiz_failed').
- **Quiz**: Belongs to `Chapter`.
- **StudentProgress**: Belongs to `Chapter`.

### Business Logic
- `ChapterProgressService` replaces `LessonProgressService`. It handles checking if a chapter is completed or if a variant chapter needs to be unlocked.
- **Goal**: Full replacement of the word `Lesson` with `Chapter` throughout the application.

## Tasks (TODO)
- [ ] 1. Delete deprecated models (`app/Models/Lesson.php`, `app/Models/LessonContent.php`, `app/Models/LessonVariant.php`) and services (`app/Services/LessonProgressService.php`).
- [ ] 2. Refactor Admin Controllers: rename `Admin/LessonController.php` to `Admin/ChapterController.php` and update model names & logic.
- [ ] 3. Refactor Student Controllers: rename `Student/LessonController.php` to `Student/ChapterController.php` and update logic.
- [ ] 4. Update routing (`routes/web.php`) to use `ChapterController` instead of `LessonController` (and update URI endpoints from `lessons` to `chapters`).
- [ ] 5. Rename View folders and files:
  - `resources/views/admin/lessons/` -> `resources/views/admin/chapters/`
  - `resources/views/student/lessons/` -> `resources/views/student/chapters/`
- [ ] 6. Update all View Blade files to use `$chapter` instead of `$lesson`, and change route calls from `*.lessons.*` to `*.chapters.*`.
- [ ] 7. Update other Controllers that reference `Lesson` (e.g., `CourseController.php`, `QuizController.php`, `AnalyticsController.php`, `StudentController.php`).
- [ ] 8. Update any component or navigation views that mention "Leçons" to "Chapitres".