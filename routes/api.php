<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\chatController;
use App\Http\Controllers\api\GoogleAuthController;
use App\Http\Controllers\api\CountryController;
use App\Http\Controllers\api\UserApiController;
use App\Http\Controllers\api\InquirieApiController;
use App\Http\Controllers\api\GroupApiController;
use App\Http\Controllers\api\LmsQuestionApiController;
use App\Http\Controllers\api\PostApiController;
use App\Http\Controllers\api\FollowApiController;
use App\Http\Controllers\api\AdvertisementApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
// routes/api.php
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/auth/redirect/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/callback/google', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/text', [GoogleAuthController::class, 'text']);
Route::get('/auth/facebook/redirect', [GoogleAuthController::class, 'redirectToFacebook']);
Route::get('/auth/facebook/callback', [GoogleAuthController::class, 'handleFacebookCallback']);

//usercontriller
Route::post('/get_user_detail',[UserApiController::class,'get_user_detail'])->middleware('auth:sanctum');
Route::post('/get_user_list',[UserApiController::class,'user_list'])->middleware('auth:sanctum');
Route::post('/update_user_details',[UserApiController::class,'update_user_details'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversation/{receiver_id}', [chatController::class, 'getOrCreateConversation']);
    Route::post('/send-message', [chatController::class, 'sendMessage']);
    Route::get('/messages/{conversation_id}', [chatController::class, 'getMessages']);
    Route::post('/get_chat_list', [chatController::class, 'get_chat_list']);
    Route::post('/generate-agora-token', [chatController::class, 'generateAgoraToken']);
    Route::post('/agora/end-call', [chatController::class, 'endCall']);
    Route::get('/agora/history', [chatController::class, 'callHistory']);
});

//CountryController
Route::get('/country',[CountryController::class,'contry']);
Route::get('/language',[CountryController::class,'language']);
Route::post('/inquirie-add',[InquirieApiController::class,'store']);
Route::post('/category-list',[UserApiController::class,'category_list']);
Route::get('/course-list',[UserApiController::class,'course_list']);

// GroupApiController
Route::post('/group-list',[GroupApiController::class,'group_list']);
Route::post('/user-group-add',[GroupApiController::class,'user_group_add']);
Route::post('/user-group-remove',[GroupApiController::class,'user_group_remove']);

// LmsQuestionApiController
Route::post('/lms-question-list',[LmsQuestionApiController::class,'lms_question_list']);
Route::post('/lms-question-submit',[LmsQuestionApiController::class,'lms_question_submit']);

// POST API
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostApiController::class, 'index']);
    Route::post('/posts/store', [PostApiController::class, 'store']);
    Route::post('/posts/update', [PostApiController::class, 'update']);
    Route::post('/posts/delete', [PostApiController::class, 'destroy']);

    Route::post('/posts/feedPage', [PostApiController::class, 'feedPage']);
    Route::post('/posts/like', [PostApiController::class, 'like']);
    Route::post('/posts/comment', [PostApiController::class, 'comment']);
    Route::post('/posts/share', [PostApiController::class, 'share']);
    Route::post('/posts/unlike', [PostApiController::class, 'unlike']);
    Route::post('/posts/comments/delete', [PostApiController::class, 'deleteComment']);
    Route::post('/posts/translate-caption', [PostApiController::class, 'translate']);
    Route::post('/posts/show-lik-user', [PostApiController::class, 'showLikeUser']);
    Route::post('/posts/show-comment-user', [PostApiController::class, 'showCommentUser']);
    Route::post('/posts/single-post-details', [PostApiController::class, 'postDetail']);

    Route::post('/posts/report_submit', [PostApiController::class, 'PostReportSubmit']);
    Route::post('/posts/block', [PostApiController::class, 'BlockPostContent']);
});

// P
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ads/get', [AdvertisementApiController::class, 'ads_get']);
    Route::post('/ads/clicks', [AdvertisementApiController::class, 'ads_click']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/follow', [FollowApiController::class, 'follow']);
    Route::post('/unfollow', [FollowApiController::class, 'unfollow']);
    Route::get('/followers', [FollowApiController::class, 'followers']);
    Route::get('/followings', [FollowApiController::class, 'followings']);
    Route::post('/follow-back', [FollowApiController::class, 'followBack']);
});




