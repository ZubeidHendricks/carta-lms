<?php

use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\JobCircularController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('customize');
Route::get('demo/{slug}', [HomeController::class, 'demo'])->name('home.demo')->middleware('customize');
Route::get('job-circulars/{job_circular}', [JobCircularController::class, 'show'])->name('job-circulars.show');

// course page
Route::controller(CourseController::class)->group(function () {
    Route::get('courses/{category}/{category_child?}', 'category_courses')->name('category.courses');
    Route::get('courses/details/{slug}/{id}', 'show')->name('course.details');
});

Route::get('instructors/{instructor}', [InstructorController::class, 'show'])->name('instructors.show');
Route::resource('subscribes', SubscribeController::class)->only(['index', 'store']);

// Student Learning Dashboard Routes
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Student\LearningDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('progress', [\App\Http\Controllers\Student\LearningDashboardController::class, 'progress'])->name('progress');
    Route::get('badges', [\App\Http\Controllers\Student\LearningDashboardController::class, 'badges'])->name('badges');
    Route::get('leaderboard', [\App\Http\Controllers\Student\LearningDashboardController::class, 'leaderboard'])->name('leaderboard');
    Route::get('ai-tutor', [\App\Http\Controllers\Student\LearningDashboardController::class, 'aiTutor'])->name('ai-tutor');
});

// AI Lecturer API Routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::post('ai-lecturers/ask', [\App\Http\Controllers\AI\AILecturerController::class, 'ask'])->name('api.ai-lecturers.ask');
});
