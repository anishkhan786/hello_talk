<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd('test');
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'login_with' =>'required',
                'native_language' => 'required|string',
                'learning_language' => 'required|string',
                'know_language' => 'required|string',
                'dob' =>'required',
                'gender' =>'required',

            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'status' => false
            ]);
        }
         
            if($request->login_with == 'mobile'){
                $user = User::where('social_login_type', $request->login_with)->where('phone_no', $request->phone_no)->first();
            } else{
                $user = User::where('social_login_type', $request->login_with)->where('email', $request->email)->first();
            } 

        if (!empty($user)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            $user['avatar'] = asset('storage/app/public/' . $user->avatar);
            return response(['user_data'=>$user,"message"=>'User already exists.','status'=>true,'token'=>$token],200);
        }

        $request_data = [
            'name'     =>  $request->name,
            'native_language'  => $request->native_language,
            'learning_language'  => $request->learning_language,
            'social_login_type'  => $request->login_with,
            'know_language'  => $request->know_language,
            'dob'  => $request->dob,
            'gender'  => $request->gender,
            'country'=>$request->country,
            'source'=>$request->source,
            'introduction'=>'A new member just joined',
        ];

        if($request->login_with == 'mobile'){
            if(empty($request->phone_no)){
                return response(["message" =>'The phone number field is required.', 'status'=>FALSE] ,422);
            }

            $request_data['phone_no'] = $request->phone_no;
         } else{
            if(empty($request->email)){
                return response(["message" =>'The email field is required.','status'=>FALSE], 422);
            }
             $request_data['email'] = $request->email;
         }

         if ($request->hasFile('avatar')) {
                $mediaPath = $request->file('avatar')->store('avatar', 'public');
                $request_data['avatar'] = $mediaPath;

            }

        $user = User::create( $request_data);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user['avatar'] = asset('storage/app/public/' . $user->avatar);
         return response(['user_data'=>$user, "message"=>'User registered successfully','status'=>true,'token'=>$token],200);
    }

    public function login(Request $request)
    {
        try{
            $request->validate([
                'login_id'    => 'required',
                'login_with' => 'required|string',
            ]);
            
        }catch(ValidationException $e){
             return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ]);
        }

         if($request->login_with == 'mobile'){
            $user = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->where('social_login_type', $request->login_with)->where('phone_no', $request->login_id)->first();
         } else{
            $user = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->where('social_login_type', $request->login_with)->where('email', $request->login_id)->first();
         } 

        if (empty($user)) {
            $response = ["message" =>'User does not exist','status'=>FALSE];
            return response([$response, 422,'status'=>FALSE]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user['profession'] = stringConvertToArray($user->profession);
        $user['personality'] = stringConvertToArray($user->personality);
        $user['interest'] = stringConvertToArray($user->interest);
        $user['avatar'] = asset('storage/app/public/' . $user->avatar);
        return response(['user_data'=>$user, 200,"message"=>'User login successfull!','status'=>true,'token'=>$token]);
    }

    public function logout(Request $request)
{
    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ]);
}
}
