<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class usercontroller extends Controller
{
    public function get_user_detail()
    {
        $id = Auth::user()->id;
        $data = User::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        return response()->json([
            'message' => 'User Details get successfully',
            'user'    => $data,
            'avatar_url' => asset('storage/' . $data->avatar),
        ]);
    }

    public function update_user_details(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // $validated = $request->validate([
        //     'name'     => 'sometimes|string|max:255',
        //     'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        //     'password' => 'sometimes|string|min:6|confirmed',
        //     'native_language'   => 'sometimes|string',
        //     'learning_language' => 'sometimes|string',
        // ]);

        // Update the fields only if they are present in the request
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('native_language')) {
            $user->native_language = $request->native_language;
        }

        if ($request->has('learning_language')) {
            $user->learning_language = $request->learning_language;
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->has('country')) {
            $user->country = $request->country;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('dob')) {
            $user->dob = $request->dob;
        }
        if ($request->has('here_about')) {
            $user->here_about = $request->here_about;
        }
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if needed
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'User details updated successfully',
            'user'    => $user,
            'status'    => 200,
        ]);
    }

    public function user_list(){
        $data = User::where('type','user')->where('is_active','1')->get();
        return response()->json([
            'message' => 'User details updated successfully',
            'user_list'    => $data,
            'status'    => 200,
        ]);
    }
}
