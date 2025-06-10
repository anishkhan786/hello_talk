<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\conversation;
use App\Models\message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use App\Helpers\RtcTokenBuilder2;

class chatController extends Controller
{
    // Fetch or create a conversation
    public function getOrCreateConversation($receiver_id)
    {
        $user_id = Auth::id();

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
            'file'            => 'nullable|file',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('messages', 'public');
        }

        $sender = auth()->user();

        $conversation = Conversation::with(['userOne', 'userTwo'])->findOrFail($request->conversation_id);

        $receiver = $conversation->user_one_id === $sender->id
            ? $conversation->userTwo
            : $conversation->userOne;

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

        // Broadcast the message
        // event(new \App\Events\MessageSent($message));
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => $message,
        ]);
    }


    // Fetch messages
    public function getMessages($conversation_id)
    {
        $messages = Message::with('sender:id,name')->where('conversation_id', $conversation_id)
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

        return response()->json($messages);
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
    $channelName = $request->channel_name;
    $uid = $request->uid ?? rand(100000, 999999);
    $expireTimeInSeconds = 3600;

    $appID = config('services.agora.app_id');
    $appCertificate = config('services.agora.certificate');
    $currentTimestamp = now()->timestamp;
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    $token = \App\Helpers\RtcTokenBuilder2::buildTokenWithUid(
        $appID,
        $appCertificate,
        $channelName,
        $uid,
        \App\Helpers\RtcTokenBuilder2::ROLE_PUBLISHER,
        $privilegeExpiredTs
    );

    return response()->json([
        'token' => $token,
        'uid' => $uid,
        'channel_name' => $channelName
    ]);
}
}
