# Advanced Quiz & Learning Management Platform

This document outlines the technical implementation plan for building the Advanced Quiz & Learning Management Platform leveraging Laravel 11.x, MySQL, and a Neumorphic UI design via Tailwind CSS.

## User Review Required

> [!IMPORTANT]
> Please review the proposed database schema, tech stack decisions, and implementation phases below. Specifically, confirm if you are comfortable with using **Blade templates combined with Alpine.js/Vanilla Javascript** for dynamic frontend interactions (like the quiz), or if you prefer a different frontend approach (like React/Vue built into Laravel).

## Proposed Architecture and Stack

- **Framework**: Laravel (Latest - v11)
- **Database**: MySQL
- **Frontend**: Blade Templating Engine + Tailwind CSS (configured for Neumorphism) + Alpine.js/Vanilla JS for dynamic UI (such as Quiz timers and popups).
- **File Storage**: Laravel local/public storage.
- **Excel/CSV Imports**: `maatwebsite/excel` (Laravel Excel) package.

## Proposed Changes

### 1. Database Schema Design (Migrations & Models)

We will create the following Eloquent Models and their corresponding migrations:

- **Schools**: `id`, `name`, `created_at`, `updated_at`
- **Branches**: `id`, `school_id`, `name`
- **AcademicYears**: `id`, `name` (e.g., "2023/2024"), `is_active` (boolean)
- **Groups**: `id`, `branch_id`, `academic_year_id`, `name`
- **Users**: (Extended Laravel Default) `id`, `role` (enum: 'admin', 'student'), `group_id` (nullable), `first_name`, `last_name`, `age`, `massar_code` (unique), `username`, `password`, `is_first_login` (boolean)
- **Lessons**: `id`, `group_id`, `title`, `content_text`, `pdf_path`, `video_path`, `image_path`, `tag`
- **Quizzes**: `id`, `lesson_id`, `title`, `scheduled_at` (datetime), `is_active` (boolean)
- **QuizRetakes**: `id`, `quiz_id`, `user_id` (To track specific students allowed to retake)
- **Questions**: `id`, `quiz_id`, `content_text`, `image_path`, `video_path`
- **Options**: `id`, `question_id`, `content_text`, `is_correct` (boolean)
- **Results**: `id`, `user_id`, `quiz_id`, `score`, `total_questions`
- **Messages**: `id`, `sender_id`, `receiver_id`, `content`, `read_at`, `created_at`

---

### 2. Authentication & Middleware

- **Custom Setup Route**: `/install` - Accessible only if the `users` table is empty. Creates the first Admin user.
- **Middleware**:
  - `AdminMiddleware`: Restricts routes to users with the 'admin' role.
  - `StudentMiddleware`: Restricts learning area to 'student' role.
  - `MaintenanceMiddleware`: Checks a system setting (stored in cache or a simple settings table/file) and redirects students to a custom maintenance page.

---

### 3. Admin Panel (Teacher Dashboard)

- **UI/UX**: Neumorphic design for cards, buttons, and forms.
- **Core CRUD**: Interface to manage Schools, Branches, Groups, and Academic Years (with logic to ensure only one is active).
- **Student Management**: 
  - Interface to manually add students.
  - Bulk import feature using CSV/Excel. Password and username will default to the `massar_code` automatically.
- **Course & Quiz Builder**:
  - Form to upload PDFs, Videos (Laravel Storage), and rich text for lessons.
  - Dynamic Quiz Builder to add Questions and Options dynamically using Javascript.
  - Quiz settings for Scheduling and Manual Toggling.
- **Analytics View**:
  - Dashboards showing group averages and individual student progress (leveraging Eloquent aggregations).
- **System Toggles**: Maintenance mode button.

---

### 4. Student Interface

- **Login & Profile**: Simple Neumorphic login. Profile allows changing password and username only.
- **Learning Area**: 
  - Sidebar/Menu to navigate to assigned lessons.
  - Popup Notification for active quizzes checking.
- **Dynamic Quiz Engine**:
  - Single Page Application feel using Vanilla JS or Alpine.js fetching questions via AJAX or pre-loaded on page.
  - Next/Previous button logic.
  - Submitting results updates the backend and displays the final score.
  - Auto-Recommendation logic if score < 50% pointing back to the Lesson's tag.
- **Progress Tracking**: Personal dashboard pulling from the `Results` table.

---

### 5. Communication System (Direct Messaging)

- Simple inbox-style view for students to message the admin and vice versa.

---

### 6. Tailwind CSS & Neumorphism

- Configure `tailwind.config.js` to add custom box-shadows for the soft UI effect:
  ```js
  theme: {
    extend: {
      boxShadow: {
        'neumorphic': '20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff',
        'neumorphic-inset': 'inset 20px 20px 60px #d9d9d9, inset -20px -20px 60px #ffffff',
      },
      backgroundColor: {
        'base': '#e0e0e0', // Typical neumorphic background base
      }
    }
  }
  ```

## Open Questions

> [!WARNING]
> 1. Do you have a preferred Rich Text Editor for the Admin Course Creator? (e.g., Quill, TinyMCE, or just a simple basic text area for now)?
> 2. For the Excel import, should we rely on CSV format initially to keep things lightweight, or do you strictly need `.xlsx` support right out of the gate?
> 3. Does the server running this have any limitations on upload sizes for the videos/PDFs? We may need to configure `php.ini` limits as part of the setup notes.

## Verification Plan

### Automated Tests
- Basic PHPUnit tests can be written for the models to ensure relationships work, but primarily relying on manual verification and visual testing for UI.

### Manual Verification
- **Admin Flow**: Run setup, login, create a school structure, import a CSV of dummy students, create a lesson, and schedule a quiz.
- **Student Flow**: Login as imported student, navigate to lesson, take quiz, see score < 50%, verify recommendation popup, and check direct messaging.
