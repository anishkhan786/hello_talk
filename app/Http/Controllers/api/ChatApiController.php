<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Events\UserOnlineOffline;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\conversation;
use App\Models\message;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use App\Helpers\RtcTokenBuilder2;
use App\Models\agora_call;
use Carbon\Carbon;
use App\Models\User;
use App\Models\HelperLanguage;
use Illuminate\Support\Facades\Storage;

// firebase

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class ChatApiController extends Controller
{

    public function conversation_list_get(Request $request){
        $userId = Auth::id();
        $userId = $request->user_id;
        $conversation = Conversation::where('user_one_id', $userId)->orWhere('user_two_id', $userId)->get();

         $conversation = $conversation->map(function ($conversation) use($userId) {
            if($conversation->user_one_id == $userId){
                $receiver_id = $conversation->user_two_id;
            } else {
                $receiver_id = $conversation->user_one_id;
            }

            $conversation_block = $conversation->user_one_block;
            if($conversation->user_two_block == '1'){
                $conversation_block = 1;
            }

            $user = User::with('countryDetail','nativeLanguageDetail','learningLanguageDetail','knowLanguageDetail')->where('id', $receiver_id)->first();
            $count = message::where('conversation_id', $conversation->id)->where('sender_id','!=', $userId)->where('is_read', '0')->count();
             // Latest message
            $latestMessage = Message::where('conversation_id', $conversation->id)->latest('created_at')->first();
            
            
            return [
                'id' => $conversation->id,
                'user_one_id' => $conversation->user_one_id,
                'user_two_id' => $conversation->user_two_id,
                'conversation_block' =>$conversation_block,
                'user_data' => $user,
                'messages_count' => $count,
                'latestMessage'=>$latestMessage->created_at??$conversation->created_at

            ];
        });

        $conversation = $conversation->sortByDesc('latestMessage')->values();

         return response()->json([
            'message' => 'Conversation List',
            'data'    => $conversation,
            'base_url' => Storage::disk('s3')->url('')
        ]);
    }


    // Fetch or create a conversation
    public function getOrCreateConversation(Request $request)
    {
        $user_id = Auth::id();
        
        $receiver_id = $request->receiver_id;
        $conversation = Conversation::where(function ($query) use ($user_id, $receiver_id) {
            $query->where('user_one_id', $user_id)->where('user_two_id', $receiver_id);
        })->orWhere(function ($query) use ($user_id, $receiver_id) {
            $query->where('user_one_id', $receiver_id)->where('user_two_id', $user_id);
        })->first();

        if (!$conversation) {
            $conversation = conversation::create([
                'user_one_id' => $user_id,
                'user_two_id' => $receiver_id,
            ]);
           $user = User::where('id', $receiver_id)->first();
            if(!empty($user->fcm_token)){
                $language_code = language_code($user->interface_language);

                $device_token = $user->fcm_token;
                $title = HelperLanguage::retrieve_message_from_arb_file($language_code, 'web_push_new_title') ??'New Chat Request';
                $msg = HelperLanguage::retrieve_message_from_arb_file($language_code, 'web_push_new_message') ??'Someone wants to chat with you!';
                $key= 'chat_connect';
                $this->notification_send($device_token,$title,$msg,$key);

                AppNotification::create([
                        'user_id' => $receiver_id,
                        'type' => 'message',
                        'title' => $title,
                        'body' => $msg ,
                        'channel' => 'push',
                        'data' =>$user_id,
                    ]);
            }           
           
        }

        return response()->json($conversation);
    }

    // Send message
    public function sendMessage(Request $request)
    {
     try {

        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'type'            => 'required|in:text,image,audio',
            'message'         => 'nullable|string',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('messages', 's3');
        }

        $sender = auth()->user();

        $conversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($request->conversation_id);

        $receiver = $conversation->user_one_id === $sender->id ? $conversation->userTwo : $conversation->userOne;
        $translated = null;

        if ($request->type === 'text' && $request->message && $receiver) {
            $senderLang = $sender->native_language ?? 'English';
            $receiverLang = $receiver->learning_language ?? $receiver->native_language ?? 'English';

            // Detect actual message language (optional, based on accuracy needs)
            // $senderLang = detectLanguage($request->message);

            $translated = translateMessageWithOpenAI($request->message, $receiverLang, $senderLang);
        }
        // dd($translated);

        $message = message::create([
            'conversation_id'    => $request->conversation_id,
            'sender_id'          => Auth::id(),
            'type'               => $request->type,
            'message'            => $request->message,
            'translated_message' => $translated,
            'file'               => $filePath,
        ]);

         $user = User::where('id', $receiver->id)->first();

          if(!empty($user->fcm_token) AND $receiver->id != $sender->id){
                $language_code = language_code($user->interface_language);

                $device_token =$receiver->fcm_token;
                $title = HelperLanguage::retrieve_message_from_arb_file($language_code, 'web_push_new_title') ??'New Message';
                $msg = HelperLanguage::retrieve_message_from_arb_file($language_code, 'push_new_message_from') ?? 'You have a new message from';
                $msg = $msg.' '.$sender->name;
                $key= 'chat_messages';
                $this->notification_send($device_token,$title,$msg,$key);
            }

        // Broadcast the message
        // event(new \App\Events\MessageSent($message));
        broadcast(new MessageSent($message));

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => $message,
            'base_url' => Storage::disk('s3')->url('')
        ]);

        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }


    // Fetch messages
    public function getMessages(Request $request)
    {
        try {

            
        $conversation = Conversation::where('id', $request->conversation_id)->first();
        $userId = Auth::id();
      
        // Determine prefix (one/two)
        $delete_data = $conversation->user_one_id == $userId ? $conversation->user_one_chat_delete : $conversation->user_two_chat_delete;
       
        if(!empty($delete_data)){
            $messages = Message::with('sender:id,name')->where('created_at', '>=', $delete_data)->where('conversation_id', $request->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();
        } else {
            $messages = Message::with('sender:id,name')->where('conversation_id', $request->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();
        }

        // Optionally customize response
        $messages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name ?? null,
                'message' => $message->message,
                'translated_message' => $message->translated_message,
                'type' => $message->type,
                'file' => $message->file,
                'created_at' => $message->created_at->toDateTimeString(),
            ];
        });

        Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', '0')
                    ->update(['is_read' => 1]);

        return response()->json(['message' => 'success', 'status' => true, 'base_url' => Storage::disk('s3')->url(''), 'data'=>$messages], 200);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
   
   
    }

    public function get_chat_list(Request $request)
    {
        try {
        $userId = Auth::id();

        $conversations = Conversation::with(['userOne', 'userTwo', 'messages' => function ($q) {
            $q->latest()->limit(1); // get last message only
        }])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->get();

            $chatList = $conversations->map(function ($conv) use ($userId) {
            $otherUser = $conv->user_one_id == $userId ? $conv->userTwo : $conv->userOne;
            $lastMessage = $conv->messages->first();

            return [
                'conversation_id' => $conv->id,
                'user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'image' => $otherUser->avatar ? asset($otherUser->avatar) : null,
                ],
                'last_message' => $lastMessage?->message,
                'last_message_time' => $lastMessage?->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $chatList
        ]);

        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function generateAgoraToken(Request $request)
    {
        try {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $callerId = Auth::id();
        $receiverId = $request->receiver_id;

        // Generate consistent channel name for caller/receiver pair
        $channelName = "call_" . min($callerId, $receiverId) . "_" . max($callerId, $receiverId);
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        //  Check for existing active call between same users
        $existingCall = agora_call::where('caller_id', $callerId)
            ->where('receiver_id', $receiverId)
            ->where('channel_name', $channelName)
            ->whereNull('ended_at') // call still active
            ->latest()
            ->first();

        if ($existingCall) {
            return response()->json([
                'token' => $existingCall->token,
                'uid' => $existingCall->agora_uid,
                'channel_name' => $existingCall->channel_name,
                'existing' => true,
            ]);
        }

        // 🔐 Agora credentials
        $appID = config('services.agora.app_id');
        $appCertificate = config('services.agora.certificate');
        $uid = 0;

        $token = RtcTokenBuilder2::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            RtcTokenBuilder2::ROLE_PUBLISHER,
            $privilegeExpiredTs
        );

        //Save new call
        agora_call::create([
            'caller_id' => $callerId,
            'receiver_id' => $receiverId,
            'channel_name' => $channelName,
            'agora_uid' => $uid,
            'token' => $token,
            'started_at' => Carbon::now(),
        ]);


        return response()->json([
            'token' => $token,
            'uid' => $uid,
            'channel_name' => $channelName,
            'existing' => false,
        ]);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function endCall(Request $request)
    {
        try {
            $request->validate([
                'channel_name' => 'required',
            ]);

            $call = agora_call::where('channel_name', $request->channel_name)
                ->whereNull('ended_at')
                ->latest()
                ->first();

            if (!$call) {
                return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_call_not_found_or_already_ended') ??'Call not found or already ended.'], 404);
            }

            $call->ended_at = Carbon::now();
            $call->save();

             $conversation = Conversation::where(function ($q) use ($call) {
                                    $q->where('user_one_id', $call->caller_id)
                                    ->where('user_two_id', $call->receiver_id);
                                })
                                ->orWhere(function ($q) use ($call) {
                                    $q->where('user_one_id', $call->receiver_id)
                                    ->where('user_two_id', $call->caller_id);
                                })
                                ->first();

             $message = message::create([
                    'conversation_id'    => $conversation->id,
                    'sender_id'          => $call->caller_id,
                    'type'               => 'voice_call',
                    'message'            => 'Audio call ended',
                ]);

            return response()->json(['message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_call_ended_successfully') ??'Call ended successfully']);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function callHistory()
    {
        try{
            $userId = auth()->id();
            $calls = agora_call::where('caller_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->latest()
                ->get();

            return response()->json($calls);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

     public function sendCallNotification(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'callerId'     => 'required',
            'callerName'   => 'required',
            'callerImage'  => 'nullable',
            'recipientId'  => 'required',
            'channelName'  => 'required',
            'callType'     => 'required', // audio / video
           
        ]);

        // Step 2: If basic validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

        $user = User::where('id', $request->recipientId)->first();
       
        
        $messaging = app('firebase.messaging');
        $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create(
           'Incoming Call',
          " Call",
        ))
        ->withData([
            'custom_key' => 'call_invitation',
             'callerId'    => $request->callerId,
                'callerName'  => $request->callerName,
                'callerImage' => $request->callerImage,
                'recipientId' => $request->recipientId,
                'channelName' => $request->channelName,
                'callType'    => $request->callType,
                'channelToken'    => $request->channelToken,
                'channeluuid'    => $request->channeluuid,
                'timestamp'   => now()->toIso8601String(),
        ]);

        try {
            $conversation = Conversation::where(function ($q) use ($request) {
                                    $q->where('user_one_id', $request->callerId)
                                    ->where('user_two_id', $request->recipientId);
                                })
                                ->orWhere(function ($q) use ($request) {
                                    $q->where('user_one_id', $request->recipientId)
                                    ->where('user_two_id', $request->callerId);
                                })
                                ->first();

           
            

            $response = $messaging->send($message);
             message::create([
                    'conversation_id'    => $conversation->id,
                    'sender_id'          => $request->callerId,
                    'type'               => 'voice_call',
                    'message'            => 'Audio call',
                ]);
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully!',
                'response' => $response
            ],200);
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

         public function respondToCall(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'recipientId'     => 'required',
            'callId'   => 'required',
            'response'  => 'nullable',
            'reason'  => 'nullable',
           
        ]);

        // Step 2: If basic validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

        $user = User::where('id', $request->recipientId)->first();
        
        $messaging = app('firebase.messaging');
        $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create(
           '',
          '',
        ))
        ->withData([
            'custom_key' => 'end_invitation',
            'recipientId'    => $request->recipientId,
            'callId'  => $request->callId,
            'response' => $request->response,
            'reason' => $request->reason,
            'timestamp'   => now()->toIso8601String(),
        ]);

        try {
            $response = $messaging->send($message);
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully!',
                'response' => $response
            ],200);
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


    public function notification_send($device_token,$title,$msg,$key)
    {
       
        $messaging = app('firebase.messaging');
        
       $message = CloudMessage::withTarget('token', $device_token)
        ->withNotification(Notification::create(
            $title,
            $msg
        ))
        ->withData([
            'screen' => 'ChatScreen',   // example key
            'custom_key' => $key
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
    
    public function userOneToOneTyping(Request $request)
    {
        try{

           $conversation_id = $request->conversation_id??'';
           $user_id = $request->user_id;
           $is_typing = $request->is_typing;

           $data = [
                    'conversation_id' => $conversation_id,
                    'user_id' => $user_id,
                    'is_typing' => $is_typing
                 ];

            broadcast(new UserTyping($data));
            return response()->json([
                'status'   => true,
                'code'     => 200,
            ]);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function userOnlineOffline(Request $request)
    {
        try{

           $user_id = $request->user_id;
           $is_online = $request->is_online??2;

           $data = [
                    'user_id' => $user_id,
                    'is_online' => $is_online
                 ];

            User::where('id', $user_id)->update(['online_status' => $is_online]);

            broadcast(new UserOnlineOffline($data));
            return response()->json([
                'status'   => true,
                'code'     => 200,
            ]);
        } catch(\Exception $e)  {
            return response()->json([
                    'message' => HelperLanguage::retrieve_message_from_arb_file($request->language_code, 'web_internal_error') ?? 'Some internal error occurred. Please try again later.',
                    'status' => false,
                    'error' => $e->getMessage()
                ], 400);
        }
    }

    public function userChatSetting(Request $request)
    {
        try {
            $conversation_id = $request->conversation_id;
            $user_id = $request->user_id;

            // Validate conversation exists
            $conversation = Conversation::find($conversation_id);
            if (!$conversation) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Conversation not found.'
                ], 404);
            }

            // Determine prefix (one/two)
            $prefix = $conversation->user_one_id == $user_id ? 'one' : 'two';

            // Mapping request keys to DB fields
            $fields = [
                'user_block'        => "user_{$prefix}_block",
                'user_notification' => "{$prefix}_user_notification",
                'user_call'         => "{$prefix}_user_call",
                'user_chat_delete'  => "user_{$prefix}_chat_delete",
            ];

            // Update only available fields
            foreach ($fields as $requestKey => $dbField) {
                if ($request->has($requestKey)) {
                    if($requestKey == 'user_chat_delete'){
                        $conversation->$dbField = now();
                    } else {
                        $conversation->$dbField = $request->$requestKey;
                    }
                    
                }
            }

            $conversation->save();
            
            $data = array();

            
            $conversation = Conversation::find($conversation_id);
            if($conversation->user_one_id == $user_id){
                $data = array(
                        'user_block'        => $conversation->user_one_block,
                        'user_notification' => $conversation->one_user_notification,
                        'user_call'         => $conversation->one_user_call,
                        );
            } else {
                $data = array(
                        'user_block'        => $conversation->user_two_block,
                        'user_notification' => $conversation->two_user_notification,
                        'user_call'         => $conversation->two_user_call,
                        );
            }


            $data['conversation_block'] = $conversation->user_one_block;
            if($conversation->user_two_block == '1'){
                $data['conversation_block'] = 1;
            }

            if($data['conversation_block'] == '1'){
                 $data['conversation_user_call'] = 1;
            } else {
                $data['conversation_user_call'] = $conversation->one_user_call;
            }
            
            if($conversation->two_user_call == '1'){
                $data['conversation_user_call'] = 1;
            }

            return response()->json([
                'status' => true,
                'code'   => 200,
                'message'=> 'User chat settings updated successfully.',
                'chat_setting'   => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => HelperLanguage::retrieve_message_from_arb_file(
                    $request->language_code,
                    'web_internal_error'
                ) ?? 'Some internal error occurred. Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
