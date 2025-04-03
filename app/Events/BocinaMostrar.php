<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Bocina;

class BocinaMostrar implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $estado;
    public $id_monitor;
    public $id_user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($estado,$id_monitor=null,$id_user=null)
    {
        $this->estado=$estado;
        $this->id_monitor=$id_monitor;
        $this->id_user=$id_user;
        //dd($this->estado,$this->id_monitor,$this->id_user);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('estado-websocket');
    }
    public function broadcastAs()
    {
        return 'estado-bocina';
    }
}
