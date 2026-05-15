# Routes & Controllers — FATI-PROJET

## Web Routes Structure

All routes are defined in `routes/web.php` and `routes/auth.php` (Laravel Breeze).

### Installation Routes
```
GET  /install                       → App\Http\Controllers\InstallController@index
POST /install                       → App\Http\Controllers\InstallController@store
```

### Public Routes
```
GET  /                              → Welcome page or redirect to install
```

## Authenticated Routes

### Dashboard (Auth Required)
```
GET  /dashboard                     → App\Http\Controllers\StudentController@dashboard
                                       (redirects to admin.dashboard or student.dashboard)
```

### Profile Routes (Auth Required)
```
GET    /profile                     → App\Http\Controllers\ProfileController@edit
PATCH  /profile                     → App\Http\Controllers\ProfileController@update
DELETE /profile                     → App\Http\Controllers\ProfileController@destroy
```

## Student Routes (Prefix: `/student`, Middleware: `auth`)

### Dashboard
```
GET /student/dashboard              → App\Http\Controllers\StudentController@dashboard
    Returns: view('student.dashboard') with user, group, courses
```

### Course Navigation
```
GET /student/courses/{id}           → App\Http\Controllers\StudentController@course
    Returns: view('student.course') with course lessons and progress
```

### Lesson Access
```
GET  /student/lessons/{id}          → App\Http\Controllers\StudentController@lesson
     Returns: view('student.lesson') with lesson content and quizzes
     Validation: Previous lesson must be completed (if order > 1)

POST /student/lessons/{id}/complete → App\Http\Controllers\StudentController@completeLesson
     Marks lesson as completed for user
     Returns: redirect with success message
```

### Quiz Navigation
```
GET  /student/quizzes/{id}          → App\Http\Controllers\StudentController@quiz
     Returns: view('student.quiz') with questions and options
     Shows previous answers if retake available

POST /student/quizzes/{id}/submit   → App\Http\Controllers\StudentController@submitQuiz
     Submits answers and creates Result record
     Returns: Result view with score and feedback
```

### Analytics
```
GET /student/analytics              → App\Http\Controllers\StudentController@analytics
    Returns: view('student.analytics') with user progress data
```

## Messaging Routes (Prefix: `/messages`, Middleware: `auth`)

```
GET  /messages                      → App\Http\Controllers\MessageController@index
     Returns: view('messages.index') with message list

GET  /messages/{id}                 → App\Http\Controllers\MessageController@show
     Returns: view('messages.show') with message conversation

POST /messages/{id}/send            → App\Http\Controllers\MessageController@store
     Creates new Message record
     Returns: redirect with success
```

## Admin Routes (Prefix: `/admin`, Middleware: `auth`)

### Admin Dashboard
```
GET /admin/dashboard                → App\Http\Controllers\Admin\AdminController@index
    Returns: view('admin.dashboard') with statistics
```

### Analytics & Reporting
```
GET  /admin/analytics/students
     → App\Http\Controllers\Admin\AnalyticsController@students
     Returns: view('admin.analytics.students') with student list and stats

GET  /admin/analytics/students/{id}
     → App\Http\Controllers\Admin\AnalyticsController@studentProfile
     Returns: view('admin.analytics.student_profile') with detailed profile

POST /admin/analytics/students/{id}/reset-quizzes
     → App\Http\Controllers\Admin\AnalyticsController@resetQuizzes
     Deletes all Result records for student
     Returns: redirect with success

DELETE /admin/analytics/results/{id}
     → App\Http\Controllers\Admin\AnalyticsController@deleteResult
     Deletes single Result record
     Returns: redirect with success
```

### Maintenance
```
POST /admin/maintenance/toggle      → App\Http\Controllers\Admin\AdminController@toggleMaintenance
     Toggles maintenance mode
     Returns: redirect with status
```

## Resource Controllers (RESTful Pattern)

All resource controllers follow this pattern:

```
GET    /admin/{resource}            → Controller@index        (list)
POST   /admin/{resource}            → Controller@store        (create)
GET    /admin/{resource}/create     → Controller@create       (form)
GET    /admin/{resource}/{id}       → Controller@show         (view)
GET    /admin/{resource}/{id}/edit  → Controller@edit         (form)
PATCH  /admin/{resource}/{id}       → Controller@update       (save)
DELETE /admin/{resource}/{id}       → Controller@destroy      (delete)
```

