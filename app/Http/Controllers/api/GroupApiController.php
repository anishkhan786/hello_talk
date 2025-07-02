<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\TroopersTogether;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
class GroupApiController extends Controller
{
 public function group_list(Request $request){
    try {
       
        $userId = $request->has('user_id')?$request->user_id:'';
        $response = DB::table('groups as g')
                ->leftJoin('user_groups as ug', function ($join) use ($userId) {
                    $join->on('g.id', '=', 'ug.group_id')
                        ->where('ug.user_id', '=', $userId);
                })
                ->select(
                    'g.id as group_id',
                    'g.group_title',
                    'g.group_description',
                    DB::raw('CASE WHEN ug.user_id IS NOT NULL THEN 1 ELSE 0 END as is_joined')
                )
                ->get();

        if(!empty($response)){
            $response = ['message'=> 'success.','status'=>true,'data' => $response];
            return response($response, 200);
        } else {
            $response = ["message" => "Group does not exit",'status'=>FALSE];
            return response($response, 422);
        }
    } catch(\Exception $e)  {
        $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
        return response($response, 400);
    }
 }

 public function user_group_add(Request $request)
    {
        try {
            UserGroup::create([
                'user_id' => $request['user_id'],
                'group_id' => $request['group_id']
            ]);
            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }

     public function user_group_remove(Request $request)
    {
        try {

            $category = UserGroup::where('user_id', $request->user_id)->where('group_id', $request->group_id)->delete();
            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
            $response = ['response' => array(),'message'=>'Some internal error occurred.','status'=>false,'error'=>$e];
            return response($response, 400);
        }
    }
}