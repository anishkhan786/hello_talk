<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->data['conversation_id']);
    }

    public function broadcastAs()
    {
        return 'typing.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->data['conversation_id'],
            'user_id' => $this->data['user_id'],
            'type' => $this->data['type']
        ];
    }
}

