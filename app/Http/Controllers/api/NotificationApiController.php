<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\HelperLanguage;
use DB;
class NotificationApiController extends Controller
{
    public function get_list(Request $request){
        try {
            $user_id =  $request->user_id??'';
            $response = AppNotification::with('user')->where('user_id', $user_id)->get();

            if(!empty($response)){
                AppNotification::where('user_id', $user_id)->update(['is_read' => 1,'read_at' => now()]);
                return response([
                    'message' => 'success.',
                    'status'  => true,
                    'data'    => $response
                ], 200);
            } else {
                $response = ["message" => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_notification_not_found') ?? "Notification not found",'status'=>false];
                return response($response, 422);
            }
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
 }

 public function notification_count(Request $request){
    $user_id =  $request->user_id??'';
    $response = AppNotification::where('is_read', '0')->where('user_id', $user_id)->count();
    return response(['message' => 'success.', 'status'  => true, 'notification' => $response], 200);
 }
}