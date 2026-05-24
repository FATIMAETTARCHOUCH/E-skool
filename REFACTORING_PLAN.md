# 📋 Refactoring Plan: Data Model Migration

**Objective:** Migrate from current user-centric model to role-based Student/Teacher split with improved relationships

**Timeline:** 4-6 weeks (can be done incrementally)  
**Risk Level:** HIGH (affects authentication, authorization, all controllers)  
**Rollback Strategy:** Git branches + feature flags for gradual migration

---

## 📊 Phase Overview

```
Phase 1: Database Preparation (Week 1)
├─ Create new Student & Teacher tables
├─ Create GroupStudent pivot table
├─ Add migrations for new enums/fields
└─ Data migration scripts (backfill)

Phase 2: Eloquent Models (Week 1-2)
├─ Create Student & Teacher models
├─ Update User model relationships
├─ Update all existing models
└─ Test relationships

Phase 3: Authentication & Authorization (Week 2)
├─ Update LoginController
├─ Update middleware (auth, role checks)
├─ Update gates/policies
└─ Test login flows

Phase 4: Controllers Refactoring (Week 2-3)
├─ StudentController → use Student model
├─ Admin controllers → reflect new relationships
├─ CourseController → use teacher_id
└─ Test all CRUD operations

Phase 5: Views & Forms (Week 3-4)
├─ Update admin forms (user creation, course assign)
├─ Update student dashboard
├─ Update teacher dashboard (new)
└─ Test UI rendering

Phase 6: Testing & Validation (Week 4-5)
├─ Feature tests
├─ Integration tests
├─ Manual QA
└─ Performance checks

Phase 7: Deployment (Week 5-6)
├─ Deploy to staging
├─ Data validation
├─ Deploy to production
└─ Monitor logs
```

---

## 🗄️ Phase 1: Database Migrations

### 1.1 Create Student Table
```php
// database/migrations/2026_05_16_000001_create_students_table.php
Schema::create('students', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('massar_code')->unique()->nullable();
    $table->unsignedTinyInteger('age')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
});
```

### 1.2 Create Teacher Table
```php
// database/migrations/2026_05_16_000002_create_teachers_table.php
Schema::create('teachers', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('specialty')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
    $table->unique('user_id');
});
```

### 1.3 Restructure users Table
```php
// database/migrations/2026_05_16_000003_refactor_users_table.php
Schema::table('users', function (Blueprint $table) {
    // Remove old columns (can be done in steps)
    $table->dropColumn(['group_id', 'role', 'age', 'massar_code']);
    
    // Add new columns
    $table->boolean('is_first_login')->default(true);
    $table->timestamp('last_login_at')->nullable();
});
```

### 1.4 Create GroupStudent Pivot Model
```php
// database/migrations/2026_05_16_000004_create_group_student_table.php
Schema::create('group_student', function (Blueprint $table) {
    $table->uuid('group_id');
    $table->uuid('student_id');
    $table->timestamp('enrolled_at')->useCurrent();
    
    $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
    $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
    $table->primary(['group_id', 'student_id']);
    
    $table->timestamps();
});
```

### 1.5 Add Course Teacher FK
```php
// database/migrations/2026_05_16_000005_add_teacher_id_to_courses_table.php
Schema::table('courses', function (Blueprint $table) {
    $table->foreignUuid('teacher_id')->nullable()->after('id')->constrained('teachers')->nullOnDelete();
});
```

### 1.6 Refactor Quiz/Results → QuizAttempt
```php
// database/migrations/2026_05_16_000006_refactor_quiz_results_to_attempts.php
// Option A: Rename results → quiz_attempts and add new fields
// Option B: Create new quiz_attempts table and migrate data

Schema::create('quiz_attempts', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('quiz_id')->constrained('quizzes')->cascadeOnDelete();
    $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
    $table->unsignedTinyInteger('attempt_number')->default(1);
    $table->unsignedTinyInteger('score');
    $table->unsignedTinyInteger('total_questions');
    $table->boolean('is_passed');
    $table->enum('triggered_by')->values(['manual', 'remediation', 'retake'])->default('manual');
    $table->timestamp('taken_at')->useCurrent();
    $table->timestamps();
    
    $table->index(['student_id', 'quiz_id']);
});
```

