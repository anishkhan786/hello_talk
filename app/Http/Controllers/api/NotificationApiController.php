<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
class NotificationApiController extends Controller
{
    public function get_list(Request $request){
        try {
            $user_id =  $request->user_id??'';
            $response = AppNotification::with('user')->where('user_id', $user_id)->get();

            if(!empty($response)){

                AppNotification::where('user_id', $user_id)->update([
                                        'is_read' => 1,
                                        'read_at' => now(),
                                    ]);

                return response([
                    'message' => 'success.',
                    'status'  => true,
                    'data'    => $response
                ], 200);
            } else {
                $response = ["message" => "Notification not found",'status'=>false];
                return response($response, 422);
            }
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
 }

 public function notification_count(Request $request){
    $user_id =  $request->user_id??'';
    $response = AppNotification::where('is_read', '0')->where('user_id', $user_id)->count();
    return response([
                    'message' => 'success.',
                    'status'  => true,
                    'notification' => $response
                ], 200);
 }
}