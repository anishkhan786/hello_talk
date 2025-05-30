<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Allow only if user is part of the conversation (optional security)
    return true; // You can customize this check
});
