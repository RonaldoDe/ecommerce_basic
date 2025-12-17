<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('home')->middleware('auth');

Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin')->middleware('auth');

// AJUSTES
Route::get('/admin/settings', [App\Http\Controllers\AjusteController::class, 'index'])->name('admin.settings.index')->middleware('auth');
Route::post('/admin/settings/create', [App\Http\Controllers\AjusteController::class, 'store'])->name('admin.settings.store')->middleware('auth');


// ROLES
Route::get('/admin/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index')->middleware('auth');
Route::get('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('admin.roles.create')->middleware('auth');
Route::post('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store')->middleware('auth');
Route::get('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'show'])->name('admin.roles.show')->middleware('auth');
Route::get('/admin/roles/{id}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('admin.roles.edit')->middleware('auth');
Route::put('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update')->middleware('auth');
Route::delete('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('auth');


// USUARIOS
Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index')->middleware('auth');
Route::get('/admin/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('admin.users.create')->middleware('auth');
Route::post('/admin/users/create', [App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store')->middleware('auth');
Route::get('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('admin.users.show')->middleware('auth');
Route::get('/admin/users/{id}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('admin.users.edit')->middleware('auth');
Route::put('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update')->middleware('auth');
Route::delete('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy')->middleware('auth');
Route::post('/admin/users/{id}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('admin.users.restore')->middleware('auth');

// CATEGORIAS
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {Route::resource('categories', CategoryController::class);});

// PRODUCTOS
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {Route::resource('products', ProductController::class);});
Route::get('/admin/products/{product}/images', [App\Http\Controllers\ProductController::class, 'images'])->name('admin.products.images')->middleware('auth');
Route::post('/admin/products/{id}/upload_image', [App\Http\Controllers\ProductController::class, 'uploadImage'])->name('admin.products.upload_image')->middleware('auth');
Route::delete('/admin/products/image/{id}/remove_image', [App\Http\Controllers\ProductController::class, 'removeImage'])->name('admin.products.remove_image')->middleware('auth');

// WEB
Route::get('/', [App\Http\Controllers\WebController::class, 'index'])->name('web.index');
