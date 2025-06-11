<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\contryController;
use App\Http\Controllers\languageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InquirieController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return 'Cache cleared!';
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created!';
});

Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');


Route::get('/', function () { 
    return redirect('/admin/dashboard');     
})->middleware('auth');


//admincontroller
Route::match(['get', 'post'], '/admin/login', [adminController::class, 'login'])->name('admin.login');

Route::group(['middleware' => 'auth'], function () {

Route::get('/admin/dashboard', [adminController::class, 'dashboard'])->name('descbord')->middleware('auth');
Route::get('admin/logout',[adminController::class,'logout'])->name('admin.logout')->middleware('auth');

//usercontroller
Route::get('user/index',[UserController::class,'index'])->name('user.index');
Route::get('user/delete/{id}',[UserController::class,'delete'])->name('user.delete');
Route::get('user/edit/{id}',[UserController::class,'edit'])->name('user.edit');
Route::post('user/update',[UserController::class,'update'])->name('user.update');

//contrycontroller
Route::get('contry/index',[contryController::class,'index'])->name('contry.index');
Route::get('contry/add',[contryController::class,'add'])->name('contry.add');
Route::get('contry/edit/{id}',[contryController::class,'edit'])->name('contry.edit');
Route::get('contry/delete/{id}',[contryController::class,'delete'])->name('contry.delete');
Route::post('contry/store',[contryController::class,'store'])->name('contry.store');
Route::post('contry/update',[contryController::class,'update'])->name('contry.update');

//languagecontroller
Route::get('language/index',[languageController::class,'index'])->name('language.index');
Route::get('language/add',[languageController::class,'add'])->name('language.add');
Route::get('language/edit/{id}',[languageController::class,'edit'])->name('language.edit');
Route::get('language/delete/{id}',[languageController::class,'delete'])->name('language.delete');
Route::post('language/store',[languageController::class,'store'])->name('language.store');
Route::post('language/update',[languageController::class,'update'])->name('language.update');

//Coursecontroller
Route::get('course/index',[CourseController::class,'index'])->name('course.index');
Route::get('course/add',[CourseController::class,'add'])->name('course.add');
Route::get('course/edit/{id}',[CourseController::class,'edit'])->name('course.edit');
Route::get('course/delete/{id}',[CourseController::class,'delete'])->name('course.delete');
Route::post('course/store',[CourseController::class,'store'])->name('course.store');
Route::post('course/update',[CourseController::class,'update'])->name('course.update');

//Categorycontroller
Route::controller(CategoryController::class)->group(function() {
        Route::get('/category', 'index')->name('category');
        Route::get('/category/new', 'create')->name('category.add');
        Route::post('/category/new', 'store')->name('category-new.store');
        Route::get('/category/edit/{id}', 'edit')->name('category-edit');
        Route::post('/category/edit/{id}', 'update')->name('category-edit.update');
        Route::get('/category/delete/{id}', 'destroy')->name('category-destroy');
    });

// InquirieController
Route::get('inquirie',[InquirieController::class,'index'])->name('inquirie.index');


});