### 1.7 Update Answer Table to Reference Attempts
```php
// database/migrations/2026_05_16_000007_refactor_answers_table.php
Schema::table('answers', function (Blueprint $table) {
    // Replace user_id with attempt_id and quiz_id with question_id
    $table->dropForeign('answers_user_id_foreign');
    $table->dropColumn('user_id');
    
    $table->foreignUuid('attempt_id')->after('id')->constrained('quiz_attempts')->cascadeOnDelete();
});
```

### 1.8 Update StudentProgress Table
```php
// database/migrations/2026_05_16_000008_update_student_progress_table.php
Schema::table('student_progress', function (Blueprint $table) {
    // Rename user_id → student_id
    $table->renameColumn('user_id', 'student_id');
    
    // Update FK constraint
    $table->dropForeign('student_progress_user_id_foreign');
    $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
    
    // Add new fields
    $table->enum('status')->values(['locked', 'unlocked', 'in_progress', 'completed', 'stuck_remediation'])->default('locked');
    $table->unsignedTinyInteger('attempt_count')->default(0);
    $table->timestamp('unlocked_at')->nullable();
    $table->dropColumn('is_completed'); // Use status instead
    
    $table->index(['student_id', 'lesson_id']);
});
```

### 1.9 Add Enums to Lesson Table
```php
// database/migrations/2026_05_16_000009_add_enums_to_lessons_table.php
Schema::table('lessons', function (Blueprint $table) {
    $table->enum('variant_type')->values(['standard', 'simplified', 'advanced'])->default('standard')->after('parent_lesson_id');
    $table->enum('content_type')->values(['text', 'video', 'pdf', 'mixed'])->default('text')->after('content_text');
});
```

### 1.10 Remove Legacy Tables (Optional)
```php
// database/migrations/2026_05_16_000010_remove_legacy_tables.php
Schema::dropIfExists('results');        // Replaced by quiz_attempts
Schema::dropIfExists('quiz_retakes');   // Replaced by attempt_number
Schema::dropIfExists('lesson_group');   // Replaced by group_course
```

---

## 🔧 Phase 2: Eloquent Models

### 2.1 Create Student Model
```php
// app/Models/Student.php
class Student extends Model
{
    protected $fillable = ['user_id', 'massar_code', 'age'];
    protected $keyType = 'string';
    public $incrementing = false;
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function groups() {
        return $this->belongsToMany(Group::class, 'group_student')
                    ->withTimestamps()
                    ->withPivot('enrolled_at');
    }
    
    public function progress() {
        return $this->hasMany(StudentProgress::class);
    }
    
    public function quizAttempts() {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function messagesSent() {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    public function messagesReceived() {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    
    public function isStuck($lessonId): bool {
        return $this->progress()
                   ->where('lesson_id', $lessonId)
                   ->where('status', 'stuck_remediation')
                   ->exists();
    }
}
```

### 2.2 Create Teacher Model
```php
// app/Models/Teacher.php
class Teacher extends Model
{
    protected $fillable = ['user_id', 'specialty'];
    protected $keyType = 'string';
    public $incrementing = false;
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function courses() {
        return $this->hasMany(Course::class);
    }
}
```

### 2.3 Update User Model
```php
// app/Models/User.php
class User extends Model implements UserContract
{
    // Remove old properties
    // protected $fillable = ['first_name', ...  'role', 'group_id', ...];
    
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 
        'password', 'is_first_login'
    ];
    
    // Add new relationships
    public function student() {
        return $this->hasOne(Student::class);
    }
    
    public function teacher() {
        return $this->hasOne(Teacher::class);
    }
    
    // Helper methods
    public function isStudent(): bool {
        return $this->student()->exists();
    }
    
    public function isTeacher(): bool {
        return $this->teacher()->exists();
    }
    
    public function isAdmin(): bool {
        return $this->email === 'admin@example.com'; // Or use roles table
    }
}
```

### 2.4 Update Course Model
```php
// app/Models/Course.php
class Course extends Model
{
    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }
    
    // Keep existing relationships
    public function groups() {
        return $this->belongsToMany(Group::class, 'course_group');
    }
    
    public function lessons() {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
}
```

