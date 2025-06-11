<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
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
            'avatar_url' => asset($data->avatar),
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
            // Delete old image if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $file = $request->file('avatar');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/\s+/', '_', $originalName); // replace spaces with underscores
            $extension = $file->getClientOriginalExtension();

            $filename = time() . '_' . $sanitizedName . '.' . $extension;

            // Ensure directory exists
            if (!file_exists(public_path('avatars'))) {
                mkdir(public_path('avatars'), 0777, true);
            }

            $file->move(public_path('avatars'), $filename);
            $user->avatar = 'avatars/' . $filename;
        }

        $user->save();

        return response()->json([
            'message' => 'User details updated successfully',
            'user'    => $user,
            'status'    => 200,
        ]);
    }

    public function user_list()
{
    $users = User::where('type', 'user')->where('is_active', '1')->get();

    $data = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset($user->avatar) : null,
            // add any other fields you need
        ];
    });

    return response()->json([
        'message' => 'User details fetched successfully',
        'user_list' => $data,
        'status' => 200,
    ]);
}


    public function category_list(Request $request)
    {
        try {
            $course_id = $request->has('course_id')?$request->course_id:'';
            $response = Category::select('id','name')->where('course_id',$course_id)->get();
            if(!empty($response)){
                $response = ['message'=> 'success.','status'=>true,'data' => $response,];
                return response($response, 200);
            } else {
                $response = ["message" => "Category does not exit",'status'=>FALSE];
                return response($response, 422);
            }
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }

        public function course_list(Request $request)
        {
            try {
                $response = Course::select('id','name')->get();
                if(!empty($response)){
                    $response = ['message'=> 'success.','status'=>true,'data' => $response,];
                    return response($response, 200);
                } else {
                    $response = ["message" => "Course does not exit",'status'=>FALSE];
                    return response($response, 422);
                }
            } catch(\Exception $e)  {
                $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
                return response($response, 400);
            }
        }

}
