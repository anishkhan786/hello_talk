<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\chatController;
use App\Http\Controllers\api\GoogleAuthController;
use App\Http\Controllers\api\ContryController;
use App\Http\Controllers\api\UserApiController;
use App\Http\Controllers\api\InquirieApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// routes/api.php
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

Route::get('/auth/redirect/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/callback/google', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/text', [GoogleAuthController::class, 'text']);
Route::get('/auth/facebook/redirect', [GoogleAuthController::class, 'redirectToFacebook']);
Route::get('/auth/facebook/callback', [GoogleAuthController::class, 'handleFacebookCallback']);

//usercontriller
Route::get('/get_user_detail',[UserApiController::class,'get_user_detail'])->middleware('auth:sanctum');
Route::get('/get_user_list',[UserApiController::class,'user_list'])->middleware('auth:sanctum');
Route::post('/update_user_details',[UserApiController::class,'update_user_details'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversation/{receiver_id}', [chatController::class, 'getOrCreateConversation']);
    Route::post('/send-message', [chatController::class, 'sendMessage']);
    Route::get('/messages/{conversation_id}', [chatController::class, 'getMessages']);
    Route::post('/get_chat_list', [chatController::class, 'get_chat_list']);
});

//contrycontroller
Route::get('/contry',[ContryController::class,'contry']);
Route::get('/language',[ContryController::class,'language']);
Route::post('/inquirie-add',[InquirieApiController::class,'store']);
Route::post('/category-list',[UserApiController::class,'category_list']);
Route::get('/course-list',[UserApiController::class,'course_list']);