### 2.5 Update Lesson Model
```php
// app/Models/Lesson.php
class Lesson extends Model
{
    protected $casts = [
        'variant_type' => 'string', // 'standard', 'simplified', 'advanced'
        'content_type' => 'string', // 'text', 'video', 'pdf', 'mixed'
    ];
    
    public function isSimplified(): bool {
        return $this->variant_type === 'simplified';
    }
    
    public function isStandard(): bool {
        return $this->variant_type === 'standard';
    }
    
    // Existing methods stay
    public function parent() {
        return $this->belongsTo(Lesson::class, 'parent_lesson_id');
    }
    
    public function variants() {
        return $this->hasMany(Lesson::class, 'parent_lesson_id');
    }
}
```

### 2.6 Create QuizAttempt Model
```php
// app/Models/QuizAttempt.php
class QuizAttempt extends Model
{
    protected $table = 'quiz_attempts';
    protected $fillable = [
        'quiz_id', 'student_id', 'attempt_number', 
        'score', 'total_questions', 'is_passed', 'triggered_by'
    ];
    protected $casts = [
        'triggered_by' => 'string', // 'manual', 'remediation', 'retake'
        'taken_at' => 'datetime',
    ];
    
    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }
    
    public function student() {
        return $this->belongsTo(Student::class);
    }
    
    public function answers() {
        return $this->hasMany(Answer::class, 'attempt_id');
    }
}
```

### 2.7 Update StudentProgress Model
```php
// app/Models/StudentProgress.php
class StudentProgress extends Model
{
    protected $fillable = [
        'student_id', 'lesson_id', 'status', 
        'attempt_count', 'unlocked_at', 'completed_at'
    ];
    protected $casts = [
        'status' => 'string', // Use enum
        'unlocked_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    public function student() {
        return $this->belongsTo(Student::class);
    }
    
    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }
    
    public function isStuck(): bool {
        return $this->status === 'stuck_remediation';
    }
    
    public function isCompleted(): bool {
        return $this->status === 'completed';
    }
    
    public function markRemediation(): void {
        $this->update(['status' => 'stuck_remediation']);
    }
    
    public function markStuck(): void {
        $this->update(['status' => 'stuck_remediation']);
    }
}
```

### 2.8 Update Answer Model
```php
// app/Models/Answer.php
class Answer extends Model
{
    protected $fillable = ['attempt_id', 'question_id', 'option_id'];
    
    public function attempt() {
        return $this->belongsTo(QuizAttempt::class);
    }
    
    public function question() {
        return $this->belongsTo(Question::class);
    }
    
    public function option() {
        return $this->belongsTo(Option::class);
    }
}
```

### 2.9 Update Other Models
- **Group**: Change `users()` → `students()` via GroupStudent pivot
- **Message**: Change `sender/receiver_id` to reference Student instead of User
- **Quiz**: Replace `results()` with `attempts()`
- **Question, Option, LessonPDF, School, Branch, AcademicYear**: Minimal changes

---

## 🎮 Phase 3: Authentication & Authorization

### 3.1 Update LoginController
```php
// app/Http/Controllers/Auth/LoginController.php
public function store(LoginRequest $request)
{
    $request->authenticate();
    
    $user = Auth::user();
    $user->update(['last_login_at' => now()]);
    
    // Redirect based on role
    if ($user->isStudent()) {
        return redirect('/student/dashboard');
    } elseif ($user->isTeacher()) {
        return redirect('/teacher/dashboard');
    } else {
        return redirect('/admin/dashboard');
    }
}
```

### 3.2 Update Middleware
```php
// app/Http/Middleware/EnsureStudentRole.php
public function handle($request, Closure $next)
{
    if (! auth()->check() || ! auth()->user()->isStudent()) {
        abort(403);
    }
    return $next($request);
}

// app/Http/Middleware/EnsureTeacherRole.php
public function handle($request, Closure $next)
{
    if (! auth()->check() || ! auth()->user()->isTeacher()) {
        abort(403);
    }
    return $next($request);
}
```

### 3.3 Update Authorization Policies
```php
// app/Policies/CoursePolicy.php
public function update(User $user, Course $course): bool
{
    return $user->teacher?->id === $course->teacher_id || $user->isAdmin();
}

public function delete(User $user, Course $course): bool
{
    return $user->teacher?->id === $course->teacher_id || $user->isAdmin();
}
```

---

## 🎯 Phase 4: Controllers Refactoring

