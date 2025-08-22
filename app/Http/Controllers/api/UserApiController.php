<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Category;
use App\Models\learningLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
// firebase 

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class UserApiController extends Controller
{

    public function get_user_detail(Request $request)
    {
        $id = $request->user_id;
        $data = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->withCount(['followers', 'favorites', 'posts',])->where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        $data['avatar'] = asset('storage/app/public/' . $data->avatar);
        $data['profession'] = stringConvertToArray($data->profession);
        $data['personality'] = stringConvertToArray($data->personality);
        $data['interest'] = stringConvertToArray($data->interest);


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


        // Update the fields only if they are present in the request
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('native_language')) {
            $user->native_language = $request->native_language;
        }

        if ($request->has('learning_language')) {
            $user->learning_language = $request->learning_language;
            $user->translate_language = $request->learning_language;
        }

        if ($request->has('know_language')) {
            $user->know_language = $request->know_language;
        }
        if ($request->has('country')) {
            $user->country = $request->country;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }

        if ($request->has('source')) {
            $user->source = $request->source;
        }

        if ($request->has('dob')) {
            $user->dob = $request->dob;
        }

        if ($request->has('introduction')) {
            $user->introduction = $request->introduction;
        } 
        
        if ($request->has('profession')) {
            $user->profession = implode(", ", $request->profession);
        } 
        
        if ($request->has('personality')) {
            $user->personality = implode(", ", $request->personality);
        } 
        
        if ($request->has('interest')) {
            $user->interest = implode(", ", $request->interest);
        }
       
        // Handle avatar upload


        if ($request->hasFile('avatar')) {
            // Delete old image if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $mediaPath = $request->file('avatar')->store('avatar', 'public');
            $user['avatar'] = $mediaPath;
        }

        $user->save();
        $user['avatar'] = asset('storage/app/public/' . $user->avatar);
        $user['profession'] = stringConvertToArray($user->profession);
        $user['personality'] = stringConvertToArray($user->personality);
        $user['interest'] = stringConvertToArray($user->interest);


        return response()->json([
            'message' => 'User details updated successfully',
            'user'    => $user,
            'status'    => 200,
        ]);
    }

    public function user_list(Request $request)
{
    $user = auth()->user();
    $users = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')
            ->where('type', 'user')
            ->where('id', '!=', $user->id)
            ->where('is_active', '1');

        if ($request->has('gender') && !empty($request->gender)) {
            $users = $users->where('gender', $request->gender);
        }

        if ($request->has('for_you') && !empty($request->for_you)) {
            $users = $users->where('native_language', $request->for_you);
        }

        if ($request->has('name') && !empty($request->name)) {
            $users = $users->where('name', 'like', '%' . $request->name . '%');
        }

        $users = $users->get();

    $data = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'introduction' => $user->introduction??'A new member just joined',
            'gender' => $user->gender,
            'avatar' => $user->avatar ? asset('storage/app/public/' . $user->avatar) : null,
            'countryDetail' => $user->countryDetail,
            'nativeLanguageDetail' => $user->nativeLanguageDetail,
            'learningLanguageDetail' => $user->learningLanguageDetail,
            'knowLanguageDetail' => $user->knowLanguageDetail,


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
                $response = ['message'=> 'success.','status'=>true,'data' => $response];
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

        public function learning_level(Request $request)
        {
            try {
                $response = learningLevel::select('id','name')->get();
                if(!empty($response)){
                    $response = ['message'=> 'success.','status'=>true,'data' => $response];
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

    public function application_setting(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }


            // Update the fields only if they are present in the request
            if ($request->has('data_deletion')) {
                $user->data_deletion = $request->data_deletion;
            }

            if ($request->has('multimedia')) {
                $user->multimedia = $request->multimedia;
            }

            if ($request->has('translate_language')) {
                // learning language hee translate language  hai 
                $user->translate_language = $request->translate_language;
                $user->learning_language = $request->translate_language;
            }

             if ($request->has('interface_language')) {
                $user->interface_language = $request->interface_language;
            }

             if ($request->has('online_status')) {
                $user->online_status = $request->online_status;
            }

             if ($request->has('location')) {
                $user->location = $request->location;
            }

             if ($request->has('notification')) {
                $user->notification = $request->notification;
            }
            $user->save();

            $user['avatar'] = asset('storage/app/public/' . $user->avatar);
            $user['profession'] = stringConvertToArray($user->profession);
            $user['personality'] = stringConvertToArray($user->personality);
            $user['interest'] = stringConvertToArray($user->interest);


            return response()->json([
                'message' => 'User details updated successfully',
                'user'    => $user,
                'status'    => 200,
            ]);

        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }


public function notification_send(Request $request)
    {
        $messaging = app('firebase.messaging');

       $message = CloudMessage::withTarget('token', $request->device_token)
        ->withNotification(Notification::create(
            'New Message',
            'Hello from Firebase with extra data!'
        ))
        ->withData([
            'screen' => 'ChatScreen',   // example key
            'custom_key' => 'notification_key_user'
        ]);

        try {
            $response = $messaging->send($message);
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully!',
                'response' => $response
            ]);
        } catch (MessagingException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Messaging error',
                'error' => $e->getMessage()
            ], 400);
        } catch (FirebaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase error',
                'error' => $e->getMessage()
            ], 400);
        }
    }

}
