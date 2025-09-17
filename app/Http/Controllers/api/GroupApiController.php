<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\TroopersTogether; // Group model name 
use App\Models\UserGroup;
use App\Models\GroupsMessages;
use App\Models\GroupSettings;
use App\Models\User;
use App\Models\AppNotification;
use App\Events\GroupMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Models\languag;
use App\Models\HelperLanguage;
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
                    "message" => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_group_does_not_exist') ??"Group does not exist",
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
                'base_url' => Storage::disk('s3')->url(''),
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
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

            $body = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_say_hello_welcome') ?? 'Say hello 👋 and make them feel welcome.';
            $title = HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_joined_group') ?? 'You joined the group';
            AppNotification::create([
                'user_id' =>$request['user_id'],
                'type' => 'message',
                'title' =>  $title,
                'body' => $body,
                'channel' => 'in_app',
                'data' =>$request['group_id'],
            ]);

            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

     public function user_group_remove(Request $request)
    {
        try {

            $category = UserGroup::where('user_id', $request->user_id)->where('group_id', $request->group_id)->delete();

            AppNotification::where('user_id', $request->user_id)
                            ->where('type', 'message')
                            ->where('channel', 'in_app')
                            ->where('data', $request->group_id)
                            ->delete();
            
            $response = ['message'=> 'success', 'status'=>200];
            return response($response, 200);
        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    // Update or create settings for a user in a group
    public function user_group_setting(Request $request)
        {
            try {
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
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_settings_saved_successfully') ??'Settings saved successfully',
                    'status'  => true,
                    'data'    => $settings
                ], 200);
            } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
            }
        }
 // Update or create settings for a user in a group
    public function user_group_chat_clear(Request $request){
        try{
        // Update or create in single call
            $settings = GroupSettings::updateOrCreate(
                ['group_id' => $request->group_id, 'user_id' => $request->user_id],
                ['last_cleared_at' => Carbon::now()->format('Y-m-d H:i:s')]
            );
            $settings = GroupSettings::where('id',  $settings->id)->first();
            return response([
                'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_chat_cleared_successfully') ?? 'Chat Cleared successfully',
                'status'  => true,
                'data'    => $settings
            ], 200);
        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function group_message(Request $request)
    {
        try {

        $settings = GroupSettings::where('user_id',  Auth::id())->where('group_id', $request->group_id)->first();
        if(!empty($settings->last_cleared_at)){
            $messages = GroupsMessages::with('user')->where('created_at', '>=', $settings->last_cleared_at)->where('group_id', $request->group_id)->latest()->get();
        } else {
            $messages = GroupsMessages::with('user')->where('group_id', $request->group_id)->latest()->get();
        }
        
       
        return response([
                'message' => 'Get Group Messages successfully',
                'status'  => true,
                'base_url' => Storage::disk('s3')->url(''),
                'data'    =>  $messages
            ], 200);
        } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function group_message_store(Request $request)
    {
        try{

            if(!empty($request->message_type) AND ( $request->message_type == 'image' || $request->message_type == 'audio' )){
                $filePath = null;
                    if ($request->hasFile('file')) {
                        $filePath = $request->file('file')->store('messages', 's3');
                    }
                $message = $filePath;
            } else {
                $message = $request->message;
            }

            $message = GroupsMessages::create([
                'group_id' => $request->group_id,
                'user_id' => $request->user_id,
                'message_type' => $request->message_type,
                'content' => $message,

            ]);

            $message = GroupsMessages::with('user')->where('id',  $message->id)->first();

            broadcast(new GroupMessageSent($message));

            return response([
                    'message' => 'Store Group Messages successfully',
                    'status'  => true,
                    'data'    =>  $message,
                    'base_url' => Storage::disk('s3')->url('')
                ], 200);

       } catch(\Exception $e)  {
             return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }


}