### 4.1 StudentController Changes
```php
// app/Http/Controllers/StudentController.php

// OLD: public function dashboard(User $user)
// NEW:
public function dashboard()
{
    $student = auth()->user()->student;
    $groups = $student->groups;
    $courses = $groups->flatMap(fn($g) => $g->courses)->unique();
    $progress = $student->progress()->with('lesson')->get();
    
    return view('student.dashboard', [
        'student' => $student,
        'courses' => $courses,
        'progress' => $progress,
    ]);
}

// OLD: public function submitQuiz(Request $request, Quiz $quiz)
// NEW:
public function submitQuiz(Request $request, Quiz $quiz)
{
    $student = auth()->user()->student;
    
    // Create attempt
    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'student_id' => $student->id,
        'attempt_number' => $student->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->count() + 1,
        'score' => $score,
        'total_questions' => count($answers),
        'is_passed' => $score >= $quiz->passing_score,
        'triggered_by' => 'manual',
    ]);
    
    // Store answers under attempt
    foreach ($answers as $qid => $oid) {
        Answer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $qid,
            'option_id' => $oid,
        ]);
    }
    
    // Update progress
    if ($attempt->is_passed) {
        $lesson = $quiz->lesson;
        StudentProgress::updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            ['status' => 'completed', 'completed_at' => now()]
        );
    } else {
        // Trigger remediation
        $this->triggerRemediation($student, $quiz->lesson);
    }
}

private function triggerRemediation(Student $student, Lesson $lesson)
{
    // Mark as stuck_remediation
    StudentProgress::updateOrCreate(
        ['student_id' => $student->id, 'lesson_id' => $lesson->id],
        ['status' => 'stuck_remediation']
    );
    
    // Recommend simplest variant
    $simplestVariant = $lesson->getSimplestVariant();
    
    return $simplestVariant ? $simplestVariant : null;
}
```

### 4.2 Admin User Controller
```php
// app/Http/Controllers/Admin/UserController.php

public function store(StoreUserRequest $request)
{
    $user = User::create($request->safe()->only([
        'first_name', 'last_name', 'email', 'username', 'password'
    ]));
    
    // Create associated Student or Teacher
    if ($request->role === 'student') {
        Student::create([
            'user_id' => $user->id,
            'massar_code' => $request->massar_code,
            'age' => $request->age,
        ]);
    } elseif ($request->role === 'teacher') {
        Teacher::create([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
        ]);
    }
    
    return redirect('/admin/users')->with('success', 'User created');
}

public function update(UpdateUserRequest $request, User $user)
{
    $user->update($request->safe()->only([
        'first_name', 'last_name', 'email'
    ]));
    
    if ($user->isStudent()) {
        $user->student->update([
            'massar_code' => $request->massar_code,
            'age' => $request->age,
        ]);
    } elseif ($user->isTeacher()) {
        $user->teacher->update([
            'specialty' => $request->specialty,
        ]);
    }
    
    return redirect('/admin/users')->with('success', 'User updated');
}
```

### 4.3 Admin Course Controller
```php
// app/Http/Controllers/Admin/CourseController.php

public function store(StoreCourseRequest $request)
{
    $course = Course::create([
        'teacher_id' => $request->teacher_id,
        'title' => $request->title,
        'description' => $request->description,
        'category' => $request->category,
        'level' => $request->level,
    ]);
    
    $course->groups()->sync($request->group_ids);
    
    return redirect("/admin/courses/{$course->id}")->with('success', 'Course created');
}
```

### 4.4 Admin Group Controller (GroupStudent)
```php
// app/Http/Controllers/Admin/GroupController.php

public function addStudent(Group $group, Student $student)
{
    $group->students()->attach($student->id);
    return back()->with('success', 'Student added to group');
}

public function removeStudent(Group $group, Student $student)
{
    $group->students()->detach($student->id);
    return back()->with('success', 'Student removed from group');
}

public function importStudents(Request $request, Group $group)
{
    // Using maatwebsite/excel
    $students = Excel::toArray(new StudentImport, $request->file('file'))[0];
    
    foreach ($students as $row) {
        $student = Student::where('massar_code', $row['massar_code'])->first();
        if ($student) {
            $group->students()->attach($student->id);
        }
    }
    
    return back()->with('success', 'Students imported');
}
```

