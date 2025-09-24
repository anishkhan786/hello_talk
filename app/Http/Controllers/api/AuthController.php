<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Models\UserSubscriptions;
use App\Models\HelperLanguage;
use App\Models\AppNotification;
use Carbon\Carbon;
use App\Models\conversation;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       
        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'login_with'        => 'required',
                'native_language'   => 'required|string',
                'learning_language' => 'required|string',
                'know_language'     => 'required|string',
                'dob'               => 'required',
                'gender'            => 'required',
            ], [
                'name.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_name_required') ?? 'Name is required',
                'login_with.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_login_required') ?? 'Login method is required',
                'native_language.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_native_language_required') ?? 'Native language is required',
                'learning_language.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_learning_language_required') ?? 'Learning language is required',
                'know_language.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_know_language_required') ?? 'Known language is required',
                'dob.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_dob_required') ?? 'Date of birth is required',
                'gender.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_gender_required') ?? 'Gender is required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_validation_failed') ?? '11 Validation failed',
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
            $user['avatar'] = $user->avatar?Storage::disk('s3')->url($user->avatar):$user->avatar;
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
            'fcm_token'=>$request->has('fcm_token')?$request->fcm_token:'',
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

                $mediaPath = $request->file('avatar')->store('avatar', 's3');
                $request_data['avatar'] = $mediaPath;

            }

        $user = User::create( $request_data);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user['avatar'] = $user->avatar?Storage::disk('s3')->url($user->avatar):$user->avatar;
         return response([ 'user_data'=>$user, "message"=>'User registered successfully','status'=>true,'token'=>$token],200);
    }

    public function login(Request $request)
    {
        try{
            $request->validate([
                'login_id'    => 'required',
                'login_with' => 'required|string',
            ],
             [
                'login_id.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_login_id_required') ?? 'A valid login ID is required: Email, Phone Number, or Apple ID.',
                'login_with.required' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_login_required') ?? 'Login method is required',
            ]);
            
        }catch(ValidationException $e){
             return response()->json([
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_validation_failed')?? 'Validation failed',
                'errors' => $e->errors(),
            ]);
        }

         if($request->login_with == 'mobile'){
            $user = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->where('social_login_type', $request->login_with)->where('phone_no', $request->login_id)->first();
         } else{
            $user = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->where('social_login_type', $request->login_with)->where('email', $request->login_id)->first();
         } 

        if (empty($user)) {
            $response = ["message" => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_not_found') ?? 'No account found for the provided details.','status'=>FALSE];
            return response([$response, 422,'status'=>FALSE]);
        }

        if ($request->has('fcm_token') AND !empty($request->fcm_token)) {
                $user->update(['fcm_token' => $request->has('fcm_token')?$request->fcm_token:'']);
            }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user['profession'] = stringConvertToArray($user->profession);
        $user['personality'] = stringConvertToArray($user->personality);
        $user['interest'] = stringConvertToArray($user->interest);
        $user['avatar'] = $user->avatar?Storage::disk('s3')->url($user->avatar):$user->avatar;

        $today_date = now();
        $subscription_res = UserSubscriptions::with('plan')
                                ->where('end_date', '>=', $today_date)
                                ->where('user_id', $user->id)
                                ->where('payment_status', 'success')
                                ->where('status', 'active')
                                ->first();
           
        if(!empty($subscription_res)){
            $givenDate = Carbon::parse($subscription_res->end_date);
            $today = Carbon::now();
            $expire_days = (int) $today->diffInDays($givenDate); // sirf pure din
            if($expire_days == 2){
                 AppNotification::create([
                        'user_id' => $user->id,
                        'type' => 'message',
                        'title' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'subscription_title') ??"Subscription",
                        'body' =>  HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'subscription_expire_in_days') ??"The subscription will expire in 2 days",
                        'channel' => 'push',
                    ]);
            }
           
                    
            $subscription_plan = true;
            $subscription_details['start_date']=$subscription_res->start_date;
            $subscription_details['end_date']= $subscription_res->end_date;
            $subscription_details['plan'] =$subscription_res->plan; 

        } else {
          $subscription_details = array();
          $subscription_plan = false;
        }
        $today = Carbon::now();
        $startOfDay = $today->copy()->startOfDay();

        if($user->data_deletion == '1'){
            $conversation_ids = conversation::where(function($q) use ($startOfDay) {
                                            $q->where('user_one_chat_delete', '<=', $startOfDay)
                                            ->orWhereNull('user_one_chat_delete');
                                        })->where('user_one_id', $user->id)->pluck('id')->toArray();
            if(!empty($conversation_ids)){
                conversation::whereIn('id', $conversation_ids)->update(['user_one_chat_delete' => now()]);
            }

            $conversation_ids = conversation::where(function($q) use ($startOfDay) {
                                            $q->where('user_two_chat_delete', '<=', $startOfDay)
                                            ->orWhereNull('user_two_chat_delete');
                                        })->where('user_two_id', $user->id)->pluck('id')->toArray();
            if(!empty($conversation_ids)){
                conversation::whereIn('id', $conversation_ids)->update(['user_two_chat_delete' => now()]);
            }
        }

        if($user->data_deletion == '2'){

            $data_deletion_date = Carbon::parse($user->data_deletion_date);
            $today = Carbon::now();
            $chat_deletion_days = (int) $today->diffInDays($data_deletion_date); 

            if($chat_deletion_days >= 7){
               $conversation_ids = conversation::where(function($q) use ($startOfDay) {
                                            $q->where('user_one_chat_delete', '<=', $startOfDay)
                                            ->orWhereNull('user_one_chat_delete');
                                        })->where('user_one_id', $user->id)->pluck('id')->toArray();

                if(!empty($conversation_ids)){
                    conversation::whereIn('id', $conversation_ids)->update(['user_one_chat_delete' => now()]);
                }

               $conversation_ids = conversation::where(function($q) use ($startOfDay) {
                                            $q->where('user_two_chat_delete', '<=', $startOfDay)
                                            ->orWhereNull('user_two_chat_delete');
                                        })->where('user_two_id', $user->id)->pluck('id')->toArray();

                if(!empty($conversation_ids)){
                    conversation::whereIn('id', $conversation_ids)->update(['user_two_chat_delete' => now()]);
                }
                User::where('id', $user->id)->update(['data_deletion_date' => now()]);
            }
        }

        return response(["message"=> HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'user_login_success') ??'You have logged in successfully.','status'=>true,'token'=>$token ,'user_data'=>$user,'subscription_plan'=>$subscription_plan ,'subscription'=>$subscription_details],200);
    }

    public function logout(Request $request)
{
    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_user_logged_out') ??'You have been logged out successfully.',
    ]);
}
}