### Schools Resource
```
GET    /admin/schools               → App\Http\Controllers\Admin\SchoolController@index
POST   /admin/schools               → App\Http\Controllers\Admin\SchoolController@store
GET    /admin/schools/create        → App\Http\Controllers\Admin\SchoolController@create
GET    /admin/schools/{school}      → App\Http\Controllers\Admin\SchoolController@show
GET    /admin/schools/{school}/edit → App\Http\Controllers\Admin\SchoolController@edit
PATCH  /admin/schools/{school}      → App\Http\Controllers\Admin\SchoolController@update
DELETE /admin/schools/{school}      → App\Http\Controllers\Admin\SchoolController@destroy
```

### Branches Resource
```
GET    /admin/branches              → App\Http\Controllers\Admin\BranchController@index
POST   /admin/branches              → App\Http\Controllers\Admin\BranchController@store
GET    /admin/branches/create       → App\Http\Controllers\Admin\BranchController@create
GET    /admin/branches/{branch}     → App\Http\Controllers\Admin\BranchController@show
GET    /admin/branches/{branch}/edit → App\Http\Controllers\Admin\BranchController@edit
PATCH  /admin/branches/{branch}     → App\Http\Controllers\Admin\BranchController@update
DELETE /admin/branches/{branch}     → App\Http\Controllers\Admin\BranchController@destroy
```

### Academic Years Resource
```
GET    /admin/academic_years        → App\Http\Controllers\Admin\AcademicYearController@index
POST   /admin/academic_years        → App\Http\Controllers\Admin\AcademicYearController@store
GET    /admin/academic_years/create → App\Http\Controllers\Admin\AcademicYearController@create
GET    /admin/academic_years/{year} → App\Http\Controllers\Admin\AcademicYearController@show
GET    /admin/academic_years/{year}/edit → App\Http\Controllers\Admin\AcademicYearController@edit
PATCH  /admin/academic_years/{year} → App\Http\Controllers\Admin\AcademicYearController@update
DELETE /admin/academic_years/{year} → App\Http\Controllers\Admin\AcademicYearController@destroy

POST   /admin/academic_years/{id}/toggle
       → App\Http\Controllers\Admin\AcademicYearController@toggleActive
       Sets is_active = !is_active
```

### Groups Resource
```
GET    /admin/groups                → App\Http\Controllers\Admin\GroupController@index
POST   /admin/groups                → App\Http\Controllers\Admin\GroupController@store
GET    /admin/groups/create         → App\Http\Controllers\Admin\GroupController@create
GET    /admin/groups/{group}        → App\Http\Controllers\Admin\GroupController@show
GET    /admin/groups/{group}/edit   → App\Http\Controllers\Admin\GroupController@edit
PATCH  /admin/groups/{group}        → App\Http\Controllers\Admin\GroupController@update
DELETE /admin/groups/{group}        → App\Http\Controllers\Admin\GroupController@destroy

POST   /admin/groups/{id}/import    → App\Http\Controllers\Admin\GroupController@importStudents
       Imports students from Excel file
```

### Users Resource
```
GET    /admin/users                 → App\Http\Controllers\Admin\UserController@index
POST   /admin/users                 → App\Http\Controllers\Admin\UserController@store
GET    /admin/users/create          → App\Http\Controllers\Admin\UserController@create
GET    /admin/users/{user}          → App\Http\Controllers\Admin\UserController@show
GET    /admin/users/{user}/edit     → App\Http\Controllers\Admin\UserController@edit
PATCH  /admin/users/{user}          → App\Http\Controllers\Admin\UserController@update
DELETE /admin/users/{user}          → App\Http\Controllers\Admin\UserController@destroy
```

### Students Resource
```
GET    /admin/students              → App\Http\Controllers\Admin\StudentController@index
POST   /admin/students              → App\Http\Controllers\Admin\StudentController@store
GET    /admin/students/create       → App\Http\Controllers\Admin\StudentController@create
GET    /admin/students/{student}    → App\Http\Controllers\Admin\StudentController@show
GET    /admin/students/{student}/edit → App\Http\Controllers\Admin\StudentController@edit
PATCH  /admin/students/{student}    → App\Http\Controllers\Admin\StudentController@update
DELETE /admin/students/{student}    → App\Http\Controllers\Admin\StudentController@destroy
```

