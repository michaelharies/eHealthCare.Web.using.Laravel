<?php
/*
 * File name: ChangeCustomerRoleToClinic.php
 * Last modified: 2021.09.15 at 13:38:29
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Listeners;

/**
 * Class ChangeCustomerRoleToClinic
 * @package App\Listeners
 */
class ChangeCustomerRoleToClinic
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->newClinic->accepted) {
            //$event->newClinic->user->syncRoles(['clinic_owner']);
        }
    }
}