### 4.5 Create Teacher Dashboard Controller
```php
// app/Http/Controllers/TeacherController.php
class TeacherController
{
    public function dashboard()
    {
        $teacher = auth()->user()->teacher;
        $courses = $teacher->courses()->with('groups')->get();
        
        return view('teacher.dashboard', ['teacher' => $teacher, 'courses' => $courses]);
    }
    
    public function courseStudents(Course $course)
    {
        // Verify ownership
        $this->authorize('view', $course);
        
        $students = $course->groups
                           ->flatMap(fn($g) => $g->students)
                           ->unique();
        
        return view('teacher.course-students', [
            'course' => $course,
            'students' => $students,
        ]);
    }
    
    public function studentProgress(Course $course, Student $student)
    {
        $this->authorize('view', $course);
        
        $progress = $student->progress()
                           ->whereIn('lesson_id', $course->lessons->pluck('id'))
                           ->get();
        
        return view('teacher.student-progress', [
            'student' => $student,
            'course' => $course,
            'progress' => $progress,
        ]);
    }
}
```

---

## 📱 Phase 5: Views & Forms

### 5.1 Update User Creation Form
```blade
<!-- resources/views/admin/users/create.blade.php -->
<form method="POST" action="/admin/users">
    @csrf
    <div class="form-group">
        <label>Role</label>
        <select name="role" x-model="role" @change="updateFields">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
        </select>
    </div>
    
    <!-- Common fields -->
    <input type="text" name="first_name" placeholder="First Name">
    <input type="text" name="last_name" placeholder="Last Name">
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    
    <!-- Student fields -->
    <div x-show="role === 'student'">
        <input type="text" name="massar_code" placeholder="Massar Code">
        <input type="number" name="age" placeholder="Age">
    </div>
    
    <!-- Teacher fields -->
    <div x-show="role === 'teacher'">
        <input type="text" name="specialty" placeholder="Specialty">
    </div>
    
    <button type="submit">Create User</button>
</form>
```

### 5.2 Update Student Dashboard
```blade
<!-- resources/views/student/dashboard.blade.php -->
<div class="dashboard">
    <h1>Welcome, {{ auth()->user()->first_name }}</h1>
    
    <!-- Groups -->
    <section>
        <h2>My Groups</h2>
        @foreach($student->groups as $group)
            <div>{{ $group->name }} - {{ $group->branch->name }}</div>
        @endforeach
    </section>
    
    <!-- Courses -->
    <section>
        <h2>My Courses</h2>
        @foreach($courses as $course)
            <div class="course-card">
                <h3>{{ $course->title }}</h3>
                <p>Teacher: {{ $course->teacher->user->first_name }}</p>
                <a href="/student/courses/{{ $course->id }}">View Lessons</a>
            </div>
        @endforeach
    </section>
    
    <!-- Progress -->
    <section>
        <h2>My Progress</h2>
        @foreach($progress as $p)
            <div class="progress-item">
                <span>{{ $p->lesson->title }}</span>
                <span class="status">{{ $p->status }}</span>
            </div>
        @endforeach
    </section>
</div>
```

### 5.3 Create Teacher Dashboard
```blade
<!-- resources/views/teacher/dashboard.blade.php -->
<div class="dashboard">
    <h1>Teacher Dashboard - {{ auth()->user()->first_name }}</h1>
    
    <section>
        <h2>My Courses</h2>
        @foreach($courses as $course)
            <div class="course-card">
                <h3>{{ $course->title }}</h3>
                <p>Groups: {{ $course->groups->count() }}</p>
                <a href="/teacher/courses/{{ $course->id }}/students">View Students</a>
                <a href="/admin/courses/{{ $course->id }}/edit">Edit Course</a>
            </div>
        @endforeach
    </section>
</div>
```

---

## ✅ Phase 6: Testing & Validation

