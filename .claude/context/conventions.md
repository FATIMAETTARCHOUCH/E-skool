# Code Conventions — FATI-PROJET

## Naming Conventions

### Controllers
- **Format:** PascalCase + "Controller"
- **Location:** `app/Http/Controllers/` or `app/Http/Controllers/Admin/`
- **Examples:**
  - `StudentController` (general controller for student actions)
  - `Admin\CourseController` (resource controller in Admin namespace)
  - `Admin\AnalyticsController` (custom admin functionality)
  
- **Pattern:** Resource controllers use standard CRUD methods
  ```php
  public function index()    // List all
  public function create()   // Show create form
  public function store()    // Save from form
  public function show()     // View single
  public function edit()     // Show edit form
  public function update()   // Update single
  public function destroy()  // Delete single
  ```

### Models
- **Format:** Singular PascalCase
- **Location:** `app/Models/`
- **Examples:**
  - `User` (not Users)
  - `Lesson` (not Lessons)
  - `StudentProgress` (compound words in PascalCase)

- **Pattern:** Models define relationships to other models
  ```php
  public function relatedModel() { return $this->hasMany(RelatedModel::class); }
  public function belongsToModel() { return $this->belongsTo(BelongsToModel::class); }
  ```

### Tables
- **Format:** Plural snake_case
- **Examples:**
  - `users` (not user)
  - `academic_years` (not academic_years_table)
  - `lesson_user` (pivot table)
  - `course_group` (pivot table)

### Columns
- **Format:** snake_case
- **Examples:**
  - `first_name`
  - `massar_code`
  - `is_active`
  - `passing_score`
  - `created_at`
  - `updated_at`

- **Boolean Columns:** prefix with `is_` or `has_`
  ```php
  is_active
  is_completed
  is_passed
  is_first_login
  has_permission
  ```

- **Foreign Key Columns:** `singular_id` format
  ```php
  user_id
  group_id
  course_id
  teacher_id
  sender_id
  receiver_id
  ```

### Routes
- **Format:** kebab-case for URI segments
- **Examples:**
  - `/student/lessons/{id}`
  - `/admin/academic_years`
  - `/admin/quiz_retakes/{id}`
  - `/admin/students/{id}/reset-quizzes`

### Route Names
- **Format:** dot notation with descriptive names
- **Examples:**
  - `student.dashboard`
  - `admin.courses.index`
  - `admin.analytics.student_profile`
  - `messages.send`

### Views
- **Format:** Blade templates organized by role/resource
- **Location:** `resources/views/{role|resource}/`
- **Examples:**
  - `resources/views/student/dashboard.blade.php`
  - `resources/views/admin/courses/index.blade.php`
  - `resources/views/admin/courses/create.blade.php`
  - `resources/views/messages/index.blade.php`

### Request/Form Classes
- **Format:** Action + "Request" (e.g., StoreRequest, UpdateRequest)
- **Location:** `app/Http/Requests/`
- **Examples:**
  - `StoreCourseRequest`
  - `UpdateGroupRequest`
  - `ImportStudentsRequest`

## Code Structure Patterns

### Controller Structure
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Http\Requests\StoreModelRequest;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index()
    {
        $models = Model::all();
        return view('admin.models.index', compact('models'));
    }

    public function create()
    {
        return view('admin.models.create');
    }

    public function store(StoreModelRequest $request)
    {
        Model::create($request->validated());
        return redirect()->route('admin.models.index')
            ->with('success', 'Model créé avec succès.');
    }

    public function show(Model $model)
    {
        return view('admin.models.show', compact('model'));
    }

    public function edit(Model $model)
    {
        return view('admin.models.edit', compact('model'));
    }

    public function update(StoreModelRequest $request, Model $model)
    {
        $model->update($request->validated());
        return redirect()->route('admin.models.index')
            ->with('success', 'Model mis à jour.');
    }

    public function destroy(Model $model)
    {
        $model->delete();
        return redirect()->route('admin.models.index')
            ->with('success', 'Model supprimé.');
    }
}
```

### Model Structure
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Model extends Model
{
    use HasFactory;

    protected $fillable = [
        'column1',
        'column2',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Relationships
    public function relatedModel() { return $this->hasMany(RelatedModel::class); }
    public function parent() { return $this->belongsTo(Parent::class); }
    public function children() { return $this->belongsToMany(Child::class, 'pivot_table'); }
}
```

