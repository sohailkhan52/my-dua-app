<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $fromUserId;
    public int $toUserId;

    /**
     * @param int $fromUserId  The user whose messages were read (the original sender)
     * @param int $toUserId    The user who just read the messages (the receiver)
     */
    public function __construct(int $fromUserId, int $toUserId)
    {
        $this->fromUserId = $fromUserId;
        $this->toUserId   = $toUserId;
    }

    /**
     * Broadcast on the original sender's private channel so they get notified.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('user.' . $this->fromUserId);
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'from_user_id' => $this->fromUserId,
            'to_user_id'   => $this->toUserId,
        ];
    }
}