### 6.1 Feature Tests
```php
// tests/Feature/AuthenticationTest.php
public function test_student_login_redirects_to_dashboard()
{
    $user = User::factory()->create();
    Student::factory()->create(['user_id' => $user->id]);
    
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/student/dashboard');
}

public function test_teacher_login_redirects_to_dashboard()
{
    $user = User::factory()->create();
    Teacher::factory()->create(['user_id' => $user->id]);
    
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/teacher/dashboard');
}

// tests/Feature/StudentProgressTest.php
public function test_failed_quiz_triggers_remediation()
{
    $student = Student::factory()->create();
    $quiz = Quiz::factory()->create();
    
    $this->actingAs($student->user)
         ->post("/student/quizzes/{$quiz->id}/submit", [
             'answers' => [1 => 3, 2 => 4] // Wrong answers
         ]);
    
    $this->assertEquals(
        'stuck_remediation',
        $student->progress()->where('lesson_id', $quiz->lesson_id)->first()->status
    );
}

// tests/Feature/CourseManagementTest.php
public function test_teacher_can_only_edit_own_courses()
{
    $teacher = Teacher::factory()->create();
    $otherTeacher = Teacher::factory()->create();
    
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);
    
    $this->actingAs($teacher->user)
         ->patch("/admin/courses/{$course->id}", [...])
         ->assertSuccessful();
    
    $this->actingAs($otherTeacher->user)
         ->patch("/admin/courses/{$course->id}", [...])
         ->assertForbidden();
}
```

### 6.2 Integration Tests
```php
// tests/Integration/StudentFlowTest.php
public function test_complete_student_learning_flow()
{
    // Create school structure
    $branch = Branch::factory()->create();
    $group = Group::factory()->create(['branch_id' => $branch->id]);
    
    // Create user
    $user = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $user->id]);
    $student->groups()->attach($group->id);
    
    // Create course
    $teacher = Teacher::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);
    $course->groups()->attach($group->id);
    
    // Create lessons
    $lesson = Lesson::factory()->create(['course_id' => $course->id]);
    $variant = Lesson::factory()->create([
        'course_id' => $course->id,
        'parent_lesson_id' => $lesson->id,
        'variant_type' => 'simplified'
    ]);
    
    // Create quiz
    $quiz = Quiz::factory()->create(['lesson_id' => $lesson->id]);
    
    // Student fails quiz
    $this->actingAs($user)->post("/student/quizzes/{$quiz->id}/submit", [...]); // Wrong
    
    // Check progress marked as stuck
    $progress = $student->progress()->where('lesson_id', $lesson->id)->first();
    $this->assertEquals('stuck_remediation', $progress->status);
    
    // Check variant recommended
    $recommendedVariant = $lesson->getSimplestVariant();
    $this->assertEquals($variant->id, $recommendedVariant->id);
}
```

### 6.3 Manual QA Checklist
- [ ] User creation (student + teacher)
- [ ] Login flows (student, teacher, admin)
- [ ] Student dashboard shows groups & courses
- [ ] Teacher dashboard shows owned courses
- [ ] Admin can manage users
- [ ] Course assignment to groups works
- [ ] Student can take quizzes
- [ ] Failed quiz triggers remediation
- [ ] QuizAttempt records created with attempt_number
- [ ] StudentProgress status updates correctly
- [ ] Messages between students work
- [ ] No SQL errors in logs
- [ ] Performance acceptable with large datasets

---

## 🚀 Phase 7: Deployment Strategy

### 7.1 Staging Deployment
```bash
# 1. Create feature branch
git checkout -b refactor/student-teacher-split

# 2. Backup production database
mysqldump -u root fati-projet > backup_$(date +%Y%m%d).sql

# 3. Run migrations on staging
php artisan migrate --database=mysql_staging

# 4. Run data migration script
php artisan db:seed --class=DataMigrationSeeder

# 5. Run tests
php artisan test

# 6. Verify in browser
```

### 7.2 Data Migration Script
```php
// database/seeders/DataMigrationSeeder.php
class DataMigrationSeeder extends Seeder
{
    public function run()
    {
        // Migrate existing users to Student/Teacher
        User::where('role', 'student')->each(function ($user) {
            if (! $user->student) {
                Student::create([
                    'user_id' => $user->id,
                    'massar_code' => $user->massar_code,
                    'age' => $user->age,
                ]);
            }
        });
        
        User::where('role', 'teacher')->each(function ($user) {
            if (! $user->teacher) {
                Teacher::create([
                    'user_id' => $user->id,
                ]);
            }
        });
        
        // Migrate group membership (user.group_id → group_student)
        User::where('group_id', '!=', null)->each(function ($user) {
            if ($user->isStudent()) {
                $user->student->groups()->attach($user->group_id);
            }
        });
        
        // Migrate results → quiz_attempts
        Result::each(function ($result) {
            QuizAttempt::create([
                'quiz_id' => $result->quiz_id,
                'student_id' => $result->user->student->id,
                'attempt_number' => 1,
                'score' => $result->score,
                'total_questions' => $result->total_questions,
                'is_passed' => $result->is_passed,
                'triggered_by' => 'manual',
                'taken_at' => $result->created_at,
            ]);
        });
        
        $this->command->info('Data migration completed');
    }
}
```

