<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('users/{id}', function($id) {

})->where('id', '[0-9]+');

Route::get('users/{username}', function($useranme) {

})->where('username', '[A-Za-z]+');
Route::get('posts/{id}/{slug}', function($id, $slug) {

})->where(['id' => '[0-9]+', 'slug' => '[A-Za-a]+']);

//Route::get('members/{id}', [MemberController::class, 'show']->name('members.show'));

Route::fallback(function () {
    // 라우트 매칭 실패 시 대체 라우트
});

Route::domain('{account}.myapp.com')->group(function () {
    Route::get('/', function ($account) {
        //
    });
    Route::get('users/{id}', function ($account, $id) {

    });
});

Route::get('/', [TaskController::class, 'index']);
Route::get('/tasks/create', [TaskController::class, 'create']);
Route::post('tasks', [TaskController::class, 'stroe']);

use App\Http\Controllers\TasksController;

Route::resource('tasks', TaskController::class);
?>
