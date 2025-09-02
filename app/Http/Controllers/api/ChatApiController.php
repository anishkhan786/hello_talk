<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
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
// firebase

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Exception\FirebaseException;

class ChatApiController extends Controller
{
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
                $device_token = $user->fcm_token;
                $title = 'New Chat Request';
                $msg = 'Someone wants to chat with you!';
                $key= 'chat_connect';
                $this->notification_send($device_token,$title,$msg,$key);
            }

            $user = User::where('id', $user_id)->first();
            AppNotification::create([
                'user_id' => $user_id,
                'type' => 'message',
                'title' => 'New Chat Request',
                'body' => $user->name.' wants to chat with you!',
                'channel' => 'push'
            ]);
           
        }

        return response()->json($conversation);
    }

    // Send message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'type'            => 'required|in:text,image,audio',
            'message'         => 'nullable|string',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('messages', 'public');
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
                $device_token =$receiver->fcm_token;
                $title = 'New Message';
                $msg = 'You have a new message from '.$sender->name;
                $key= 'chat_messages';
                $this->notification_send($device_token,$title,$msg,$key);
            }

        // Broadcast the message
        // event(new \App\Events\MessageSent($message));
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => $message,
        ]);
    }


    // Fetch messages
    public function getMessages(Request $request)
    {
        $messages = Message::with('sender:id,name')->where('conversation_id', $request->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Optionally customize response
        $messages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name ?? null,
                'message' => $message->message,
                'translated_message' => $message->translated_message,
                'created_at' => $message->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['message' => 'success', 'status' => true,'data'=>$messages], 200);
    }

    public function get_chat_list(Request $request)
    {
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
    }

    public function generateAgoraToken(Request $request)
    {
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
    }

    public function endCall(Request $request)
    {
        $request->validate([
            'channel_name' => 'required',
        ]);

        $call = agora_call::where('channel_name', $request->channel_name)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if (!$call) {
            return response()->json(['message' => 'Call not found or already ended.'], 404);
        }

        $call->ended_at = Carbon::now();
        $call->save();

        return response()->json(['message' => 'Call ended successfully']);
    }

    public function callHistory()
    {
        $userId = auth()->id();
        $calls = agora_call::where('caller_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->latest()
            ->get();

        return response()->json($calls);
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

}
