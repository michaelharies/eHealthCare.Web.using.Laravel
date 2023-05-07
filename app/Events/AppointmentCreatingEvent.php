<?php
/*
 * File name: AppointmentCreatingEvent.php
 * Last modified: 2021.09.15 at 13:30:06
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        if (!empty($appointment->appointment_at)) {
            $appointment->appointment_at = convertDateTime($appointment->appointment_at);
        }
        if (!empty($appointment->start_at)) {
            $appointment->start_at = convertDateTime($appointment->start_at);
        }
        if (!empty($appointment->ends_at)) {
            $appointment->ends_at = convertDateTime($appointment->ends_at);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
