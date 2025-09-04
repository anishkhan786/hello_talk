<?php
namespace App\Events;

use App\Models\GroupsMessages;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(GroupsMessages $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('group.' . $this->message->group_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

     // broadcast data (optional, default $message data send)
        public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'group_id' => $this->message->group_id,
            'user_id' => $this->message->user_id,
            'message_type' => $this->message->message_type,
            'created_at' => $this->message->created_at,
            'updated_at' => $this->message->updated_at,
            'user' => $this->message->user

        ];
    }
}