### Form Request Structure
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
        ];
    }
}
```

## Database Patterns

### Migrations
- Use `foreignId()` for foreign keys (Laravel 8+)
- Use `cascadeOnDelete()` or `nullOnDelete()` as appropriate
- Use `constrained()` to create constraint automatically

```php
public function up(): void
{
    Schema::create('table_name', function (Blueprint $table) {
        $table->id();
        $table->foreignId('related_id')
            ->constrained('related_table')
            ->cascadeOnDelete();
        $table->string('name');
        $table->boolean('is_active')->default(false);
        $table->timestamps();
    });
}
```

### Pivot Tables
- Name as `table1_table2` in alphabetical order
- Include `id` primary key
- Include timestamps if tracking related data

```php
Schema::create('course_group', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->cascadeOnDelete();
    $table->foreignId('group_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

## Eloquent Patterns

### Loading Relationships
```php
// Eager load to avoid N+1 queries
$courses = Course::with(['lessons', 'groups'])->get();

// Conditional eager loading
$courses = Course::with(['lessons' => function($q) {
    $q->where('is_active', true);
}])->get();

// Select specific columns
$users = User::select('id', 'name', 'email')->get();
```

### Updating Relationships
```php
// Attach/Detach (many-to-many)
$course->groups()->attach($group_ids);
$course->groups()->detach($group_ids);

// Sync (replace all)
$course->groups()->sync($request->group_ids);

// Update association
$group->courses()->update(['is_active' => true]);
```

## View Patterns

### Include Common Components
```blade
@include('components.alert', ['type' => 'success', 'message' => 'Success!'])
@include('admin.shared.form-errors')
```

### Form Patterns
```blade
<form action="{{ route('admin.courses.update', $course) }}" method="POST">
    @csrf
    @method('PATCH')
    
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" 
               value="{{ old('title', $course->title) }}"
               class="@error('title') is-invalid @enderror">
        @error('title')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>
</form>
```

### Conditional Display
```blade
@if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
@else
    <a href="{{ route('student.dashboard') }}">Student</a>
@endif
```

## Service & Helper Patterns

### Validation
Always use Form Requests, not inline validation:

```php
// ✅ Good
public function store(StoreCourseRequest $request)
{
    Course::create($request->validated());
}

// ❌ Avoid
public function store(Request $request)
{
    $request->validate([...]);
}
```

### Authorization
Use model binding with resource controllers:

```php
// ✅ Good - Route model binding
public function show(Course $course) { }

// Middleware/Gate checks for custom logic
public function destroy(Course $course)
{
    $this->authorize('delete', $course);
}
```

## File Organization

```
app/
├── Models/
│   ├── User.php
│   ├── Course.php
│   ├── Lesson.php
│   └── [...]
├── Http/
│   ├── Controllers/
│   │   ├── StudentController.php
│   │   ├── Admin/
│   │   │   ├── CourseController.php
│   │   │   └── [...]
│   ├── Requests/
│   │   ├── StoreCourseRequest.php
│   │   └── [...]
├── Providers/
└── View/
    └── Components/

database/
├── migrations/
├── seeders/
└── factories/

resources/
├── views/
│   ├── admin/
│   │   ├── courses/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   ├── student/
│   └── [...]
└── js/

routes/
├── web.php
└── auth.php
```

## Comments & Documentation

- Use meaningful variable/method names (self-documenting code)
- Add comments only for complex business logic
- Document public methods with PHPDoc comments

```php
/**
 * Calculate the passing percentage for a quiz result
 *
 * @param Result $result
 * @return float Percentage (0-100)
 */
public function calculatePercentage(Result $result): float
{
    return ($result->score / $result->total_questions) * 100;
}
```

## Error Handling

- Use Laravel's built-in exception handling
- Return redirect with error message for form submissions
- Return JSON errors for AJAX requests

```php
// Form submission
return redirect()->back()
    ->withErrors($validator)
    ->withInput();

// AJAX response
return response()->json(['error' => 'Not found'], 404);
```

## Testing Expectations

While tests aren't fully implemented, follow these patterns:
- Test controllers with real requests
- Mock external services
- Use factories for test data
- Name tests: `testActionReturnsExpectedResult()`
