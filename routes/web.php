<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AnalyticsController;

Route::get('/install', [InstallController::class, 'index']);
Route::post('/install', [InstallController::class, 'store']);

Route::get('/', function () {
    if (\App\Models\User::count() === 0) {
        return redirect('/install');
    }
    return view('welcome');
});

// Student and General Auth Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    // Student specific routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses/{id}', [\App\Http\Controllers\StudentController::class, 'course'])->name('course');
        Route::get('/chapters/{chapter}', [\App\Http\Controllers\Student\ChapterController::class, 'show'])->name('chapter');
        Route::post('/chapters/{id}/complete', [\App\Http\Controllers\StudentController::class, 'completeChapter'])->name('chapter.complete');
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Student\QuizController::class, 'show'])->name('quiz');
        Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\Student\QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('/quizzes/{quiz}/result', [\App\Http\Controllers\Student\QuizController::class, 'result'])->name('quiz.result');
        Route::get('/analytics', [\App\Http\Controllers\StudentController::class, 'analytics'])->name('analytics');
    });

    // Messaging routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\MessageController::class, 'show'])->name('show');
        Route::post('/{id}/send', [App\Http\Controllers\MessageController::class, 'store'])->name('send');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Area
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/analytics/students', [AnalyticsController::class, 'students'])->name('analytics.students');
    Route::get('/analytics/students/{id}', [AnalyticsController::class, 'studentProfile'])->name('analytics.student_profile');
    Route::post('/analytics/students/{id}/reset-quizzes', [AnalyticsController::class, 'resetQuizzes'])->name('analytics.student_reset_quizzes');
    Route::delete('/analytics/results/{id}', [AnalyticsController::class, 'deleteResult'])->name('analytics.delete_result');
    Route::post('/maintenance/toggle', [AdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('schools', SchoolController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('academic_years', AcademicYearController::class);
    Route::post('academic_years/{id}/toggle', [AcademicYearController::class, 'toggleActive'])->name('academic_years.toggle');
    Route::resource('groups', GroupController::class);
    Route::post('groups/{id}/import', [GroupController::class, 'importStudents'])->name('groups.import');
    Route::resource('users', UserController::class);
    Route::resource('students', AdminStudentController::class);
    Route::resource('chapters', ChapterController::class);
    Route::get('courses/{courseId}/chapters-for-parent', [ChapterController::class, 'getChaptersForParent'])->name('chapters.for_parent');
    Route::delete('pdfs/{pdf}', [ChapterController::class, 'deletePdf'])->name('pdfs.delete');
    Route::post('chapters/{chapter}/reorder-pdfs', [ChapterController::class, 'reorderPdfs'])->name('chapters.reorder_pdfs');
    Route::get('progress/{group}', [\App\Http\Controllers\Admin\StudentProgressController::class, 'index'])->name('progress.index');
    Route::get('progress/student/{student}', [\App\Http\Controllers\Admin\StudentProgressController::class, 'show'])->name('progress.show');
    Route::resource('quizzes', QuizController::class);
    Route::get('quizzes/{quiz}/questions', [QuestionController::class, 'index'])->name('quizzes.questions.index');
    Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('quizzes.questions.store');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
});

require __DIR__.'/auth.php';
