<?php
/**
 * File name: ClinicChangedEvent.php
 * Last modified: 2021.01.02 at 21:11:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Events;

use App\Models\Clinic;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClinicChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $newClinic;

    public $oldClinic;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Clinic $newClinic, Clinic $oldClinic)
    {
        //
        $this->newClinic = $newClinic;
        $this->oldClinic = $oldClinic;
    }

}
