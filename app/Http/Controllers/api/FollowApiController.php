<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\HelperLanguage;
use DB;
class FollowApiController extends Controller
{
     // Follow a user
    public function follow(Request $request)
    {
        try {
            $request->validate(['following_id' => 'required|exists:users,id']);

            $follow = Follow::firstOrCreate([
                'follower_id' => Auth::id(),
                'following_id' => $request->following_id,
            ]);

            $user = User::where('id', Auth::id())->first();

                AppNotification::firstOrCreate([
                    'user_id' => $request->following_id,
                    'type' => 'message',
                    'title' => 'New Follower',
                    'body' => $user->name.' just followed you.',
                    'channel' => 'in_app',
                    'data'=>Auth::id()
                ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_followed_successfully') ??'User followed successfully.'],200);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

     // Unfollow a user
    public function unfollow(Request $request)
    {
        try {
            $request->validate(['following_id' => 'required|exists:users,id']);

            Follow::where([
                'follower_id' => Auth::id(),
                'following_id' => $request->following_id,
            ])->delete();

            AppNotification::where('user_id',  $request->following_id)
                                ->where('type', 'message')
                                ->where('channel', 'in_app')
                                ->where('data', Auth::id())
                                ->delete();

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_unfollowed_successfully') ??'User unfollowed successfully.'],200);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

        // List of who I follow
    public function followings(Request $request)
    {
        try {
            $users = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->whereIn('id', function ($query) {
                $query->select('following_id')
                    ->from('follows')
                    ->where('follower_id', Auth::id());
            })->get();

            return response()->json([
                    'success' => true,
                     'base_url'=>Storage::disk('s3')->url(''),
                    'data' => $users
                ],200);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }

    }

      // List of who follows me
    public function followers()
    {
        try {
            $users = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')
            ->whereIn('id', function ($query) {
                $query->select('follower_id')
                    ->from('follows')
                    ->where('following_id', Auth::id());
            })->get();

            if(!empty($users)){

                $users->map(function($user){
                    $today_date = now();
                    $subscription_res = UserSubscriptions::with('plan')
                                        ->where('end_date', '>=', $today_date)
                                        ->where('user_id', $user->id)
                                        ->where('payment_status', 'success')
                                        ->where('status', 'active')
                                        ->first();
                    if(!empty($subscription_res)){
                        $subscription_plan = true;
                    } else {
                        $subscription_plan = false;
                    }

                    $user->subscription_plan = $subscription_plan;
                    return $user;
                });

                return response()->json([
                                'success' => true,
                                 'base_url'=>Storage::disk('s3')->url(''),
                                'data' => $users
                            ],200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => array()
                ],422);
            }
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
        
    }

      // Check if someone follows me, then I follow back
    public function followBack(Request $request)
    {
        try{
            $request->validate(['follower_id' => 'required|exists:users,id']);

            $isFollower = Follow::where([
                'follower_id' => $request->follower_id,
                'following_id' => Auth::id()
            ])->exists();

            if (!$isFollower) {
                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_does_not_follow_you') ??'User does not follow you.'], 422);
            }

            Follow::firstOrCreate([
                'follower_id' => Auth::id(),
                'following_id' => $request->follower_id,
            ]);
            $title = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_followed_you_back') ??'Followed You Back';
            $bodymsg = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_got_follow_back') ??'You have got a follow back! Time to connect';
            AppNotification::firstOrCreate([
                    'user_id' => $request->following_id,
                    'type' => 'message',
                    'title' => $title,
                    'body' =>  $bodymsg,
                    'channel' => 'in_app',
                    'data'=>Auth::id()
                ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ??'Followed back successfully.'],200);
        } catch (Exception $e) {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

}