<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameOver implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $result;
    public int $gameId;
    public int $userId;
    public string $type;
    public int $location;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($gameId, $userId, $result, $location, $type)
    {
        $this->gameId = $gameId;
        $this->type = $type;
        $this->location = $location;
        $this->userId = $userId;
        $this->result = $result;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('game-over-channel-' . $this->gameId . '-' . $this->userId);
    }
}