### 7.3 Production Deployment Checklist
- [ ] All tests passing
- [ ] Staging thoroughly tested
- [ ] Database backup taken
- [ ] Maintenance mode enabled (`php artisan down`)
- [ ] Migrations run (`php artisan migrate --force`)
- [ ] Data migration script run
- [ ] Validation queries executed
- [ ] Cache cleared (`php artisan cache:clear`)
- [ ] Assets compiled (`npm run build`)
- [ ] Health checks pass
- [ ] Monitoring active (error logs, user logins)
- [ ] Rollback plan ready
- [ ] Maintenance mode disabled (`php artisan up`)

### 7.4 Rollback Plan
```bash
# If critical issues:
# 1. Enable maintenance mode
php artisan down

# 2. Restore database from backup
mysql -u root fati-projet < backup_20260516.sql

# 3. Revert code to previous version
git revert HEAD --no-edit

# 4. Disable maintenance mode
php artisan up
```

---

## 📊 Effort & Risks

### Effort Breakdown
| Phase | Time | Risk |
|-------|------|------|
| Migrations | 2-3 days | HIGH (data loss) |
| Models | 2-3 days | MEDIUM (relationships) |
| Auth | 2-3 days | HIGH (security) |
| Controllers | 3-4 days | HIGH (business logic) |
| Views | 2-3 days | LOW |
| Testing | 3-4 days | HIGH (coverage) |
| **Total** | **4-6 weeks** | **HIGH** |

### Key Risks
1. **Data Loss**: Migrations on production could fail → backup required
2. **Authentication Breakage**: Role checks everywhere → extensive testing needed
3. **Performance**: New joins (Student, Teacher, GroupStudent) → indexes required
4. **Hidden Dependencies**: Other code relying on `user.role` or `user.group_id` → grep needed
5. **Downtime**: Can't be done incrementally on production

### Mitigation Strategies
- Feature flags to enable new code gradually
- Duplicate tables (keep old ones temporarily)
- Run data migration in background job
- Test with production-sized datasets on staging
- Have rollback plan ready
- Monitor error logs intensely first week

---

## 📌 Implementation Checklist

### Before Starting
- [ ] Read entire plan
- [ ] Backup current database
- [ ] Create feature branch
- [ ] Set up staging environment clone

### Phase 1 Checklist
- [ ] All migrations created
- [ ] Data migration script tested on staging
- [ ] Migration files validated for syntax

### Phase 2 Checklist
- [ ] All models created
- [ ] Relationships tested with tinker
- [ ] No circular dependencies
- [ ] Fillable properties correct

### Phase 3 Checklist
- [ ] LoginController updated
- [ ] Middleware created and applied
- [ ] Policies created
- [ ] Login flow tested

### Phase 4 Checklist
- [ ] All controllers updated
- [ ] No references to `user.role` or `user.group_id`
- [ ] CRUD operations tested

### Phase 5 Checklist
- [ ] Forms updated
- [ ] Dashboards created
- [ ] Views tested in browser

### Phase 6 Checklist
- [ ] Feature tests written
- [ ] Integration tests passing
- [ ] QA checklist completed
- [ ] Performance acceptable

### Phase 7 Checklist
- [ ] Staging deployed successfully
- [ ] Data validation passed
- [ ] Backup confirmed
- [ ] Production deployment completed

---

## 📞 Questions to Answer Before Starting

1. **Timeline**: Can you afford 4-6 weeks with limited new features?
2. **Rollback**: Do you have database backup & restore processes in place?
3. **Messaging**: How should users be notified of Student/Teacher roles?
4. **Admin**: How should admin users be handled (separate Admin model)?
5. **Legacy**: Can you drop old Result/QuizRetake tables immediately?
6. **Tests**: What's your target test coverage (70%, 80%, 90%)?
7. **Downtime**: Can you take application down for 1-2 hours?

---

**Status**: Ready for Phase 1 execution  
**Last Updated**: 2026-05-16  
**Owner**: Development Team
