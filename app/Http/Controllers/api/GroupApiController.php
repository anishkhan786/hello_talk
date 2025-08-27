<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\TroopersTogether; // Group model name 
use App\Models\UserGroup;
use App\Models\GroupsMessages;
use App\Models\GroupSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\languag;
use Carbon\Carbon;
class GroupApiController extends Controller
{
 public function group_list(Request $request)
    {
        try {
            $userId = $request->user_id ?? null;
            $type = $request->type;
            if($type == 'All'){
                $languageId = null;
            } else {
                $languageId = $type;
            }
            // Fetch all groups with join info in one query
            $groups = DB::table('groups as g')
                ->leftJoin('user_groups as ug', function ($join) use ($userId) {
                    $join->on('g.id', '=', 'ug.group_id')
                        ->where('ug.user_id', '=', $userId);
                })
                ->when($languageId, function ($query) use ($languageId) {
                    return $query->where('g.language_id', $languageId);
                })
                ->select(
                    'g.id as group_id',
                    'g.group_title',
                    'g.group_description',
                    'g.language_id',
                    DB::raw('CASE WHEN ug.user_id IS NOT NULL THEN 1 ELSE 0 END as is_joined')
                )
                ->get();

            if ($groups->isEmpty()) {
                return response([
                    "message" => "Group does not exist",
                    "status"  => false
                ], 422);
            }

            // Get all members for all groups in one query
            $allMembers = UserGroup::whereIn('group_id', $groups->pluck('group_id'))
                ->with(['user:id,name,country,learning_language,avatar,online_status',
                    'user.countryDetail',
                    'user.learningLanguageDetail'
                ])
                ->get()
                ->groupBy('group_id');

            // Build response data
            $data = $groups->map(function ($group) use ($allMembers) {
                $members = $allMembers[$group->group_id] ?? collect();

                $membersData = $members->pluck('user');
                $language_data = languag::where('id',$group->language_id)->first();
                return [
                    'group_id'        => $group->group_id,
                    'group_title'     => $group->group_title,
                    'group_description' => $group->group_description,
                    'group_language' => $language_data,

                    'is_joined'       => $group->is_joined,
                    'total_members'   => $membersData->count(),
                    'online_members'  => $membersData->where('online_status', 1)->count(),
                    'ofline_members'  => $membersData->where('online_status', 2)->count(),
                    'members_data'    => $membersData->values(),
                ];
            });

            return response([
                'message' => 'success.',
                'status'  => true,
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
            return response([
                'response' => [],
                'message'  => 'Some internal error occurred.',
                'status'   => false,
                'error'    => $e->getMessage()
            ], 400);
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

    // Update or create settings for a user in a group
    public function user_group_setting(Request $request)
        {
            // Validate incoming values
            $validated = $request->validate([
                'mute'          => 'nullable|in:1,2',
                'notifications' => 'nullable|in:1,2',
                'blocked'       => 'nullable|in:1,2',
                
            ]);

            // Prepare update data
            $data = [];
            if ($request->has('mute')) {
                $data['mute'] = $request->mute;
            }
            if ($request->has('notifications')) {
                $data['notifications'] = $request->notifications;
            }
            if ($request->has('blocked')) {
                $data['blocked'] = $request->blocked;
            }

            // Update or create in single call
            $settings = GroupSettings::updateOrCreate(
                ['group_id' => $request->group_id, 'user_id' => $request->user_id],
                $data
            );
            $settings = GroupSettings::where('id',  $settings->id)->first();
            return response([
                'message' => 'Settings saved successfully',
                'status'  => true,
                'data'    => $settings
            ], 200);
        }
 // Update or create settings for a user in a group
    public function user_group_chat_clear(Request $request){

        // Update or create in single call
            $settings = GroupSettings::updateOrCreate(
                ['group_id' => $request->group_id, 'user_id' => $request->user_id],
                ['last_cleared_at' => Carbon::now()->format('Y-m-d H:i:s')]
            );
            $settings = GroupSettings::where('id',  $settings->id)->first();
            return response([
                'message' => 'Settings saved successfully',
                'status'  => true,
                'data'    => $settings
            ], 200);
    }


}