### Courses Resource
```
GET    /admin/courses               → App\Http\Controllers\Admin\CourseController@index
POST   /admin/courses               → App\Http\Controllers\Admin\CourseController@store
GET    /admin/courses/create        → App\Http\Controllers\Admin\CourseController@create
GET    /admin/courses/{course}      → App\Http\Controllers\Admin\CourseController@show
GET    /admin/courses/{course}/edit → App\Http\Controllers\Admin\CourseController@edit
PATCH  /admin/courses/{course}      → App\Http\Controllers\Admin\CourseController@update
DELETE /admin/courses/{course}      → App\Http\Controllers\Admin\CourseController@destroy
```

### Lessons Resource
```
GET    /admin/lessons               → App\Http\Controllers\Admin\LessonController@index
POST   /admin/lessons               → App\Http\Controllers\Admin\LessonController@store
GET    /admin/lessons/create        → App\Http\Controllers\Admin\LessonController@create
GET    /admin/lessons/{lesson}      → App\Http\Controllers\Admin\LessonController@show
GET    /admin/lessons/{lesson}/edit → App\Http\Controllers\Admin\LessonController@edit
PATCH  /admin/lessons/{lesson}      → App\Http\Controllers\Admin\LessonController@update
DELETE /admin/lessons/{lesson}      → App\Http\Controllers\Admin\LessonController@destroy
```

### Quizzes Resource
```
GET    /admin/quizzes               → App\Http\Controllers\Admin\QuizController@index
POST   /admin/quizzes               → App\Http\Controllers\Admin\QuizController@store
GET    /admin/quizzes/create        → App\Http\Controllers\Admin\QuizController@create
GET    /admin/quizzes/{quiz}        → App\Http\Controllers\Admin\QuizController@show
GET    /admin/quizzes/{quiz}/edit   → App\Http\Controllers\Admin\QuizController@edit
PATCH  /admin/quizzes/{quiz}        → App\Http\Controllers\Admin\QuizController@update
DELETE /admin/quizzes/{quiz}        → App\Http\Controllers\Admin\QuizController@destroy
```

### Quiz Questions (Nested Resource)
```
GET  /admin/quizzes/{quiz}/questions
     → App\Http\Controllers\Admin\QuestionController@index
     Returns: view('admin.quizzes.questions') with question list for quiz

POST /admin/quizzes/{quiz}/questions
     → App\Http\Controllers\Admin\QuestionController@store
     Creates Question with options
     Redirects to quiz show

DELETE /admin/questions/{question}
     → App\Http\Controllers\Admin\QuestionController@destroy
     Deletes question and related options
```

## Authentication Routes (from Laravel Breeze)

Located in `routes/auth.php`:

```
GET  /login                         → LoginController@create
POST /login                         → LoginController@store
GET  /register                      → RegisterController@create
POST /register                      → RegisterController@store
GET  /forgot-password               → PasswordResetLinkController@create
POST /forgot-password               → PasswordResetLinkController@store
GET  /reset-password/{token}        → NewPasswordController@create
POST /reset-password                → NewPasswordController@store
GET  /verify-email                  → EmailVerificationPromptController@__invoke
POST /email/verification-notification → EmailVerificationNotificationController@store
GET  /verify-email/{id}/{hash}      → VerifyEmailController@__invoke
POST /logout                        → LogoutController@store
```

## Controller Files Location

```
app/Http/Controllers/
├── Admin/
│   ├── AcademicYearController.php
│   ├── AdminController.php
│   ├── AnalyticsController.php
│   ├── BranchController.php
│   ├── CourseController.php
│   ├── GroupController.php
│   ├── LessonController.php
│   ├── QuestionController.php
│   ├── QuizController.php
│   ├── SchoolController.php
│   ├── StudentController.php
│   └── UserController.php
├── Auth/
│   ├── AuthenticatedSessionController.php
│   ├── EmailVerificationNotificationController.php
│   ├── EmailVerificationPromptController.php
│   ├── NewPasswordController.php
│   ├── PasswordResetLinkController.php
│   ├── RegisteredUserController.php
│   ├── VerifyEmailController.php
│   └── LogoutController.php
├── Controller.php
├── InstallController.php
├── MessageController.php
├── ProfileController.php
└── StudentController.php
```
