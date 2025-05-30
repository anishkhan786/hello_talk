<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        // Log::info("Broadcasting message for receiver: " . $message->conversation_id);
    }

    /**
     * The name of the channel to broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.' . $this->message->conversation_id);
    }

    /**
     * (Optional) Give a custom name to this broadcasted event.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    // broadcast data (optional, default $message data send)
        public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'translated_message' => $this->message->translated_message,
            'sender_id' => $this->message->sender_id,
            'type' => $this->message->type,
            'file' => $this->message->file,
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}
