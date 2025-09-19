<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\contryController;
use App\Http\Controllers\languageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InquirieController;
use App\Http\Controllers\TroopersTogetherController;
use App\Http\Controllers\LmsQuestionController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\MarketingItemController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTopicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionPrivilegeController;
use App\Http\Controllers\SubscriptionPlanPrivilegeController;
use App\Http\Controllers\CurrencyController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function() {
        Route::get('privacy-policy', 'privacy_policy_page');

    });


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

//TroopersTogetherController
Route::controller(TroopersTogetherController::class)->group(function() {
        Route::get('/trooper-together', 'index')->name('trooper-together');
        Route::get('/group-member-view/{id}', 'group_member')->name('group-member-view');
        Route::get('/group-member/delete/{id}', 'group_member_destroy')->name('group-member-destroy');
        Route::get('/group-member/unblock/{id}', 'group_member_unblock')->name('group-member-unblock');


        Route::get('/trooper-together/new', 'create')->name('trooper-together.add');
        Route::post('/trooper-together/new', 'store')->name('trooper-together.store');
        Route::get('/trooper-together/edit/{id}', 'edit')->name('trooper-together-edit');
        Route::post('/trooper-together/edit/{id}', 'update')->name('trooper-together-edit.update');
        Route::get('/trooper-together/delete/{id}', 'destroy')->name('trooper-together-destroy');
    });


//LmsQuestionController
Route::controller(LmsQuestionController::class)->group(function() {
        Route::get('/LMSQuestion', 'index')->name('LMSQuestion');
        Route::get('/LMSQuestion/new', 'create')->name('LMSQuestion.add');
        Route::post('/LMSQuestion/new', 'store')->name('LMSQuestion.store');
        Route::get('/LMSQuestion/edit/{id}', 'edit')->name('LMSQuestion-edit');
        Route::post('/LMSQuestion/edit/{id}', 'update')->name('LMSQuestion-edit.update');
        Route::get('/LMSQuestion/delete/{id}', 'destroy')->name('LMSQuestion-destroy');

        Route::get('/get-categories-by-course/{course_id}', 'getCategories');
    });

    //post
Route::controller(PostsController::class)->group(function() {
        Route::get('/posts/view', 'index')->name('post.view');
        Route::get('/posts/report/view', 'post_report')->name('post.report.view');

        Route::get('/posts/delete/{id}', 'delete')->name('posts-destroy');
        Route::get('/course-demo-details', 'courseDemoDetails')->name('course-demo-details');

    });

    //marketing
    Route::controller(MarketingItemController::class)->group(function() {
        Route::get('/marketing', 'index')->name('marketing');
        Route::get('/marketing/new', 'create')->name('marketing.add');
        Route::post('/marketing/new', 'store')->name('marketing.store');
        Route::get('/marketing/edit/{id}', 'edit')->name('marketing-edit');
        Route::post('/marketing/edit/{id}', 'update')->name('marketing-edit.update');
        Route::get('/marketing/delete/{id}', 'destroy')->name('marketing-destroy');

    });

    //questions
    Route::controller(QuestionController::class)->group(function() {
        Route::get('/questions', 'index')->name('questions');
        Route::get('/questions/new', 'create')->name('questions.add');
        Route::post('/questions/new', 'store')->name('questions.store');
        Route::get('/questions/edit/{id}', 'edit')->name('questions.edit');
        Route::get('/questions/show/{id}', 'show')->name('questions.show');
        Route::post('/questions/edit/{id}', 'update')->name('questions-edit.update');
        Route::get('/questions/delete/{id}', 'destroy')->name('questions.destroy');

    });

     //marketing
    Route::controller(QuestionTopicController::class)->group(function() {
        Route::get('/question_topic', 'index')->name('question_topic');
        Route::get('/question_topic/new', 'create')->name('question_topic.add');
        Route::post('/question_topic/new', 'store')->name('question_topic.store');
        Route::get('/question_topic/edit/{id}', 'edit')->name('question_topic-edit');
        Route::post('/question_topic/edit/{id}', 'update')->name('question_topic-edit.update');
        Route::get('/question_topic/delete/{id}', 'delete')->name('question_topic-destroy');
    });

        Route::get('subscription_plans', [SubscriptionPlanController::class, 'index'])->name('subscription_plans.index');
        Route::get('subscription_plans/create', [SubscriptionPlanController::class, 'create'])->name('subscription_plans.create');
        Route::post('subscription_plans', [SubscriptionPlanController::class, 'store'])->name('subscription_plans.store');
        Route::get('subscription_plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'show'])->name('subscription_plans.show');
        Route::get('subscription_plans/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('subscription_plans.edit');
        Route::post('subscription_plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('subscription_plans.update');
        Route::delete('subscription_plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'destroy'])->name('subscription_plans.destroy');

        Route::get('subscription_privileges', [SubscriptionPrivilegeController::class, 'index'])->name('subscription_privileges.index');
        Route::get('subscription_privileges/create', [SubscriptionPrivilegeController::class, 'create'])->name('subscription_privileges.create');
        Route::post('subscription_privileges', [SubscriptionPrivilegeController::class, 'store'])->name('subscription_privileges.store');
        Route::get('subscription_privileges/{subscriptionPrivilege}/edit', [SubscriptionPrivilegeController::class, 'edit'])->name('subscription_privileges.edit');
        Route::post('subscription_privileges/{subscriptionPrivilege}', [SubscriptionPrivilegeController::class, 'update'])->name('subscription_privileges.update');
        Route::delete('subscription_privileges/{subscriptionPrivilege}', [SubscriptionPrivilegeController::class, 'destroy'])->name('subscription_privileges.destroy');

        Route::get('subscription_plan_privileges', [SubscriptionPlanPrivilegeController::class, 'index'])->name('subscription_plan_privileges.index');
        Route::get('subscription_plan_privileges/create', [SubscriptionPlanPrivilegeController::class, 'create'])->name('subscription_plan_privileges.create');
        Route::post('subscription_plan_privileges', [SubscriptionPlanPrivilegeController::class, 'store'])->name('subscription_plan_privileges.store');
        Route::get('subscription_plan_privileges/{subscriptionPrivilege}/edit', [SubscriptionPlanPrivilegeController::class, 'edit'])->name('subscription_plan_privileges.edit');
        Route::post('subscription_plan_privileges/{subscriptionPrivilege}', [SubscriptionPlanPrivilegeController::class, 'update'])->name('subscription_plan_privileges.update');
        Route::get('/question_topic/delete/{id}', [SubscriptionPlanPrivilegeController::class,'delete'])->name('subscription_plan_privileges.destroy');

        // List all currencies
        Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');
        Route::get('currencies/create', [CurrencyController::class, 'create'])->name('currencies.create');
        Route::post('currencies', [CurrencyController::class, 'store'])->name('currencies.store');
        Route::get('currencies/{currency}', [CurrencyController::class, 'show'])->name('currencies.show');
        Route::get('currencies/{currency}/edit', [CurrencyController::class, 'edit'])->name('currencies.edit');
        Route::post('currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');
        Route::delete('currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');

});