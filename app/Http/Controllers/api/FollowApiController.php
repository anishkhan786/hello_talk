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
use DB;
class FollowApiController extends Controller
{
     // Follow a user
    public function follow(Request $request)
    {
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

        return response()->json(['message' => 'User followed successfully.'],200);
    }

     // Unfollow a user
    public function unfollow(Request $request)
    {
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

        return response()->json(['message' => 'User unfollowed successfully.'],200);
    }

        // List of who I follow
    public function followings(Request $request)
    {
        $users = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->whereIn('id', function ($query) {
            $query->select('following_id')
                ->from('follows')
                ->where('follower_id', Auth::id());
        })->get();

         return response()->json([
                'success' => true,
                'data' => $users
            ],200);

    }

      // List of who follows me
    public function followers()
    {
        $users = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->whereIn('id', function ($query) {
            $query->select('follower_id')
                ->from('follows')
                ->where('following_id', Auth::id());
        })->get();

        if(!empty( $users)){
            return response()->json([
                            'success' => true,
                            'data' => $users
                        ],200);
        } else {
            return response()->json([
                'success' => true,
                'data' => array()
            ],422);
        }
        
    }

      // Check if someone follows me, then I follow back
    public function followBack(Request $request)
    {
        $request->validate(['follower_id' => 'required|exists:users,id']);

        $isFollower = Follow::where([
            'follower_id' => $request->follower_id,
            'following_id' => Auth::id()
        ])->exists();

        if (!$isFollower) {
            return response()->json(['message' => 'User does not follow you.'], 422);
        }

        Follow::firstOrCreate([
            'follower_id' => Auth::id(),
            'following_id' => $request->follower_id,
        ]);
        
         AppNotification::firstOrCreate([
                'user_id' => $request->following_id,
                'type' => 'message',
                'title' => 'Followed You Back',
                'body' => ' You’ve got a follow back! Time to connect',
                'channel' => 'in_app',
                'data'=>Auth::id()
            ]);

        return response()->json(['message' => 'Followed back successfully.'],200);
    }

}