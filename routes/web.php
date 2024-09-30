<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();
    return view('components.landing.landing', compact('user'));
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/edit_profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/edit_profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user?}', [ProfileController::class, 'display'])->name('profile.display');

    Route::resource('tasks', TaskController::class)->only(['index', 'create', 'store', 'show']);
    Route::put('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::put('/tasks/{task}/refuse', [TaskController::class, 'refuse'])->name('tasks.refuse');
    Route::put('/tasks/{task}/respond', [TaskController::class, 'respond'])->name('tasks.respond');
    Route::put('/tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');
    Route::get('/my_tasks', [TaskController::class, 'myTasks'])->name('tasks.my_tasks');

    Route::get('/responses/{response}/accept', [ResponseController::class, 'accept'])->name('responses.accept');
    Route::get('/responses/{response}/reject', [ResponseController::class, 'reject'])->name('responses.reject');
});

require __DIR__.'/auth.php';
